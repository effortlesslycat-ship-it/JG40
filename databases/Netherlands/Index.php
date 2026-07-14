<?php
/* ============================================================================
   /databases/Netherlands/index.php  -  JewishGen Netherlands Database
   Project Shorashim / JG40-Build
   ----------------------------------------------------------------------------
   sys  = reg_data "sys" id  -> setRegions() (via jgCollectionInit regionSys)
   solr = reg_data "val"     -> hidden allcountry (the real search filter)
   Both come straight from RegionsData_solr.js - never guess them.
   CHW
============================================================================ */

$jg_collection = array(
    'sys'        => 'NETH',
    'solr'       => '00Holland',
    'tagline'    => 'Regional Collection',
    'title'      => 'JewishGen Netherlands Database',
    'lede'       => 'Records for the Jewish communities of the Netherlands.',
    'stat'       => '',
    'card_title' => 'Search the Netherlands Collection',
    'button'     => 'Search the Netherlands Database',
    'intro'      => '<p>Welcome to the JewishGen Netherlands Database. This is a multiple database search facility which incorporates all the JewishGen databases listed below. The database is a work in progress, and new entries are being added regularly.</p>'
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JewishGen Netherlands Database</title>
    <meta name="keywords" content="Netherlands, Holland">
    <meta name="description" content="Search JewishGen's Netherlands records, and browse every database in the collection.">

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

    <div class="catalog-list db-compact" id="collectionList" aria-label="Netherlands database listing"></div>

    <div class="catalog-empty" id="collectionEmpty" aria-live="polite">
        <h3>No databases to display</h3>
        <p>The collection could not be loaded. Please try again later.</p>
    </div>

</main>

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
        regionSys:  'NETH',
        regionTag:  ['Netherlands','Holland']
    });
</script>

</body>
</html>