<?php
/**
 * jowbrjson.php - JOWBR Burial Record JSON API
 *
 * Returns structured JSON for a single JOWBR burial record, including:
 *   - Parsed burial fields (name, dates, family, plot, comments, photo)
 *   - Cemetery details (name, location, ID, burial/photo counts)
 *   - Dataset title
 *
 * Usage:
 *   /databases/jowbrjson.php?rec=DATAID_VALUE
 *
 * The rec value is the Solr dataid (e.g., from a jowbr.php link in search results).
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

include_once 'cureetc.php';
include 'bootstrap.php';

// ---------------------------------------------------------------------------
// 1. READ PARAMETERS
// ---------------------------------------------------------------------------

function getParam($name, $default = '') {
    if (isset($_GET[$name]) && $_GET[$name] !== '') return $_GET[$name];
    if (isset($_POST[$name]) && $_POST[$name] !== '') return $_POST[$name];
    return $default;
}

function respondError($code, $type, $message) {
    http_response_code($code);
    echo json_encode(['error' => ['type' => $type, 'message' => $message]]);
    exit;
}

$rec = getParam('rec');
if (empty($rec)) {
    respondError(400, 'MISSING_PARAM', 'Required parameter "rec" not provided.');
}

// Sanitize — rec should be UUID_number format
if (!preg_match('/^[a-zA-Z0-9_-]+$/', $rec)) {
    respondError(400, 'INVALID_PARAM', 'Invalid rec format.');
}

$test = getParam('test');
$dbName = ($test === 'Y') ? 'staging' : 'jewishgen';

// ---------------------------------------------------------------------------
// 2. QUERY BURIAL RECORD FROM MySQL
// ---------------------------------------------------------------------------
// Try bg_jowbr first (newer table, used by jowbr_2.php).
// Fall back to jgdata_jowbrmem (older table, used by jowbr.php).

$con = @mysqli_connect('jewishgen13:3306', 'mtobias', 'nt732b#$', $dbName);
if (!$con) {
    respondError(500, 'DB_CONNECT_FAILED', 'Could not connect to database.');
}

$tabrow2 = null;
$cemeteryid = null;

// Try jgdata_jowbrmem first (matches legacy jowbr.php's table)
$stmt = mysqli_prepare($con, 'SELECT tabrow2, cemeteryid FROM jgdata_jowbrmem WHERE dataid = ?');
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 's', $rec);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $tabrow2 = $row['tabrow2'];
        $cemeteryid = $row['cemeteryid'];
    }
    mysqli_stmt_close($stmt);
}

// Fallback to bg_jowbr (newer table, used by jowbr_2.php)
if (empty($tabrow2)) {
    $stmt2 = mysqli_prepare($con, 'SELECT tabrow2, cemeteryid FROM bg_jowbr WHERE dataid = ?');
    if ($stmt2) {
        mysqli_stmt_bind_param($stmt2, 's', $rec);
        mysqli_stmt_execute($stmt2);
        $result2 = mysqli_stmt_get_result($stmt2);
        if ($result2 && $row2 = mysqli_fetch_assoc($result2)) {
            $tabrow2 = $row2['tabrow2'];
            $cemeteryid = $row2['cemeteryid'];
        }
        mysqli_stmt_close($stmt2);
    }
}

if (empty($tabrow2)) {
    mysqli_close($con);
    respondError(404, 'RECORD_NOT_FOUND', 'No JOWBR burial record found for this ID.');
}

// ---------------------------------------------------------------------------
// 3. PARSE tabrow2 INTO STRUCTURED FIELDS
// ---------------------------------------------------------------------------
// tabrow2 is HTML containing <th> headers and <td> values.
// We pair them up as field label -> value.

$fields = [];

// Extract all <th> content (field labels)
$headers = [];
if (preg_match_all('/<th[^>]*>(.*?)<\/th>/is', $tabrow2, $thMatches)) {
    foreach ($thMatches[1] as $th) {
        $headers[] = trim(strip_tags($th));
    }
}

// Extract all <td> content (field values) from data rows only
// Skip header rows by finding <td> elements
$values = [];
if (preg_match_all('/<td[^>]*>(.*?)<\/td>/is', $tabrow2, $tdMatches)) {
    foreach ($tdMatches[1] as $td) {
        $values[] = trim($td);
    }
}

// Pair headers with values
$fieldCount = min(count($headers), count($values));
for ($i = 0; $i < $fieldCount; $i++) {
    $label = $headers[$i];
    $value = $values[$i];
    // Clean up value — strip tags for plain text, preserve links
    $plainValue = trim(strip_tags($value, '<a><img><br><hr>'));
    $fields[] = [
        'label' => $label,
        'value' => $plainValue,
        'html'  => $value,
    ];
}

// ---------------------------------------------------------------------------
// 4. QUERY CEMETERY DETAILS
// ---------------------------------------------------------------------------

$cemetery = null;
if (!empty($cemeteryid)) {
    $cemStmt = mysqli_prepare($con, 'SELECT * FROM cemetery WHERE cemeteryid = ?');
    if ($cemStmt) {
        mysqli_stmt_bind_param($cemStmt, 's', $cemeteryid);
        mysqli_stmt_execute($cemStmt);
        $cemResult = mysqli_stmt_get_result($cemStmt);
        if ($cemResult && $cemRow = mysqli_fetch_assoc($cemResult)) {
            $cemetery = [
                'id'          => $cemRow['cemeteryid'],
                'name'        => $cemRow['cem_name'],
                'section'     => $cemRow['landsman'],
                'country'     => $cemRow['country'],
                'state'       => $cemRow['state'],
                'city'        => $cemRow['city'],
                'street'      => $cemRow['street'],
                'region'      => $cemRow['region'],
                'usbgn_code'  => $cemRow['usbgn_code'],
                'burials'     => $cemRow['burials'],
                'photos'      => $cemRow['photos'],
                'comments'    => $cemRow['comments'],
                'date_live'   => $cemRow['date_live'],
                'land_city'   => $cemRow['land_city'],
                'land_ctry'   => $cemRow['land_ctry'],
                'land_usbgn'  => $cemRow['land_usbgn'],
            ];
        }
        mysqli_stmt_close($cemStmt);
    }
}

// ---------------------------------------------------------------------------
// 5. GET DATASET TITLE
// ---------------------------------------------------------------------------

$datasetTitle = 'JOWBR Burial Record';
$filenamePos = strrpos($rec, '_');
if ($filenamePos !== false) {
    $filename = substr($rec, 0, $filenamePos);
    $safeFilename = mysqli_real_escape_string($con, $filename);
    $titleResult = mysqli_query($con, "SELECT solrtitle FROM alldatalist_solr WHERE filename = '" . $safeFilename . "'");
    if ($titleResult && $titleRow = mysqli_fetch_assoc($titleResult)) {
        $st = $titleRow['solrtitle'];
        // Strip sort prefix and brackets, extract clean title
        $st = rtrim($st, "\0");
        $bracketClose = strpos($st, ']');
        $stClean = ($bracketClose !== false) ? substr($st, $bracketClose + 1) : $st;
        if (preg_match('/<a[^>]*>(.*?)<\/a>/is', $stClean, $m)) {
            $datasetTitle = trim(strip_tags($m[1]));
        } else {
            $datasetTitle = trim(strip_tags($stClean));
        }
    }
}

mysqli_close($con);

// ---------------------------------------------------------------------------
// 6. BUILD PHOTO URL
// ---------------------------------------------------------------------------
// JOWBR photos are stored at data.jewishgen.org/imagedata/jowbr/CEMID/
// The photo filename is typically in the tabrow2 data as an <img> or link.

$photoUrl = null;
$photoAlt = null;
if (preg_match('/<img[^>]*src=["\x27]([^"\x27]+)["\x27]/i', $tabrow2, $imgMatch)) {
    $photoUrl = $imgMatch[1];
    // Ensure absolute URL
    if (strpos($photoUrl, 'http') !== 0) {
        $photoUrl = 'https://data.jewishgen.org' . $photoUrl;
    }
    // Extract alt text
    if (preg_match('/alt=["\x27]([^"\x27]*)["\x27]/i', $tabrow2, $altMatch)) {
        $photoAlt = $altMatch[1];
    }
}

// ---------------------------------------------------------------------------
// 7. OUTPUT JSON
// ---------------------------------------------------------------------------

$output = [
    'record' => [
        'dataid'    => $rec,
        'fields'    => $fields,
        'photo_url' => $photoUrl,
        'photo_alt' => $photoAlt,
    ],
    'cemetery'      => $cemetery,
    'dataset_title' => $datasetTitle,
];

echo json_encode($output, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);