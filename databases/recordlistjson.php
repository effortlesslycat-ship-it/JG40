<?php
/**
 * recordlistjson.php — Tier 2 record list endpoint (JSON)
 * ========================================================
 *
 * Mirrors jgdetail_2.php's Solr query and pagination logic, but returns
 * a clean structured JSON response instead of server-rendered HTML.
 * Consumed by /search/record-list.html.
 *
 * Drop at: \\databases\recordlistjson.php
 *
 * Same params as jgdetail_2.php (POST or GET):
 *   - df              dataset id (required, e.g. BESSREV)
 *   - georegion       region filter (e.g. 0*, 00ALL, 01holocaust, allromania)
 *   - srch1..srch4    search text per row
 *   - srch1v..srch4v  S/G/T/X (Surname/GivenName/Town/AnyField)
 *   - srch1t..srch4t  Q/D/S/E/F1/F2/FM (match type)
 *   - srchbool        AND or OR
 *   - dates           all or some
 *   - Months, Years   (when dates=some)
 *   - rectype, yearfilt1, yearfilt2, radius, lat, long, langu, litvak
 *   - recstart        pagination offset (default 0)
 *
 * Response shape:
 *   {
 *     "dataset":    { title, info_url, df_id },
 *     "query":      { summary, srch_bool, ran_at, region, within },
 *     "pagination": { total_count, page_size, current_page, total_pages,
 *                     start_record, end_record, has_prev, has_next },
 *     "columns":    [ { lines: ["Town", "Uyezd", "Guberniya"] }, ... ],
 *     "groups":     [
 *       { glue_id, rows: [
 *           { cells: [
 *               { html, rowspan?, highlighted? }, ...
 *             ] }, ...
 *         ] }, ...
 *     ]
 *   }
 *
 * v1 SCOPE — what this DOES NOT yet do:
 *   - No favorite-search saving (logid)
 *   - No JRI cross-search (jri_api2.php)
 *   - No Gesher Galicia merge
 *   - No Yad Vashem partner API
 *   These can be layered in v2 once the Tier 2 UI is solid.
 *
 * @author  CHW
 * @version 1.0
 */

// -----------------------------------------------------------------------------
// 1. HEADERS
// -----------------------------------------------------------------------------

$allowedOrigins = [
    'https://dev.jewishgen.org',
    'http://dev.jewishgen.org',
    'https://www.jewishgen.org',
    'https://jewishgen.org',
];
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
if (in_array($origin, $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

// -----------------------------------------------------------------------------
// 2. DEPENDENCIES — same pattern as jgform.php / jgdetail_2.php
// -----------------------------------------------------------------------------
//
// cureetc.php  ? MySQL config + CUREDB constant + helper functions
// bootstrap.php ? SOLR_SERVER_HOSTNAME and other Solr connection constants

include_once 'cureetc.php';
include 'bootstrap.php';

// -----------------------------------------------------------------------------
// 3. INPUT — read from POST or GET (jgdetail_2.php supports both)
// -----------------------------------------------------------------------------

$src = $_POST ?: $_GET;

function getParam($name, $default = '') {
    global $src;
    if (!isset($src[$name])) { return $default; }
    return is_string($src[$name]) ? trim($src[$name]) : $src[$name];
}

$df         = getParam('df');
$rec        = getParam('rec');           // Single-record mode (Tier 3)
$georegion  = getParam('georegion', '00ALL');
$srchBool   = strtoupper(getParam('srchbool', getParam('SrchBOOL', 'AND')));
if ($srchBool !== 'OR') { $srchBool = 'AND'; }

$rows = [];
for ($i = 1; $i <= 4; $i++) {
    $rows[$i] = [
        'value'      => getParam('srch' . $i),
        'dataType'   => getParam('srch' . $i . 'v'),
        'searchType' => getParam('srch' . $i . 't'),
    ];
}

$dates     = getParam('dates', 'all');
$months    = getParam('Months');
$years     = getParam('Years');
$rectype1  = getParam('rectype');
$yearfilt1 = getParam('yearfilt1');
$yearfilt2 = getParam('yearfilt2');
$radius    = getParam('radius');
$lat       = getParam('lat');
$long      = getParam('long');
$langu     = getParam('langu');
$litvak    = getParam('litvak');
$test      = '';

$recstart  = (int)getParam('recstart', 0);
$recbatch  = 50;
$recjump   = (int)getParam('recjump', 0);
if ($recjump === 1) {
    $recstart = ($recstart - 1) * $recbatch;
}
if ($recstart < 0) { $recstart = 0; }

// Determine mode: single-record (rec) or list (df + search terms)
$isRecordMode = !empty($rec);

if (!$isRecordMode && empty($df)) {
    respondError(400, 'MISSING_PARAMS', 'Either "df" (dataset) or "rec" (record ID) parameter is required.');
}

// Hidden flag for test/staging data (jgform/jgdetail check this)
foreach ($rows as $i => $r) {
    if (stripos($r['value'], '#TEST18#') !== false) {
        $test = 'Y';
        $rows[$i]['value'] = substr($r['value'], 0, stripos($r['value'], '#TEST18#'));
    }
}

// -----------------------------------------------------------------------------
// 4. BUILD SOLR QUERY
// -----------------------------------------------------------------------------

if ($isRecordMode) {
    // ---- SINGLE-RECORD MODE (Tier 3) ----
    // Query directly by dataid — bypasses all search-term processing.
    // The dataid value comes from the <tr id="..."> in the tabrow HTML.
    $query1 = 'dataid:' . $rec;
    $querySummary = ['Single record view'];
    $recstart = 0;
    $recbatch = 100;  // generous — a family group shouldn't exceed this

} else {
    // ---- LIST MODE (Tier 2) ----
    // Normal search-term-based query, mirrors jgdetail_2.php

$query1 = '';
$querySummary = [];
$activeRows   = [];

$dataTypeLabel = [
    'S' => 'Surname',
    'G' => 'Given Name',
    'T' => 'Town',
    'X' => 'Any Field',
];
$searchTypeLabel = [
    'Q'  => 'phonetically like',
    'D'  => 'sounds like',
    'E'  => 'is exactly',
    'S'  => 'starts with',
    'F1' => 'fuzzy',
    'F2' => 'fuzzier',
    'FM' => 'fuzziest',
];

foreach ($rows as $i => $r) {
    $val = $r['value'];
    $dt  = $r['dataType'];
    $st  = $r['searchType'];
    if ($val === '') { continue; }
    $activeRows[] = $r;

    // Field selection
    $fieldn = '';
    switch ($dt) {
        case 'S': $fieldn = 'record_surnames';   break;
        case 'G': $fieldn = 'record_givennames'; break;
        case 'T': $fieldn = 'record_towns';      break;
        case 'X': $fieldn = 'all_text';          break;
    }
    if ($fieldn === '') { continue; }

    // Match-type ? field variant
    $field    = $fieldn;
    $fuzz     = '';
    $contains1 = '';
    $contains2 = '';
    $fuzz2    = 0;
    $fieldb   = '';
    $fieldc   = '';

    if ($dt === 'X') {
        // All-fields text — single word wildcards
        if (strpos(trim($val), ' ') === false) {
            $contains1 = '*';
            $contains2 = '*';
        }
    } else {
        switch (substr($st, 0, 1)) {
            case 'Q': $field = $fieldn . '_bmpm'; break;
            case 'D': $field = $fieldn . '_dm';   break;
            case 'E': $field = $fieldn;           break;
            case 'S': $field = $fieldn;           break;
            case 'F':
                $field = $fieldn;
                $len1 = substr($st, 1, 1);
                $len  = $len1;
                if ($len1 === 'M' || $len1 > min(round(strlen($val) / 3), 4)) {
                    $len = min(round(strlen($val) / 3), 4);
                }
                $fuzz   = '~' . $len;
                $fuzz2  = 1;
                $fieldb = $fieldn . '_bmpm';
                $fieldc = $fieldn . '_dm';
                break;
        }
    }

    $srch = $val;
    if (strpos(trim($srch), ' ') !== false) {
        $srch = '(' . trim($srch) . ')';
    }

    // OR-on-comma support (Alex Kotovsky 26.11.21 — kept for parity)
    if (strpos($srch, ',') !== false) {
        $srch = '(' . str_replace(',', ' OR ', $srch) . ')';
    }

    // Strip brackets for now (legacy supports them; v2 if needed)
    if (strpos($srch, '[') !== false) {
        $srch = str_replace(']', '', str_replace('[', '', $srch));
    }

    if (strpos(trim($srch), ' ') !== false && $dt !== 'X') {
        $srch = str_replace(' ', ' AND ', trim($srch));
    } elseif ($dt === 'X' && strpos(trim($srch), ' ') !== false) {
        $srch = '"' . str_replace(['(', ')'], '', $srch) . '"';
    }

    if (!empty($query1)) {
        $query1 .= ' ' . $srchBool . ' ';
    }
    $query1 .= '(' . $field . ':' . $contains1 . $srch . $contains2;
    if ($st === 'S') { $query1 .= '*'; }
    $query1 .= $fuzz;

    if ($fuzz2 === 1) {
        $query1 .= ' AND NOT (' . $fieldb . ':' . $srch . ' OR ' . $fieldc . ':' . $srch . ')';
    }
    $query1 .= ')';

    // Human-readable summary
    $dtLabel = isset($dataTypeLabel[$dt]) ? $dataTypeLabel[$dt] : $dt;
    $stLabel = isset($searchTypeLabel[$st]) ? $searchTypeLabel[$st] : '';
    $querySummary[] = $dtLabel . ($stLabel ? ' (' . $stLabel . ')' : '') . ' : ' . strtoupper($val);
}

if ($srchBool === 'OR' && !empty($query1)) {
    $query1 = '(' . $query1 . ')';
}

// Always filter by dataset
if (empty($query1)) {
    $query1 = 'datafile:' . $df;
} else {
    $query1 .= ' AND datafile:' . $df;
}

// Region filter
if ($georegion !== '00ALL' && $georegion !== '0*' && $georegion !== '') {
    $query1 .= ' AND regionsdecoded:' . $georegion;
}

// Record type filter (B/M/D)
$rectypetext = '';
if (!empty($rectype1)) {
    $query1 .= ' AND type:' . $rectype1;
    $typeNames = ['B' => 'Birth Records', 'M' => 'Marriage Records', 'D' => 'Death Records'];
    if (isset($typeNames[$rectype1])) {
        $rectypetext = $typeNames[$rectype1];
    }
}

// Year filter
$yearfilttext = '';
if (!empty($yearfilt1) && !empty($yearfilt2)) {
    $query1 .= ' AND year:[' . $yearfilt1 . ' TO ' . $yearfilt2 . ']';
    $yearfilttext = 'Registered between ' . $yearfilt1 . ' and ' . $yearfilt2;
}

// Test vs production
if ($test === 'Y') {
    $query1 .= ''; // jgdetail_2.php behavior: don't add test:0 when in test mode
} else {
    $query1 .= ' AND test:0';
}

// Date filter (added/changed since)
if ($dates === 'some' && !empty($months) && !empty($years)) {
    $query1 .= ' AND filedate:[' . $years . '-' . $months . '-01T00:00:00Z TO NOW]';
}

// Radius filter
$within = '';
if (!empty($radius) && !empty($lat) && !empty($long)) {
    $radius2 = $radius / 0.621371192;  // miles ? km
    $lat2  = substr($lat, 0, 2)  + substr($lat, 2, 2) / 60;
    $long2 = substr($long, 0, 2) + substr($long, 2, 2) / 60;
    $query1 .= ' AND {!geofilt sfield=latlong pt=' . $lat2 . ',' . $long2 . ' d=' . $radius2 . '}';
    $within = 'within ' . $radius . ' miles';
}

// LitvakSIG filter
if ($litvak === 'Y') {
    $query1 .= ' AND (litvaksig:Y OR litvaksig:y)';
}

} // end of list-mode else branch

// -----------------------------------------------------------------------------
// 5. RUN SOLR QUERY
// -----------------------------------------------------------------------------

$options = [
    'hostname' => SOLR_SERVER_HOSTNAME,
    'login'    => SOLR_SERVER_USERNAME,
    'password' => SOLR_SERVER_PASSWORD,
    'port'     => SOLR_SERVER_PORT,
    'path'     => 'solr/JewishGen',
];

try {
    $client = new SolrClient($options);
    $query = new SolrQuery($query1);

    if ($isRecordMode) {
        // Request ALL fields in record mode — we need the expanded-view
        // fields (s2 columns, witnesses, book_info, etc.) that aren't
        // included in the summary tabrow.
        $query->addField('*');
    } else {
        $query->addField('tabrow');
        $query->addField('glue');
        $query->addField('solrhead');
        $query->addField('solrtitle');
        $query->addField('solrcolumns');
    }

    $query->setStart($recstart);
    $query->setRows($recbatch);
    $query->addSortField('glue', 0);

    $response_array = $client->query($query)->getResponse();
    $found = $response_array->response;
    $docs = isset($found['docs']) ? $found['docs'] : [];
    $rowcount = isset($found['numFound']) ? (int)$found['numFound'] : 0;
} catch (Throwable $e) {
    respondError(500, 'SOLR_QUERY_FAILED', $e->getMessage());
}

// In rec mode with debug, dump all available field names so we can
// identify which ones contain the expanded-view data.
$debugFields = [];
if ($isRecordMode && getParam('debug') === '1' && !empty($docs)) {
    $firstDoc = $docs[0];
    if (is_object($firstDoc)) {
        $debugFields = $firstDoc->getPropertyNames();
    } elseif (is_array($firstDoc)) {
        $debugFields = array_keys($firstDoc);
    }
}

// -----------------------------------------------------------------------------
// 6. PARSE — extract columns, dataset title, and structured row data
// -----------------------------------------------------------------------------

$columns = [];
$datasetTitle   = '';
$datasetInfoUrl = '';

if (!empty($docs)) {
    $firstDoc = $docs[0];

    // Columns from solrhead
    if (!empty($firstDoc['solrhead'])) {
        $columns = parseSolrHead($firstDoc['solrhead']);
    }

    // Dataset title + info_url from solrtitle.
    // Actual format: "BS100[JGBSBIRTH01]<a href='...'>Title</a>\0"
    //   - Sort prefix before [  (e.g. BS100, CA100, H_100)
    //   - df_id between [ and ]
    //   - Title HTML after ]
    //   - Trailing null byte
    if (!empty($firstDoc['solrtitle'])) {
        $st = rtrim($firstDoc['solrtitle'], "\0");
        // Extract everything after ] as the title HTML
        $bracketClose = strpos($st, ']');
        if ($bracketClose !== false) {
            $stClean = substr($st, $bracketClose + 1);
        } else {
            $stClean = $st;
        }
        // Pull href and link text
        if (preg_match('/<a[^>]*href=["\']?([^"\' >]+)/i', $stClean, $m)) {
            $datasetInfoUrl = $m[1];
        }
        if (preg_match('/<a[^>]*>(.*?)<\/a>/is', $stClean, $m)) {
            $datasetTitle = trim(strip_tags($m[1]));
        }
        // Fallback if no anchor — use plain text
        if ($datasetTitle === '') {
            $datasetTitle = trim(strip_tags($stClean));
        }
    }
}

// Group rows by glue id for family/multi-record visual grouping.
//
// CRITICAL: The FIRST Solr document per glue group contains ALL the
// <tr> rows for the entire household/family in its tabrow field.
// Subsequent documents with the same glue are redundant (they contain
// individual rows that are already embedded in the first doc's tabrow).
// This matches jgdetail_2.php's behavior: it only renders the first
// doc per glue group.
$groupsByGlue = [];

foreach ($docs as $i => $doc) {
    $glueId = isset($doc['glue']) ? $doc['glue'] : ('no-glue-' . $i);

    // Skip redundant documents — only process the first per glue group
    if (isset($groupsByGlue[$glueId])) {
        continue;
    }

    $groupsByGlue[$glueId] = [
        'glue_id' => $glueId,
        'rows'    => [],
    ];

    // Parse tabrow into multiple rows (one per <tr> in the HTML)
    $parsedRows = !empty($doc['tabrow']) ? parseTabRowMulti($doc['tabrow']) : [];

    foreach ($parsedRows as $cells) {
        // Apply test-mode link rewriting (mirrors jgdetail_2.php behavior)
        if ($test === 'Y') {
            foreach ($cells as &$cell) {
                $cell['html'] = str_ireplace('jowbr.php?rec=', 'jowbr.php?test=Y&rec=', $cell['html']);
                $cell['html'] = str_ireplace('/c1851.php?', '/c1851test.php?', $cell['html']);
            }
            unset($cell);
        }

        // Rewrite legacy link paths to absolute production URLs where needed
        foreach ($cells as &$cell) {
            if (stripos($cell['html'], '/wconnect/wc.dll') !== false) {
                $cell['html'] = str_ireplace('/wconnect/wc.dll', 'https://data.jewishgen.org/wconnect/wc.dll', $cell['html']);
            }
            if (stripos($cell['html'], '/imagedata/') !== false &&
                stripos($cell['html'], 'data.jewishgen.org/imagedata/') === false) {
                $cell['html'] = str_ireplace('/imagedata/', 'https://data.jewishgen.org/imagedata/', $cell['html']);
            }
            // NOTE: glue_s2.php link rewriting is handled client-side in
            // record-list.html's rewriteLegacyLinks() to avoid file-encoding
            // corruption of the '?' character during SFTP transfer.
        }
        unset($cell);

        $groupsByGlue[$glueId]['rows'][] = ['cells' => $cells];
    }
}

// -----------------------------------------------------------------------------
// 6b. EXPANDED VIEW (rec mode only) — query MySQL for tabrow2
// -----------------------------------------------------------------------------
// The Solr tabrow is the SUMMARY view (fewer columns). The EXPANDED view
// lives in MySQL table jgdata_glue.tabrow2 and has ALL fields (witnesses,
// book_info, archive details, etc.). This mirrors glue_s2.php's behavior.

if ($isRecordMode && !empty($rec)) {
    $dbName = ($test === 'Y') ? 'staging' : 'jewishgen';
    $mcon = @mysqli_connect('jewishgen13:3306', 'mtobias', 'nt732b#$', $dbName);

    if ($mcon) {
        // Get expanded row from jgdata_glue
        $safeRec = mysqli_real_escape_string($mcon, $rec);
        $sql = "SELECT tabrow2 FROM jgdata_glue WHERE dataid='" . $safeRec . "'";
        $mresult = mysqli_query($mcon, $sql);

        if ($mresult && $mrow = mysqli_fetch_array($mresult)) {
            $tabrow2 = $mrow['tabrow2'];

            if (!empty($tabrow2)) {
                // Parse expanded columns from <th> elements
                $expandedColumns = parseSolrHead($tabrow2);

                // Parse expanded rows from <tr>/<td> elements
                $expandedRows = parseTabRowMulti($tabrow2);

                if (!empty($expandedColumns) && !empty($expandedRows)) {
                    // Replace summary data with expanded data
                    $columns = $expandedColumns;
                    $groupsByGlue = [];
                    $groupsByGlue[$rec] = [
                        'glue_id' => $rec,
                        'rows'    => [],
                    ];
                    foreach ($expandedRows as $cells) {
                        $groupsByGlue[$rec]['rows'][] = ['cells' => $cells];
                    }
                    $rowcount = 1; // Single record
                }
            }
        }

        // Also get the dataset title from alldatalist_solr if we don't have one
        if (empty($datasetTitle)) {
            $filename = substr($rec, 0, strrpos($rec, '_'));
            $safeFilename = mysqli_real_escape_string($mcon, $filename);
            $sql2 = "SELECT solrtitle FROM alldatalist_solr WHERE filename='" . $safeFilename . "'";
            $mresult2 = mysqli_query($mcon, $sql2);
            if ($mresult2 && $mrow2 = mysqli_fetch_array($mresult2)) {
                $st = rtrim($mrow2['solrtitle'], "\0");
                $bracketClose = strpos($st, ']');
                if ($bracketClose !== false) {
                    $stClean = substr($st, $bracketClose + 1);
                } else {
                    $stClean = $st;
                }
                if (preg_match('/<a[^>]*href=["\x27]([^"\x27]+)/i', $stClean, $m)) {
                    $datasetInfoUrl = $m[1];
                }
                if (preg_match('/<a[^>]*>(.*?)<\/a>/is', $stClean, $m)) {
                    $datasetTitle = trim(strip_tags($m[1]));
                }
                if ($datasetTitle === '') {
                    $datasetTitle = trim(strip_tags($stClean));
                }
            }
        }

        mysqli_close($mcon);
    }
}

// -----------------------------------------------------------------------------
// 7. BUILD QUERY SUMMARY STRING
// -----------------------------------------------------------------------------

$summary = implode(' ' . $srchBool . ' ', $querySummary);
if (!empty($rectypetext))  { $summary .= ' — ' . $rectypetext; }
if (!empty($yearfilttext)) { $summary .= ' — ' . $yearfilttext; }
if (!empty($within))       { $summary .= ' — ' . $within; }

// -----------------------------------------------------------------------------
// 8. PAGINATION INFO
// -----------------------------------------------------------------------------

$pageSize    = $recbatch;
$totalPages  = max(1, (int)ceil($rowcount / $pageSize));
$currentPage = (int)floor($recstart / $pageSize) + 1;
$beyondEof   = $recstart > max(0, $rowcount - 1);

// -----------------------------------------------------------------------------
// 9. OUTPUT JSON
// -----------------------------------------------------------------------------

$output = [
    'dataset' => [
        'title'    => $datasetTitle,
        'info_url' => $datasetInfoUrl,
        'df_id'    => $df,
    ],
    'query' => [
        'summary'   => $summary,
        'srch_bool' => $srchBool,
        'ran_at'    => date('c'),
        'region'    => $georegion,
        'within'    => $within,
    ],
    'pagination' => [
        'total_count'  => $rowcount,
        'page_size'    => $pageSize,
        'current_page' => $currentPage,
        'total_pages'  => $totalPages,
        'start_record' => $rowcount === 0 ? 0 : $recstart + 1,
        'end_record'   => min($recstart + $pageSize, $rowcount),
        'has_prev'     => $recstart >= $pageSize,
        'has_next'     => ($recstart + $pageSize) < $rowcount,
        'beyond_eof'   => $beyondEof,
    ],
    'is_record_mode' => $isRecordMode,
    'columns' => $columns,
    'groups'  => array_values($groupsByGlue),
];

// In rec+debug mode, include all Solr field names so we can find the expanded-view fields
if (!empty($debugFields)) {
    $output['_debug'] = [
        'solr_field_names' => $debugFields,
        'solr_query' => $query1,
        'record_count' => count($docs),
    ];
    // Also include a sample of each field's value (truncated) to help identify expanded-view data
    if (!empty($docs)) {
        $sampleValues = [];
        $firstDoc = $docs[0];
        foreach ($debugFields as $fn) {
            $val = is_object($firstDoc) ? (isset($firstDoc->$fn) ? $firstDoc->$fn : null) : (isset($firstDoc[$fn]) ? $firstDoc[$fn] : null);
            if ($val !== null) {
                $sampleValues[$fn] = substr(is_string($val) ? $val : json_encode($val), 0, 200);
            }
        }
        $output['_debug']['sample_values'] = $sampleValues;
    }
}

echo json_encode($output, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

exit;

// =============================================================================
// HELPER FUNCTIONS
// =============================================================================

/**
 * Parse a solrhead HTML fragment into a structured columns array.
 *
 * Input: "<TR><TH>Town<hr>Uyezd<hr>Guberniya</TH><TH>Surname</TH>...</TR>"
 *   (or just "<TH>...</TH>..." without the wrapping TR)
 *
 * Output: [
 *   { lines: ["Town", "Uyezd", "Guberniya"] },
 *   { lines: ["Surname"] },
 *   ...
 * ]
 */
function parseSolrHead($solrhead) {
    $columns = [];
    if (!preg_match_all('/<th[^>]*>(.*?)<\/th>/is', $solrhead, $matches)) {
        return $columns;
    }
    foreach ($matches[1] as $thContent) {
        // Decode HTML entities first (so &nbsp; etc. become spaces)
        $thContent = html_entity_decode($thContent, ENT_QUOTES | ENT_HTML5);
        // Split on <hr> / <hr/> / <hr />
        $parts = preg_split('/<hr\s*\/?\s*>/i', $thContent);
        // Strip any remaining tags and trim
        $parts = array_map(function ($p) {
            return trim(strip_tags($p));
        }, $parts);
        // Drop empty trailing lines
        $parts = array_values(array_filter($parts, function ($p) { return $p !== ''; }));
        if (empty($parts)) { $parts = ['']; }
        $columns[] = ['lines' => $parts];
    }
    return $columns;
}

/**
 * Parse a tabrow HTML fragment into a structured array of ROWS,
 * each containing an array of cells.
 *
 * CRITICAL: One Solr document's tabrow field can contain MULTIPLE
 * <tr> elements (the entire family group). The legacy jgdetail_2.php
 * renders only the first doc per glue group, which already contains
 * all the rows for the household. We split by <tr> boundaries here
 * so each HTML row becomes a separate JSON row object.
 *
 * Input: "<tr><td rowspan='10' bgcolor='yellow'>Town</td><td>Name1</td>...</tr>
 *         <tr><td>Name2</td>...</tr>..."
 *
 * Output: [
 *   [ {html, rowspan?, highlighted?}, ... ],   // row 1 (11 cells)
 *   [ {html}, ... ],                            // row 2 (7 cells)
 *   ...
 * ]
 */
function parseTabRowMulti($tabrow) {
    $allRows = [];

    // Split by <tr> boundaries
    if (preg_match_all('/<tr[^>]*>(.*?)<\/tr>/is', $tabrow, $trMatches)) {
        foreach ($trMatches[1] as $trContent) {
            $cells = parseTabRowCells($trContent);
            if (!empty($cells)) {
                $allRows[] = $cells;
            }
        }
    }

    // Fallback: if no <tr> tags found, try parsing the whole thing as one row
    if (empty($allRows)) {
        $cells = parseTabRowCells($tabrow);
        if (!empty($cells)) {
            $allRows[] = $cells;
        }
    }

    return $allRows;
}

/**
 * Parse <td> elements from a single <tr>'s inner content.
 */
function parseTabRowCells($trContent) {
    $cells = [];
    if (!preg_match_all('/<td([^>]*)>(.*?)<\/td>/is', $trContent, $matches, PREG_SET_ORDER)) {
        return $cells;
    }
    foreach ($matches as $m) {
        $attrs   = $m[1];
        $content = $m[2];

        $cell = ['html' => trim($content)];

        if (preg_match('/rowspan\s*=\s*["\']?(\d+)/i', $attrs, $rs)) {
            $cell['rowspan'] = (int)$rs[1];
        }
        if (preg_match('/bgcolor\s*=\s*["\']?yellow/i', $attrs)) {
            $cell['highlighted'] = true;
        }
        if (preg_match('/colspan\s*=\s*["\']?(\d+)/i', $attrs, $cs)) {
            $cell['colspan'] = (int)$cs[1];
        }

        $cells[] = $cell;
    }
    return $cells;
}

/**
 * Respond with a JSON error and exit.
 */
function respondError($status, $code, $message) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'error'   => true,
        'code'    => $code,
        'message' => $message,
    ]);
    exit;
}