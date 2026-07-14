<?php
/**
 * searchjson.php  JSON-returning version of jgform.php
 * ======================================================
 *
 * Endpoint that takes the same POST/GET parameters as jgform.php and
 * returns the per-database hit counts as JSON instead of rendered HTML.
 *
 * Drop this file at: \databases\searchjson.php
 *
 * Mirrors the form contract from /databases/all/index.asp and
 * SearchForm_solr.txt:
 *   - srch1..srch4         (search text)
 *   - srch1v..srch4v       (S/G/T/X  Surname/GivenName/Town/AnyField)
 *   - srch1t..srch4t       (Q/D/S/E/F1/F2/FM  match type)
 *   - SrchBOOL             (AND or OR)
 *   - allcountry           (region alias, e.g. ALLPOLAND)
 *   - GeoRegion            (sub-region; preferred over allcountry)
 *   - dates                (all or some)
 *   - Months, Years        (when dates=some)
 *
 * v1 SCOPE  what this DOES NOT do (intentional, to be added later):
 *   - Does not query JGFF, FTJP, JRI Solr cores (jgform.php hits 5 cores total)
 *   - Does not call external partner APIs (Yad Vashem, IGRA, Shapell,
 *     Gesher Galicia)
 *   - Does not log to MySQL jg_log (logging parity can be added by
 *     including cureetc.php and calling write_mysql_jglog())
 *   - Does not require login (matches jgform.php's public-search behavior)
 *
 * @author  JG40 redesign project (CHW)
 * @version 1.0  initial draft
 */

// -----------------------------------------------------------------------------
// 1. HEADERS
// -----------------------------------------------------------------------------

// CORS  list of origins allowed to call this endpoint. Since the form
// will be on the same origin as this PHP file (both on dev.jewishgen.org,
// later both on www.jewishgen.org), CORS isn't strictly required, but
// listing them explicitly keeps things clean and ready for any subdomain
// or staging variant.
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

// Preflight short-circuit
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

// -----------------------------------------------------------------------------
// 2. DEPENDENCIES
// -----------------------------------------------------------------------------
//
// Mirror the include pattern used by jgform.php. Since this file lives at
// /databases/searchjson.php, PHP's working directory is /databases/ and these
// relative-path includes resolve to /databases/cureetc.php and
// /databases/bootstrap.php  the same files jgform.php uses.
//
//   - cureetc.php  defines CUREDB and the MySQL config used across searches
//   - bootstrap.php defines SOLR_SERVER_HOSTNAME, _USERNAME, _PASSWORD, _PORT
//                   (plus TEST_SOLR_SERVER_HOSTNAME)

include_once 'cureetc.php';
include 'bootstrap.php';

// -----------------------------------------------------------------------------
// 3. INPUT  read params from POST or GET (mirroring jgform.php)
// -----------------------------------------------------------------------------

$src = $_POST ?: $_GET;

function getParam($name, $default = '') {
    global $src;
    return isset($src[$name]) ? trim($src[$name]) : $default;
}

$rows = [];
for ($i = 1; $i <= 4; $i++) {
    $rows[$i] = [
        'value'      => getParam('srch' . $i),
        'dataType'   => getParam('srch' . $i . 'v'),   // S/G/T/X
        'searchType' => getParam('srch' . $i . 't'),   // Q/D/S/E/F1/F2/FM
    ];
}

$srchBool   = strtoupper(getParam('SrchBOOL', 'AND'));
if ($srchBool !== 'OR') { $srchBool = 'AND'; }

$allcountry = getParam('allcountry', '0*');
$geoRegion  = getParam('GeoRegion');
$dates      = getParam('dates', 'all');
$months     = getParam('Months');
$years      = getParam('Years');

// jgform.php logic: prefer GeoRegion if set, fall back to allcountry.
$region = strtolower($geoRegion !== '' ? $geoRegion : $allcountry);

// JOWBR country picker: the JOWBR search form submits a bare country slug as
// "geo" (e.g. "usa") alongside allcountry=01jowbr. The reg_data pattern for
// JOWBR country codes is 01jowbr_00<slug>, so append it when present.
$jowbrGeo = strtolower(getParam('geo'));
if ($jowbrGeo !== '' && strpos($region, '01jowbr') === 0 && strpos($region, '_00') === false) {
    $region = $region . '_00' . $jowbrGeo;
}

// -----------------------------------------------------------------------------
// 4. INPUT VALIDATION
// -----------------------------------------------------------------------------
// Replicates the contract of cureetc.php's valid():
//   - empty is OK (means "this row not used")
//   - non-empty must be =3 chars after stripping wildcards
//
// We don't enforce jgform.php's strict character whitelist here because
// Solr handles UTF-8 natively and the form already filters via maxlength=25.

function isValidTerm($term) {
    if ($term === '') { return null; }   // empty = unused row
    $stripped = preg_replace('/[*?%#\[\]\s]/u', '', $term);
    if (mb_strlen($stripped) < 3) { return false; }
    return true;
}

$activeRows = [];
foreach ($rows as $i => $r) {
    $valid = isValidTerm($r['value']);
    if ($valid === false) {
        respondError(400, 'VALIDATION_FAILED',
            'Row ' . $i . ': search term must be at least 3 allowed characters.');
    }
    if ($valid === true && $r['dataType'] !== '') {
        $activeRows[] = $r;
    }
}

if (count($activeRows) === 0) {
    respondError(400, 'NO_SEARCH_TERMS', 'At least one search row must contain a valid term.');
}

// -----------------------------------------------------------------------------
// 5. BUILD SOLR QUERY
// -----------------------------------------------------------------------------
// Field mapping (from jgform.php):
//   S = surname     ? record_surnames
//   G = given name  ? record_givennames
//   T = town        ? record_towns
//   X = any field   ? all_text
//
// Search-type suffix (appended to field, except for X):
//   Q (phonetic)    ? _bmpm
//   D (sounds like) ? _dm
//   E (exact)       ? no suffix
//   S (starts with) ? no suffix, append '*' to value
//   F1/F2/FM (fuzzy)? no suffix, append '~N' to value

function buildFieldClause($row) {
    $dataType   = $row['dataType'];
    $searchType = $row['searchType'];
    $term       = $row['value'];

    $fieldMap = [
        'S' => 'record_surnames',
        'G' => 'record_givennames',
        'T' => 'record_towns',
        'X' => 'all_text',
    ];
    if (!isset($fieldMap[$dataType])) { return null; }
    $field = $fieldMap[$dataType];

    // For "Any Field" (X), search is just contains-style on all_text.
    if ($dataType === 'X') {
        // Wrap single-word terms with wildcards for substring match.
        if (strpos($term, ' ') === false) {
            $term = '*' . $term . '*';
        } else {
            $term = '"' . $term . '"';
        }
        return $field . ':' . $term;
    }

    // Multi-word handling: jgform.php wraps in parens + AND.
    if (strpos($term, ' ') !== false) {
        $term = '(' . str_replace(' ', ' AND ', $term) . ')';
    }

    switch (substr($searchType, 0, 1)) {
        case 'Q': // phonetic  Beider-Morse
            return $field . '_bmpm:' . $term;

        case 'D': // D-M soundex
            return $field . '_dm:' . $term;

        case 'E': // exact
            return $field . ':' . $term;

        case 'S': // starts with
            return $field . ':' . $term . '*';

        case 'F': // fuzzy variants (F1, F2, FM)
            $len = substr($searchType, 1, 1);
            if ($len === 'M' || $len > 4) { $len = min(round(strlen($term) / 3), 4); }
            return $field . ':' . $term . '~' . $len;

        default:
            // No search type chosen  fall back to phonetic.
            return $field . '_bmpm:' . $term;
    }
}

$clauses = [];
foreach ($activeRows as $row) {
    $clause = buildFieldClause($row);
    if ($clause !== null) {
        $clauses[] = '(' . $clause . ')';
    }
}

$mainQuery = implode(' ' . $srchBool . ' ', $clauses);

// Region filter
if ($region !== '0*' && $region !== '00all' && $region !== 'all' && $region !== '') {
    // Match jgform.php behavior  strip the JOWBR cemetery prefix if present.
    $region = str_replace('01jowbr_99', '', $region);
    $mainQuery .= ' AND regionsdecoded:' . $region;
}

// Test-data filter (jgform.php's default is to exclude test records)
$mainQuery .= ' AND test:0';

// Date filter
if ($dates === 'some' && $months !== '' && $years !== '') {
    $months = str_pad((int)$months, 2, '0', STR_PAD_LEFT);
    $years  = (int)$years;
    if ($years > 1900 && $years < 2100) {
        $mainQuery .= ' AND filedate:[' . $years . '-' . $months . '-01T00:00:00Z TO NOW]';
    }
}

// -----------------------------------------------------------------------------
// 6. RUN SOLR QUERY
// -----------------------------------------------------------------------------

try {
    $solrOptions = [
        'hostname' => SOLR_SERVER_HOSTNAME,
        'login'    => SOLR_SERVER_USERNAME,
        'password' => SOLR_SERVER_PASSWORD,
        'port'     => SOLR_SERVER_PORT,
        'path'     => 'solr/JewishGen',
        'timeout'  => defined('SOLR_SERVER_TIMEOUT') ? SOLR_SERVER_TIMEOUT : 10,
    ];
    $client = new SolrClient($solrOptions);

    $query = new SolrQuery($mainQuery);
    $query->setRows(0);  // we only want facet counts, not records
    $query->setFacet(true);
    $query->addFacetField('solrtitle')->setFacetMinCount(1)->setFacetSort(0);
    $query->setFacetLimit(2000);

    $response = $client->query($query)->getResponse();

} catch (Exception $e) {
    respondError(502, 'SOLR_ERROR', 'Solr query failed: ' . $e->getMessage());
}

$totalMatches = isset($response->response->numFound) ? (int)$response->response->numFound : 0;

// Use offsetGet + getPropertyNames  matches the proven pattern from jgform.php.
// The SolrObject iterator via foreach doesn't reliably expose property names as keys.
$facets = null;
if (isset($response->facet_counts->facet_fields)) {
    $facetFields = $response->facet_counts->facet_fields;
    $facets = $facetFields->offsetGet('solrtitle');
}

// Debug mode: append raw facet keys to the response so we can see what Solr returns.
$debug = (getParam('debug', '') === '1');
$debugFacetSamples = [];

// -----------------------------------------------------------------------------
// 7. PARSE FACETS INTO STRUCTURED DATABASES
// -----------------------------------------------------------------------------
// Each facet key has the shape "[df_id]<a href=\"url\">Title</a>"
// We extract df_id, info_url, and a clean title.

function parseSolrTitle($raw) {
    $result = ['df_id' => '', 'info_url' => '', 'title' => $raw];

    // Strip trailing null byte that Solr appends
    $raw = rtrim($raw, "\0");

    // df_id lives between [ and ] but is NOT at position 0  there's a sort
    // prefix like "BS100" before it.  Use strpos (matching jgform.php's approach)
    // rather than a ^-anchored regex.
    $bracketOpen  = strpos($raw, '[');
    $bracketClose = strpos($raw, ']');
    if ($bracketOpen !== false && $bracketClose !== false && $bracketClose > $bracketOpen) {
        $result['df_id'] = substr($raw, $bracketOpen + 1, $bracketClose - $bracketOpen - 1);
        $rest = substr($raw, $bracketClose + 1);
    } else {
        $rest = $raw;
    }

    if (preg_match('/<a\s+href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/i', $rest, $m)) {
        $result['info_url'] = $m[1];
        $result['title']    = trim(strip_tags($m[2]));
    } else {
        $result['title'] = trim(strip_tags($rest));
    }

    return $result;
}

// -----------------------------------------------------------------------------
// 8. GROUP BY PRIMARY REGION / COLLECTION
// -----------------------------------------------------------------------------
// The folder token after /databases/ in a database's info URL IS its primary
// region (e.g. /databases/AustriaCzech/... -> AUSTRIACZECH). We map that token
// to the collection display names used across the JG40 site (landing tiles,
// Global Search optgroups, collection pages). Topical cross-collections
// (Holocaust, JOWBR, Given Names, Memorial, Yizkor Necrology, Arolsen) keep
// their own groups. Legacy folder aliases are mapped to their current
// collection. Unrecognized folders fall to OTHER.
// JSON response key stays 'research_divisions' so search-results.php needs no
// change; the grouping axis is now region.

function deriveRegionGroup($infoUrl, $title) {
    $tok = '';
    $pos = strpos(strtolower($infoUrl), '/databases/');
    if ($pos !== false) {
        $tail = substr($infoUrl, $pos + 11);
        $tok  = strtoupper(strtok(strtok($tail, '/'), '"'));
    } elseif ($infoUrl !== '') {
        $parts = explode('/', $infoUrl);
        if (count($parts) > 1) {
            $tok = strtoupper($parts[count($parts) - 2]);
        }
    }

    $map = array(
        // ---- Regional collections (display names match the landing tiles) --
        'ALGERIA'       => 'Algeria',
        'ARGENTINA'     => 'Argentina',
        'AUSTRIACZECH'  => 'Austria / Czechia',
        'BELARUS'       => 'Belarus',
        'BULGARIA'      => 'Bulgaria',
        'CANADA'        => 'Canada',
        'EGYPT'         => 'Egypt',
        'FRANCE'        => 'France',
        'GERMANY'       => 'Germany',
        'GREECE'        => 'Greece',
        'HUNGARY'       => 'Hungary / Slovakia',
        'INDIA'         => 'India',
        'IRAQ'          => 'Iraq',
        'IRELAND'       => 'Ireland',
        'ISRAEL'        => 'Israel',
        'ITALY'         => 'Italy',
        'LATINAMERICA'  => 'Latin America',
        'LATVIA'        => 'Latvia',
        'LEBANON'       => 'Lebanon',
        'LIBYA'         => 'Libya',
        'LITHUANIA'     => 'Lithuania',
        'MOROCCO'       => 'Morocco',
        'NETHERLANDS'   => 'Netherlands',
        'HOLLAND'       => 'Netherlands',
        'POLAND'        => 'Poland',
        'PORTUGAL'      => 'Portugal',
        'ROMANIA'       => 'Romania / Moldova',
        'RUSSIA'        => 'Russia',
        'SCANDINAVIA'   => 'Scandinavia',
        'SEPHARDIC'     => 'Sephardic',
        'SAFRICA'       => 'South Africa',
        'SPAIN'         => 'Spain',
        'SYRIA'         => 'Syria',
        'TUNISIA'       => 'Tunisia',
        'TURKEY'        => 'Turkey',
        'UK'            => 'United Kingdom',
        'UKRAINE'       => 'Ukraine',
        'USA'           => 'United States',
        'VENEZUELA'     => 'Venezuela',
        'YUGOSLAVIA'    => 'Former Yugoslavia',

        // ---- Legacy folder aliases -> current collection -------------------
        'LITVAK'                    => 'Lithuania',
        'LISTS'                     => 'Belarus',
        'ARCHIVES-AND-REPOSITORIES' => 'Russia',
        'VSIA'                      => 'Russia',
        'BIALYGEN'                  => 'Russia',
        'MISC'                      => 'Russia',
        'BESSARABIA'                => 'Romania / Moldova',
        'JCR-UK'                    => 'United Kingdom',

        // ---- Topical / cross-collection databases ---------------------------
        'HOLOCAUST'  => 'JewishGen Holocaust Database',
        'GIVENNAMES' => 'JewishGen Given Names Database',
        'CEMETERY'   => 'JewishGen Online Worldwide Burial Registry',
        'MEMORIAL'   => 'JewishGen Memorials & Plaques Database',
        'YIZKOR'     => 'The JewishGen Yizkor Book Necrology Database',
        'AROLSEN'    => 'Arolsen Archives Database',
    );
    if (isset($map[$tok])) { return $map[$tok]; }
    return $tok !== '' ? 'OTHER' : 'OTHER';
}

$grouped = [];

if ($facets !== null) {
    // Use getPropertyNames() to iterate  mirrors jgform.php's proven approach.
    // This ensures we get the full facet key (including [df_id] prefix) reliably.
    $facetKeys = $facets->getPropertyNames();
    foreach ($facetKeys as $raw) {
        $count = $facets[$raw];
        if ($count <= 0) { continue; }

        // Capture a few raw keys for debug output
        if ($debug && count($debugFacetSamples) < 5) {
            $debugFacetSamples[] = substr($raw, 0, 200);
        }

        $parsed = parseSolrTitle($raw);

        // JOWBR facets are one-per-country: df_id "J_<CODE>", title
        // "JewishGen Online Worldwide Burial Registry - <Country>". Group
        // those by country (matching the legacy site) instead of letting them
        // collapse into a single JOWBR bucket. The country comes from the
        // title suffix (already human-readable); df_id J_ prefix is the flag.
        if (strpos($parsed['df_id'], 'J_') === 0) {
            $dashPos = strrpos($parsed['title'], ' - ');
            $country = $dashPos !== false ? trim(substr($parsed['title'], $dashPos + 3)) : '';
            $rd = $country !== ''
                ? 'Burial Registry (JOWBR): ' . $country
                : 'JewishGen Online Worldwide Burial Registry';
        } else {
            $rd = deriveRegionGroup($parsed['info_url'], $parsed['title']);
        }
        if (!isset($grouped[$rd])) { $grouped[$rd] = []; }
        $grouped[$rd][] = [
            'df_id'    => $parsed['df_id'],
            'title'    => $parsed['title'],
            'info_url' => $parsed['info_url'],
            'count'    => (int)$count,
        ];
    }
}

// Sort each RD's databases by title, and RDs themselves alphabetically.
foreach ($grouped as $rd => $dbs) {
    usort($grouped[$rd], function($a, $b) {
        return strcmp(strtolower($a['title']), strtolower($b['title']));
    });
}
ksort($grouped);

// Convert assoc array to indexed list for JSON.
$researchDivisions = [];
foreach ($grouped as $name => $dbs) {
    $researchDivisions[] = [
        'name'      => $name,
        'databases' => $dbs,
    ];
}

// -----------------------------------------------------------------------------
// 9. BUILD HUMAN-READABLE SEARCH SUMMARY
// -----------------------------------------------------------------------------

$summaryParts = [];
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
foreach ($activeRows as $row) {
    $dt = isset($dataTypeLabel[$row['dataType']]) ? $dataTypeLabel[$row['dataType']] : $row['dataType'];
    $st = isset($searchTypeLabel[$row['searchType']]) ? $searchTypeLabel[$row['searchType']] : '';
    $summaryParts[] = $dt . ($st ? ' (' . $st . ')' : '') . ' : ' . strtoupper($row['value']);
}
$summary = implode(' ' . $srchBool . ' ', $summaryParts);

// -----------------------------------------------------------------------------
// 10. OUTPUT
// -----------------------------------------------------------------------------

$output = [
    'search_summary' => [
        'description'   => $summary,
        'ran_at'        => date('c'),
        'total_matches' => $totalMatches,
        'region'        => $region,
        'srch_bool'     => $srchBool,
    ],
    'research_divisions' => $researchDivisions,
];

// Include raw facet samples when debug=1 so we can inspect the Solr format
if ($debug) {
    $output['_debug'] = [
        'raw_facet_samples' => $debugFacetSamples,
        'facet_count'       => $facets !== null ? count($facets->getPropertyNames()) : 0,
        'solr_query'        => isset($mainQuery) ? $mainQuery : '(not captured)',
    ];
}

echo json_encode($output, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

// -----------------------------------------------------------------------------
// UTILITIES
// -----------------------------------------------------------------------------

function respondError($httpCode, $code, $message) {
    http_response_code($httpCode);
    echo json_encode([
        'error'   => $message,
        'code'    => $code,
    ]);
    exit;
}