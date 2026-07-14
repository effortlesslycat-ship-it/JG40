<?php
/* ============================================================================
   searchform.php  -  Shared collection search form (Solr)
   Project Shorashim / JG40-Build
   ----------------------------------------------------------------------------
   Included by /databases/<Region>/index.php. New-stack equivalent of the
   legacy /databases/Regions/SearchForm_solr.txt SSI include.

   The host page MUST define $jg_collection (array) BEFORE including:

       $jg_collection = array(
           'sys'        => 'ALLROMANIA',           // reg_data sys id (setRegions, via jgCollectionInit)
           'solr'       => '00romania',            // reg_data val -> hidden allcountry (the real filter)
           'title'      => 'JewishGen Romania Database',
           'lede'       => 'Search more than ...', // hero subtitle (HTML entities ok)
           'card_title' => 'Search the Romania Collection',
           'button'     => 'Search the Romania Database'
       );

   PHP 5.x: array() syntax, isset() ternaries, no null coalescing.
   Field names, IDs, and option values are dictated by the legacy JS files
   (FormUtils, Utils, RegionsData_solr, Regions, Overlay, SearchForm_solr)
   loaded by /databases/jg-collection.js - do NOT rename.
   ASCII only; special characters as HTML entities.
   CHW
============================================================================ */

if (!isset($jg_collection) || !is_array($jg_collection)) { $jg_collection = array(); }

$jg_solr       = isset($jg_collection['solr'])       ? $jg_collection['solr']       : '0*';
$jg_tagline    = isset($jg_collection['tagline'])    ? $jg_collection['tagline']    : 'Regional Collection';
$jg_title      = isset($jg_collection['title'])      ? $jg_collection['title']      : 'JewishGen Database Search';
$jg_lede       = isset($jg_collection['lede'])       ? $jg_collection['lede']       : '';
$jg_stat       = isset($jg_collection['stat'])       ? $jg_collection['stat']       : '';
$jg_card_title = isset($jg_collection['card_title']) ? $jg_collection['card_title'] : 'Search This Collection';
$jg_button     = isset($jg_collection['button'])     ? $jg_collection['button']     : 'Search Databases';
$jg_intro      = isset($jg_collection['intro'])      ? $jg_collection['intro']      : '';

$jg_solr_attr  = htmlspecialchars($jg_solr, ENT_QUOTES);
?>
    <!-- ================================================================
         PAGE TITLE BAND + SEARCH - matches Global_Search.php heading
         style: navy .page-title-band banner, then the search card in a
         separate .jg-band-content section. Region is FIXED: no country
         dropdown; the hidden allcountry override carries the collection's
         solr val. setRegions(sys) populates the GeoRegion "refine" dropdown
         (called from jg-collection.js init).
         ================================================================ -->
    <div class="page-title-band">
        <span class="tagline"><?php echo $jg_tagline; ?></span>
        <h1><?php echo $jg_title; ?></h1>
        <?php if ($jg_lede != '') { ?>
        <p class="hero-subtitle"><?php echo $jg_lede; ?></p>
        <?php } ?>
        <?php if ($jg_stat != '') { ?>
        <span class="stat-line"><?php echo $jg_stat; ?></span>
        <?php } ?>
    </div>

    <section class="jg-band-content" aria-label="<?php echo htmlspecialchars($jg_title, ENT_QUOTES); ?> search">
        <div class="jg-search-hero__inner">

            <?php if ($jg_intro != '') { ?>
            <!-- Collection description (above the search card) -->
            <div class="db-collection-intro"><?php echo $jg_intro; ?></div>
            <?php } ?>

            <form name="f" id="f" method="post" action="/search-results.php"
                  class="jg-search-card" role="search"
                  aria-label="<?php echo htmlspecialchars($jg_title, ENT_QUOTES); ?>"
                  data-region-solr="<?php echo $jg_solr_attr; ?>">

                <div class="jg-search-card__header">
                    <h2><?php echo $jg_card_title; ?></h2>
                    <button type="button" class="jg-tips-link"
                            aria-expanded="false" aria-controls="jg-search-tips-panel"
                            onclick="toggleSearchTips(this)">
                        Tips &amp; Tricks
                        <img src="/images/site/TipsBulbReg.png"  alt="" class="jg-tips-link__icon light-logo" aria-hidden="true">
                        <img src="/images/site/TipsBulbDark.png" alt="" class="jg-tips-link__icon dark-logo"  aria-hidden="true">
                    </button>
                </div>
                <hr class="jg-search-card__rule" aria-hidden="true">

                <!-- Shared tips panel component (fetched by jg-collection.js;
                     NOT PHP-included: SearchTips.html's own header comment
                     contains a live PHP tag that causes infinite recursion
                     if the file is parsed by PHP) -->
                <div id="jg-search-tips"></div>

                <!-- ====================== FREE ROW 1 ====================== -->
                <div class="search-row">
                    <div class="sf n">
                        <label class="field-label" for="SEL1">Data Type</label>
                        <select id="SEL1" name="srch1v" onchange="SelProc(this, 'OPT1', 'SRCH1', 0)">
                            <option value="">Data Type</option>
                            <option value="S" selected>Surname</option>
                            <option value="G">Given Name</option>
                            <option value="T">Town</option>
                            <option value="X">Any Field</option>
                        </select>
                    </div>
                    <div class="sf n">
                        <label class="field-label" for="OPT1">Search Type</label>
                        <select id="OPT1" name="srch1t"
                                onchange="if(value==''){document.getElementById('SRCH1').value='';document.getElementById('SRCH1').disabled=true;}else{document.getElementById('SRCH1').disabled=false;}">
                            <option value="">Search Type</option>
                            <option value="Q" selected>Phonetically Like</option>
                            <option value="D">Sounds Like</option>
                            <option value="S">Starts With</option>
                            <option value="E">Is Exactly</option>
                            <option value="F1">Fuzzy Match</option>
                            <option value="F2">Fuzzier Match</option>
                            <option value="FM">Fuzziest Match</option>
                        </select>
                    </div>
                    <div class="sf w">
                        <label class="field-label" for="SRCH1">Search Term</label>
                        <input type="text" id="SRCH1" name="srch1" size="25" maxlength="25"
                               placeholder="e.g. Hollander" autocomplete="off">
                    </div>
                </div>

                <!-- ====================== FREE ROW 2 ====================== -->
                <div class="search-row">
                    <div class="sf n">
                        <label class="field-label" for="SEL2">Data Type</label>
                        <select id="SEL2" name="srch2v" onchange="SelProc(this, 'OPT2', 'SRCH2', 0)">
                            <option value="" selected>Data Type</option>
                            <option value="S">Surname</option>
                            <option value="G">Given Name</option>
                            <option value="T">Town</option>
                            <option value="X">Any Field</option>
                        </select>
                    </div>
                    <div class="sf n">
                        <label class="field-label" for="OPT2">Search Type</label>
                        <select id="OPT2" name="srch2t" disabled
                                onchange="if(value==''){document.getElementById('SRCH2').value='';document.getElementById('SRCH2').disabled=true;}else{document.getElementById('SRCH2').disabled=false;}">
                            <option value="" selected>Search Type</option>
                            <option value="Q">Phonetically Like</option>
                            <option value="D">Sounds Like</option>
                            <option value="S">Starts With</option>
                            <option value="E">Is Exactly</option>
                            <option value="F1">Fuzzy Match</option>
                            <option value="F2">Fuzzier Match</option>
                            <option value="FM">Fuzziest Match</option>
                        </select>
                    </div>
                    <div class="sf w">
                        <label class="field-label" for="SRCH2">Search Term</label>
                        <input type="text" id="SRCH2" name="srch2" size="25" maxlength="25"
                               placeholder="Search term" autocomplete="off">
                    </div>
                </div>

                <!-- ====================== LOCKED ROWS 3 & 4 ======================
                     Canonical value-added contributor overlay (persistent).
                     When CURE donor detection is wired, real srch3/srch4
                     inputs replace these and the overlay lifts. ============ -->
                <div class="jg-locked-rows-wrapper">

                    <div aria-hidden="true">
                        <div class="search-row locked">
                            <div class="sf n"><select tabindex="-1" disabled><option>Data Type</option></select></div>
                            <div class="sf n"><select tabindex="-1" disabled><option>Search Type</option></select></div>
                            <div class="sf w"><input type="text" tabindex="-1" disabled placeholder="Search term"></div>
                        </div>
                        <div class="search-row locked" style="margin-bottom: 0;">
                            <div class="sf n"><select tabindex="-1" disabled><option>Data Type</option></select></div>
                            <div class="sf n"><select tabindex="-1" disabled><option>Search Type</option></select></div>
                            <div class="sf w"><input type="text" tabindex="-1" disabled placeholder="Search term"></div>
                        </div>
                    </div>

                    <div class="jg-locked-overlay" role="note" aria-label="Value-added feature: donate to unlock additional search rows">
                        <div class="jg-locked-overlay__inner">
                            <div class="jg-locked-overlay__icon" aria-hidden="true">&#128275;</div>
                            <div class="jg-locked-overlay__text">
                                <strong>Unlock Two Additional Search Rows</strong>
                                <p>A donation of $100 or more to the JewishGen General Fund unlocks multi-field search, letting you combine up to four simultaneous criteria for more precise results.</p>
                                <a href="https://www.jewishgen.org/jewishgen-secure/donate.asp"
                                   class="jg-btn-unlock" target="_blank" rel="noopener noreferrer">
                                    Donate Now to Unlock &rarr;
                                </a>
                            </div>
                        </div>
                    </div>

                </div><!-- /.jg-locked-rows-wrapper -->

                <!-- Hidden: AND/OR operator (AND so single-row searches behave) -->
                <input type="hidden" name="SrchBOOL" id="SrchBOOL" value="AND">

                <!-- ====================== REGION (FIXED) ======================
                     Populated by setRegions(sys) from jg-collection.js init.
                     GeoRegion's value is what /search-results.php filters on;
                     left at "All Regions", the hidden allcountry code scopes
                     the search to the whole collection. ===================== -->
                <div class="region-block">
                    <div id="SubRegionsDiv" role="region" aria-live="polite">
                        <span class="sub-region-label" for="GeoRegion">Refine by Region (optional)</span>
                        <select id="GeoRegion" name="GeoRegion" disabled>
                            <option value="ALL" selected>All Regions</option>
                        </select>
                    </div>
                </div>

                <!-- Hidden: date filter (defaults to all entries) -->
                <input type="hidden" name="dates"  id="dates"  value="all">
                <input type="hidden" name="Months" id="Months" value="01">
                <input type="hidden" name="Years"  id="Years"  value="2018">

                <!-- Hidden override: region FIXED to this collection.
                     /search-results.php falls back to whichever allcountry
                     input comes LAST in the form. -->
                <input type="hidden" name="allcountry" value="<?php echo $jg_solr_attr; ?>">
                <input type="hidden" name="submitform" value="submitform">

                <div class="search-submit-wrap">
                    <button class="btn-search" type="button" id="SearchButton"
                            onclick="doSubmit(document.f);"><?php echo $jg_button; ?></button>
                </div>

            </form>
        </div><!-- /.jg-search-hero__inner -->
    </section>