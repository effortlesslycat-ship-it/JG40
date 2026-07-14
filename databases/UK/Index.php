<?php
/* ============================================================================
   /databases/UK/index.php  -  JewishGen United Kingdom Database
   Project Shorashim / JG40-Build
   ----------------------------------------------------------------------------
   sys  = reg_data "sys" id  -> setRegions() (via jgCollectionInit regionSys)
   solr = reg_data "val"     -> hidden allcountry (the real search filter)
   Both come straight from RegionsData_solr.js - never guess them.
   CHW
============================================================================ */

$jg_collection = array(
    'sys'        => 'ALLUK',
    'solr'       => '00uk',
    'tagline'    => 'Regional Collection',
    'title'      => 'JewishGen United Kingdom Database',
    'lede'       => 'Records for the United Kingdom &mdash; England, Scotland, Wales, and Northern Ireland; the Isle of Man, the Channel Islands, and Gibraltar; and the Republic of Ireland.',
    'stat'       => 'Some 480,000 records.',
    'card_title' => 'Search the United Kingdom Collection',
    'button'     => 'Search the United Kingdom Database',
    'intro'      => '<img class="db-intro-logo" src="https://jewishgen.org/JCR-UK/images/JCR-UK_logo.gif" alt="JCR-UK logo"><p>Welcome to JCR-UK\'s All United Kingdom Database. This search system incorporates all of the databases listed below. These databases have been contributed to <a class="db-textlink" href="https://jewishgen.org/JCR-UK/">Jewish Communities and Records - United Kingdom (JCR-UK)</a> &mdash; a joint project of JewishGen and the Jewish Genealogical Society of Great Britain (JGSGB) &mdash; by JewishGen, JGSGB, and individual donors. The combined databases contain some 480,000 records referring to individuals in the United Kingdom &mdash; England, Scotland, Wales and Northern Ireland; the Isle of Man, the Channel Islands and Gibraltar; as well as the Republic of Ireland. This database is a work in progress, and new entries are being added regularly.</p>'
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JewishGen United Kingdom Database</title>
    <meta name="keywords" content="United Kingdom, England, Scotland, Wales, Ireland">
    <meta name="description" content="Search JewishGen's United Kingdom records, and browse every database in the collection.">

    <!-- 1. Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- 2. JewishGen Global Design System -->
    <link rel="stylesheet" href="/jg-global.css">
    <!-- 3. Databases section stylesheet -->
    <link rel="stylesheet" href="/databases/jg-databases.css?v=13">
</head>
<body>

<div id="site-header"></div>

<main id="main-content" class="db-collection-page">

<?php include $_SERVER['DOCUMENT_ROOT'] . '/databases/searchform.php'; ?>

    <!-- Database listing (rendered by jg-collection.js) -->
    <div class="db-listing-head">
        <h2>Databases in this Collection</h2>
    </div>

    <!-- Filter/sort bar (controls injected by jg-collection.js) -->
    <div class="db-filters" id="collectionFilters"></div>

    <div class="results-header">
        <div class="db-inner">
            <div class="results-count">
                Showing <strong id="collectionCount">0</strong> databases
            </div>
        </div>
    </div>

    <div class="catalog-list db-compact" id="collectionList" aria-label="United Kingdom database listing"></div>

    <div class="catalog-empty" id="collectionEmpty" aria-live="polite">
        <h3>No databases to display</h3>
        <p>The collection could not be loaded. Please try again later.</p>
    </div>

</main>

<div class="db-partner-footer">
    <div>
        <a class="db-textlink" href="/JCR-UK/">JCR-UK</a> is a joint project between
        <a class="db-textlink" href="/">JewishGen</a> and the
        <a class="db-textlink" href="https://www.jgsgb.org.uk">Jewish Genealogical Society of Great Britain</a>.
    </div>
    <a href="https://www.jgsgb.org.uk"><img src="https://jewishgen.org/JCR-UK/images/JCR-UK_logo.gif" alt="JGSGB logo"></a>
</div>

<div id="site-footer"></div>

<!-- Legacy search JS (canonical source; order matters) -->
<script src="https://www.jewishgen.org/JG/Scripts/FormUtils.js"></script>
<script src="https://www.jewishgen.org/Communities/Utils.js"></script>
<script src="https://www.jewishgen.org/databases/Regions/RegionsData_solr.js"></script>
<script src="https://www.jewishgen.org/databases/Regions/Regions.js"></script>
<script src="https://www.jewishgen.org/databases/Regions/Overlay.js"></script>
<script src="https://www.jewishgen.org/databases/Regions/SearchForm_solr.js"></script>

<!-- Shared collection-page machinery (bump ?v= on every edit) -->
<script src="/databases/jg-collection.js?v=7"></script>
<script>
    jgCollectionInit({
        regionSys:  'ALLUK',
        regionTag:  ['UK','United Kingdom']
    });
</script>

</body>
</html>