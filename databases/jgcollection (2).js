/* ============================================================================
   jg-collection.js  -  Shared JS for /databases/ collection pages
   Project Shorashim / JG40-Build
   ----------------------------------------------------------------------------
   Loaded by /databases/<Region>/index.php AFTER the legacy search JS
   (FormUtils, Utils, RegionsData_solr, Regions, Overlay, SearchForm_solr).

   Host page calls, in its own script block:

       jgCollectionInit({
           regionCode: 'ALLROMANIA',   // Solr code - drives setRegions()
           regionTag:  'romania'       // stats datasets.json regions[] tag
       });

   Provides: header/footer loading, tips toggle, legacy form init wiring,
   SVG icon sprite injection, and the region-filtered card listing.

   CARD MIRROR NOTE: buildCard() and its helpers are ported from
   catalog.html. Until the catalog is refactored to consume this file,
   changes to card markup must be made in BOTH places.
   ASCII only.  CHW
============================================================================ */

/* -- Tips panel toggle (referenced by onclick in searchform.php; panel is
      the shared /SearchTips.html component, same as Global_Search.php) ---- */
function toggleSearchTips(btn) {
    var panel = document.getElementById('jg-search-tips-panel');
    if (!panel) return;
    var isOpen = panel.classList.toggle('open');
    btn.setAttribute('aria-expanded', String(isOpen));
}

function jgCollectionInit(config) {

    config = config || {};
    var REGION_SYS  = config.regionSys || config.regionCode || 'ALLALL';  /* reg_data sys id for setRegions() */
    /* A dataset belongs to this collection when primary_region OR any regions[]
       entry matches (case-insensitive). regionTag accepts a string or an array
       of aliases, e.g. Netherlands: ['Netherlands','Holland']. */
    var REGION_TAGS = (function(){
        var t = config.regionTag || '';
        if (Object.prototype.toString.call(t) !== '[object Array]') { t = [t]; }
        var out = [];
        for (var i=0;i<t.length;i++){ var v=String(t[i]).toLowerCase(); if(v) out.push(v); }
        return out;
    })();
    var SUBREGIONS  = config.subregions || '';   /* 'us-states' adds a State filter to the bar */
    var SEARCH_URL  = config.searchUrl || 'https://www.jewishgen.org/databases/all/';
    var UPDATED_WINDOW_DAYS = 90;

    /* ==================================================================
       1. SVG ICON SPRITE (injected so host pages stay thin)
       ================================================================== */
    var SPRITE = '<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">'
        + '<symbol id="jg-ic-date" viewBox="0 0 24 24"><rect x="4" y="5" width="16" height="16" rx="2" fill="none" stroke="currentColor" stroke-width="1.7"/><line x1="4" y1="9" x2="20" y2="9" stroke="currentColor" stroke-width="1.7"/><line x1="8" y1="3" x2="8" y2="6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><line x1="16" y1="3" x2="16" y2="6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></symbol>'
        + '<symbol id="jg-ic-records" viewBox="0 0 24 24"><path d="M6 3h8l4 4v14H6z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M14 3v4h4" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><line x1="9" y1="12" x2="15" y2="12" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><line x1="9" y1="16" x2="15" y2="16" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></symbol>'
        + '<symbol id="jg-ic-language" viewBox="0 0 24 24"><path d="M4 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H9l-4 4v-4H5a1 1 0 0 1-1-1z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></symbol>'
        + '<symbol id="jg-ic-updated" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="1.7"/><path d="M12 7v5l3.5 2" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></symbol>'
        + '</svg>';
    document.body.insertAdjacentHTML('afterbegin', SPRITE);

   
    /* ==================================================================
       2. HEADER / FOOTER + keyboard nav (canonical fetch pattern)
       ================================================================== */
    function loadComponent(id, file) {
        return fetch(file)
            .then(function(r) { if (!r.ok) throw new Error('Could not load ' + file); return r.text(); })
            .then(function(html) { document.getElementById(id).innerHTML = html; })
            .catch(function(err) { console.warn(err); });
    }

    Promise.all([
        loadComponent('site-header', '/Header_NavBar.html'),
        loadComponent('site-footer', '/Footer.html'),
        loadComponent('jg-search-tips', '/SearchTips.html')
    ]).then(function() {
        document.querySelectorAll('.jg-nav .dropbtn, .main-nav .dropbtn').forEach(function(btn) {
            btn.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    var menu = this.nextElementSibling;
                    if (menu) menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
                }
                if (e.key === 'Escape') {
                    var menu = this.nextElementSibling;
                    if (menu) menu.style.display = 'none';
                }
            });
        });
    });

    /* ==================================================================
       3. LEGACY SEARCH FORM WIRING (region fixed to this collection)
       ================================================================== */
    function ConfigSubReg() {
        var subreg_sel = document.getElementById('GeoRegion');
        var subreg_div = document.getElementById('SubRegionsDiv');
        if (!subreg_sel || !subreg_div) return;
        subreg_div.style.display = (subreg_sel.options.length <= 1) ? 'none' : 'block';
    }

    function SetCollectionRegion() {
        try {
            if (typeof setRegions === 'function') setRegions(REGION_SYS);
            ConfigSubReg();
            var regval = (typeof getQueryVariable === 'function') ? getQueryVariable('region') : null;
            if (regval && typeof setOption === 'function') setOption('GeoRegion', regval);
        } catch (e) { console.warn('SetCollectionRegion: ', e); }
    }

    window.addEventListener('load', function() {
        if (typeof InitSearchForm === 'function') InitSearchForm();
        SetCollectionRegion();
    });
    window.addEventListener('pageshow', function() { SetCollectionRegion(); });
    window.addEventListener('pagehide', function() {
        var gr = document.getElementById('GeoRegion');
        if (gr && typeof set_cookie === 'function') { set_cookie(gr.value); }
        if (typeof EnableForm === 'function') EnableForm(document.f);
    });

    /* ==================================================================
       4. DATABASE LISTING (flat, region-filtered catalog cards)
       Ported from catalog.html - see CARD MIRROR NOTE in header.
       ================================================================== */
    var REGION_LABELS = {
        romania:'Romania / Moldova', bessarabia:'Bessarabia', subcarpathia:'Subcarpathia',
        ukraine:'Ukraine', hungary:'Hungary / Slovakia', moldova:'Moldova',
        austria:'Austria / Czechia', russia:'Russia', maramaros:'Maramaros',
        bukovina:'Bukovina', czechoslovakia:'Czechoslovakia', sephardic:'Sephardic',
        usa:'USA', 'europe-multiple':'Europe (multiple)', germany:'Germany',
        poland:'Poland', belarus:'Belarus', latvia:'Latvia', lithuania:'Lithuania',
        holocaust:'Holocaust', canada:'Canada', scandinavia:'Scandinavia'
    };

    function getName(d)      { return d.display_name || d.name || ''; }
    function getYears(d)     { return d.years || '\u2014'; }
    function getCountNum(d)  { var n = d.record_count != null ? d.record_count : d.recordCount; return typeof n === 'number' ? n : (parseInt(String(n).replace(/[^0-9]/g,''),10) || 0); }
    function getCount(d)     { var n = getCountNum(d); return n ? n.toLocaleString('en-US') : '\u2014'; }
    function getLanguage(d)  { return d.source_language || d.language || '\u2014'; }
    function getUpdatedRaw(d){ return d.last_updated || d.lastUpdated || ''; }
    function getDesc(d)      { return (d.description || '').trim(); }
    function getInfoUrl(d)   { return JGStats.localUrl(d.info_url || d.infoUrl || ''); }
    function getSource(d)    { return d.source_citation || d.sources || '\u2014'; }
    function getRegions(d)   { return (d.regions && d.regions.length) ? d.regions : []; }
    function getPrimary(d)   { return String(d.primary_region || '').toLowerCase(); }
    function isFeatured(d)   { return d.is_featured === true; }

    /* URL-family merge (same rule as jg-glance): rows in the stats file that
       share a new_url/info_url PATH are one combined description page (e.g.
       Hungarian Births + Deaths + Marriages on hungaryvitalrecords.html).
       Show ONE card per URL: record_count summed, most recent last_updated,
       record_types and regions unioned. The slugged row (uq_slug marks the
       primary) names the card; a multi-row card derives its title from the
       slug (camel-case split), since sub-row names like "Hungarian Births"
       don't describe the whole page. */

    /* Data rules live in /jg-stats.js (single source of truth). Load it on
       demand so host pages need no extra script tag. */
    function jgEnsureStats() {
        return new Promise(function(resolve, reject) {
            if (window.JGStats) { resolve(); return; }
            var s = document.createElement('script');
            s.src = '/jg-stats.js';
            s.onload = function() { resolve(); };
            s.onerror = function() { reject(new Error('jg-stats.js failed to load')); };
            document.head.appendChild(s);
        });
    }

    /* Collection membership: primary_region OR any regions[] entry equals one
       of this page's tags (case-insensitive). This is what makes crossover
       datasets (e.g. Burgenland: regions ["AustriaCzech","Hungary"]) appear on
       every listed collection's page. */
    function belongsToCollection(d) {
        return JGStats.belongs(d, '', REGION_TAGS);
    }

    /* US state abbreviation -> full name (for the USA listing State filter) */
    var US_STATES = {AL:'Alabama',AK:'Alaska',AZ:'Arizona',AR:'Arkansas',CA:'California',
        CO:'Colorado',CT:'Connecticut',DE:'Delaware',DC:'District of Columbia',FL:'Florida',
        GA:'Georgia',HI:'Hawaii',ID:'Idaho',IL:'Illinois',IN:'Indiana',IA:'Iowa',KS:'Kansas',
        KY:'Kentucky',LA:'Louisiana',ME:'Maine',MD:'Maryland',MA:'Massachusetts',MI:'Michigan',
        MN:'Minnesota',MS:'Mississippi',MO:'Missouri',MT:'Montana',NE:'Nebraska',NV:'Nevada',
        NH:'New Hampshire',NJ:'New Jersey',NM:'New Mexico',NY:'New York',NC:'North Carolina',
        ND:'North Dakota',OH:'Ohio',OK:'Oklahoma',OR:'Oregon',PA:'Pennsylvania',PR:'Puerto Rico',
        RI:'Rhode Island',SC:'South Carolina',SD:'South Dakota',TN:'Tennessee',TX:'Texas',
        UT:'Utah',VT:'Vermont',VA:'Virginia',WA:'Washington',WV:'West Virginia',WI:'Wisconsin',WY:'Wyoming'};
    function getRecordTypes(d){ return JGStats.cleanTypes(d); }

    function parseDate(s) {
        if (!s) return null;
        s = String(s);
        if (s.indexOf('/') !== -1) { var p=s.split('/'); if(p.length===3) return new Date(+p[2], +p[0]-1, +p[1]); }
        if (s.indexOf('-') !== -1) { var q=s.split('-'); if(q.length>=2) return new Date(+q[0], +q[1]-1, +(q[2]||1)); }
        if (/^\d{4}$/.test(s)) return new Date(+s, 0, 1);
        return null;
    }
    function badgeFor(d) {
        if (d.badge && d.badge !== 'none') return d.badge;
        var dt = parseDate(getUpdatedRaw(d));
        if (!dt) return 'none';
        var days = (Date.now() - dt.getTime()) / 86400000;
        return days >= 0 && days <= UPDATED_WINDOW_DAYS ? 'updated' : 'none';
    }
    function formatMonthYear(s) {
        var dt = parseDate(s);
        if (!dt) return s || '';
        var months=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return months[dt.getMonth()] + ' ' + dt.getFullYear();
    }

    function regionLabel(r) { return JGStats.regionLabel(r); }
    function titleCase(s)   { return (s||'').replace(/[-_]/g,' ').toLowerCase().replace(/\b([a-z])/g,function(_,c){return c.toUpperCase();}); }
    function esc(s){ if(s==null) return ''; return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }

    function buildCard(d, idx) {
        var badge = badgeFor(d);
        var badgeHtml = badge === 'new'
            ? '<span class="ds-badge ds-badge-new">New</span>'
            : badge === 'updated'
            ? '<span class="ds-badge ds-badge-updated">Updated</span>' : '';

        var typeChips = getRecordTypes(d).map(function(rt){
            var bucket = typeBuckets({record_types:[rt]})[0] || '';
            return '<span class="ds-chip chip-record" data-val="'+esc(bucket)+'" role="button" tabindex="0" title="Filter by record type">'+esc(titleCase(rt))+'</span>';
        }).join('');
        var geoChips = getRegions(d).map(function(g){
            return '<span class="ds-chip chip-geo">'+esc(regionLabel(g))+'</span>';
        }).join('');

        var regions = getRegions(d);
        var regionText = regions.length ? regions.map(regionLabel).join(' & ') : '\u2014';
        var typeText = getRecordTypes(d).map(titleCase).join(' \u00b7 ') || '\u2014';

        var facts = [
            { label:'Date',            value: esc(getYears(d)) },
            { label:'Region',          value: esc(regionText) },
            { label:'Total Records',   value: esc(getCount(d)) },
            { label:'Source Language', value: esc(getLanguage(d)) },
            { label:'Record Type',     value: esc(typeText) },
            { label:'Source',          value: esc(getSource(d)) },
            { label:'Last Updated',    value: esc(formatMonthYear(getUpdatedRaw(d)) || '\u2014') }
        ].map(function(f){
            return '<div class="ds-fact"><span class="ds-fact-label">'+f.label+'</span><span class="ds-fact-value">'+f.value+'</span></div>';
        }).join('');

        var infoUrl = getInfoUrl(d);
        var cta = '';
        if (infoUrl) cta += '<a href="'+esc(infoUrl)+'" class="ds-btn ds-btn-navy" target="_blank" rel="noopener noreferrer">View Description &rarr;</a>';
        cta += '<a href="'+SEARCH_URL+'" class="ds-btn ds-btn-sage" target="_blank" rel="noopener noreferrer">Search Records</a>';

        var cardId = 'ds-' + idx;
        return '<article class="ds-card" id="'+cardId+'" aria-expanded="false">'
            + '<button type="button" class="ds-summary" aria-controls="'+cardId+'-panel" aria-expanded="false">'
                + '<div>'
                    + '<div class="ds-title-row"><h3 class="ds-card-name">'+esc(getName(d))+'</h3>'+badgeHtml+'</div>'
                    + '<div class="ds-meta-row">'
                        + '<span class="ds-meta-item"><svg class="ds-meta-icon" aria-hidden="true"><use href="#jg-ic-date"></use></svg> <span class="ds-meta-label">Years:</span> <span class="ds-meta-value">'+esc(getYears(d))+'</span></span>'
                        + '<span class="ds-meta-item"><svg class="ds-meta-icon" aria-hidden="true"><use href="#jg-ic-records"></use></svg> <span class="ds-meta-label">Records:</span> <span class="ds-meta-value">'+esc(getCount(d))+'</span></span>'
                        + '<span class="ds-meta-item"><svg class="ds-meta-icon" aria-hidden="true"><use href="#jg-ic-language"></use></svg> <span class="ds-meta-label">Language:</span> <span class="ds-meta-value">'+esc(getLanguage(d))+'</span></span>'
                        + '<span class="ds-meta-item"><svg class="ds-meta-icon" aria-hidden="true"><use href="#jg-ic-updated"></use></svg> <span class="ds-meta-label">Updated:</span> <span class="ds-meta-value">'+esc(formatMonthYear(getUpdatedRaw(d)) || '\u2014')+'</span></span>'
                    + '</div>'
                    + '<div class="ds-chips">'+typeChips+geoChips+'</div>'
                + '</div>'
                + '<span class="chevron-toggle" aria-hidden="true">&#9662;</span>'
            + '</button>'
            + '<div class="ds-expand" id="'+cardId+'-panel" role="region">'
                + '<div class="ds-expand-inner">'
                    + '<div>'
                        + (getDesc(d) ? '<p class="ds-description">'+esc(getDesc(d))+'</p>' : '')
                        + '<div class="ds-facts">'+facts+'</div>'
                    + '</div>'
                    + '<div class="ds-cta-col">'+cta+'</div>'
                + '</div>'
            + '</div>'
        + '</article>';
    }

    /* Type bucket matching (ported from catalog.html) */
    function typeBuckets(d) {
        var lower = getRecordTypes(d).join(' ').toLowerCase();
        var map = {
            vital:['birth','marriage','death','vital'], birth:['birth'], marriage:['marriage'],
            death:['death','burial'], census:['census','revision','family list'],
            names:['name list','voter','duma'], immigration:['immigration','passenger','migration'],
            directory:['directory','business'], holocaust:['holocaust'], military:['military','war'],
            yizkor:['yizkor'], legal:['legal','notary','judicial','court']
        };
        var out = [];
        Object.keys(map).forEach(function(k){
            if (map[k].some(function(kw){ return lower.indexOf(kw)!==-1; })) out.push(k);
        });
        return out;
    }

    var listEl  = document.getElementById('collectionList');
    var countEl = document.getElementById('collectionCount');
    var emptyEl = document.getElementById('collectionEmpty');
    var barEl   = document.getElementById('collectionFilters');

    var collectionData = [];

    /* ==================================================================
       5. FILTER / SORT BAR
       Injected into #collectionFilters (host page provides the empty
       div). Light bar: text search + record type + sort + reset. Region
       controls are omitted - the page is already region-scoped.
       ================================================================== */
    var STATE_GROUP = (SUBREGIONS === 'us-states')
        ? '<div class="db-filter-group">'
          + '<label for="dbFilterState">State</label>'
          + '<select id="dbFilterState" aria-label="Filter by state"><option value="">All States</option></select>'
          + '</div>'
        : '';

    var FILTER_BAR_HTML = ''
        + '<div class="db-filter-line">'
        +   '<div class="db-filter-group db-search-group">'
        +     '<label for="dbTextSearch">Filter This List</label>'
        +     '<input type="text" id="dbTextSearch" placeholder="e.g. revision lists, Kishinev, business&hellip;" autocomplete="off" aria-label="Filter databases by keyword">'
        +   '</div>'
        +   '<div class="db-filter-group">'
        +     '<label for="dbFilterType">Record Type</label>'
        +     '<select id="dbFilterType" aria-label="Filter by record type">'
        +       '<option value="">All Record Types</option>'
        +       '<optgroup label="Vital Records">'
        +         '<option value="vital">Vital Records (BMD)</option>'
        +         '<option value="birth">Birth / Brit Milah</option>'
        +         '<option value="marriage">Marriage / Ketubot</option>'
        +         '<option value="death">Death / Burial</option>'
        +       '</optgroup>'
        +       '<optgroup label="Census &amp; Lists">'
        +         '<option value="census">Census / Revision Lists</option>'
        +         '<option value="names">Name Lists</option>'
        +         '<option value="immigration">Passenger / Immigration</option>'
        +       '</optgroup>'
        +       '<optgroup label="Other Record Types">'
        +         '<option value="directory">Business Directory</option>'
        +         '<option value="holocaust">Holocaust</option>'
        +         '<option value="military">Military</option>'
        +         '<option value="yizkor">Yizkor Books</option>'
        +         '<option value="legal">Notary / Judicial</option>'
        +       '</optgroup>'
        +     '</select>'
        +   '</div>'
        +   STATE_GROUP
        +   '<div class="db-filter-group">'
        +     '<label for="dbSortSelect">Sort by</label>'
        +     '<select id="dbSortSelect" aria-label="Sort databases">'
        +       '<option value="name">Name (A&ndash;Z)</option>'
        +       '<option value="updated-desc">Most Recently Updated</option>'
        +       '<option value="records-desc">Most Records</option>'
        +     '</select>'
        +   '</div>'
        +   '<button class="db-btn-reset" id="dbResetBtn" aria-label="Reset list filters">&times; Reset</button>'
        + '</div>';

    /* Populate the State dropdown from the abbreviations actually present in
       this collection's datasets (regions[] entries that are US state codes). */
    function populateStates() {
        var sel = document.getElementById('dbFilterState');
        if (!sel) return;
        var seen = {};
        collectionData.forEach(function(d){
            getRegions(d).forEach(function(r){
                var ab = String(r).toUpperCase();
                if (US_STATES[ab]) seen[ab] = true;
            });
        });
        var abbrs = Object.keys(seen).sort(function(a,b){ return US_STATES[a].localeCompare(US_STATES[b]); });
        for (var i=0;i<abbrs.length;i++){
            var o = document.createElement('option');
            o.value = abbrs[i]; o.textContent = US_STATES[abbrs[i]];
            sel.appendChild(o);
        }
    }

    function readFilters() {
        var stateEl = document.getElementById('dbFilterState');
        return {
            text: document.getElementById('dbTextSearch').value.toLowerCase().trim(),
            type: document.getElementById('dbFilterType').value.toLowerCase(),
            state: stateEl ? stateEl.value.toUpperCase() : '',
            sort: document.getElementById('dbSortSelect').value
        };
    }

    function matches(d, f) {
        if (f.text) {
            var hay = (getName(d)+' '+getDesc(d)+' '+getSource(d)).toLowerCase();
            if (hay.indexOf(f.text) === -1) return false;
        }
        if (f.type && typeBuckets(d).indexOf(f.type) === -1) return false;
        if (f.state) {
            var regs = getRegions(d).map(function(r){ return String(r).toUpperCase(); });
            if (regs.indexOf(f.state) === -1) return false;
        }
        return true;
    }

    function sortList(list, key) {
        var c = list.slice();
        switch (key) {
            case 'name':
                c.sort(function(a,b){ return getName(a).localeCompare(getName(b)); });
                break;
            case 'updated-desc':
                c.sort(function(a,b){
                    var da = parseDate(getUpdatedRaw(a)), db = parseDate(getUpdatedRaw(b));
                    var ta = da ? da.getTime() : -Infinity;   /* undated sort last */
                    var tb = db ? db.getTime() : -Infinity;
                    return tb - ta;
                });
                break;
            case 'records-desc':
                c.sort(function(a,b){ return getCountNum(b) - getCountNum(a); });
                break;
        }
        return c;
    }

    var INITIAL_SHOW = (typeof config.initialShow === 'number') ? config.initialShow : 15;
    var revealed = false;

    function applyFilters() {
        var f = readFilters();
        var matched = collectionData.filter(function(d){ return matches(d, f); });
        var sorted = sortList(matched, f.sort);

        /* Split featured (is_featured) from the rest; featured are pinned in a
           block above the main list, no duplication. */
        var featured = [], rest = [];
        sorted.forEach(function(d){ (isFeatured(d) ? featured : rest).push(d); });

        /* Show-more reveal applies to the main (non-featured) list. */
        var visibleRest = (revealed || rest.length <= INITIAL_SHOW)
            ? rest : rest.slice(0, INITIAL_SHOW);

        var html = '';
        if (featured.length) {
            html += '<div class="db-section-head">Featured Databases</div>';
            html += featured.map(function(d,i){ return buildCard(d, 'f'+i); }).join('');
            html += '<div class="db-section-head">All Databases</div>';
        }
        html += visibleRest.map(function(d,i){ return buildCard(d, 'r'+i); }).join('');
        if (visibleRest.length < rest.length) {
            html += '<div class="db-show-more-wrap">'
                 +  '<button type="button" class="db-show-more" id="dbShowMore">'
                 +  'Show All ' + rest.length + ' Databases</button>'
                 +  '</div>';
        }
        listEl.innerHTML = html;
        wireCards();
        var moreBtn = document.getElementById('dbShowMore');
        if (moreBtn) {
            moreBtn.addEventListener('click', function(){ revealed = true; applyFilters(); });
        }

        if (countEl) countEl.textContent = matched.length;
        if (emptyEl) emptyEl.classList.toggle('visible', matched.length === 0);
    }

    function resetFilters() {
        document.getElementById('dbTextSearch').value = '';
        document.getElementById('dbFilterType').value = '';
        var st = document.getElementById('dbFilterState');
        if (st) st.value = '';
        document.getElementById('dbSortSelect').value = 'name';
        revealed = false;
        applyFilters();
    }

    function onFilterChange() {
        revealed = false;
        applyFilters();
    }

    function wireFilterBar() {
        if (!barEl) return;
        barEl.innerHTML = FILTER_BAR_HTML;
        document.getElementById('dbTextSearch').addEventListener('input', onFilterChange);
        document.getElementById('dbFilterType').addEventListener('change', onFilterChange);
        var st = document.getElementById('dbFilterState');
        if (st) st.addEventListener('change', onFilterChange);
        document.getElementById('dbSortSelect').addEventListener('change', onFilterChange);
        document.getElementById('dbResetBtn').addEventListener('click', resetFilters);
    }

    function wireCards() {
        listEl.querySelectorAll('.ds-summary').forEach(function(btn){
            btn.addEventListener('click', function(e){
                if (e.target.classList.contains('ds-chip')) return;  /* chip clicks handled below */
                var card = btn.closest('.ds-card');
                var open = card.getAttribute('aria-expanded') === 'true';
                card.setAttribute('aria-expanded', open?'false':'true');
                btn.setAttribute('aria-expanded', open?'false':'true');
                card.classList.toggle('is-open', !open);
            });
        });
        /* Record-type chips filter the list (only when the bar exists) */
        if (!barEl) return;
        listEl.querySelectorAll('.ds-chip.chip-record[data-val]').forEach(function(chip){
            function act(e){
                e.stopPropagation();
                var val = chip.getAttribute('data-val');
                if (!val) return;
                document.getElementById('dbFilterType').value = val;
                revealed = false;
                applyFilters();
            }
            chip.addEventListener('click', act);
            chip.addEventListener('keydown', function(e){ if(e.key==='Enter'||e.key===' '){ e.preventDefault(); act(e); } });
        });
    }

    if (listEl && REGION_TAGS.length) {
        wireFilterBar();
        jgEnsureStats()
            .then(function(){ return JGStats.load(config.dataUrl); })
            .then(function(all){
                collectionData = all.filter(belongsToCollection);
                populateStates();
                if (barEl) { applyFilters(); }
                else {
                    var sorted = sortList(collectionData, 'name');
                    listEl.innerHTML = sorted.map(function(d,i){ return buildCard(d, i); }).join('');
                    wireCards();
                    if (countEl) countEl.textContent = collectionData.length;
                    if (emptyEl) emptyEl.classList.toggle('visible', collectionData.length === 0);
                }
            })
            .catch(function(err){
                console.error('Collection listing load error:', err);
                if (emptyEl) emptyEl.classList.add('visible');
            });
    }
}