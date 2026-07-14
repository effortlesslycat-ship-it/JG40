<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JewishGen: The Global Home for Jewish Genealogy</title>
    <meta name="description" content="JewishGen is the largest online Jewish genealogy resource. Search millions of records, discover your ancestral towns, and connect with our global community.">

    <!-- 1. Bootstrap (layout only - design tokens override below) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- 2. JewishGen Global Design System (always wins the cascade) -->
    <link rel="stylesheet" href="jg-global.css?v=59">

    <style>
        /* SRA logo -- single image, washed out in dark mode */
        .jg-feature-card__icon-sra {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        body.dark-mode .jg-feature-card__icon-sra {
            opacity: 0.4;
            filter: grayscale(40%);
        }
    </style>

</head>
<body>

<!-- ===========================================================
     SPONSOR BANNER - inline (updated daily by staff)
     To edit: find EDIT START / EDIT END markers below
=========================================================== -->
<div class="jg-sponsor-banner" aria-live="polite" aria-label="Daily research dedication">
    <div id="jg-sponsor-container" style="position: relative; min-height: 1.4rem;">
        <!-- EDIT START -->
        <div class="jg-sponsor-slide active">
            Today's research is dedicated by: <strong>The Hollander Family</strong> in loving memory of <strong>Leo Hollander</strong>.
        </div>
        <div class="jg-sponsor-slide">
            Today's research is dedicated in honor of all the dedicated volunteers who index our records.
        </div>
        <!-- EDIT END -->
    </div>
    <span class="jg-sponsor-banner__dedicate">
        <a href="https://www.jewishgen.org/jewishgen-secure/donate.asp" target="_blank" rel="noopener noreferrer">To Dedicate a Day of Research, please click here</a>
    </span>
</div>

<!-- ===========================================================
     HEADER + NAV - loaded from Header_NavBar.html
     To edit: open Header_NavBar.html
=========================================================== -->
<div id="site-header">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/Header_NavBar.html'; ?>
</div>

<!-- ===========================================================
     HERO SEARCH - loaded from HeroSearch.html
     To edit: open HeroSearch.html
=========================================================== -->
<div id="hero-search">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/HeroSearch.html'; ?>
</div>

<!-- ===========================================================
     ANNOUNCEMENT BANNER - loaded from Announcement.html
     To edit: open Announcement.html
=========================================================== -->
<div id="announcement">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/Announcement.html'; ?>
</div>

<!-- ===========================================================
     CORE RESOURCE TILES - inline (8 cards: 5 confirmed + 3 placeholders)
     To edit: update card content directly here.
     Keep total count at 8. Replace placeholder cards as confirmed.
 
     PATTERN: whole card is an <a> link. Two <img> tags per icon -
     the light/dark classes toggle visibility via jg-global.css.
 
     PLACEHOLDERS: use <div> not <a>, and .jg-feature-card--placeholder
     class dims + disables pointer events.
=========================================================== -->
<div class="jg-cards-section">
    <div class="jg-cards-section__intro">
        <h2>Explore Our Core Resources</h2>
        <p>Beyond the search engine, JewishGen offers powerful tools to help you break through your brick walls.</p>
    </div>
    <div class="jg-grid-4">
 
        <!-- 1. Discussion Group -->
        <a class="jg-feature-card" href="https://groups.jewishgen.org/g/main">
            <div class="jg-feature-card__icon">
                <img src="/images/site/discussion-group-icon-reg.svg"  alt="" class="jg-feature-card__icon-light">
                <img src="/images/site/discussion-group-icon-dark.svg" alt="" class="jg-feature-card__icon-dark">
            </div>
            <h3>Discussion Group</h3>
            <p>Get help solving your genealogy mysteries and check out our extensive message archive.</p>
            <span class="jg-feature-card__chevron" aria-hidden="true">›</span>
        </a>
 
        <!-- 2. Yizkor Books -->
        <a class="jg-feature-card" href="/Yizkor/translations/">
            <div class="jg-feature-card__icon">
                <img src="/images/site/yizkor-book-icon-reg.svg"  alt="" class="jg-feature-card__icon-light">
                <img src="/images/site/yizkor-book-icon-dark.svg" alt="" class="jg-feature-card__icon-dark">
            </div>
            <h3>Yizkor Books</h3>
            <p>Read translated memorial books documenting Jewish life and history in destroyed communities.</p>
            <span class="jg-feature-card__chevron" aria-hidden="true">›</span>
        </a>
 
        <!-- 3. Catalogue of Records -->
        <a class="jg-feature-card" href="/catalog/">
            <div class="jg-feature-card__icon">
                <img src="/images/site/catalog-icon-reg.svg"  alt="" class="jg-feature-card__icon-light">
                <img src="/images/site/catalog-icon-dark.svg" alt="" class="jg-feature-card__icon-dark">
            </div>
            <h3>Catalogue of Records</h3>
            <p>Browse our comprehensive inventory of historical datasets, organized by country and region.</p>
            <span class="jg-feature-card__chevron" aria-hidden="true">›</span>
        </a>
 
        <!-- 4. Communities Database -->
        <a class="jg-feature-card" href="/Communities/search.php">
            <div class="jg-feature-card__icon">
                <img src="/images/site/communities-icon-reg.svg"  alt="" class="jg-feature-card__icon-light">
                <img src="/images/site/communities-icon-dark.svg" alt="" class="jg-feature-card__icon-dark">
            </div>
            <h3>Communities Database (Town Finder)</h3>
            <p>Identify your ancestral town, and uncover historical, geographical, and linguistic details about Jewish communities worldwide.</p>
            <span class="jg-feature-card__chevron" aria-hidden="true">›</span>
        </a>
 
        <!-- 5. Burial Registry (JOWBR) -->
        <a class="jg-feature-card" href="/databases/cemetery/">
            <div class="jg-feature-card__icon">
                <img src="/images/site/jowbr-icon-reg.svg"  alt="" class="jg-feature-card__icon-light">
                <img src="/images/site/jowbr-icon-dark.svg" alt="" class="jg-feature-card__icon-dark">
            </div>
            <h3>Burial Registry (JOWBR)</h3>
            <p>Search cemetery and burial records from Jewish communities around the world.</p>
            <span class="jg-feature-card__chevron" aria-hidden="true">›</span>
        </a>
 
        
        <!-- 6. Shul Records America -->
        <a class="jg-feature-card" href="https://www.jewishgen.org/sra/">
            <div class="jg-feature-card__icon">
                <img src="/images/site/SRALogo.png" alt="" class="jg-feature-card__icon-sra">
            </div>
            <h3>Shul Records America</h3>
            <p>Search synagogue membership, burial society, and vital records from American Jewish communities.</p>
            <span class="jg-feature-card__chevron" aria-hidden="true">›</span>
        </a>

         <!-- 7. Recently Updated  -->
        <a class="jg-feature-card" href="/databases/recently-updated.html">
            <div class="jg-feature-card__icon">
            <img src="/images/site/NewUpdatedReg.svg"  alt="" class="jg-feature-card__icon-light">
            <img src="/images/site/NewUpdatedDark.svg" alt="" class="jg-feature-card__icon-dark">
            </div>
            <h3>New and Updated Records</h3>
            <p>Discover the latest additions and updates across JewishGen's database collection.</p>
            <span class="jg-feature-card__chevron" aria-hidden="true">›</span>
        </a>
    
 <!-- 8. How To JewishGen -->
        <a class="jg-feature-card" href="/HowToFAQ/howto.html">
            <div class="jg-feature-card__icon">
            <img src="/images/site/faq.svg"  alt="" class="jg-feature-card__icon-light">
            <img src="/images/site/faq_dark.svg" alt="" class="jg-feature-card__icon-dark">
            </div>
            <h3>How To/FAQ</h3>
            <p>Everything you need to know to get started with JewishGen and genealogy.</p>
            <span class="jg-feature-card__chevron" aria-hidden="true">›</span>
        </a>
 
    </div>
</div>

<!-- ===========================================================
     KALIKOW CENTER BANNER - full-width navy band
     Link: jgUpdateKalikowLink() rewrites the button href to
     today's date in NYC time. Default href is the MJH genealogy
     fallback page, which stays if the JS ever fails.
=========================================================== -->
<section class="jg-kalikow" aria-label="Kalikow Center">
    <div class="jg-kalikow__inner">

        <div class="jg-kalikow__photo">
            <img src="/images/site/KalCenCat.jpg"
                 alt="Researchers working at the Peter and Mary Kalikow Jewish Genealogy Research Center">
        </div>

        <div class="jg-kalikow__body">
            <h2>Visiting New York City?</h2>
            <p>The <strong>Peter and Mary Kalikow Jewish Genealogy Research Center</strong>
               is the physical presence of JewishGen, located at the Museum of Jewish Heritage.
               Our expert genealogists are on hand to assist in uncovering your family's past,
               and to help you navigate JewishGen and many other critical resources
               accessible at the Center.</p>

            <a id="jg-kalikow-book-btn"
               href="https://mjhnyc.org/genealogy/"
               class="jg-kalikow__cta-btn"
               target="_blank"
               rel="noopener noreferrer">
                Book an Appointment
            </a>

            <div class="jg-kalikow__cta-email">
                or email us at <a href="mailto:kalikow@jewishgen.org">kalikow@jewishgen.org</a>
            </div>

            <p class="jg-kalikow__cta-note">
                Appointments are recommended but not required.
                Museum tickets are required to visit the Center.
            </p>
        </div>

    </div>
</section>

<!-- ===========================================================
     UPCOMING EVENTS
     Cards populated by JGEvents.initHomeCal() below.
     Reuses .jg-cards-section wrapper for consistent ecru band.
     To update events: edit the MJH event listing directly.
     TODO: confirm URL for "Submit a Program Idea"
=========================================================== -->
<section class="jg-cards-section" aria-label="Upcoming Events">

    <div class="jg-cards-section__intro">
        <h2>Upcoming Events</h2>
    </div>

    <div class="jg-grid-3" id="jg-homecal-grid" role="region" aria-label="Upcoming Events">
        <p class="jg-events-loading" aria-live="polite">Loading upcoming events&hellip;</p>
    </div>

    <div class="jg-events-links">
        <a href="/talks.html">Watch Past JewishGen Talks</a>
        <span class="jg-events-links__sep" aria-hidden="true">|</span>
        <a href="/Calendar.html">View Full Events Calendar</a>
        <span class="jg-events-links__sep" aria-hidden="true">|</span>
        <a href="#"><!-- TODO: real URL -->Submit a Program Idea</a>
    </div>

</section>
<!-- ===========================================================
     PUBLICATIONS LIBRARY - loaded from PubLib.html
     To edit: open PubLib.html
=========================================================== -->
<div id="pub-lib">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/PubLib.html'; ?>
</div>

<!-- ===========================================================
     FOOTER - loaded from Footer.html
     To edit: open Footer.html
=========================================================== -->
<div id="site-footer">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/Footer.html'; ?>
</div>

<!-- ===========================================================
     SCRIPTS
=========================================================== -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Legacy JewishGen search dependencies (drive setRegions, doSubmit, etc.) -->
<script src="https://www.jewishgen.org/JG/Scripts/FormUtils.js"></script>
<script src="https://www.jewishgen.org/Communities/Utils.js"></script>
<script src="https://www.jewishgen.org/databases/Regions/RegionsData_solr.js"></script>
<script src="https://www.jewishgen.org/databases/Regions/Regions.js"></script>
<script src="https://www.jewishgen.org/databases/Regions/Overlay.js"></script>
<script src="https://www.jewishgen.org/databases/Regions/SearchForm_solr.js"></script>

<!-- JewishGen Events Module -->
<script src="jg-events.js"></script>

<script>
/* -- Component Loader =====================================
   Fetches each component file and injects it into its
   placeholder div. initPage() runs after all fetches
   complete so scripts that depend on injected HTML
   (e.g. nav keyboard handler) fire at the right time.
---------------------------------------------------------- */

initPage();

/* -- Page Initialisation ==================================
   Runs after all components are injected into the DOM.
---------------------------------------------------------- */
function initPage() {

    /* Search: wire up the legacy form now that HeroSearch is in the DOM */
    if (typeof InitSearchForm === 'function') {
        try { InitSearchForm(); } catch (e) { console.warn('InitSearchForm:', e); }
    }

    /* Search: define helpers used by the legacy region cascade */
    window.ConfigSubReg = function() {
        var subreg_sel = document.getElementById('GeoRegion');
        var subreg_div = document.getElementById('SubRegionsDiv');
        if (!subreg_sel || !subreg_div) return;
        subreg_div.style.display = (subreg_sel.options.length <= 1) ? 'none' : 'block';
    };

    window.ResetRegion = function() {
        try {
            var subreg = (typeof get_cookie === 'function')
                ? get_cookie(window.location.pathname.toLowerCase()) : null;
            var country = (typeof getOptionValue === 'function')
                ? getOptionValue('allcountry')
                : document.getElementById('allcountry').value;
            if (typeof setRegions === 'function') setRegions(country);
            ConfigSubReg();
            if (subreg && typeof setOptionValue === 'function') {
                setOptionValue('GeoRegion', subreg);
            }
        } catch (e) { console.warn('ResetRegion:', e); }
    };

    /* Search: restore previously-selected sub-region from cookie on show,
       persist current selection on hide. Registered here (not at document
       level) because document.f only exists after HeroSearch is injected. */
    window.addEventListener('pageshow', function() { ResetRegion(); });
    window.addEventListener('pagehide', function() {
        var gr = document.getElementById('GeoRegion');
        if (gr && typeof set_cookie === 'function') set_cookie(gr.value);
        if (typeof EnableForm === 'function' && document.f) EnableForm(document.f);
    });


    /* Nav: keyboard accessibility for dropdown menus */
    document.querySelectorAll('.jg-nav .dropbtn').forEach(function(btn) {
        btn.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                var expanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', String(!expanded));
                var menu = document.getElementById(this.getAttribute('aria-controls'));
                if (menu) menu.style.display = expanded ? 'none' : 'block';
            }
            if (e.key === 'Escape') {
                this.setAttribute('aria-expanded', 'false');
                var menu = document.getElementById(this.getAttribute('aria-controls'));
                if (menu) menu.style.display = 'none';
            }
        });
    });

    /* Sponsor banner: rotate slides every 6 seconds */
    (function() {
        var slides = document.querySelectorAll('.jg-sponsor-slide');
        if (slides.length <= 1) return;
        var current = 0;
        setInterval(function() {
            slides[current].classList.remove('active');
            current = (current + 1) % slides.length;
            slides[current].classList.add('active');
        }, 6000);
    })();
}

/* -- Events: fires immediately since #jg-homecal-grid is inline == */
JGEvents.initHomeCal();

/* -- Kalikow: fires immediately since #jg-kalikow-book-btn is inline == */
jgUpdateKalikowLink();

/* -- Auth State Toggle (demo only - remove before go-live) -- */
var loggedIn = false;
function toggleAuthState() {
    loggedIn = !loggedIn;
    var out = document.getElementById('auth-logged-out');
    var inn = document.getElementById('auth-logged-in');
    if (out) out.style.display = loggedIn ? 'none' : 'flex';
    if (inn) inn.style.display  = loggedIn ? 'flex' : 'none';
}

/* -- Search: Tips panel toggle =============================== */
function jgToggleTips(btn) {
    var panel = document.getElementById('jg-tips-panel');
    var isOpen = panel.classList.contains('open');
    panel.classList.toggle('open');
    btn.setAttribute('aria-expanded', String(!isOpen));
}


    /* -- Kalikow: rewrite booking link to today's date in NYC time ==
   Format the URL requires: DDMmmYYYY (e.g. 22Apr2026).
   If anything fails, href keeps its fallback (MJH genealogy page).
---------------------------------------------------------- */
function jgUpdateKalikowLink() {
    var link = document.getElementById('jg-kalikow-book-btn');
    if (!link) return;

    try {
        var parts = new Intl.DateTimeFormat('en-US', {
            timeZone: 'America/New_York',
            day:   '2-digit',
            month: 'short',
            year:  'numeric'
        }).formatToParts(new Date());

        var day   = parts.find(function(p) { return p.type === 'day';   }).value;
        var month = parts.find(function(p) { return p.type === 'month'; }).value;
        var year  = parts.find(function(p) { return p.type === 'year';  }).value;

        link.href = 'https://898a.blackbaudhosting.com/898a/'
                  + 'Peter-and-Mary-Kalikow-Jewish-Genealogy-Research-Center-Appointment-'
                  + day + month + year;
    } catch (err) {
        console.warn('Kalikow link: falling back to MJH genealogy page.', err);
    }
}
</script>

</body>
</html>
