<?php
// jgffform.php -- CHW JG40 redesign
// Search results handler. All backend logic preserved unchanged.
// Only HTML output sections replaced with new design.

$thisIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
$dt = date('Y-m-d H:i:s');
$postParms = implode(";", $_POST);
$cure = $_COOKIE['jgcure'] ?? '';
$msgText = 'At start, ' . $dt . ' [' . $thisIp . '] Cure: ' . $cure . ' Post: ' . $postParms;
register_shutdown_function('shutdown', $msgText);

include_once '../databases/cureetc.php';
include '../databases/bootstrap.php';
include '../databases/msbootstrap.php';
include '_bootstrap.php';
include '_jgff_messages.php';

$time_start = microtime(true);

$debug = false;
$logok = 0;
$inst = 0;
$userdata = ['unchanged'];
$jgid = require_userinfo($userdata);

$profile_incomplete = false;
if (array_key_exists('fullname', $userdata)) {
    if (is_null($userdata['fullname']) || strlen($userdata['fullname']) < 2) {
        $profile_incomplete = true;
    }
} elseif (array_key_exists('surname', $userdata)) {
    if (is_null($userdata['surname']) || strlen($userdata['surname']) < 2) {
        $profile_incomplete = true;
    }
}

$profile_full_check = check_userdata_isok($userdata);

$isDI = false;
if (array_key_exists('statres', $userdata)) {
    if ('DI' === substr($userdata['statres'], 0, 2)) {
        $isDI = true;
    }
}

$GLOBALS['saved'] = '';
$GLOBALS['logid'] = '';

$query1 = '';
$surname = '';
$town = '';
$surname1 = '';
$town1 = '';
$stype = '';
$ttype = '';
$country = '';
$dates = '';
$months = '';
$years = '';
$feature = '';
$townid = '';

$getvars = 0;
$displaysave = 1;
if (isset($_GET['saved']) or $inst === 0) {
    $displaysave = 0;
}
if (!empty($_GET)) {
    $getvars = 1;
    $townid   = $_GET['townid'] ?? '';
    $surname  = $_GET['surname'] ?? '';
    $town     = $_GET['town'] ?? '';
    if (array_key_exists('Town', $_GET)) { $town = $_GET['Town']; }
    $stype  = $_GET['stype'] ?? '';
    $ttype  = $_GET['ttype'] ?? '';
    if (array_key_exists('Coun1', $_GET))   { $country = $_GET['Coun1']; }
    if (array_key_exists('country', $_GET)) { $country = $_GET['country']; }
    $usesynonyms = $_GET['synonym'] ?? '';
    $dates   = $_GET['dates'] ?? '';
    $months  = $_GET['Months'] ?? '';
    $years   = $_GET['Years'] ?? '';
    $feature = $_GET['feature'] ?? '';
    $IAJGS   = strtoupper($_GET['IAJGS'] ?? '');
} else {
    $surname = $_POST['surname'] ?? '';
    $town    = $_POST['town'];
    if (array_key_exists('Town', $_POST)) { $town = $_POST['Town']; }
    $stype  = $_POST['stype'] ?? '';
    $ttype  = $_POST['ttype'] ?? '';
    if (array_key_exists('Coun1', $_POST))   { $country = $_POST['Coun1']; }
    if (array_key_exists('country', $_POST)) { $country = $_POST['country']; }
    $usesynonyms = $_POST['synonym'] ?? '';
    $dates   = $_POST['dates'] ?? '';
    $months  = $_POST['Months'] ?? '';
    $years   = $_POST['Years'] ?? '';
    $feature = $_POST['feature'] ?? '';
    $IAJGS   = isset($_POST['IAJGS']) ? strtoupper($_POST['IAJGS']) : '';
}

$stat999 = 0;
if (1 == 0) {
    $surname = 'Stats';
    $stat999 = 1;
}

if (strlen(trim($townid)) > 0) {
    $con = mysqli_connect(MYSQL_SERVER_HOSTNAME, MYSQL_SERVER_USERNAME, MYSQL_SERVER_PASSWORD, MYSQL_SERVER_DATABASE);
    $sql = 'SELECT town,country, id FROM jewishgen.jgfftowns where id=' . $townid;
    $result = $con->query($sql);
    $row = mysqli_fetch_row($result);
    $town = $row[0];
    $country = $row[1];
    mysqli_close($con);
}
if (strlen(trim($feature)) > 0) {
    $con = mysqli_connect(MYSQL_SERVER_HOSTNAME, MYSQL_SERVER_USERNAME, MYSQL_SERVER_PASSWORD, MYSQL_SERVER_DATABASE);
    $sql = 'SELECT town,country, id FROM jewishgen.jgfftowns where usbgn=' . $feature;
    $result = $con->query($sql);
    $row = mysqli_fetch_row($result);
    $town = $row[0];
    $country = $row[1];
    mysqli_close($con);
}

if (strlen(trim($country)) === 0) { $country = 'Any'; }
if (strlen(trim($dates)) === 0)   { $dates = 'ALL'; }
if (strlen(trim($ttype)) === 0)   { $ttype = 'exact'; }

$dataok = 0;
if (strlen(trim($townid)) > 0) { $dataok = 1; }
if (strlen(trim($feature)) > 0) { $dataok = 1; }

if ($dataok === 0) {
    $townok = 0;
    $surnameok = 0;
    if (preg_match('/^(?=.*\p{L}.*\p{L}.*\p{L})[\p{L}\-\,\[\]\(\) ]+$/u', $town) or ($ttype === 'exact' and preg_match('/^(?=.*\p{L}.*\p{L})[\p{L}\-\,\[\]\(\) ]+$/u', $town))) {
        $townok = 1;
    } elseif (strlen(trim($town)) > 0) {
        $townok = 999;
    }
    if (preg_match('/^(?=.*\p{L}.*\p{L}.*\p{L})[\p{L}\-,\[\]\(\) ]+$/u', $surname) or ($stype === 'exact' and preg_match('/^(?=.*\p{L}.*\p{L})[\p{L}\-,\[\]\(\) ]+$/u', $surname))) {
        $surnameok = 1;
    } elseif (strlen(trim($surname)) > 0) {
        $surnameok = 999;
    }
    if (($townok + $surnameok === 0) or ($townok + $surnameok > 2)) {
        // Output validation error in new shell
        ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Search Error &ndash; JewishGen Family Finder</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="/jg-global.css">
<style>
.page-title-band { background-color: #09497a; padding: 44px 50px; text-align: center; }
body.dark-mode .page-title-band { background-color: #0d2a45; }
.page-title-band h1 { margin: 0; font-family: Georgia, 'Times New Roman', serif; font-size: 2.25rem; font-weight: normal; color: #ffffff; }
.jgff-ecru-wrap { background-color: var(--ecru); padding: 3rem 2rem; }
.jgff-ecru-inner { max-width: 900px; margin: 0 auto; }
.jgff-error-card { background-color: var(--white); border: 1px solid #d1caba; border-left: 4px solid #c0392b; border-radius: 8px; padding: 24px 28px; }
body.dark-mode .jgff-error-card { background-color: #1e1e1e; border-color: #333; }
.jgff-error-card p { font-size: 0.9375rem; color: var(--charcoal); margin: 0 0 16px 0; line-height: 1.6; }
.jgff-error-card p:last-child { margin-bottom: 0; }
.jgff-back-btn { display: inline-block; background-color: #09497a; color: #ffffff; border-radius: 5px; padding: 8px 20px; font-size: 0.875rem; font-weight: bold; text-decoration: none; }
.jgff-back-btn:hover { filter: brightness(1.1); color: #ffffff; }
</style>
</head>
<body>
<div id="site-header"></div>
<div class="page-title-band" role="banner"><h1>JewishGen Family Finder</h1></div>
<div class="jgff-ecru-wrap">
    <div class="jgff-ecru-inner">
        <div class="jgff-error-card">
            <p>You can only enter Latin characters and no digits. Please try again with at least 3 characters (2 for exact spelling).</p>
            <a class="jgff-back-btn" href="/JGFF/jgff-search.html">&larr; Back to Search</a>
        </div>
    </div>
</div>
<div id="site-footer"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/jg/scripts/jg-jgff.js"></script>
<script>
function loadComponent(id, file) {
    return fetch(file).then(function(r){ if(!r.ok) throw new Error('Cannot load '+file); return r.text(); }).then(function(h){ document.getElementById(id).innerHTML=h; }).catch(function(e){ console.warn(e); });
}
Promise.all([
    loadComponent('site-header', '/Header_NavBar.html'),
    loadComponent('site-footer', '/Footer.html'),
    loadComponent('jgff-subnav', '/JGFF/jgff-subnav.html')
]).then(function() { initJgffSubnav(); });
</script>
</body>
</html>
        <?php
        return;
        exit;
    }
}

$con = mysqli_connect(MYSQL_SERVER_HOSTNAME, MYSQL_SERVER_USERNAME, MYSQL_SERVER_PASSWORD, MYSQL_SERVER_DATABASE);
$sql = 'SELECT country,name FROM jewishgen.country2';
$countrylist = '<select name="Coun1" ID="jgffs_C" size="1">';
$countrylist .= '<option value="Any" selected>Any Country';
$result = $con->query($sql);
while ($row = mysqli_fetch_row($result)) {
    $countrylist .= '<option value="' . trim($row[0]) . '">' . trim($row[1]);
}
mysqli_close($con);
$countrylist .= '</select>';

if ($country !== 'Any') {
    if (strpos($country, '.') !== false) {
        if (substr($country, strlen($country) - 1, 1) === '.') {
            $country = substr($country, 0, strlen($country) - 1);
        }
        $countrycount = substr_count($country, '.');
        $tmp = substr($country, 0, strpos($country, '.'));
        $countryname = ctryname($tmp, $countrylist);
        $tmpcountry = trim(substr($country, strpos($country, '.'), 100));
        $querycountry = 'country:' . $tmp;
        for ($i = 0; $i < $countrycount; ++$i) {
            $tmpcountry = trim(substr($tmpcountry, 1, 100));
            if (strpos($tmpcountry, '.') !== false) {
                $tmp = substr($tmpcountry, 0, strpos($tmpcountry, '.'));
                $countryname .= ' or ' . ctryname($tmp, $countrylist);
                $querycountry .= ' OR country:' . $tmp;
                $tmpcountry = trim(substr($tmpcountry, strpos($tmpcountry, '.'), 100));
            } else {
                $countryname .= ' or ' . ctryname($tmpcountry, $countrylist);
                $querycountry .= ' OR country:' . $tmpcountry;
            }
        }
        $querycountry = ' AND (' . $querycountry . ')';
    } else {
        $querycountry = ' AND country:' . $country;
        $countryname = ctryname($country, $countrylist);
    }
}

$synonymtext = '';
if ($usesynonyms === 'on' and !empty($town)) {
    $townsynonym = '';
    $countrysynonym = '';
    $con = mysqli_connect(MYSQL_SERVER_HOSTNAME, MYSQL_SERVER_USERNAME, MYSQL_SERVER_PASSWORD, MYSQL_SERVER_DATABASE);
    if ($country !== 'Any') {
        $synonym_sql = "SELECT distinct townsynonym,countrysynonym FROM jgffsynonyms_view where (town='" . $town . "' AND country='" . $country . "')";
    } else {
        $synonym_sql = "SELECT distinct townsynonym,countrysynonym FROM jgffsynonyms_view where town='" . $town . "'";
    }
    $synonym = mysqli_query($con, $synonym_sql);
    $syncount = 0;
    $synonymtext = '<br>(';
    $syncountries = '';
    while ($synonym_row = mysqli_fetch_row($synonym)) {
        $syncount = $syncount + 1;
        $syncountries .= ctryname($synonym_row[1], $countrylist) . ' ';
        if ($syncount === 1) {
            $townsynonym = $synonym_row[0];
            $countrysynonym = $synonym_row[1];
            if ($country === 'Any') {
                $synonymtext .= ucfirst(strtolower($town)) . ', Any Country';
            } else {
                $synonymtext .= ucfirst(strtolower($town)) . ', ' . $countryname;
            }
            $synonymtext .= ' &rArr; ' . $synonym_row[0] . ', ' . ctryname($synonym_row[1], $countrylist);
            $countrysynonymname = ctryname($countrysynonym, $countrylist);
        }
    }
    $synonymtext .= ')<br>';
    mysqli_close($con);
    if ($syncount === 0) { $synonymtext = ''; }
    if ($syncount === 1) {
        $town = $townsynonym;
        $country = $countrysynonym;
        $countryname = $countrysynonymname;
        $querycountry = ' AND country:' . $country;
    } elseif ($syncount > 0) {
        $synonymtext = '<br>(There is more than 1 possible town/country synonym. Please try again entering one of the countries ' . str_ireplace(' ', ' or ', trim($syncountries)) . ')<br>';
    }
}

$sfuzz = ''; $sfuzz2 = 0;
$tfuzz = ''; $tfuzz2 = 0;
$sstarts = ''; $scontains1 = ''; $scontains2 = '';
$tstarts = ''; $tcontains1 = ''; $tcontains2 = '';

switch ($stype) {
    case 'bmpm':    $sfield = 'surname_bmpm'; $stext = ' (phonetically like): '; break;
    case 'dm':      $sfield = 'surname_dm';   $stext = ' (DM soundex): '; break;
    case 'exact':   $sfield = 'surname';      $stext = ' (exact): '; break;
    case 'starts':  $sfield = 'surname';      $stext = ' (starts with): '; $sstarts = '*'; break;
    case 'contains': $sfield = 'surname';     $stext = ' (contains): '; $scontains1 = '*'; $scontains2 = '*'; break;
    case 'fuzzy':   $sfield = 'surname'; $slen = '1'; $sfuzz = '~'.$slen; $sfuzz2 = 1; $stext = ' (fuzzy): '; $sfield2 = 'surname_bmpm'; $sfield3 = 'surname_dm'; break;
    case 'fuzzier': $sfield = 'surname'; $slen = min(2,min(round(strlen($_POST['surname'])/3),4)); $sfuzz = '~'.$slen; $sfuzz2 = 1; $stext = ' (fuzzier): '; $sfield2 = 'surname_bmpm'; $sfield3 = 'surname_dm'; break;
    case 'fuzziest': $sfield = 'surname'; $slen = min(round(strlen($_POST['surname'])/3),4); $sfuzz = '~'.$slen; $sfuzz2 = 1; $stext = ' (fuzziest): '; $sfield2 = 'surname_bmpm'; $sfield3 = 'surname_dm'; break;
    default:        $sfield = 'surname_bmpm'; $stext = ' (phonetically like): ';
}

switch ($ttype) {
    case 'bmpm':    $tfield = 'town_bmpm'; $ttext = ' (phonetically like): '; break;
    case 'dm':      $tfield = 'town_dm';   $ttext = ' (DM soundex): '; break;
    case 'exact':   $tfield = 'town';      $ttext = ' (exact): '; break;
    case 'starts':  $tfield = 'town';      $ttext = ' (starts with): '; $tstarts = '*'; break;
    case 'contains': $tfield = 'town';     $ttext = ' (contains): '; $tcontains1 = '*'; $tcontains2 = '*'; break;
    case 'fuzzy':   $tfield = 'town'; $tlen = '1'; $tfuzz = '~'.$tlen; $tfuzz2 = 1; $ttext = ' (fuzzy): '; $tfield2 = 'town_bmpm'; $tfield3 = 'town_dm'; break;
    case 'fuzzier': $tfield = 'town'; $tlen = min(2,min(round(strlen($_POST['town'])/3),4)); $tfuzz = '~'.$tlen; $tfuzz2 = 1; $ttext = ' (fuzzier): '; $tfield2 = 'town_bmpm'; $tfield3 = 'town_dm'; break;
    case 'fuzziest': $tfield = 'town'; $tlen = min(round(strlen($_POST['town'])/3),4); $tfuzz = '~'.$tlen; $tfuzz2 = 1; $ttext = ' (fuzziest): '; $tfield2 = 'town_bmpm'; $tfield3 = 'town_dm'; break;
    default:        $tfield = 'town_bmpm'; $ttext = ' (phonetically like): ';
}

if (empty($surname)) {
    $surname = '';
} else {
    $surnameorig = $surname;
    $surname1 = $surname;
    if (strpos($surname, '[') !== false) {
        $surnamebrackets = $surname; $surnamebrackets2 = '*'; $sbrack = 0;
        while (strpos($surnamebrackets, '[') !== false) {
            $sbrack++;
            $surnamebracketstmp = substr($surnamebrackets, strpos($surnamebrackets,'[')+1);
            $surnamebracketstmp = substr($surnamebracketstmp, 0, strpos($surnamebracketstmp,']'));
            if (strpos($surnamebrackets,'[')===0 and $sbrack===1) { $surnamebrackets2 = $surnamebracketstmp.'*'; }
            else { $surnamebrackets2 .= $surnamebracketstmp.'*'; }
            $surnamebrackets = substr($surnamebrackets, strpos($surnamebrackets,']')+1);
        }
        $surname = str_replace(']','',str_replace('[','',$surname));
        $query1 .= $sfield.':'.$surname.$sfuzz;
        if ($sfield !== 'surname') { $query1 .= ' AND surname:'.$surnamebrackets2; }
        else { if ($sfuzz2 !== 1) { $surnameorig = str_replace(']','',str_replace('[','',$surnameorig)); } }
    } else {
        $query1 .= $sfield.':'.$scontains1.$surname.$scontains2.$sstarts.$sfuzz;
    }
    if ($sfuzz2 === 1) { $query1 .= ' AND NOT ('.$sfield2.':'.$surname.' OR '.$sfield3.':'.$surname.')'; }
}

if (empty($town) or (strtolower(trim($town)) == 'any')) {
    $town = ''; $townorig = '';
} else {
    $townorig = $town;
    $town1 = $town;
    $town = str_replace('-',' ',$town);
    $town = str_replace(' ','',$town);
    if (stripos($town,',(') > 0) { $town = substr($town,0,stripos($town,',')); }
    $query1b = '';
    if (strpos($town,'[') !== false) {
        $townbrackets = $town; $townbrackets2 = '*'; $tbrack = 0;
        while (strpos($townbrackets,'[') !== false) {
            $tbrack++;
            $townbracketstmp = substr($townbrackets,strpos($townbrackets,'[')+1);
            $townbracketstmp = substr($townbracketstmp,0,strpos($townbracketstmp,']'));
            if (strpos($townbrackets,'[')===0 and $tbrack===1) { $townbrackets2 = $townbracketstmp.'*'; }
            else { $townbrackets2 .= $townbracketstmp.'*'; }
            $townbrackets = substr($townbrackets,strpos($townbrackets,']')+1);
        }
        $town = str_replace(']','',str_replace('[','',$town));
        if ($tfield !== 'town') { $query1b = ' AND town:'.$townbrackets2; }
        else { if ($tfuzz2 !== 1) { $townorig = str_replace(']','',str_replace('[','',$townorig)); } }
    }
    if (empty($surname)) { $query1 .= $tfield.':'.$tcontains1.$town.$tcontains2.$tstarts.$tfuzz.$query1b; }
    else { $query1 .= ' AND ('.$tfield.':'.$tcontains1.$town.$tcontains2.$tstarts.$tfuzz.$query1b.')'; }
    if ($tfuzz2 === 1) { $query1 .= ' AND NOT ('.$tfield2.':'.$town.' OR '.$tfield3.':'.$town.')'; }
}

if ($country !== 'Any' && strlen($country) > 0) { $query1 .= $querycountry; }

$datetext = '';
if ($dates === 'some') {
    if (!is_null($years) && strlen($years) > 1 && !is_null($months) && strlen($months) > 1) {
        $datefrom = '[' . $years . '-' . $months . '-01T00:00:00Z TO NOW]';
        $query1 .= ' AND recdate:' . $datefrom;
        $datetext = 'Only showing matches in data last updated since ' . date('F', mktime(0,0,0,$months,10)) . ' ' . $years;
    }
}

if ($jgid === '5200') { echo 'query1=' . $query1 . '<br>'; }
if (!empty($feature)) { $query1 = "usbgn:'" . $feature . "'"; }

$filterlist = '';
$filters = 0;
if ($IAJGS === 'Y') {
    $con = mysqli_connect(MYSQL_SERVER_HOSTNAME, MYSQL_SERVER_USERNAME, MYSQL_SERVER_PASSWORD, MYSQL_SERVER_DATABASE);
    $sql = 'SELECT jgid FROM jewishgen.iajgs';
    $result = $con->query($sql);
    $filterlist = '!,';
    while ($row = mysqli_fetch_row($result)) { $filters++; $filterlist .= "'".$row[0]."',"; }
    mysqli_close($con);
}

if (strlen(trim($townid)) > 0) { $query1 = 'townid:' . $townid; }
if ($stat999 === 1) { $query1 = '*:*'; }

$client = new SolrClient($options_JGFF);
$query  = new SolrQuery($query1);
if ($stat999 === 1) { $query->setRows(0); } else { $query->setRows(100000); }
$query->setFacet(true);
$query->addFacetField('code');
$query->setFacetMinCount(1);
$query->setFacetLimit(-1);

$updateResponse  = $client->query($query);
$response_array  = $updateResponse->getResponse();
$found_array     = $response_array->response;
$datarows        = $found_array->offsetGet('docs');

if (is_array($datarows)) { usort($datarows, 'cmp'); $datacount = count($datarows); } else { $datacount = 0; }
$totalhits = $found_array['numFound'];
if ($stat999 === 1) { exit('Total Records = ' . $totalhits); }

$codesummary = $response_array['facet_counts']['facet_fields'];
$coderows    = $codesummary->offsetGet('code');

$codelist = ''; $codes = 0;
for ($i = 0; $i < $datacount; ++$i) {
    if (stristr($codelist, "'".$datarows[$i]['code']."',") === false) {
        $codelist .= "'".$datarows[$i]['code']."',";
        ++$codes;
    }
}
$codelist = substr($codelist, 0, strlen($codelist) - 1);

$concomm = mysqli_connect(MYSQL_SERVER_HOSTNAME, MYSQL_SERVER_USERNAME, MYSQL_SERVER_PASSWORD, MYSQL_SERVER_DATABASE);

// Build search description for page title and heading
$GLOBALS['saved'] = 'JewishGen Family Finder -- Searching for ';
$htmlhead = '';
if (!empty($surname)) { $htmlhead .= 'Surname' . $stext . strtoupper($surnameorig); }
if (!empty($town)) {
    if (!empty($htmlhead)) { $htmlhead .= ' and '; }
    if (empty($feature)) { $htmlhead .= 'Town' . $ttext . ($getvars === 0 ? strtoupper($town1) : $town1); }
    else { $htmlhead .= 'Town ' . ($getvars === 0 ? strtoupper($town1) : $town1); }
}
if ($country !== 'Any') { $htmlhead .= ' in ' . $countryname; }
$GLOBALS['saved'] .= $htmlhead;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Family Finder Results &ndash; JewishGen</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="/jg-global.css">
<style>
/* == JGFF Results page styles -- CHW ======================= */
.page-title-band {
    background-color: #09497a;
    padding: 36px 50px;
    text-align: center;
}
body.dark-mode .page-title-band { background-color: #0d2a45; }
.page-title-band h1 {
    margin: 0 0 6px 0;
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 2rem;
    font-weight: normal;
    color: #ffffff;
}
.page-title-band p.hero-subtitle {
    margin: 0 auto;
    font-size: 0.9375rem;
    color: rgba(255,255,255,0.85);
    max-width: 700px;
    line-height: 1.5;
}
.jgff-ecru-wrap { background-color: var(--ecru); padding: 2.5rem 2rem; flex: 1; }
.jgff-ecru-inner { max-width: 1100px; margin: 0 auto; }
.jgff-results-card {
    background-color: var(--white);
    border: 1px solid #d1caba;
    border-radius: 8px;
    padding: 22px 24px;
}
body.dark-mode .jgff-results-card { background-color: #1e1e1e; border-color: #333; }

/* -- Hit count banner -------------------------------------- */
.jgff-hit-count {
    background-color: #09497a;
    color: #ffffff;
    border-radius: 6px;
    padding: 12px 18px;
    margin-bottom: 18px;
    font-size: 0.9375rem;
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}
body.dark-mode .jgff-hit-count { background-color: #0d2a45; }
.jgff-hit-count strong { font-size: 1.25rem; }
.jgff-hit-date-note {
    font-size: 0.8125rem;
    color: rgba(255,255,255,0.8);
    margin-left: auto;
}

/* -- Status messages (profile incomplete, DI, etc.) -------- */
.jgff-status-msg {
    background-color: var(--cream);
    border-left: 4px solid var(--sage);
    border-radius: 0 6px 6px 0;
    padding: 14px 18px;
    margin-bottom: 18px;
    font-size: 0.9375rem;
    color: var(--charcoal);
    line-height: 1.6;
}
body.dark-mode .jgff-status-msg { background-color: #1a1a1a; color: #a0a0a0; }
.jgff-status-msg a { color: var(--navy); font-weight: bold; }

/* -- IAJGS banner ------------------------------------------ */
.jgff-iajgs-banner {
    text-align: center;
    margin-bottom: 16px;
}
.jgff-iajgs-banner img { max-width: 200px; }
.jgff-iajgs-banner p { font-weight: bold; font-size: 0.875rem; margin-bottom: 6px; }

/* -- Results table ----------------------------------------- */
.jgff-table-wrap { overflow-x: auto; }
.jgff-results-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
.jgff-results-table thead tr { background-color: #09497a; color: #ffffff; }
body.dark-mode .jgff-results-table thead tr { background-color: #0d2a45; }
.jgff-results-table th {
    padding: 10px 12px;
    text-align: left;
    font-weight: bold;
    font-size: 0.8125rem;
    letter-spacing: 0.3px;
    white-space: nowrap;
}
.jgff-results-table td {
    padding: 9px 12px;
    border-bottom: 1px solid #e8e1d1;
    color: var(--charcoal);
    vertical-align: top;
}
body.dark-mode .jgff-results-table td { border-bottom-color: #2a2a2a; color: #a0a0a0; }
.jgff-results-table tbody tr:last-child td { border-bottom: none; }
.jgff-results-table tbody tr:hover td { background-color: rgba(147,155,81,0.05); }
.jgff-results-table tbody tr.jgff-group-start td {border-top: 2px solid #09497a;}
.jgff-results-table td.jgff-researcher-cell {border-left: 3px solid var(--sage);}

/* -- Town link --------------------------------------------- */
.jgff-town-link { color: var(--navy); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
.jgff-town-link:hover { color: var(--sage); text-decoration: underline; }
body.dark-mode .jgff-town-link { color: #a8b361; }
.jgff-town-icon { width: 16px; height: 16px; vertical-align: middle; }

/* -- Researcher cell --------------------------------------- */
.jgff-researcher-cell { font-size: 0.8125rem; line-height: 1.6; color: var(--charcoal); }
body.dark-mode .jgff-researcher-cell { color: #a0a0a0; }
.jgff-researcher-cell a { color: var(--navy); font-weight: bold; }
.jgff-researcher-cell a:hover { color: var(--sage); }
.jgff-last-login { font-size: 0.75rem; color: #888; margin-top: 4px; }

/* -- Footer actions ---------------------------------------- */
.jgff-results-footer {
    margin-top: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    border-top: 1px solid #d1caba;
    padding-top: 16px;
}
body.dark-mode .jgff-results-footer { border-top-color: #333; }
.jgff-another-search {
    display: inline-block;
    background-color: #09497a;
    color: #ffffff;
    border-radius: 5px;
    padding: 8px 20px;
    font-size: 0.875rem;
    font-weight: bold;
    text-decoration: none;
}
.jgff-another-search:hover { filter: brightness(1.1); color: #ffffff; }
body.dark-mode .jgff-another-search { background-color: #0d2a45; }
.jgff-save-search { font-size: 0.875rem; color: var(--navy); font-weight: bold; }
.jgff-save-search:hover { color: var(--sage); }
body.dark-mode .jgff-save-search { color: #a8b361; }
.jgff-run-time { font-size: 0.75rem; color: #aaa; margin-left: auto; }

/* -- No results -------------------------------------------- */
.jgff-no-results {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--charcoal);
    font-size: 0.9375rem;
}
body.dark-mode .jgff-no-results { color: #a0a0a0; }
.jgff-no-results a { color: var(--navy); font-weight: bold; }

/* -- Sidebar ----------------------------------------------- */
.jgff-grid {
    display: grid;
    grid-template-columns: minmax(0, 7fr) minmax(0, 3fr);
    gap: 28px;
    align-items: start;
}
.jgff-sidebar { position: sticky; top: 20px; display: flex; flex-direction: column; gap: 20px; }
@media (max-width: 768px) {
    .jgff-grid { grid-template-columns: 1fr; }
    .jgff-sidebar { position: static; }
    .page-title-band { padding: 28px 20px; }
    .page-title-band h1 { font-size: 1.6rem; }
    .jgff-ecru-wrap { padding: 1.5rem 1rem; }
}

.jgff-contact-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 0.75rem;
    font-weight: bold;
    color: #09497a;
    text-decoration: none;
    border: 1px solid #09497a;
    border-radius: 4px;
    padding: 4px 10px;
    margin-top: 8px;
    transition: background-color 0.15s, color 0.15s;
}
.jgff-contact-btn:hover { background-color: #09497a; color: #ffffff; }
.jgff-contact-btn i { font-size: 13px; }

</style>
</head>
<body>

<div id="site-header"></div>

<div class="page-title-band" role="banner">
    <span class="tagline">JewishGen Family Finder</span>

<h1>JGFF</h1>
    <?php if (!empty($htmlhead)) { ?>
    <p class="hero-subtitle">Searching for <?php echo htmlspecialchars($htmlhead); ?><?php if (!empty($synonymtext)) { echo ' ' . $synonymtext; } ?></p>
    <?php } ?>
</div>

<div class="jgff-ecru-wrap">
    <div class="jgff-ecru-inner">
        <div class="jgff-grid">

            <div>
                <div class="jgff-results-card">

                <?php if ($IAJGS === 'Y') { ?>
                <div class="jgff-iajgs-banner">
                    <p>Filtered by IAJGS Conference Attendees</p>
                    <img src="https://www.iajgs.org/wp21/wp-content/uploads/2024/03/IAJGS-conference-logo.png" alt="IAJGS Conference">
                </div>
                <?php } ?>

                <?php if ($isDI) { ?>
                <div class="jgff-status-msg">
                    <p><?php echo JGFF_MESSAGE_VALIDATION; ?></p>
                    <p><?php echo JGFF_MESSAGE_DATA_INCOMPLETE; ?></p>
                    <p><?php echo $userdata['usermsg']; ?></p>
                    <p><?php echo JGFF_MESSAGE_UPDATE_PROFILE; ?></p>
                    <p><?php echo JGFF_MESSAGE_CONTACT_SUPPORT; ?></p>
                </div>
                <?php } elseif (!$userdata['validated']) { ?>
                <div class="jgff-status-msg">
                    <p><?php echo JGFF_MESSAGE_VALIDATION; ?></p>
                    <p><?php echo JGFF_MESSAGE_CANNOT_EMAIL; ?></p>
                    <p><?php echo JGFF_MESSAGE_CONTACT_SUPPORT; ?></p>
                </div>
                <?php } elseif ($profile_incomplete) { ?>
                <div class="jgff-status-msg">
                    <p><?php echo JGFF_MESSAGE_CANNOT_EMAIL; ?></p>
                    <p><?php echo JGFF_MESSAGE_UPDATE_PROFILE; ?></p>
                    <p><?php echo JGFF_MESSAGE_CONTACT_SUPPORT; ?></p>
                </div>
                <?php } ?>

                <?php if ($profile_incomplete || $isDI || !$userdata['validated']) { ?>
                    <p style="color:var(--charcoal);font-size:0.875rem;">Results cannot be shown. Please resolve the issue above and try again.</p>
                <?php } elseif ($totalhits === 0) { ?>
                    <div class="jgff-no-results">
                        <p>No matches found for your search.</p>
                        <p style="margin-top:12px;"><a href="/JGFF/jgff-search.html">&larr; Try another search</a></p>
                    </div>
                <?php } else {
                    // Build results table
                    $filterres  = 0;
                    $filterrecs = 0;
                    $table_html = '';

                    $gconn = sqlsrv_connect($MSserverName, $optionsGoldmine);
                    if ($gconn) {
                        $gquery = "SELECT dbo.CONTACT1.CONTACT AS name,
                            ISNULL(dbo.CONTACT1.ADDRESS1,'') AS add1, ISNULL(dbo.CONTACT1.ADDRESS2,'') AS add2,
                            ISNULL(dbo.CONTACT1.ADDRESS3,'') AS add3,
                            ISNULL(dbo.CONTACT1.CITY,'') AS city, ISNULL(dbo.CONTACT1.STATE,'') AS state,
                            ISNULL(dbo.CONTACT1.ZIP,'') AS zip, ISNULL(dbo.CONTACT1.COUNTRY,'') AS country,
                            dbo.CONTACT1.KEY3 AS code,
                            ISNULL(dbo.CONTACT2.UEMAIL_PRI,'') AS email, ISNULL(dbo.CONTACT2.UJGFFNOTES,'') AS notes,
                            ISNULL(dbo.CONTACT2.UJGFFDISPL,'') AS flag, ISNULL(dbo.CONTACT2.USHARE,'') AS gmshare,
                            ISNULL(dbo.CONTACT2.USTATRES,'') AS resstatus,
                            ISNULL(dbo.v_iphistory.LASTDATE,'') AS lastlogin,
                            ISNULL(dbo.CONTACT2.ULASTNAME,'') AS ulastname
                            FROM dbo.CONTACT1
                            INNER JOIN dbo.CONTACT2 ON dbo.CONTACT1.ACCOUNTNO = dbo.CONTACT2.ACCOUNTNO
                            INNER JOIN dbo.v_iphistory ON dbo.CONTACT1.ACCOUNTNO = dbo.v_iphistory.ACCOUNTNO
                            WHERE dbo.Contact1.key3 IN ( {$codelist} )
                            ORDER by CAST(dbo.Contact1.key3 as INT)";
                        $stmt = sqlsrv_query($gconn, $gquery);
                        if ($stmt === false) { echo "Error in executing query.\n"; exit(print_r(sqlsrv_errors(), true)); }

                        $rowc = 0; $lastcode = 0;
                        for ($i = 0; $i < count($datarows); ++$i) {
                            $htmlres = '';
                            if ($datarows[$rowc]['code'] !== $lastcode) {
                                $row = sqlsrv_fetch_array($stmt);
                                $htmlres = '<td class="jgff-researcher-cell" rowspan="' . $coderows[$datarows[$rowc]['code']] . '">' . researcher_new($row, $userdata) . '</td>';
                                $lastcode = $datarows[$rowc]['code'];
                                if (stripos($filterlist, ",'".$datarows[$rowc]['code']."',") or $IAJGS !== 'Y') { ++$filterres; }
                            }

                            $dataproblem = 0;
                            while ($rowc < count($datarows) && is_array($row) && $datarows[$rowc]['code'] !== $row['code']) {
                                ++$rowc; ++$i; $dataproblem = 1;
                            }
                            if ($dataproblem === 1) {
                                $htmlres = '<td class="jgff-researcher-cell" rowspan="' . $coderows[$datarows[$rowc]['code']] . '">' . researcher_new($row, $userdata) . '</td>';
                                $lastcode = $datarows[$rowc]['code'];
                            }

                            if ($rowc < count($datarows)) {
                                $surname_val = $datarows[$rowc]['surname'];
                                $town_val    = $datarows[$rowc]['town'];
                                $country_val = $datarows[$rowc]['countryname'];
                                $usbgn_val   = trim($datarows[$rowc]['usbgn']);
                                $townid_val  = trim($datarows[$rowc]['townid']);

                                $JGCD = 0;
                                if (strlen($usbgn_val) > 0) {
                                    $sqlcomm = "SELECT feature FROM jewishgen.communities where feature='" . $usbgn_val . "'";
                                    $resultcomm = $concomm->query($sqlcomm);
                                    if ($resultcomm !== false) {
                                        $rowcomm = mysqli_fetch_row($resultcomm);
                                        if (is_array($rowcomm) && $usbgn_val === $rowcomm[0]) { $JGCD = 1; }
                                    }
                                }

                                $row2 = '<tr' . ($htmlres !== '' ? ' class="jgff-group-start"' : '') . '>';
                                $row2 .= '<td>' . htmlspecialchars($surname_val) . '</td>';
                                $row2 .= '<td>';
                                if ($JGCD === 1) {
                                    $row2 .= '<a class="jgff-town-link" href="/Communities/community.php?usbgn=' . $usbgn_val . '">';
                                    $row2 .= '<img class="jgff-town-icon" src="/jg/images/JGLogoBtn16.gif" alt="Communities DB" title="More info about ' . htmlspecialchars($town_val) . '">';
                                    $row2 .= htmlspecialchars($town_val) . '</a>';
                                } else {
                                    $row2 .= htmlspecialchars($town_val);
                                }
                                $row2 .= '</td>';
                                $row2 .= '<td>' . htmlspecialchars($country_val) . '</td>';
                                $row2 .= $htmlres . '</tr>';

                                if (stripos($filterlist, ",'".$datarows[$rowc]['code']."',") or $IAJGS !== 'Y') {
                                    $table_html .= $row2;
                                    ++$filterrecs;
                                }
                            }
                            ++$rowc;
                        }
                        sqlsrv_free_stmt($stmt);
                        sqlsrv_close($gconn);
                    } else {
                        echo "Connection could not be established.\n";
                        exit(print_r(sqlsrv_errors(), true));
                    }
                ?>

                <div class="jgff-hit-count">
                    <div>
                        <strong><?php echo $filterrecs; ?></strong> <?php echo ($filterrecs === 1 ? 'match' : 'matches'); ?>
                        &mdash;
                        <strong><?php echo $filterres; ?></strong> <?php echo ($filterres === 1 ? 'researcher' : 'researchers'); ?>
                    </div>
                    <?php if (!empty($datetext)) { ?>
                    <div class="jgff-hit-date-note"><?php echo htmlspecialchars($datetext); ?></div>
                    <?php } ?>
                </div>

                <div class="jgff-table-wrap">
                    <table class="jgff-results-table" aria-label="Search results">
                        <thead>
                            <tr>
                                <th scope="col">Surname</th>
                                <th scope="col">Town</th>
                                <th scope="col">Country</th>
                                <th scope="col">Researcher</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php echo $table_html; ?>
                        </tbody>
                    </table>
                </div>

                <?php } // end hits ?>

                <div class="jgff-results-footer">
                    <a class="jgff-another-search" href="/JGFF/jgff-search.html">&larr; Another Search</a>
                    <a class="jgff-save-search" href="/databases/searches/favsearch.php?id=<?php echo $GLOBALS['logid']; ?>" target="_blank">Save as favorite search</a>
                    <span class="jgff-run-time">Run on <?php echo date(DATE_RFC2822); ?></span>
                </div>

                </div><!-- /jgff-results-card -->
            </div>

            <aside class="jgff-sidebar" aria-label="JGFF navigation">
                <div id="jgff-subnav" data-jgff-page="search"></div>
            </aside>

        </div>
    </div>
</div>

<div id="site-footer"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/ajax/popUpHypertext.js"></script>
<script src="/jg/scripts/jg-jgff.js"></script>
<script>
function loadComponent(id, file) {
    return fetch(file)
        .then(function(r){ if(!r.ok) throw new Error('Cannot load '+file); return r.text(); })
        .then(function(h){ document.getElementById(id).innerHTML=h; })
        .catch(function(e){ console.warn(e); });
}
Promise.all([
    loadComponent('site-header', '/Header_NavBar.html'),
    loadComponent('site-footer', '/Footer.html'),
    loadComponent('jgff-subnav', '/JGFF/jgff-subnav.html')
]).then(function() {
    initJgffSubnav();
    document.querySelectorAll('.jg-nav .dropbtn').forEach(function(btn) {
        btn.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                var expanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', String(!expanded));
                var menu = document.getElementById(this.getAttribute('aria-controls'));
                if (menu) menu.style.display = expanded ? 'none' : 'block';
            }
            if (e.key === 'Escape') {
                this.setAttribute('aria-expanded', 'false');
                var menu = document.getElementById(this.getAttribute('aria-controls'));
                if (menu) menu.style.display = 'none';
            }
        });
    });
});
</script>

</body>
</html>
<?php

mysqli_close($concomm);

error_clear_last();
$this_ip = get_client_ip();
$dt = date('Y-m-d H:i:s');
$logtext = "('".$surname."','".$townorig."','".$country."','".$this_ip."','".$jgid."','".$dt."')";
writeJgffLog('saveSearch', $logtext);
register_shutdown_function('shutdown', $logtext);

$con = mysqli_connect(MYSQL_SERVER_HOSTNAME, MYSQL_SERVER_USERNAME, MYSQL_SERVER_PASSWORD, MYSQL_SERVER_DATABASE);
$stmt = mysqli_prepare($con, "INSERT into jewishgen.searches (surname, town, country, ip, jgid, datetime) VALUES (?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssssss", $surname, $townorig, $country, $this_ip, $jgid, $dt);
mysqli_stmt_execute($stmt);
mysqli_close($con);

$time_end = microtime(true);
$duration = ($time_end - $time_start);
write_mysql_jglog($duration, $jgid, $this_ip);


/* -- Helper functions -- preserved unchanged from original -- */

function shutdown($text) {
    $err = error_get_last();
    if (is_array($err)) { writeJgffLog('Shutdown ' . implode(";", $err), $text); }
}

function writeJgffLog($event, $text) {
    $date    = (new DateTime('NOW'))->format('Y-m-d H:i:s');
    $logdate = (new DateTime('NOW'))->format('Y-m-d');
    $dir     = $_SERVER['DOCUMENT_ROOT'] . '\\JGFF';
    $logname = $dir . '\\logfiles\\srch' . $logdate . '.log';
    if (!is_dir($dir . '\\logfiles')) { mkdir($dir . '\\logfiles'); }
    if ($fp = fopen($logname, 'a')) {
        fwrite($fp, $date . ' ' . getClientIP() . ' ' . session_id() . ' ' . $event . ' ' . $text . "\n");
        fclose($fp);
    }
}

function cmp($a, $b) {
    return intval($a['code']) - intval($b['code']);
}

/* -- researcher_new() --------------------------------------
   Outputs researcher contact cell content in new design.
   Logic identical to original researcher() function.
   Renamed to avoid collision if old function also loaded.
----------------------------------------------------------- */
function researcher_new($row, $userdata) {
    global $profile_incomplete;
    $jgffshare = 0;
    $restext   = '';
    if (is_array($row)) {
        if (!isset($row['name']) or !isset($row['ulastname'])) {
            return '';
        }
        $inits   = strtoupper(substr(trim($row['name']),0,1) . substr(trim($row['ulastname']),0,1));
        $restext = '<strong>' . htmlspecialchars(rtrim($row['name'])) . '</strong> (#' . trim($row['code']) . ')<br>';

        switch (true) {
            case preg_match('/^[ 01]*$/', $row['flag']):
                if ($row['flag'] === 0) {
                    if ($jgffshare === 0 or ($jgffshare === 1 and $row['gmshare'] === 0)) {
                        $restext .= empty($row['add1']) ? '' : htmlspecialchars(trim($row['add1'])).'<br>';
                        $restext .= empty($row['add2']) ? '' : htmlspecialchars(trim($row['add2'])).'<br>';
                        $restext .= empty($row['add3']) ? '' : htmlspecialchars(trim($row['add3'])).'<br>';
                        $restext .= empty($row['city']) ? '' : htmlspecialchars(trim($row['city']));
                        $restext .= empty($row['state']) ? '<br>' : ', '.htmlspecialchars(trim($row['state'])).'<br>';
                        $restext .= empty($row['zip'])   ? '' : htmlspecialchars(trim($row['zip'])).'<br>';
                        $restext .= empty($row['country']) ? '' : htmlspecialchars(trim($row['country'])).'<br>';
                        if (!$profile_incomplete) {
                            $restext .= empty($row['email']) ? '' : '<a href="jgffcontactform.php?code='.trim($row['code']).$inits.'" target="_blank" class="jgff-contact-btn"><i class="ti ti-mail" aria-hidden="true"></i> Contact Researcher</a>';
                        }
                    } else { $restext .= 'Must Not Contact'; }
                } elseif ($row['flag'] === 1) {
                    if ($jgffshare === 0 or ($jgffshare === 1 and $row['gmshare'] === 0)) {
                        if (!$profile_incomplete) {
                            $restext .= empty($row['email']) ? '' : '<a href="jgffcontactform.php?code='.trim($row['code']).$inits.'" target="_blank" class="jgff-contact-btn"><i class="ti ti-mail" aria-hidden="true"></i> Contact researcher</a>';
                        }
                    } else { $restext = htmlspecialchars(trim($row['name'])).' (#'.trim($row['code']).')<br>Must Not Contact'; }
                }
                break;
            case $row['flag'] === 2:
                if ($jgffshare === 0 or ($jgffshare === 1 and $row['gmshare'] === 0)) {
                    if (!$profile_incomplete) {
                        $restext = empty($row['email']) ? '' : '<a href="jgffcontactform.php?code='.trim($row['code']).$inits.'" target="_blank" class="jgff-contact-btn"><i class="ti ti-mail" aria-hidden="true"></i> Contact researcher</a>';
                    }
                } else { $restext = 'Researcher #'.trim($row['code']).'<br>Must Not Contact'; }
                break;
            case $row['flag'] === 3:
                if ($jgffshare === 0 or ($jgffshare === 1 and $row['gmshare'] === 0)) {
                    if (!$profile_incomplete) {
			$restext .= empty($row['email']) ? '' : '<a href="jgffcontactform.php?code='.trim($row['code']).$inits.'" target="_blank" class="jgff-contact-btn"><i class="ti ti-mail" aria-hidden="true"></i> Contact researcher</a>';
                    }
                } else { $restext = htmlspecialchars(trim($row['name'])).' (#'.trim($row['code']).')<br>Must Not Contact'; }
                break;
        }

        if (isset($row['lastlogin'])) {
            $dt    = $row['lastlogin'];
            $tmp_y = date_format($dt,'Y');
            $tmp_m = date_format($dt,'m');
        } else { $tmp_y = '0'; $tmp_m = '0'; }

        if ($tmp_y === '0' or ($tmp_y === '2004' and $tmp_m === '09')) {
            $restext .= '<div class="jgff-last-login">Last logged in: before 2004</div>';
        } else {
            $restext .= '<div class="jgff-last-login">Last logged in: '.date_format($dt,'F Y').'</div>';
        }

        if (!empty($row['notes']) and stripos($row['notes'],'EMAIL') === false) {
            $restext .= '<br><strong>'.htmlspecialchars($row['notes']).'</strong>';
        }
        if (!empty($row['resstatus']) and stripos($row['resstatus'],'DI Data') === false and stripos($row['resstatus'],'AC Awaiting') === false) {
            $restext .= '<br><strong>'.htmlspecialchars($row['resstatus']).'</strong>';
        }
    }
    return $restext;
}

function ctryname($country, $countrylist) {
    if (strtoupper($country) === 'USA') {
        $countryname1 = strpos($countrylist, 'value="'.strtoupper($country).'"');
    } else {
        $countryname1 = strpos($countrylist, 'value="'.ucfirst(strtolower($country)).'"');
    }
    $countryname2 = substr($countrylist, $countryname1+9+strlen($country), 100);
    $countryname3 = strpos($countryname2, '<option');
    return substr($countryname2, 0, $countryname3);
}
?>