/* =====================================================================
   jg-glance.js  --  At a Glance auto-population (JG40)
   ---------------------------------------------------------------------
   Pulls the two values that actually change over time -- Total Records
   and Last Updated -- from datasets-unified.json (the single source of
   truth) into a dataset description page's "At a Glance" sidebar.

   PAGE OPT-IN (minimum):
     1. Add  data-slug="<slug>"  to any element on the page
        (the dataset sidebar is a natural home).
     2. Include this script:  <script src="/jg-glance.js"></script>

   The script gathers EVERY record that shares that slug (so combined
   pages like Bessarabia Vital, which is Births + Marriages + Deaths under
   one slug, just work): it SUMS record_count and shows the most recent
   last_updated.

   OPTIONAL breakdown pills:
     Put  data-jg-pill="birth"  (or "marriage,divorce", "death", ...) on a
     count element; it gets filled with the summed count of sub-records
     whose record_types match. Multiple types are comma-separated.

   OPTIONAL explicit targets (override label matching):
     data-jg-glance="records"   on the Total Records value element
     data-jg-glance="updated"   on the Last Updated value element

   SAFE BY DEFAULT: if there's no data-slug, the file can't load, or no
   record matches, the page's hand-typed values are left untouched.
   CHW + JG40
   ===================================================================== */
(function () {
    var DATA_URL = '/catalog/datasets-unified.json';

    var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June',
                  'July', 'August', 'September', 'October', 'November', 'December'];

    function ready(fn) {
        if (document.readyState !== 'loading') fn();
        else document.addEventListener('DOMContentLoaded', fn);
    }

    function getSlug() {
        var el = document.querySelector('[data-slug]');
        return el ? (el.getAttribute('data-slug') || '').trim() : '';
    }

    function formatNumber(n) {
        return (typeof n === 'number' && !isNaN(n)) ? n.toLocaleString('en-US') : null;
    }

    /* Parse mixed date formats to { time, label }.
       M/D/YYYY and YYYY-MM-DD become "Month YYYY"; a bare year stays the
       year; anything else is kept as-is so we never invent a date. */
    function parseUpdated(s) {
        if (!s) return null;
        s = String(s).trim();
        var y, mo, d;
        if (s.indexOf('/') !== -1) {
            var p = s.split('/');
            if (p.length === 3) { mo = +p[0]; d = +p[1]; y = +p[2]; }
        } else if (/^\d{4}-\d{1,2}/.test(s)) {
            var q = s.split('-');
            y = +q[0]; mo = +q[1]; d = +(q[2] || 1);
        } else if (/^\d{4}$/.test(s)) {
            return { time: new Date(+s, 0, 1).getTime(), label: s };
        } else {
            return { time: NaN, label: s };
        }
        if (!y || !mo || mo < 1 || mo > 12) return { time: NaN, label: s };
        return { time: new Date(y, mo - 1, d || 1).getTime(),
                 label: MONTHS[mo - 1] + ' ' + y };
    }

    function mostRecentLabel(records) {
        var best = null;
        records.forEach(function (r) {
            var u = parseUpdated(r.last_updated);
            if (!u) return;
            if (best === null) { best = u; return; }
            var a = isNaN(best.time) ? -Infinity : best.time;
            var b = isNaN(u.time)    ? -Infinity : u.time;
            if (b > a) best = u;
        });
        return best ? best.label : null;
    }

    /* Fill a value: explicit [data-jg-glance] hook wins; otherwise match the
       sidebar row whose label reads e.g. "total records" / "last updated". */
    function fillValue(hookName, labelText, value) {
        if (value === null || value === undefined) return;
        var explicit = document.querySelector('[data-jg-glance="' + hookName + '"]');
        if (explicit) { explicit.textContent = value; return; }
        var rows = document.querySelectorAll('.sidebar-row');
        for (var i = 0; i < rows.length; i++) {
            var lbl = rows[i].querySelector('.sidebar-label');
            if (lbl && lbl.textContent.trim().toLowerCase() === labelText) {
                var val = rows[i].querySelector('.sidebar-value');
                if (val) val.textContent = value;
                return;
            }
        }
    }

    function fillPills(records) {
        var pills = document.querySelectorAll('[data-jg-pill]');
        Array.prototype.forEach.call(pills, function (pill) {
            var types = (pill.getAttribute('data-jg-pill') || '')
                .split(',').map(function (t) { return t.trim().toLowerCase(); })
                .filter(Boolean);
            if (!types.length) return;
            var sum = 0, any = false;
            records.forEach(function (r) {
                var rts = (r.record_types || []).map(function (x) { return String(x).toLowerCase(); });
                var hit = rts.some(function (t) { return types.indexOf(t) !== -1; });
                if (hit && typeof r.record_count === 'number') { sum += r.record_count; any = true; }
            });
            if (any) pill.textContent = formatNumber(sum);
        });
    }

    ready(function () {
        var slug = getSlug();
        if (!slug) return;                              /* not a wired page */
        fetch(DATA_URL)
            .then(function (r) { if (!r.ok) throw new Error('jg-glance: HTTP ' + r.status); return r.json(); })
            .then(function (data) {
                var all = Array.isArray(data) ? data
                    : (data.datasets && Array.isArray(data.datasets)) ? data.datasets : [];
                var mine = all.filter(function (r) {
                    return String(r.slug || '').toLowerCase() === slug.toLowerCase();
                });
                if (!mine.length) return;               /* leave hand-typed values alone */

                var total = 0, haveTotal = false;
                mine.forEach(function (r) {
                    if (typeof r.record_count === 'number') { total += r.record_count; haveTotal = true; }
                });
                if (haveTotal) fillValue('records', 'total records', formatNumber(total));
                fillValue('updated', 'last updated', mostRecentLabel(mine));
                fillPills(mine);
            })
            .catch(function (err) { if (window.console && console.warn) console.warn(err); });
    });
})();
