<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

include_once 'cureetc.php';
include 'bootstrap.php';

$id = '';
if (isset($_GET['id']) && $_GET['id'] !== '') {
    $id = trim($_GET['id']);
} elseif (isset($_GET['ID']) && $_GET['ID'] !== '') {
    $id = trim($_GET['ID']);
}

if ($id === '') {
    http_response_code(400);
    echo json_encode(array('error' => 'Missing id parameter'));
    exit;
}

if (strlen($id) > 20) {
    http_response_code(400);
    echo json_encode(array('error' => 'Invalid id'));
    exit;
}

$con = @mysqli_connect('jewishgen13:3306', 'mtobias', 'nt732b#$', 'jewishgen');
if (!$con) {
    http_response_code(500);
    echo json_encode(array('error' => 'Database connection failed'));
    exit;
}

$stmt = mysqli_prepare($con, 'SELECT * FROM cemetery WHERE cemeteryid = ?');
if (!$stmt) {
    mysqli_close($con);
    http_response_code(500);
    echo json_encode(array('error' => 'Query preparation failed'));
    exit;
}

mysqli_stmt_bind_param($stmt, 's', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    mysqli_stmt_close($stmt);
    mysqli_close($con);
    http_response_code(500);
    echo json_encode(array('error' => 'Query execution failed'));
    exit;
}

$row = mysqli_fetch_assoc($result);
if (!$row) {
    mysqli_stmt_close($stmt);
    mysqli_close($con);
    http_response_code(404);
    echo json_encode(array('error' => 'Cemetery not found'));
    exit;
}

mysqli_stmt_close($stmt);
mysqli_close($con);

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

$output = array(
    'cemetery' => array(
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
    )
);

echo json_encode($output, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);