<?php
/**
 * MEMsearch.php  -  JewishGen Memorial Plaques Database search form
 *
 * Cloned from JOWBRsearch.php (JG40). Differences from JOWBR:
 *
 *   1. Two search rows (legacy memorial form used num_params = 2)
 *   2. Region select is name="GeoRegion" with FULL Solr region codes
 *      (01memorial_...) per the searchjson.php convention, which prefers
 *      GeoRegion and falls back to the hidden allcountry input.
 *      "All Regions" has value="" so the hidden allcountry=01memorial
 *      applies when no region is chosen.
 *   3. Region list is the MEMPLAQUES tree from RegionsData_solr.js.
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
    <title>Memorial Plaques Database &mdash; JewishGen</title>
    <meta name="description" content="Search the JewishGen Memorial Plaques Database: yahrzeit and memorial plaques from synagogues and societies around the world.">

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
        .mem-nudge {
            text-align: center;
            padding: 16px 20px 28px;
            font-size: 0.875rem;
            color: var(--charcoal);
            background-color: var(--ecru);
        }
        .mem-nudge a {
            color: var(--navy);
            font-weight: bold;
            text-decoration: underline dotted;
            text-decoration-color: var(--sage);
        }
        .mem-nudge a:hover { color: var(--sage); }

        /* -- Responsive -------------------------------------- */
        @media (max-width: 900px) {
            .mem-search-band { padding: 2rem 1.25rem 2.5rem; }
        }
        @media (max-width: 640px) {
            .jg-search-card .search-row { flex-direction: column; align-items: stretch; }
            .mem-controls { flex-direction: column; }
        }
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
            <span class="tagline">Memorial Plaques Database</span>
            <h1>JewishGen Memorial Plaques Database</h1>
            <p class="hero-subtitle">Search memorial and yahrzeit plaques from synagogues and societies around the world.</p>
            <span class="stat-line">225,000 memorial records from 417 synagogues and societies across 41 countries.</span>
        </div>
    </div>

    <!-- =======================================================
         SEARCH SECTION
         ======================================================= -->
    <div class="mem-search-band">
        <div class="mem-search-wrap">
            <div class="jg-search-card">

                <div class="jg-search-card__header">
                    <h2>Search Memorial Plaques</h2>
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

                <form action="/search-results.php" method="post" aria-label="Memorial plaques search">

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
                            <!-- Values are full Solr region codes (MEMPLAQUES tree,
                                 RegionsData_solr.js). "All Regions" is empty so the
                                 hidden allcountry=01memorial applies as fallback. -->
                            <select id="GeoRegion" name="GeoRegion" aria-label="Region to search">
                                <option value="">All Regions</option>
                                <optgroup label="Canada">
                                    <option value="01memorial_00canada">All Canada</option>
                                    <option value="01memorial_00canada_02bc">British Columbia</option>
                                    <option value="01memorial_00canada_09on">Ontario</option>
                                    <option value="01memorial_00canada_11qc">Quebec</option>
                                </optgroup>
                                <optgroup label="United States">
                                    <option value="01memorial_00usa">All USA</option>
                                    <option value="01memorial_00usa_01al">Alabama</option>
                                    <option value="01memorial_00usa_05ca">California</option>
                                    <option value="01memorial_00usa_06co">Colorado</option>
                                    <option value="01memorial_00usa_07ct">Connecticut</option>
                                    <option value="01memorial_00usa_09dc">District of Columbia</option>
                                    <option value="01memorial_00usa_10fl">Florida</option>
                                    <option value="01memorial_00usa_11ga">Georgia</option>
                                    <option value="01memorial_00usa_14il">Illinois</option>
                                    <option value="01memorial_00usa_15in">Indiana</option>
                                    <option value="01memorial_00usa_16ia">Iowa</option>
                                    <option value="01memorial_00usa_18ky">Kentucky</option>
                                    <option value="01memorial_00usa_19la">Louisiana</option>
                                    <option value="01memorial_00usa_21md">Maryland</option>
                                    <option value="01memorial_00usa_22ma">Massachusetts</option>
                                    <option value="01memorial_00usa_23mi">Michigan</option>
                                    <option value="01memorial_00usa_25ms">Mississippi</option>
                                    <option value="01memorial_00usa_26mo">Missouri</option>
                                    <option value="01memorial_00usa_27mt">Montana</option>
                                    <option value="01memorial_00usa_29nv">Nevada</option>
                                    <option value="01memorial_00usa_30nh">New Hampshire</option>
                                    <option value="01memorial_00usa_31nj">New Jersey</option>
                                    <option value="01memorial_00usa_33ny">New York</option>
                                    <option value="01memorial_00usa_34nc">North Carolina</option>
                                    <option value="01memorial_00usa_36oh">Ohio</option>
                                    <option value="01memorial_00usa_38or">Oregon</option>
                                    <option value="01memorial_00usa_39pa">Pennsylvania</option>
                                    <option value="01memorial_00usa_41ri">Rhode Island</option>
                                    <option value="01memorial_00usa_42sc">South Carolina</option>
                                    <option value="01memorial_00usa_43sd">South Dakota</option>
                                    <option value="01memorial_00usa_44tn">Tennessee</option>
                                    <option value="01memorial_00usa_45tx">Texas</option>
                                    <option value="01memorial_00usa_48va">Virginia</option>
                                    <option value="01memorial_00usa_52wy">Wyoming</option>
                                </optgroup>
                                <optgroup label="Europe">
                                    <option value="01memorial_00croatia">Croatia</option>
                                    <option value="01memorial_00hungary">Hungary</option>
                                    <option value="01memorial_00italy">Italy</option>
                                    <option value="01memorial_00slovakia">Slovakia</option>
                                    <option value="01memorial_00wales">Wales</option>
                                </optgroup>
                                <optgroup label="Middle East &amp; Africa">
                                    <option value="01memorial_00israel">Israel</option>
                                    <option value="01memorial_00morocco">Morocco</option>
                                    <option value="01memorial_00tunisia">Tunisia</option>
                                </optgroup>
                                <optgroup label="South America">
                                    <option value="01memorial_00argentina">Argentina</option>
                                </optgroup>
                            </select>
                        </div>

                    </div><!-- /.mem-controls -->

                    <div class="mem-search-actions">
                        <button type="submit" class="btn-search">Search Memorial Plaques</button>
                        <button type="reset" class="jg-btn-reset">Clear</button>
                    </div>
                    <input type="hidden" name="allcountry" value="01memorial">
                </form>
            </div><!-- /.jg-search-card -->
        </div><!-- /.mem-search-wrap -->
    </div><!-- /.mem-search-band -->

    <div class="mem-nudge">
        New to the Memorial Plaques Database? <a href="/databases/Memorial/">Learn what's in the database and how to contribute &rarr;</a>
    </div>

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

</script>

</body>
</html>