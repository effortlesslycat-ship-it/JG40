<?php
/**
 * yizkor-necrology-index.php  -  The JewishGen Yizkor Book Necrology Database search form
 *
 * Cloned from JOWBRsearch.php (JG40). Differences from JOWBR:
 *
 *   1. Two search rows (legacy memorial form used num_params = 2)
 *   2. Region select is name="GeoRegion" with FULL Solr region codes
 *      (01memorial_...) per the searchjson.php convention, which prefers
 *      GeoRegion and falls back to the hidden allcountry input.
 *      "All Regions" has value="" so the hidden allcountry=01memorial
 *      applies when no region is chosen.
 *   3. Region list is the NECROLOGY tree from RegionsData_solr.js.
 *
 * Header, footer, and page chrome match JOWBRsearch.php.
 * CHW + JG40
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The JewishGen Yizkor Book Necrology Database &mdash; JewishGen</title>
    <meta name="description" content="Search the JewishGen Yizkor Book Necrology Database: names from the necrologies (lists of Holocaust martyrs) in Yizkor Books.">

    <!-- 1. Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- 2. JewishGen Global Design System -->
    <link rel="stylesheet" href="/jg-global.css">
    <!-- 3. Page-specific styles -->
    <style>
        /* =====================================================
           MEMORIAL PLAQUES SEARCH - PAGE-SPECIFIC STYLES
           Shared search infrastructure (jg-search-card, sf rows,
           tips panel, btn-search) is in jg-global.css.
           ===================================================== */

        /* Page title band uses the shared .page-title-band component
           from jg-global.css (tagline pill, h1, hero-subtitle, stat-line,
           and its own dark-mode navy fix). No local overrides needed. */

        /* -- Search Band + Wrapper --------------------------- */
        .mem-search-band {
            background-color: var(--ecru);
            padding: 2.5rem 1.5rem 3rem;
        }
        .mem-search-wrap {
            max-width: 900px;
            margin: 0 auto;
        }

        /* Submit button inline override */
        .mem-search-actions .btn-search {
            display: inline-block;
            width: auto;
        }

        /* Tips panel grid styling is owned by jg-global.css
           (.jg-tips-panel__grid) via the shared SearchTips_Burial.html. */

        /* -- Divider between search rows and controls -------- */
        .mem-row-divider {
            border: none;
            border-top: 1px solid var(--cream);
            margin: 18px 0;
        }
        body.dark-mode .mem-row-divider { border-top-color: #333; }

        /* -- Controls row (AND/OR toggle + geo filter) ------- */
        .mem-controls {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 24px;
            padding-top: 18px;
            border-top: 2px solid var(--cream);
            margin-top: 4px;
        }
        body.dark-mode .mem-controls { border-top-color: #333; }

        .mem-match-group legend,
        .mem-geo-group label {
            font-size: 0.6875rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--charcoal);
            margin-bottom: 6px;
            display: block;
        }
        .mem-match-group .form-check { margin-bottom: 4px; }
        .mem-match-group .form-check-input { accent-color: #09497a; }
        .mem-match-group .form-check-label {
            font-size: 0.875rem;
            color: var(--charcoal);
            cursor: pointer;
        }
        body.dark-mode .mem-match-group .form-check-label { color: #a0a0a0; }

        .mem-geo-group select { min-width: 230px; }

        /* -- Search actions (Submit + Clear) ----------------- */
        .mem-search-actions {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            margin-top: 28px;
            flex-wrap: wrap;
        }

        .jg-btn-reset {
            display: inline-block;
            background-color: transparent;
            color: var(--navy);
            border: 2px solid var(--navy);
            padding: 0.6875rem 1.5rem;
            font-size: 0.875rem;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            font-family: inherit;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: background-color 0.2s;
        }
        .jg-btn-reset:hover { background-color: var(--cream); }
        body.dark-mode .jg-btn-reset { color: #e0e0e0; border-color: #555; }
        body.dark-mode .jg-btn-reset:hover { background-color: #2a2a2a; }

        /* -- "New to the database?" nudge -------------------- */

        /* -- Responsive -------------------------------------- */
        @media (max-width: 900px) {
            .mem-search-band { padding: 2rem 1.25rem 2.5rem; }
        }
        @media (max-width: 640px) {
            .jg-search-card .search-row { flex-direction: column; align-items: stretch; }
            .mem-controls { flex-direction: column; }
        }
    
        /* -- Collection description (description.txt) ---------
           Mirrors .db-collection-intro in jg-databases.css
           (centered serif between sage rules). Inlined here
           because these pages do not load jg-databases.css. */
        .db-collection-intro {
            max-width: 820px;
            margin: 0 auto 1.75rem;
            padding: 1.1rem 20px;
            box-sizing: border-box;
            text-align: center;
            border-top: 1px solid #8b8f72;
            border-bottom: 1px solid #8b8f72;
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 1.08rem;
            line-height: 1.7;
            color: var(--charcoal);
        }
        .db-collection-intro p { margin: 0 0 0.75rem; }
        .db-collection-intro p:last-child { margin-bottom: 0; }
        body.dark-mode .db-collection-intro { color: #cfcfcf; border-color: #4a4f3f; }
        .db-textlink {
            color: #09497a;
            text-decoration: underline;
            text-underline-offset: 2px;
            font-weight: bold;
        }
        .db-textlink:hover { color: #0d5c99; }
        body.dark-mode .db-textlink { color: #8ab0c9; }
        body.dark-mode .db-textlink:hover { color: #a9c9dd; }
    </style>
</head>
<body>

<?php
// -----------------------------------------------------------------------------
// HEADER - server-side include
// -----------------------------------------------------------------------------
$headerPath = $_SERVER['DOCUMENT_ROOT'] . '/Header_NavBar.html';
if (file_exists($headerPath)) {
    echo '<div id="site-header">';
    include $headerPath;
    echo '</div>';
} else {
    echo '<div id="site-header"></div>';
}
?>

<main id="main-content">

    <!-- =======================================================
         PAGE TITLE BAND  (shared component - jg-global.css)
         ======================================================= -->


    <div class="page-title-band">
        <div class="page-title-band__inner">
            <span class="tagline">Yizkor Book Project</span>
            <h1>The JewishGen Yizkor Book Necrology Database</h1>
            <p class="hero-subtitle">Search the necrologies &mdash; the lists of Holocaust martyrs &mdash; published in Yizkor Books.</p>
            <span class="stat-line">More than 353,000 entries from the necrologies of 422 Yizkor Books.</span>
        </div>
    </div>

<?php
// -----------------------------------------------------------------------------
// YIZKOR SUB-NAV - shared section toolbar, directly under the header.
// PHP include is safe here: the component is inert markup (no script/PHP
// tags). Its own docs describe fetch() for .html hosts; on PHP pages the
// include renders with no flash and the active-tab JS below runs at once.
// -----------------------------------------------------------------------------
$yzNavPath = $_SERVER['DOCUMENT_ROOT'] . '/Yizkor/Yizkor_SubNav.html';
if (file_exists($yzNavPath)) {
    echo '<div id="yizkor-subnav">';
    include $yzNavPath;
    echo '</div>';
}
?>

    <!-- =======================================================
         SEARCH SECTION
         ======================================================= -->
    <div class="mem-search-band">
        <div class="mem-search-wrap">
            <div class="jg-search-card">

                <div class="jg-search-card__header">
                    <h2>Search the Necrology Database</h2>
                    <button type="button"
                            class="jg-tips-link"
                            aria-expanded="false"
                            aria-controls="jg-search-tips-panel"
                            onclick="toggleSearchTips(this)">
                        Tips &amp; Tricks
                        <img src="/images/site/TipsBulbReg.png"  alt="" class="jg-tips-link__icon light-logo" aria-hidden="true">
                        <img src="/images/site/TipsBulbDark.png" alt="" class="jg-tips-link__icon dark-logo"  aria-hidden="true">
                    </button>
                </div>
                <hr class="jg-search-card__rule" aria-hidden="true">

                <?php include $_SERVER['DOCUMENT_ROOT'] . '/SearchTips_Burial.html'; ?>

                <form action="/search-results.php" method="post" aria-label="Yizkor Book necrology search">

                    <!-- Row 1 -->
                    <div class="search-row">
                        <div class="sf n">
                            <label class="field-label" for="dtype1">Search Field</label>
                            <select id="dtype1" name="srch1v">
                                <option value="S">Surname</option>
                                <option value="G">Given Name</option>
                                <option value="T">Town</option>
                                <option value="X">Any Field</option>
                            </select>
                        </div>
                        <div class="sf n">
                            <label class="field-label" for="stype1">Search Type</label>
                            <select id="stype1" name="srch1t">
                                <option value="Q">Phonetically Like</option>
                                <option value="E">is Exactly</option>
                                <option value="S">Starts With</option>
                                <option value="D">Sounds Like (DM)</option>
                                <option value="F1">Fuzzy</option>
                                <option value="F2">Fuzzier</option>
                                <option value="FM">Fuzziest</option>
                            </select>
                        </div>
                        <div class="sf w">
                            <label class="field-label" for="val1">Search Term</label>
                            <input type="text" id="val1" name="srch1" placeholder="For example: Cohen" autocomplete="off">
                        </div>
                    </div>

                    <!-- Row 2 -->
                    <div class="search-row">
                        <div class="sf n">
                            <label class="field-label" for="dtype2">Search Field</label>
                            <select id="dtype2" name="srch2v">
                                <option value="S">Surname</option>
                                <option value="G">Given Name</option>
                                <option value="T">Town</option>
                                <option value="X">Any Field</option>
                            </select>
                        </div>
                        <div class="sf n">
                            <label class="field-label" for="stype2">Search Type</label>
                            <select id="stype2" name="srch2t">
                                <option value="Q">Phonetically Like</option>
                                <option value="E">is Exactly</option>
                                <option value="S">Starts With</option>
                                <option value="D">Sounds Like (DM)</option>
                                <option value="F1">Fuzzy</option>
                                <option value="F2">Fuzzier</option>
                                <option value="FM">Fuzziest</option>
                            </select>
                        </div>
                        <div class="sf w">
                            <label class="field-label" for="val2">Search Term</label>
                            <input type="text" id="val2" name="srch2" placeholder="" autocomplete="off">
                        </div>
                    </div>
                    <hr class="mem-row-divider">

                    <div class="mem-controls">

                        <fieldset class="mem-match-group" style="border:none; margin:0; padding:0;">
                            <legend>Match rows using</legend>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="SrchBOOL" id="match-all" value="AND" checked>
                                <label class="form-check-label" for="match-all">Match <strong>ALL</strong> of the above (AND)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="SrchBOOL" id="match-any" value="OR">
                                <label class="form-check-label" for="match-any">Match <strong>ANY</strong> of the above (OR)</label>
                            </div>
                        </fieldset>

                        <div class="mem-geo-group">
                            <label class="field-label" for="GeoRegion">Region</label>
                            <!-- Values are full Solr region codes (NECROLOGY tree,
                                 RegionsData_solr.js). "All Regions" is empty so the
                                 hidden allcountry=01necrology applies as fallback. -->
                            <select id="GeoRegion" name="GeoRegion" aria-label="Region to search">
                                <option value="">All Regions</option>
                                <option value="01necrology_00austria">Austria</option>
                                <optgroup label="Belarus">
                                    <option value="01necrology_00belarus">All Belarus</option>
                                    <option value="01necrology_00belarus_01grodno">&nbsp;&nbsp;&nbsp;Grodno Gubernia</option>
                                    <option value="01necrology_00belarus_02minsk">&nbsp;&nbsp;&nbsp;Minsk Gubernia</option>
                                    <option value="01necrology_00belarus_03mogilev">&nbsp;&nbsp;&nbsp;Mogilev Gubernia</option>
                                    <option value="01necrology_00belarus_04vilna">&nbsp;&nbsp;&nbsp;Vilna Gubernia</option>
                                    <option value="01necrology_00belarus_05vitebsk">&nbsp;&nbsp;&nbsp;Vitebsk Gubernia</option>
                                </optgroup>
                                <optgroup label="Czech Republic">
                                    <option value="01necrology_00czechrepublic">All Czech Republic</option>
                                    <option value="01necrology_00czechrepublic_01bohemia">&nbsp;&nbsp;&nbsp;Bohemia</option>
                                </optgroup>
                                <option value="01necrology_00germany">Germany</option>
                                <option value="01necrology_00greece">Greece</option>
                                <option value="01necrology_00hungary">Hungary</option>
                                <optgroup label="Latvia">
                                    <option value="01necrology_00latvia">All Latvia</option>
                                    <option value="01necrology_00latvia_01courland">&nbsp;&nbsp;&nbsp;Courland</option>
                                    <option value="01necrology_00latvia_02livlandriga">&nbsp;&nbsp;&nbsp;Livland</option>
                                    <option value="01necrology_00latvia_03vitebsk">&nbsp;&nbsp;&nbsp;Vitebsk Gubernia</option>
                                    <option value="01necrology_00latvia_04estland">&nbsp;&nbsp;&nbsp;Estonia</option>
                                </optgroup>
                                <optgroup label="Lithuania">
                                    <option value="01necrology_00lithuania">All Lithuania</option>
                                    <option value="01necrology_00lithuania_01kovno">&nbsp;&nbsp;&nbsp;Kovno Gubernia</option>
                                    <option value="01necrology_00lithuania_02vilna">&nbsp;&nbsp;&nbsp;Vilna Gubernia</option>
                                    <option value="01necrology_00lithuania_03suwalki">&nbsp;&nbsp;&nbsp;Suwalki Gubernia</option>
                                </optgroup>
                                <option value="01necrology_00macedonia">North Macedonia</option>
                                <option value="01necrology_00moldova">Moldova</option>
                                <option value="01necrology_00netherlands">Netherlands</option>
                                <optgroup label="Poland">
                                    <option value="01necrology_00poland">All Poland</option>
                                    <option value="01necrology_00poland_01pale">&nbsp;&nbsp;&nbsp;Russian Pale</option>
                                    <option value="01necrology_00poland_01pale_01grodno">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Grodno Gubernia</option>
                                    <option value="01necrology_00poland_01pale_02vilna">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vilna Gubernia</option>
                                    <option value="01necrology_00poland_01pale_03volhynia">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Volhynia Gubernia</option>
                                    <option value="01necrology_00poland_02congress">&nbsp;&nbsp;&nbsp;Congress Poland</option>
                                    <option value="01necrology_00poland_02congress_01kalisz">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kalisz Gubernia</option>
                                    <option value="01necrology_00poland_02congress_02kielce">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kielce Gubernia</option>
                                    <option value="01necrology_00poland_02congress_03lomza">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&#321;om&#380;a Gubernia</option>
                                    <option value="01necrology_00poland_02congress_04lublin">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Lublin Gubernia</option>
                                    <option value="01necrology_00poland_02congress_05piotrkow">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Piotrk&#243;w Gubernia</option>
                                    <option value="01necrology_00poland_02congress_06plock">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;P&#322;ock Gubernia</option>
                                    <option value="01necrology_00poland_02congress_07radom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Radom Gubernia</option>
                                    <option value="01necrology_00poland_02congress_08siedlce">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Siedlce Gubernia</option>
                                    <option value="01necrology_00poland_02congress_09suwalki">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Suwalki Gubernia</option>
                                    <option value="01necrology_00poland_02congress_10warszawa">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Warszawa Gubernia</option>
                                    <option value="01necrology_00poland_03galicia">&nbsp;&nbsp;&nbsp;Galicia</option>
                                    <option value="01necrology_00poland_03galicia_01krakow">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Krak&#243;w Wojew&#243;dztwa</option>
                                    <option value="01necrology_00poland_03galicia_02lwow">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Lw&#243;w Wojew&#243;dztwa</option>
                                    <option value="01necrology_00poland_03galicia_03stanislawow">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Stanis&#322;aw&#243;w Wojew&#243;dztwa</option>
                                    <option value="01necrology_00poland_03galicia_04tarnopol">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tarnopol Wojew&#243;dztwa</option>
                                    <option value="01necrology_00poland_04prussia">&nbsp;&nbsp;&nbsp;Prussia</option>
                                </optgroup>
                                <optgroup label="Romania">
                                    <option value="01necrology_00romania">All Romania</option>
                                    <option value="01necrology_00romania_02bessarabia">&nbsp;&nbsp;&nbsp;Bessarabia Gubernia</option>
                                    <option value="01necrology_00romania_03bucovina">&nbsp;&nbsp;&nbsp;Bucovina</option>
                                    <option value="01necrology_00romania_04crisana">&nbsp;&nbsp;&nbsp;Cri&#351;ana</option>
                                    <option value="01necrology_00romania_06maramures">&nbsp;&nbsp;&nbsp;Maramure&#351;</option>
                                    <option value="01necrology_00romania_07moldavia">&nbsp;&nbsp;&nbsp;Moldavia</option>
                                    <option value="01necrology_00romania_10transylvania">&nbsp;&nbsp;&nbsp;Transylvania</option>
                                </optgroup>
                                <option value="01necrology_00serbia">Serbia</option>
                                <option value="01necrology_00slovakia">Slovakia</option>
                                <optgroup label="Ukraine">
                                    <option value="01necrology_00ukraine">All Ukraine</option>
                                    <option value="01necrology_00ukraine_01russianempire">&nbsp;&nbsp;&nbsp;Russian Empire</option>
                                    <option value="01necrology_00ukraine_01russianempire_01bessarabia">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Bessarabia Gubernia</option>
                                    <option value="01necrology_00ukraine_01russianempire_02chernigov">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Chernigov Gubernia</option>
                                    <option value="01necrology_00ukraine_01russianempire_03ekaterinoslav">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ekaterinoslav Gubernia</option>
                                    <option value="01necrology_00ukraine_01russianempire_04kiev">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kiev Gubernia</option>
                                    <option value="01necrology_00ukraine_01russianempire_05kharkov">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kharkov Gubernia</option>
                                    <option value="01necrology_00ukraine_01russianempire_06kherson">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kherson Gubernia</option>
                                    <option value="01necrology_00ukraine_01russianempire_07podolia">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Podolia Gubernia</option>
                                    <option value="01necrology_00ukraine_01russianempire_08poltava">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Poltava Gubernia</option>
                                    <option value="01necrology_00ukraine_01russianempire_09taurida">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Taurida Gubernia</option>
                                    <option value="01necrology_00ukraine_01russianempire_10volhynia">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Volhynia Gubernia</option>
                                    <option value="01necrology_00ukraine_02austrianempire">&nbsp;&nbsp;&nbsp;Austrian Empire</option>
                                    <option value="01necrology_00ukraine_02austrianempire_01galicia">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Galicia</option>
                                    <option value="01necrology_00ukraine_02austrianempire_01galicia_01lwow">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Lw&#243;w Wojew&#243;dztwa</option>
                                    <option value="01necrology_00ukraine_02austrianempire_01galicia_02stanislawow">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Stanis&#322;aw&#243;w Wojew&#243;dztwa</option>
                                    <option value="01necrology_00ukraine_02austrianempire_01galicia_03tarnopol">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tarnopol Wojew&#243;dztwa</option>
                                    <option value="01necrology_00ukraine_02austrianempire_02bucovina">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Bucovina</option>
                                    <option value="01necrology_00ukraine_02austrianempire_03transcarparthia">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Transcarpathia</option>
                                </optgroup>
                            </select>
                        </div>

                    </div><!-- /.mem-controls -->

                    <div class="mem-search-actions">
                        <button type="submit" class="btn-search">Search the Necrology Database</button>
                        <button type="reset" class="jg-btn-reset">Clear</button>
                    </div>
                    <input type="hidden" name="allcountry" value="01necrology">
                </form>
            </div><!-- /.jg-search-card -->
        </div><!-- /.mem-search-wrap -->
    </div><!-- /.mem-search-band -->


</main>

<?php
// -----------------------------------------------------------------------------
// FOOTER - server-side include
// -----------------------------------------------------------------------------
$footerPath = $_SERVER['DOCUMENT_ROOT'] . '/Footer.html';
if (file_exists($footerPath)) {
    echo '<div id="site-footer">';
    include $footerPath;
    echo '</div>';
} else {
    echo '<div id="site-footer"></div>';
}
?>

<script>

    /* -- Nav dropdown keyboard handlers ---------------------------
       Header is server-included so its DOM is ready immediately;
       no Promise.all wait needed. */
    document.querySelectorAll('.jg-nav .dropbtn, .main-nav .dropbtn').forEach(function (btn) {
        btn.addEventListener('keydown', function (e) {
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

    /* -- Tips panel toggle (shared burial component) -------------- */
    function toggleSearchTips(btn) {
        var panel = document.getElementById('jg-search-tips-panel');
        if (!panel) return;
        var isOpen = panel.classList.toggle('open');
        btn.setAttribute('aria-expanded', String(isOpen));
    }


    /* -- Yizkor sub-nav active tab --------------------------------
       Component ships inert; host page marks the current tab by
       matching pathname (its documented convention). */
    (function () {
        var here = window.location.pathname.replace(/\/index\.php$/, '/').toLowerCase();
        document.querySelectorAll('#yizkor-subnav a[href]').forEach(function (a) {
            var href = a.getAttribute('href').replace(/\/index\.php$/, '/').toLowerCase();
            if (href === here) { a.classList.add('active'); a.setAttribute('aria-current', 'page'); }
        });
    })();
</script>

</body>
</html>