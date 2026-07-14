/* =====================================================================
   jg-datedrawer.js -- JOWBR Hebrew Date Converter drawer
   =====================================================================
   Load order on the host page:
     1. jg-hebcal.min.js   (defines window.JGHebcal: HDate, months, greg)
     2. jg-datedrawer.js   (this file)
   Then, inside the page's Promise.all([...loadComponent...]).then():
     jgDateDrawerInit();
   The trigger button is injected into the record .action-bar
   automatically once the record renders (MutationObserver on
   #jowbr-content), so renderRecord() needs no changes.
   CHW
   ===================================================================== */

function jgDateDrawerInit() {
    var drawer   = document.getElementById('jg-datedrawer');
    var backdrop = document.getElementById('jg-datedrawer-backdrop');
    if (!drawer || !backdrop || typeof JGHebcal === 'undefined') {
        if (typeof console !== 'undefined') {
            console.warn('jg-datedrawer: component or hebcal bundle missing; drawer disabled.');
        }
        return;
    }

    var HDate  = JGHebcal.HDate;
    var months = JGHebcal.months;

    var closeBtn   = document.getElementById('jg-datedrawer-close');
    var triggerBtn = null;   /* set when injected into the action bar */
    var lastFocus  = null;

    /* ---------------- Open / close / focus ---------------- */

    function openDrawer() {
        lastFocus = document.activeElement;
        drawer.classList.add('open');
        backdrop.classList.add('open');
        if (triggerBtn) triggerBtn.setAttribute('aria-expanded', 'true');
        /* Move focus to the first field after the slide transition */
        var first = document.getElementById('jg-dd-gday');
        window.setTimeout(function () { if (first) first.focus(); }, 260);
    }

    function closeDrawer() {
        drawer.classList.remove('open');
        backdrop.classList.remove('open');
        if (triggerBtn) triggerBtn.setAttribute('aria-expanded', 'false');
        if (lastFocus && typeof lastFocus.focus === 'function') lastFocus.focus();
        lastFocus = null;
    }

    closeBtn.addEventListener('click', closeDrawer);
    backdrop.addEventListener('click', closeDrawer);

    document.addEventListener('keydown', function (e) {
        if (!drawer.classList.contains('open')) return;
        if (e.key === 'Escape') {
            closeDrawer();
            return;
        }
        /* Keep Tab focus inside the drawer while it is open */
        if (e.key === 'Tab') {
            var focusables = drawer.querySelectorAll(
                'button, input, select, a[href]');
            if (focusables.length === 0) return;
            var firstEl = focusables[0];
            var lastEl  = focusables[focusables.length - 1];
            if (e.shiftKey && document.activeElement === firstEl) {
                e.preventDefault();
                lastEl.focus();
            } else if (!e.shiftKey && document.activeElement === lastEl) {
                e.preventDefault();
                firstEl.focus();
            }
        }
    });

    /* ---------------- Result helpers ---------------- */

    function showResult(el, html, isError) {
        el.innerHTML = html;
        el.hidden = false;
        if (isError) el.classList.add('jg-dd-error');
        else el.classList.remove('jg-dd-error');
    }

    function esc(s) {
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    function weekdayName(d) {
        var names = ['Sunday', 'Monday', 'Tuesday', 'Wednesday',
                     'Thursday', 'Friday', 'Saturday'];
        return names[d.getDay()];
    }

    function civilMonthName(m) {
        var names = ['January', 'February', 'March', 'April', 'May', 'June',
                     'July', 'August', 'September', 'October', 'November',
                     'December'];
        return names[m];
    }

    /* ---------------- Gregorian -> Hebrew ---------------- */

    function convertG2H() {
        var out   = document.getElementById('jg-dd-g2h-result');
        var day   = parseInt(document.getElementById('jg-dd-gday').value, 10);
        var month = parseInt(document.getElementById('jg-dd-gmonth').value, 10);
        var year  = parseInt(document.getElementById('jg-dd-gyear').value, 10);
        var sunset = document.getElementById('jg-dd-sunset').checked;

        if (isNaN(day) || isNaN(year)) {
            showResult(out, 'Please enter a day and a year.', true);
            return;
        }
        if (year < 1600 || year > 2200) {
            showResult(out, 'Please enter a civil year between 1600 and 2200.', true);
            return;
        }
        /* Validate the civil date is real (e.g. no Feb 30) */
        var g = new Date(year, month - 1, day);
        if (g.getFullYear() !== year || g.getMonth() !== month - 1 ||
            g.getDate() !== day) {
            showResult(out, esc(civilMonthName(month - 1)) + ' ' + day + ', ' +
                year + ' is not a valid civil date.', true);
            return;
        }

        var hd = new HDate(g);
        if (sunset) hd = hd.next();

        var hebrew = '';
        if (typeof hd.renderGematriya === 'function') {
            hebrew = '<span class="jg-dd-hebrew" lang="he">' +
                esc(hd.renderGematriya()) + '</span>';
        }
        showResult(out,
            esc(weekdayName(g)) + ', ' + esc(civilMonthName(month - 1)) + ' ' +
            day + ', ' + year + (sunset ? ' (after sunset)' : '') +
            ' corresponds to<br><strong>' + hd.getDate() + ' ' +
            esc(hd.getMonthName()) + ' ' + hd.getFullYear() + '</strong>' +
            hebrew, false);
    }

    /* ---------------- Hebrew -> Gregorian ---------------- */

    function convertH2G() {
        var out   = document.getElementById('jg-dd-h2g-result');
        var day   = parseInt(document.getElementById('jg-dd-hday').value, 10);
        var month = parseInt(document.getElementById('jg-dd-hmonth').value, 10);
        var year  = parseInt(document.getElementById('jg-dd-hyear').value, 10);

        if (isNaN(day) || isNaN(year)) {
            showResult(out, 'Please enter a day and a year.', true);
            return;
        }
        if (year < 5360 || year > 5960) {
            showResult(out, 'Please enter a Hebrew year between 5360 and 5960 ' +
                '(circa 1600 to 2200 CE).', true);
            return;
        }
        var leap = HDate.isLeapYear(year);
        if (month === months.ADAR_II && !leap) {
            showResult(out, year + ' is not a leap year, so it has no Adar II. ' +
                'Choose Adar instead.', true);
            return;
        }
        var maxDay = HDate.daysInMonth(month, year);
        if (day < 1 || day > maxDay) {
            var mLabel = (month === months.ADAR_I && !leap) ? 'Adar'
                : new HDate(1, month, year).getMonthName();
            showResult(out, esc(mLabel) + ' ' + year + ' has only ' + maxDay +
                ' days.', true);
            return;
        }

        var hd = new HDate(day, month, year);
        var g  = hd.greg();
        var prev = new Date(g.getTime());
        prev.setDate(prev.getDate() - 1);

        showResult(out,
            '<strong>' + hd.getDate() + ' ' + esc(hd.getMonthName()) + ' ' +
            hd.getFullYear() + '</strong> corresponds to<br><strong>' +
            esc(weekdayName(g)) + ', ' + esc(civilMonthName(g.getMonth())) +
            ' ' + g.getDate() + ', ' + g.getFullYear() + '</strong>' +
            '<br><small>The Hebrew day begins the previous evening, at ' +
            'sunset on ' + esc(civilMonthName(prev.getMonth())) + ' ' +
            prev.getDate() + ', ' + prev.getFullYear() + '.</small>', false);
    }

    document.getElementById('jg-dd-g2h-btn').addEventListener('click', convertG2H);
    document.getElementById('jg-dd-h2g-btn').addEventListener('click', convertH2G);

    /* Enter key inside a section triggers that section's convert */
    drawer.querySelectorAll('section').forEach(function (sec) {
        sec.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter') return;
            if (e.target.tagName === 'BUTTON' || e.target.tagName === 'A') return;
            e.preventDefault();
            var btn = sec.querySelector('.jg-datedrawer__convert');
            if (btn) btn.click();
        });
    });

    /* ---------------- Trigger button injection ---------------- */
    /* The record page builds .action-bar inside #jowbr-content after
       its own async fetch. Watch for it, then insert the trigger. */

    function makeTrigger() {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn-print';   /* reuse existing action-bar style */
        btn.id = 'jg-datedrawer-trigger';
        btn.innerHTML = '<img src="/images/site/date_blue.svg" alt="" ' +
            'aria-hidden="true" class="jg-dd-icon"> Hebrew Date Converter';
        btn.setAttribute('aria-haspopup', 'dialog');
        btn.setAttribute('aria-expanded', 'false');
        btn.addEventListener('click', openDrawer);
        return btn;
    }

    function injectTrigger() {
        if (document.getElementById('jg-datedrawer-trigger')) return true;
        var bar = document.querySelector('#jowbr-content .action-bar');
        if (!bar) return false;
        triggerBtn = makeTrigger();
        var idNote = bar.querySelector('.record-id-note');
        if (idNote) bar.insertBefore(triggerBtn, idNote);
        else bar.appendChild(triggerBtn);
        return true;
    }

    if (!injectTrigger()) {
        var content = document.getElementById('jowbr-content');
        if (content && typeof MutationObserver !== 'undefined') {
            var mo = new MutationObserver(function () {
                if (injectTrigger()) mo.disconnect();
            });
            mo.observe(content, { childList: true, subtree: true });
        }
    }
}