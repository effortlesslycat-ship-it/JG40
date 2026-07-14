<?php
/**
 * memjson.php - Memorial Plaques individual record JSON API
 *
 * JOWBR's jowbrjson.php reads record HTML from MySQL (jgdata_jowbrmem /
 * bg_jowbr), but Memorial Plaques rows exist ONLY in Solr. This endpoint
 * therefore queries the Solr JewishGen core by dataid (same client,
 * credentials, and core as jgdetail_2.php) and parses the stored tabrow.
 *
 * Output shape mirrors jowbrjson.php so the record frontend ports
 * mechanically:
 *   {
 *     "record": {
 *       "dataid":       "M_AUSTRIA_0003097",
 *       "fields":       [ { "label", "value", "html" }, ... ],
 *       "photo_url":    "https://data.jewishgen.org/imagedata/..." or "",
 *       "synagogue_id": "AUS-00813" or "",
 *       "tabrow_html":  "<tr ...>...</tr>"   (raw row, for debugging)
 *     },
 *     "synagogue":     { ...same shape as synjson.php... } or null,
 *     "dataset_title": "JewishGen Memorial Plaque Database - Austria"
 *   }
 *
 * Usage:
 *   /databases/memjson.php?rec=M_AUSTRIA_0003097
 *
 * CHW + JG40
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

include 'bootstrap.php';
include_once 'cureetc.php';

function respondError($code, $type, $message)
{
    http_response_code($code);
    echo json_encode(array('error' => array('type' => $type, 'message' => $message)));
    exit;
}

/* Plain-text version of a cell: strip nbsp, <br>, tags, collapse spaces */
function plainText($s)
{
    $s = str_ireplace('&nbsp;', ' ', $s);
    $s = preg_replace('/<br\s*\/?' . '>/i', ' ', $s);
    $s = strip_tags($s);
    $s = preg_replace('/\s+/', ' ', $s);
    return trim($s);
}

// -----------------------------------------------------------------------------
// 1. VALIDATE INPUT
// -----------------------------------------------------------------------------

$rec = '';
if (isset($_GET['rec']) && $_GET['rec'] !== '') {
    $rec = trim($_GET['rec']);
}

if ($rec === '') {
    respondError(400, 'MISSING_PARAM', 'Missing rec parameter.');
}

if (strlen($rec) > 40 || !preg_match('/^[A-Za-z0-9_\-]+$/', $rec)) {
    respondError(400, 'INVALID_PARAM', 'Invalid rec parameter.');
}

$rec = strtoupper($rec);

// -----------------------------------------------------------------------------
// 2. QUERY SOLR FOR THE RECORD ROW
//    Same client options as jgdetail_2.php (constants from bootstrap/cureetc)
// -----------------------------------------------------------------------------

$options = array(
    'hostname' => SOLR_SERVER_HOSTNAME,
    'login'    => SOLR_SERVER_USERNAME,
    'password' => SOLR_SERVER_PASSWORD,
    'port'     => SOLR_SERVER_PORT,
    'path'     => 'solr/JewishGen',
);

$tabrow = '';
$solrhead = '';
$solrtitle = '';

try {
    $client = new SolrClient($options);

    $query = new SolrQuery('dataid:' . $rec . ' AND test:0');
    $query->addField('tabrow');
    $query->addField('solrhead');
    $query->addField('solrtitle');
    $query->setRows(1);

    $updateResponse = $client->query($query);
    $response_array = $updateResponse->getResponse();

    $found = $response_array->response;
    if (!isset($found['numFound']) || $found['numFound'] < 1) {
        respondError(404, 'RECORD_NOT_FOUND', 'No memorial plaque record found for this ID.');
    }

    $doc = $found['docs'][0];
    $tabrow    = isset($doc['tabrow']) ? $doc['tabrow'] : '';
    $solrhead  = isset($doc['solrhead']) ? $doc['solrhead'] : '';
    $solrtitle = isset($doc['solrtitle']) ? $doc['solrtitle'] : '';
} catch (Exception $e) {
    respondError(500, 'SOLR_ERROR', 'Search index query failed.');
}

if ($tabrow === '') {
    respondError(404, 'RECORD_NOT_FOUND', 'No memorial plaque record found for this ID.');
}

// -----------------------------------------------------------------------------
// 3. PARSE THE ROW
// -----------------------------------------------------------------------------

// Column labels from the dataset's stored header (<th> cells)
$labels = array();
if (preg_match_all('/<th[^>]*>(.*?)<\/th>/is', $solrhead, $m)) {
    foreach ($m[1] as $th) {
        $labels[] = plainText($th);
    }
}

// Cell contents from the data row (<td> cells)
$fields = array();
if (preg_match_all('/<td[^>]*>(.*?)<\/td>/is', $tabrow, $m)) {
    $i = 0;
    foreach ($m[1] as $td) {
        $fields[] = array(
            'label' => isset($labels[$i]) ? $labels[$i] : '',
            'value' => plainText($td),
            'html'  => trim($td),
        );
        ++$i;
    }
}

if (count($fields) === 0) {
    respondError(500, 'PARSE_ERROR', 'Record row could not be parsed.');
}

// Synagogue/Society ID from the memorialshow.php link baked into the row
$synId = '';
if (preg_match('/memorialshow\.php\?id=([A-Za-z0-9\-]+)/i', $tabrow, $m)) {
    $synId = $m[1];
}

// Photo URL: first imagedata link or image in the row
$photoUrl = '';
$photoFile = '';
if (preg_match('/(?:href|src)=["\']([^"\']*imagedata[^"\']*)["\']/i', $tabrow, $m)) {
    $photoUrl = $m[1];
    if (stripos($photoUrl, 'http') !== 0) {
        $photoUrl = 'https://data.jewishgen.org' . $photoUrl;
    }
    // Permanent filename (last path segment). Unlike the record number,
    // this stays constant across photo replacements and semi-annual
    // updates - Nolan uses it to resolve broken links, so it must be
    // visible on the record page.
    $photoPath = parse_url($photoUrl, PHP_URL_PATH);
    if ($photoPath) {
        $photoFile = basename($photoPath);
    }
}

// Dataset title: text after the ] prefix, tags stripped (same as jgdetail_2)
$datasetTitle = '';
if ($solrtitle !== '') {
    $t = $solrtitle;
    $bracket = strpos($t, ']');
    if ($bracket !== false) {
        $t = substr($t, $bracket + 1);
    }
    $datasetTitle = plainText($t);
}

// -----------------------------------------------------------------------------
// 4. SYNAGOGUE/SOCIETY LOOKUP (MySQL memorial table, matches synjson.php)
// -----------------------------------------------------------------------------

$synagogue = null;

if ($synId !== '') {
    $con = @mysqli_connect('jewishgen13:3306', 'mtobias', 'nt732b#$', 'jewishgen');
    if ($con) {
        $stmt = mysqli_prepare($con, 'SELECT * FROM memorial WHERE cemeteryid = ?');
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $synId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                if ($row) {
                    $stateMap = array(
                        'AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas','CA'=>'California',
                        'CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware','DC'=>'District of Columbia',
                        'FL'=>'Florida','GA'=>'Georgia','HI'=>'Hawaii','ID'=>'Idaho','IL'=>'Illinois',
                        'IN'=>'Indiana','IA'=>'Iowa','KS'=>'Kansas','KY'=>'Kentucky','LA'=>'Louisiana',
                        'ME'=>'Maine','MD'=>'Maryland','MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota',
                        'MS'=>'Mississippi','MO'=>'Missouri','MT'=>'Montana','NE'=>'Nebraska','NV'=>'Nevada',
                        'NH'=>'New Hampshire','NJ'=>'New Jersey','NM'=>'New Mexico','NY'=>'New York',
                        'NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio','OK'=>'Oklahoma','OR'=>'Oregon',
                        'PA'=>'Pennsylvania','PR'=>'Puerto Rico','RI'=>'Rhode Island','SC'=>'South Carolina',
                        'SD'=>'South Dakota','TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah','VT'=>'Vermont',
                        'VA'=>'Virginia','WA'=>'Washington','WI'=>'Wisconsin','WV'=>'West Virginia','WY'=>'Wyoming',
                        'AB'=>'Alberta','BC'=>'British Columbia','MB'=>'Manitoba','NB'=>'New Brunswick',
                        'NL'=>'Newfoundland and Labrador','NS'=>'Nova Scotia','NT'=>'Northwest Territories',
                        'NU'=>'Nunavut','ON'=>'Ontario','PE'=>'Prince Edward Island','QC'=>'Quebec',
                        'SK'=>'Saskatchewan','YT'=>'Yukon'
                    );

                    $stateCode = isset($row['state']) ? trim($row['state']) : '';
                    $stateName = isset($stateMap[strtoupper($stateCode)]) ? $stateMap[strtoupper($stateCode)] : $stateCode;

                    $dateLive = isset($row['date_live']) ? $row['date_live'] : '';
                    $dateLive = str_replace('0000-00-00', '', $dateLive);
                    $dateLive = str_replace('1899-12-30', '', $dateLive);
                    $dateLive = str_replace(' 00:00:00', '', $dateLive);
                    $lastUpdated = (trim($dateLive) !== '') ? date('j F Y', strtotime($dateLive)) : '';

                    $synagogue = array(
                        'id'           => isset($row['cemeteryid']) ? $row['cemeteryid'] : '',
                        'name'         => isset($row['cem_name']) ? $row['cem_name'] : '',
                        'section'      => isset($row['landsman']) ? $row['landsman'] : '',
                        'country'      => isset($row['country']) ? $row['country'] : '',
                        'state_code'   => $stateCode,
                        'state'        => $stateName,
                        'city'         => isset($row['city']) ? $row['city'] : '',
                        'street'       => isset($row['street']) ? $row['street'] : '',
                        'region'       => isset($row['region']) ? $row['region'] : '',
                        'usbgn_code'   => isset($row['usbgn_code']) ? $row['usbgn_code'] : '',
                        'burials'      => isset($row['burials']) ? (int)$row['burials'] : 0,
                        'photos'       => isset($row['photos']) ? (int)$row['photos'] : 0,
                        'comments'     => isset($row['comments']) ? $row['comments'] : '',
                        'last_updated' => $lastUpdated,
                        'land_city'    => isset($row['land_city']) ? $row['land_city'] : '',
                        'land_ctry'    => isset($row['land_ctry']) ? $row['land_ctry'] : '',
                        'land_usbgn'   => isset($row['land_usbgn']) ? $row['land_usbgn'] : ''
                    );
                }
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_close($con);
    }
}

// -----------------------------------------------------------------------------
// 5. OUTPUT
// -----------------------------------------------------------------------------

$output = array(
    'record' => array(
        'dataid'       => $rec,
        'fields'       => $fields,
        'photo_url'    => $photoUrl,
        'photo_file'   => $photoFile,
        'synagogue_id' => $synId,
        'tabrow_html'  => $tabrow
    ),
    'synagogue'     => $synagogue,
    'dataset_title' => $datasetTitle
);

echo json_encode($output, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
