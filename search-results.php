<?php

// -----------------------------------------------------------------------------
// HELPER: read a param from POST first, fall back to GET
// Lets the form POST (so the URL stays clean, matching the legacy jgform.php
// behavior) while still supporting GET for direct links or bookmark testing.
// -----------------------------------------------------------------------------

function srGet($key, $default = '') {
    if (isset($_POST[$key]) && $_POST[$key] !== '') return $_POST[$key];
    if (isset($_GET[$key])  && $_GET[$key]  !== '') return $_GET[$key];
    return $default;
}

// -----------------------------------------------------------------------------
// EXTRACT SEARCH PARAMS - mirror of the JS renderQueryBand
// -----------------------------------------------------------------------------

$pairs       = array();   // each: array('label' => ..., 'term' => ...)
$titleTerms  = array();
$hasSearch   = false;

$dataTypeLabel = array(
    'S' => 'Surname',
    'G' => 'Given Name',
    'T' => 'Town',
    'X' => 'Any Field',
);
$searchTypeLabel = array(
    'Q'  => 'phonetically like',
    'D'  => 'sounds like',
    'E'  => 'is exactly',
    'S'  => 'starts with',
    'F1' => 'fuzzy',
    'F2' => 'fuzzier',
    'FM' => 'fuzziest',
);

for ($i = 1; $i <= 4; $i++) {
    $term = trim(srGet('srch' . $i));
    if ($term === '') {
        continue;
    }
    $hasSearch = true;

    $dtKey = srGet('srch' . $i . 'v');
    $stKey = srGet('srch' . $i . 't');
    $dt    = isset($dataTypeLabel[$dtKey])   ? $dataTypeLabel[$dtKey]   : 'Search';
    $st    = isset($searchTypeLabel[$stKey]) ? $searchTypeLabel[$stKey] : '';

    $label = $dt . ($st !== '' ? ' (' . $st . ')' : '');
    $pairs[]      = array('label' => $label, 'term' => strtoupper($term));
    $titleTerms[] = strtoupper($term);
}

// -----------------------------------------------------------------------------
// FRIENDLY REGION - mirror of the JS friendlyRegion utility
// "01holocaust" -> "Holocaust", "00ukraine" -> "Ukraine".
// -----------------------------------------------------------------------------

function srFriendlyRegion($code) {
    $clean = preg_replace('/^[0-9]+/', '', $code);
    $clean = str_replace('_', ' ', $clean);
    $clean = trim($clean);
    if ($clean === '' || strtolower($clean) === 'all') {
        return 'All Countries';
    }
    return ucfirst($clean);
}

if ($hasSearch) {
    $regionRaw = srGet('GeoRegion');
    if ($regionRaw === '') {
        $regionRaw = srGet('allcountry');
    }
    $regionLower = strtolower($regionRaw);
    if ($regionLower !== '' && $regionLower !== '0*' && $regionLower !== 'all' && $regionLower !== '00all') {
        $pairs[] = array('label' => 'Region', 'term' => srFriendlyRegion($regionLower));
    } else {
        $pairs[] = array('label' => 'Region', 'term' => 'All Countries');
    }
}

// -----------------------------------------------------------------------------
// PAGE TITLE
// -----------------------------------------------------------------------------

$pageTitle = 'Search Results';
if (count($titleTerms) > 0) {
    $pageTitle .= ': ' . implode(', ', $titleTerms);
}

// -----------------------------------------------------------------------------
// BUILD SEARCH PARAMS FOR JAVASCRIPT
// PHP read these from POST (or GET fallback). JS needs them for the
// searchjson.php fetch but can no longer read them from the URL since
// we switched to POST. So embed them as window.JG_SEARCH_PARAMS.
// -----------------------------------------------------------------------------
$jsSearchParams = array();
$paramKeys = array('SrchBOOL', 'GeoRegion', 'allcountry', 'dates', 'Months', 'Years');
for ($i = 1; $i <= 4; $i++) {
    $paramKeys[] = 'srch' . $i;
    $paramKeys[] = 'srch' . $i . 'v';
    $paramKeys[] = 'srch' . $i . 't';
}
foreach ($paramKeys as $k) {
    $v = srGet($k);
    if ($v !== '') {
        $jsSearchParams[$k] = $v;
    }
}

function srEsc($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo srEsc($pageTitle); ?> &mdash; JewishGen</title>

    <!-- 1. Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- 2. JewishGen Global Design System -->
    <link rel="stylesheet" href="/jg-global.css">

    <!-- 3. Page-specific styles -->
    <style>

        a { text-decoration: none; color: inherit; }

        /* ============================================
           TIPS PANEL GRID
        ============================================ */
        .sr-tips-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 24px;
            font-size: 13px;
            line-height: 1.5;
        }
        @media (max-width: 640px) {
            .sr-tips-grid { grid-template-columns: 1fr; }
        }

        /* ============================================
           SEARCH HERO OVERRIDES
        ============================================ */
        .sr-hero-inner { max-width: 1040px; margin: 0 auto; padding: 3.5rem 2rem 4rem; }
        .jg-search-card { background-color: #ffffff; }
        body.dark-mode .jg-search-card { background-color: #1e1e1e; }
        .sr-card-inner { padding: 24px 28px 36px; }

        /* ============================================
           QUERY BAND
        ============================================ */
        .sr-query-band {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            padding: 14px 20px;
            background-color: #ffffff;
            border-bottom: 2px solid var(--navy);
        }
        body.dark-mode .sr-query-band { background-color: #1a1a1a; border-color: #2a4060; }
        .sr-query-band-left { display: flex; align-items: center; flex-wrap: wrap; gap: 6px 16px; }
        .sr-query-band-pair { display: flex; align-items: baseline; gap: 6px; }
        .sr-query-band-label {
            font-size: 11px; font-weight: bold; text-transform: uppercase;
            letter-spacing: 0.7px; color: var(--navy); white-space: nowrap;
        }
        body.dark-mode .sr-query-band-label { color: #8ab4d4; }
        .sr-query-band-term {
            font-size: 15px; font-weight: bold; color: var(--charcoal);
            font-family: Georgia, serif;
        }
        body.dark-mode .sr-query-band-term { color: #e0e0e0; }
        .sr-query-band-divider {
            display: inline-block; width: 1px; height: 16px;
            background-color: var(--navy); opacity: 0.2; vertical-align: middle;
        }
        .sr-new-search-link {
            font-size: 12px; font-weight: bold; color: var(--navy);
            border: 1.5px solid var(--navy); border-radius: 20px;
            padding: 4px 14px; white-space: nowrap;
            transition: background-color 0.15s, color 0.15s; flex-shrink: 0;
        }
        .sr-new-search-link:hover { background-color: #09497a; border-color: #09497a; color: #ffffff; }
        body.dark-mode .sr-new-search-link { color: #a8b361; border-color: #a8b361; }
        body.dark-mode .sr-new-search-link:hover { background-color: #a8b361; color: #121212; }

        /* ============================================
           TOOLBAR
        ============================================ */
        .sr-toolbar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 8px 20px; border-bottom: 1px solid #e0e8ed;
            background-color: #f9fbfc;
        }
        body.dark-mode .sr-toolbar { background-color: #181e22; border-color: #2a2a2a; }
        .sr-toolbar-left { display: flex; align-items: center; gap: 10px; }

        .filter-btn-disabled {
            display: inline-flex; align-items: center; gap: 8px;
            font-size: 13px; font-weight: bold; color: #aaa;
            background-color: #f4f4f4; border: 1.5px dashed #ccc;
            border-radius: 6px; padding: 6px 14px;
            cursor: not-allowed; user-select: none;
        }
        body.dark-mode .filter-btn-disabled { color: #444; background-color: #1a1a1a; border-color: #333; }
        .filter-coming-soon {
            font-size: 10px; font-weight: bold; text-transform: uppercase;
            letter-spacing: 0.6px; color: #aaa;
            border: 1px solid #ccc; border-radius: 3px; padding: 2px 6px;
            white-space: nowrap;
        }
        body.dark-mode .filter-coming-soon { color: #444; border-color: #333; }

        /* ============================================
           COLUMN HEADER
        ============================================ */
        .col-header-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 7px 14px; background-color: #09497a;
            border-radius: 4px; margin-bottom: 10px;
        }
        body.dark-mode .col-header-row { background-color: #0d2a45; }
        .col-header-label {
            font-size: 11px; font-weight: bold; text-transform: uppercase;
            letter-spacing: 0.8px; color: #ffffff;
        }

        /* ============================================
           SHARED COUNT PILL
        ============================================ */
        .db-count-pill {
            display: inline-block; font-size: 13px; font-weight: bold;
            color: var(--navy); background-color: #ffffff;
            border: 1.5px solid var(--navy); border-radius: 20px;
            padding: 5px 18px; white-space: nowrap; flex-shrink: 0;
            transition: background-color 0.15s, color 0.15s, border-color 0.15s;
            cursor: pointer; font-family: inherit;
        }
        .db-count-pill:hover { background-color: #09497a; border-color: #09497a; color: #ffffff; }
        .db-count-pill.sm { font-size: 12px; padding: 3px 13px; border-width: 1px; }
        body.dark-mode .db-count-pill { background-color: #1a1a1a; border-color: #8ab4d4; color: #8ab4d4; }
        body.dark-mode .db-count-pill:hover { background-color: #09497a; border-color: #09497a; color: #e0e0e0; }
        .db-count-pill.ext::after,
        a.db-name-link.ext::after { content: ' \2197'; font-size: 10px; opacity: 0.7; }

        /* ============================================
           ACCORDION GROUPS
        ============================================ */
        .groups-list { display: flex; flex-direction: column; gap: 6px; }

        .result-group { border: 1px solid #d0d8e0; border-radius: 6px; overflow: hidden; }
        body.dark-mode .result-group { border-color: #2a2a2a; }

        .group-toggle {
            width: 100%; display: flex; align-items: center; justify-content: space-between;
            padding: 10px 14px; background-color: #ffffff; border: none;
            border-left: 3px solid var(--sage); cursor: pointer;
            text-align: left; gap: 10px; font-family: inherit;
            transition: filter 0.15s;
        }
        body.dark-mode .group-toggle { background-color: #1e1e1e; border-left-color: #a8b361; }
        .group-toggle:hover { filter: brightness(0.96); }
        .group-toggle:focus-visible { outline: 3px solid var(--navy); outline-offset: -3px; }

        .group-toggle-left { display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0; }
        .group-jg-logo { height: 22px; width: auto; flex-shrink: 0; }
        .group-name { font-size: 14px; font-weight: bold; color: var(--navy); font-family: Georgia, serif; }
        body.dark-mode .group-name { color: #e0e0e0; }
        .group-db-count { font-size: 12px; color: #999; white-space: nowrap; }
        body.dark-mode .group-db-count { color: #555; }
        .group-toggle-right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }

        .group-record-badge {
            font-size: 11px; font-weight: bold; color: var(--navy);
            border: 1.5px solid var(--navy); border-radius: 20px;
            padding: 2px 11px; white-space: nowrap; background-color: transparent;
        }
        body.dark-mode .group-record-badge { color: #8ab4d4; border-color: #8ab4d4; }

        .group-chevron {
            font-size: 11px; color: var(--sage);
            transition: transform 0.2s ease;
            display: inline-block; line-height: 1;
        }
        body.dark-mode .group-chevron { color: #a8b361; }
        .group-toggle[aria-expanded="true"]  .group-chevron { transform: rotate(180deg); }

        .group-panel { border-top: 1px solid #d0d8e0; }
        .group-panel[hidden] { display: none; }
        body.dark-mode .group-panel { border-color: #2a2a2a; }

        .db-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 8px 14px 8px 48px; border-bottom: 1px solid #ebebeb;
            gap: 12px;
        }
        .db-row:last-child { border-bottom: none; }
        .db-row:hover { background-color: #eaf2f6; }
        .group-panel .db-row { background-color: #f4f8fa; }
        .group-panel .db-row:nth-child(even) { background-color: #eaf2f6; }
        body.dark-mode .group-panel .db-row { background-color: #161c20; }
        body.dark-mode .group-panel .db-row:nth-child(even) { background-color: #131a1f; }
        body.dark-mode .db-row { border-color: #2a2a2a; }
        body.dark-mode .db-row:hover { background-color: #1a2533; }

        .db-name-link { font-size: 13px; color: var(--navy); text-decoration: none; flex: 1; min-width: 0; }
        body.dark-mode .db-name-link { color: #8ab4d4; }
        .db-name-link:hover { color: var(--sage); text-decoration: underline dotted; text-underline-offset: 2px; }

        /* ============================================
           STATE STYLES (loading / error / empty)
        ============================================ */
        .sr-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--charcoal);
        }
        body.dark-mode .sr-state { color: #a0a0a0; }
        .sr-state-spinner {
            display: inline-block;
            width: 32px; height: 32px;
            border: 3px solid #d0d8e0;
            border-top-color: var(--navy);
            border-radius: 50%;
            animation: sr-spin 0.8s linear infinite;
            margin-bottom: 14px;
        }
        @keyframes sr-spin { to { transform: rotate(360deg); } }
        .sr-state-title { font-size: 16px; font-weight: bold; color: var(--navy); margin-bottom: 6px; font-family: Georgia, serif; }
        body.dark-mode .sr-state-title { color: #e0e0e0; }
        .sr-state-detail { font-size: 14px; color: #666; }
        body.dark-mode .sr-state-detail { color: #888; }
        .sr-state-error .sr-state-title { color: #b03030; }
        body.dark-mode .sr-state-error .sr-state-title { color: #e08080; }

        /* ============================================
           BOTTOM ACTIONS
        ============================================ */
        .bottom-actions {
            margin-top: 36px; padding-top: 20px;
            border-top: 1px solid #d0cabb;
            display: flex; justify-content: center;
            gap: 16px; flex-wrap: wrap;
        }
        body.dark-mode .bottom-actions { border-top-color: #333; }
        .bottom-action-link {
            display: inline-block; font-size: 14px; font-weight: bold;
            color: var(--navy); background-color: #ffffff;
            border: 2px solid var(--navy); border-radius: 6px;
            padding: 8px 20px;
            transition: background-color 0.15s, color 0.15s;
        }
        .bottom-action-link:hover { background-color: #09497a; border-color: #09497a; color: #ffffff; }
        body.dark-mode .bottom-action-link { background-color: #1a1a1a; color: #e0e0e0; border-color: #e0e0e0; }
        body.dark-mode .bottom-action-link:hover { background-color: #e0e0e0; color: #121212; }

        /* ============================================
           RESPONSIVE
        ============================================ */
        @media (max-width: 768px) {
            .sr-title-band h1 { font-size: 1.5rem; }
            .sr-outer { padding: 16px 10px 48px; }
            .sr-card-inner { padding: 14px 12px 24px; }

            .sr-query-band {
                flex-direction: column; align-items: flex-start; gap: 8px;
            }
            .sr-query-right { width: 100%; justify-content: space-between; }

            .sr-filter-bar {
                flex-direction: column; align-items: stretch; gap: 8px;
            }

            .jg-tips-link { font-size: 13px; }

            .col-header-row { display: none; }

            .group-toggle {
                flex-wrap: wrap; padding: 10px 12px; gap: 6px;
            }
            .group-toggle-left {
                flex: 1 1 100%; min-width: 0;
            }
            .group-jg-logo { height: 18px; }
            .group-name {
                font-size: 13px;
                overflow: hidden; text-overflow: ellipsis;
            }
            .group-db-count { font-size: 11px; }
            .group-toggle-right {
                flex: 1 1 100%;
                justify-content: flex-end;
                padding-left: 28px;
            }
            .group-record-badge { font-size: 10px; padding: 2px 8px; }

            .db-row {
                flex-direction: column; align-items: flex-start;
                gap: 6px; padding: 10px 12px 10px 20px;
            }
            .db-count-pill { align-self: flex-start; }
            .db-count-pill.sm { font-size: 11px; padding: 3px 10px; }

            .sr-bottom-actions {
                flex-direction: column; align-items: stretch;
            }
            .bottom-action-link { text-align: center; }
        }

        @media (max-width: 480px) {
            .sr-hero-inner { padding: 0 4px; }
            .sr-card-inner { padding: 10px 8px 20px; }
            .group-toggle { padding: 8px 10px; }
            .group-toggle-left { gap: 6px; }
            .group-name { font-size: 12px; }
            .group-toggle-right { padding-left: 24px; }
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
    <div class="page-title-band">
        <span class="tagline">Search Results</span>
        <h1>All JewishGen Databases</h1>
        <p class="hero-subtitle" id="sr-total"><?php echo $hasSearch ? 'Searching&hellip;' : 'No search submitted'; ?></p>
    </div>

    <section aria-label="Search results">
        <div class="sr-hero-inner">


        <div class="jg-search-card">
            <div class="sr-card-inner">

                <?php if ($hasSearch): ?>
                <!-- Query band - server-rendered from URL params -->
                <div class="sr-query-band" role="region" aria-label="Search query summary">
                    <div class="sr-query-band-left">
                        <?php foreach ($pairs as $idx => $pair): ?>
                            <?php if ($idx > 0): ?>
                                <span class="sr-query-band-divider" aria-hidden="true"></span>
                            <?php endif; ?>
                            <div class="sr-query-band-pair">
                                <span class="sr-query-band-label"><?php echo srEsc($pair['label']); ?></span>
                                <span class="sr-query-band-term"><?php echo srEsc($pair['term']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="/search/Global_Search.php" class="sr-new-search-link">&#8592; New Search</a>
                </div>
                <?php endif; ?>

                <!-- Toolbar: filter + tips -->
                <div class="sr-toolbar" role="toolbar" aria-label="Results tools">
                    <div class="sr-toolbar-left">
                        <div class="filter-btn-disabled" aria-disabled="true" role="button" tabindex="-1"
                             title="Filtering is coming in a future update">
                            &#9881; Filter
                            <span class="filter-coming-soon">Coming Soon</span>
                        </div>
                    </div>
                    <button type="button" class="jg-tips-link"
                            aria-expanded="false" aria-controls="sr-tips-panel"
                            onclick="toggleResultsTips(this)">
                        Tips for getting the most out of your results
                        <img src="/images/site/TipsBulbReg.svg"  alt="" class="jg-tips-link__icon light-logo" aria-hidden="true">
                        <img src="/images/site/TipsBulbDark.svg" alt="" class="jg-tips-link__icon dark-logo"  aria-hidden="true">
                    </button>
                </div>

                <!-- Tips panel -->
                <div class="jg-tips-card-panel" id="sr-tips-panel" role="region" aria-label="Results tips">
                    <div class="sr-tips-grid">
                        <div><strong>Collapsed groups still have matches.</strong> A closed accordion means there are results inside &mdash; click it to expand.</div>
                        <div><strong>Click the database name</strong> to learn what that database contains and how records were collected before diving in.</div>
                        <div><strong>Too many results?</strong> Return to search and narrow by country, region, or record type to focus on the most relevant databases.</div>
                        <div><strong>Not finding what you expect?</strong> Try a phonetic search &mdash; spelling varied widely in historical records.</div>
                    </div>
                    <div class="jg-tips-panel__footer">
                        <a href="/HowToFAQ/howto.html">Need more help? Read our complete "How to JewishGen" guide &rarr;</a>
                    </div>
                </div>

                <!-- Column header bar -->
                <div class="col-header-row" aria-hidden="true">
                    <span class="col-header-label">Dataset</span>
                    <span class="col-header-label">Click to view results</span>
                </div>

                <!-- ============================================
                     RESULTS CONTAINER
                     PHP renders initial state; JS replaces with
                     real results after searchjson.php returns.
                ============================================ -->
                <div id="sr-results-container">
                    <?php if ($hasSearch): ?>
                        <div class="sr-state">
                            <div class="sr-state-spinner" aria-hidden="true"></div>
                            <div class="sr-state-title">Searching the JewishGen databases&hellip;</div>
                            <div class="sr-state-detail">This usually takes a few seconds.</div>
                        </div>
                    <?php else: ?>
                        <div class="sr-state">
                            <div class="sr-state-title">No search submitted</div>
                            <div class="sr-state-detail">Use the search form to look for records across the JewishGen databases.</div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Bottom navigation -->
                <div class="bottom-actions" role="navigation" aria-label="Search navigation">
                    <a href="/search/Global_Search.php" class="bottom-action-link">&#8592; New Search</a>
                    <a href="/index.php" class="bottom-action-link">JewishGen Home</a>
                </div>

            </div><!-- /sr-card-inner -->
        </div><!-- /jg-search-card -->

    </div><!-- /sr-hero-inner -->
</section>
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

<!-- ============================================
     SCRIPTS
============================================ -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
/* Search params embedded by PHP so JS can use them after a POST submission
   (when window.location.search is empty). */
window.JG_SEARCH_PARAMS = <?php echo json_encode($jsSearchParams, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

/* ============================================================
   CONFIGURATION
============================================================ */
const SEARCHJSON_URL  = '/databases/searchjson.php';
const RECORD_LIST_URL = '/search/record-list.php';


/* ============================================================
   NAV DROPDOWN KEYBOARD HANDLERS
   Header is server-included so its DOM is ready immediately.
============================================================ */
document.querySelectorAll('.jg-nav .dropbtn').forEach(btn => {
    btn.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            const expanded = btn.getAttribute('aria-expanded') === 'true';
            btn.setAttribute('aria-expanded', String(!expanded));
            const menu = document.getElementById(btn.getAttribute('aria-controls'));
            if (menu) menu.style.display = expanded ? 'none' : 'block';
        }
        if (e.key === 'Escape') {
            btn.setAttribute('aria-expanded', 'false');
            const menu = document.getElementById(btn.getAttribute('aria-controls'));
            if (menu) menu.style.display = 'none';
        }
    });
});


/* ============================================================
   ENTRY POINT
   Query band already rendered server-side; JS only needs to
   fire the data fetch when search params exist. Params come
   from window.JG_SEARCH_PARAMS (embedded by PHP), not from
   the URL - the form POSTs, so the URL stays clean.
============================================================ */
<?php if ($hasSearch): ?>
const params = new URLSearchParams(window.JG_SEARCH_PARAMS || {});
fetchAndRender(params);
<?php endif; ?>


/* ============================================================
   FETCH searchjson.php AND RENDER
============================================================ */
function fetchAndRender(params) {
    fetch(SEARCHJSON_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: params.toString()
    })
    .then(r => {
        if (!r.ok) throw new Error('Server returned ' + r.status);
        return r.json();
    })
    .then(data => {
        if (data.error) throw new Error(data.error);
        renderResults(data);
    })
    .catch(err => {
        console.error(err);
        showState('error',
            'Search failed',
            err.message + ' &mdash; try a new search, or contact support if this persists.');
    });
}


/* ============================================================
   RENDER RESULTS into the accordion
============================================================ */
function renderResults(data) {
    const total = data.search_summary.total_matches || 0;
    document.getElementById('sr-total').textContent =
        formatCount(total) + ' total match' + (total === 1 ? '' : 'es') + ' found';

    const container = document.getElementById('sr-results-container');

    if (!data.research_divisions || data.research_divisions.length === 0) {
        showState('empty',
            'No matching records',
            'Try a different spelling, broader region, or a phonetic search.');
        return;
    }

    const groupsHtml = data.research_divisions.map(rd => buildGroupHtml(rd)).join('');
    container.innerHTML = '<div class="groups-list" role="list">' + groupsHtml + '</div>';

    initAccordion();
}


function buildGroupHtml(rd) {
    const dbCount = rd.databases.length;
    const totalRecords = rd.databases.reduce((sum, db) => sum + db.count, 0);
    // Defensive: PHP edge cases (or a debug/error response) might send a
    // non-string name. Cast to string so .toLowerCase never throws.
    const rdName = String(rd.name || 'OTHER');
    const safeId = 'panel-' + rdName.toLowerCase().replace(/[^a-z0-9]+/g, '-');
    const rowsHtml = rd.databases.map(db => buildDbRow(db)).join('');

    return `
    <div class="result-group" role="listitem" data-db-count="${dbCount}">
        <button class="group-toggle" aria-expanded="false" aria-controls="${safeId}" id="toggle-${safeId}">
            <div class="group-toggle-left">
                <img src="https://www.jewishgen.org/JG/Images/JGlogo.svg" alt="JewishGen" class="light-logo group-jg-logo">
                <img src="https://www.jewishgen.org/JG/Images/JGlogoWhite.png" alt="JewishGen" class="dark-logo group-jg-logo">
                <span class="group-name">${escapeHtml(rdName)}</span>
                <span class="group-db-count">${dbCount} database${dbCount === 1 ? '' : 's'}</span>
            </div>
            <div class="group-toggle-right">
                <span class="group-record-badge">${formatCount(totalRecords)} record${totalRecords === 1 ? '' : 's'}</span>
                <span class="group-chevron" aria-hidden="true">&#9660;</span>
            </div>
        </button>
        <div id="${safeId}" class="group-panel" role="region" aria-labelledby="toggle-${safeId}" hidden>
            ${rowsHtml}
        </div>
    </div>`;
}


function buildDbRow(db) {
    const nameLink = db.info_url
        ? `<a href="${escapeAttr(db.info_url)}" class="db-name-link" target="_blank" rel="noopener noreferrer">${escapeHtml(db.title)}</a>`
        : `<span class="db-name-link">${escapeHtml(db.title)}</span>`;

    const recordsLabel = 'List ' + formatCount(db.count) + ' record' + (db.count === 1 ? '' : 's');

    return `
        <div class="db-row">
            ${nameLink}
            <button type="button" class="db-count-pill sm"
                    onclick="listRecords('${escapeAttr(db.df_id)}')"
                    aria-label="${escapeAttr(recordsLabel + ' from ' + db.title)}">
                ${escapeHtml(recordsLabel)}
            </button>
        </div>`;
}


/* ============================================================
   LIST RECORDS - navigate to the Tier 2 record list page
============================================================ */
function listRecords(dfId) {
    const fields = new URLSearchParams(window.JG_SEARCH_PARAMS || {});
    const p = new URLSearchParams();

    p.set('df', dfId);
    p.set('georegion', fields.get('GeoRegion') || fields.get('allcountry') || '0*');
    p.set('srchbool', fields.get('SrchBOOL') || 'AND');
    p.set('dates', fields.get('dates') || 'all');
    if (fields.get('Months')) p.set('Months', fields.get('Months'));
    if (fields.get('Years'))  p.set('Years', fields.get('Years'));
    p.set('recstart', '0');

    for (let i = 1; i <= 4; i++) {
        ['srch' + i, 'srch' + i + 'v', 'srch' + i + 't'].forEach(name => {
            const val = fields.get(name);
            if (val) p.set(name, val);
        });
    }

    window.open(RECORD_LIST_URL + '?' + p.toString(), '_blank');
}


/* ============================================================
   ACCORDION - always start collapsed
============================================================ */
function initAccordion() {
    document.querySelectorAll('.result-group').forEach(group => {
        const dbCount = parseInt(group.dataset.dbCount, 10) || 0;
        const toggle = group.querySelector('.group-toggle');
        const panel = group.querySelector('.group-panel');
        if (!toggle || !panel) return;

        const startExpanded = false;
        toggle.setAttribute('aria-expanded', startExpanded ? 'true' : 'false');
        if (startExpanded) panel.removeAttribute('hidden');
        else panel.setAttribute('hidden', '');

        toggle.addEventListener('click', () => {
            const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', isExpanded ? 'false' : 'true');
            if (isExpanded) panel.setAttribute('hidden', '');
            else panel.removeAttribute('hidden');
        });

        toggle.addEventListener('keydown', e => {
            if (e.key === ' ') { e.preventDefault(); toggle.click(); }
        });
    });
}


/* ============================================================
   STATE HELPERS
============================================================ */
function showState(kind, title, detail) {
    const container = document.getElementById('sr-results-container');
    const cls = kind === 'error' ? 'sr-state sr-state-error' : 'sr-state';
    const spinner = kind === 'loading' ? '<div class="sr-state-spinner" aria-hidden="true"></div>' : '';
    container.innerHTML = `
        <div class="${cls}">
            ${spinner}
            <div class="sr-state-title">${escapeHtml(title)}</div>
            <div class="sr-state-detail">${escapeHtml(detail)}</div>
        </div>
    `;
    if (kind !== 'loading') {
        document.getElementById('sr-total').textContent =
            kind === 'error' ? 'Search error' : '0 matches';
    }
}


/* ============================================================
   UTILITIES
============================================================ */
function formatCount(n) {
    return (n || 0).toLocaleString('en-US');
}

function escapeHtml(s) {
    if (s == null) return '';
    return String(s).replace(/[&<>"']/g, c => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));
}

function escapeAttr(s) {
    return escapeHtml(s);
}


/* ============================================================
   TIPS PANEL TOGGLE
============================================================ */
function toggleResultsTips(btn) {
    const panel = document.getElementById('sr-tips-panel');
    const isOpen = panel.classList.toggle('open');
    btn.setAttribute('aria-expanded', String(isOpen));
}
</script>

</body>
</html>