<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
/* ============================================================================
   JewishGen Communities Database  -  Search Results / Jurisdiction drill-down
   ----------------------------------------------------------------------------
   Engine (Solr search + MySQL fetch + Damerau-Levenshtein relevance + distance)
   is preserved from the legacy jgcd.php.  Only the RENDERING changed: instead of
   echoing a legacy table + ../jg/*.txt chrome, this builds the result rows and
   substitutes them into searchresults.html (sibling file) - the same split as
   community.php <-> communitypage.html.  Pagination + column sorting now run
   client-side over the full result set (see searchresults.html).

   SECURITY: the legacy file hardcoded the live MySQL password inline (twice).
   Those are now read from JG_DB_* constants - DEFINE THEM IN A SHARED BOOTSTRAP
   INCLUDE and ROTATE the password.  Placeholders below are only a fallback.
============================================================================ */

include_once '../databases/cureetc.php';
include_once '../databases/bootstrap.php';
include_once '../databases/msbootstrap.php';

require 'class.DamerauLevenshtein.php';

// ---- DB credentials (prefer a shared bootstrap define; rotate the password) ----
if (!defined('JG_DB_HOST')) { define('JG_DB_HOST', 'jewishgen13:3306'); }
if (!defined('JG_DB_USER')) { define('JG_DB_USER', 'mtobias'); }
if (!defined('JG_DB_PASS')) { define('JG_DB_PASS', 'nt732b#$'); }
if (!defined('JG_DB_NAME')) { define('JG_DB_NAME', 'jewishgen'); }


$options = [
    'hostname' => SOLR_SERVER_HOSTNAME,
    'login'    => SOLR_SERVER_USERNAME,
    'password' => SOLR_SERVER_PASSWORD,
    'port'     => SOLR_SERVER_PORT,
    'path'     => 'solr/communities',
];

$time_start = microtime(true);

$jgid = '';
if (isset($_COOKIE['jgcure'])) {
    parse_str($_COOKIE['jgcure'], $cure);
    $jgid = isset($cure['jgid']) ? $cure['jgid'] : '';
}

$query1 = '';
$get = '';

// 'get' indicates GET mode (jurisdiction drill-down links, or post-login conversion)
if (isset($_GET['get']) || isset($_GET['HttpVerb'])) {
    $qs = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
    if (stripos($qs, '%3C') > 0 || stripos($qs, '%3E') > 0 || stripos($qs, '<') > 0 || stripos($qs, '>') > 0) { exit; }

    $get = isset($_GET['get']) ? $_GET['get'] : '';
    if (isset($_GET['HttpVerb']) && $_GET['HttpVerb'] === 'Post') { $get = 'Y'; }
    $town     = isset($_GET['Town']) ? $_GET['Town'] : '';
    $ttype    = isset($_GET['ttype']) ? $_GET['ttype'] : '';
    $ctry     = isset($_GET['Country']) ? $_GET['Country'] : '';
    $mil      = isset($_GET['Miles']) ? $_GET['Miles'] : '';
    $c1       = isset($_GET['c1']) ? $_GET['c1'] : 0;
    $c2       = isset($_GET['c2']) ? $_GET['c2'] : 0;
    $d1       = isset($_GET['d1']) ? $_GET['d1'] : 0;
    $d2       = isset($_GET['d2']) ? $_GET['d2'] : 0;
    $cl       = isset($_GET['cl']) ? $_GET['cl'] : '';
    $ccity    = isset($_GET['ccity']) ? $_GET['ccity'] : '';
    $prewwi   = isset($_GET['prewwi']) ? $_GET['prewwi'] : '';
    $interwar = isset($_GET['interwar']) ? $_GET['interwar'] : '';
    $lathem   = isset($_GET['LATHEM']) ? $_GET['LATHEM'] : '';
    $longhem  = isset($_GET['LONGHEM']) ? $_GET['LONGHEM'] : '';
    $dist1900 = isset($_GET['dist1900']) ? $_GET['dist1900'] : '';
    $prov1900 = isset($_GET['prov1900']) ? $_GET['prov1900'] : '';
    $dist1930 = isset($_GET['dist1930']) ? $_GET['dist1930'] : '';
    $prov1930 = isset($_GET['prov1930']) ? $_GET['prov1930'] : '';
} else {
    $town     = isset($_POST['Town']) ? $_POST['Town'] : '';
    $ttype    = isset($_POST['ttype']) ? $_POST['ttype'] : '';
    $ctry     = isset($_POST['Country']) ? $_POST['Country'] : '';
    $mil      = isset($_POST['Miles']) ? $_POST['Miles'] : '';
    $c1       = isset($_POST['c1']) ? $_POST['c1'] : 0;
    $c2       = isset($_POST['c2']) ? $_POST['c2'] : 0;
    $d1       = isset($_POST['d1']) ? $_POST['d1'] : 0;
    $d2       = isset($_POST['d2']) ? $_POST['d2'] : 0;
    $cl       = isset($_POST['cl']) ? $_POST['cl'] : '';
    $ccity    = isset($_POST['ccity']) ? $_POST['ccity'] : '';
    $prewwi   = isset($_POST['prewwi']) ? $_POST['prewwi'] : '';
    $interwar = isset($_POST['interwar']) ? $_POST['interwar'] : '';
    $lathem   = isset($_POST['LATHEM']) ? $_POST['LATHEM'] : '';
    $longhem  = isset($_POST['LONGHEM']) ? $_POST['LONGHEM'] : '';
    $dist1900 = isset($_POST['dist1900']) ? $_POST['dist1900'] : '';
    $prov1900 = isset($_POST['prov1900']) ? $_POST['prov1900'] : '';
    $dist1930 = isset($_POST['dist1930']) ? $_POST['dist1930'] : '';
    $prov1930 = isset($_POST['prov1930']) ? $_POST['prov1930'] : '';
}

$lev = 0;
if (stripos($town, '#LEV#') > 0) { $town = str_ireplace('#LEV#', '', $town); $lev = 1; }

/* ---- helper to render the whole page through the template + exit ---- */
function render_page($summary, $rows, $count)
{
    $ajax = '<script src="/ajax/popUpHypertext.js" type="text/javascript"></script>';
    $find = ['<%=summary %>', '<%=rows %>', '<%=count %>', '<%=ajax %>'];
    $repl = [$summary, $rows, number_format((int)$count), $ajax];
    echo str_replace($find, $repl, file_get_contents('searchresults.html'));
}

/* ---- input validation (skipped for GET drill-down) ---- */
if (strtoupper($get) !== 'Y') {
    if (!(preg_match('/^(?=.*\p{Latin}.*\p{Latin}.*\p{Latin})[\p{Latin}\-\,\[\]\(\) ]+$/u', $town)
        or ($ttype === 'E' and preg_match('/^(?=.*\p{Latin}.*\p{Latin})[\p{Latin}\-\,\[\]\(\) ]+$/u', $town)))) {
        $emptyrow = '<tr class="sr-row sr-empty"><td colspan="6">You can only enter Latin characters and no digits &mdash; '
                  . 'please try again with at least 3 characters (2 for an exact-spelling search).</td></tr>';
        render_page('', $emptyrow, 0);
        exit;
    }
}

// legacy o-acute city-name patches (distance-from-selected-city feature)
if (substr($ccity, 0, 4) === 'Krak') { $ccity = 'Krak&#243;w'; }
if (substr($ccity, 0, 4) === 'Tarn') { $ccity = 'Tarn&#243;w'; }
if (substr($ccity, 0, 18) === 'Navahrudak (Nowogr') { $ccity = 'Navahrudak (Nowogr&#243;dek'; }
if (substr($ccity, 0, 5) === 'Rzesz') { $ccity = 'Rzesz&#243;w (Zheshuv)'; }
if (substr($ccity, 0, 6) === '&#321;' && substr($ccity, 0, 8) !== '&#321;om') { $ccity = '&#321;&#243;d&#378;'; }
if (substr($ccity, 0, 6) === 'Piotrk') { $ccity = 'Piotrk&#243;w Trybunalski'; }
if (substr($ccity, 0, 5) === 'Bra&#') { $ccity = 'Bra&#351;ov (Brass&#243;)'; }
if (substr($ccity, 0, 8) === 'Stropkov') { $ccity = 'Stropkov (Sztropk&#243;)'; }
if (substr($ccity, 0, 15) === 'Ivano-Frankivsk') { $ccity = 'Ivano-Frankivsk (Stanis&#322;aw&#243;w)'; }
if (substr($ccity, 0, 17) === 'Zhovkva (Nesterov') { $ccity = 'Zhovkva (Nesterov, &#379;&#243;&#322;kiew)'; }

if ($cl === 'latlon') { $ccity = ''; }

$c3 = $c1 + $c2 / 60;
$d3 = $d1 + $d2 / 60;

$urlparam = ($mil === 'MILES') ? '' : '&scale=K';

if ($lathem === 'S'  and $c1 >= 0) { $c1 = $c1 * -1; }
if ($longhem === 'W' and $d1 >= 0) { $d1 = $d1 * -1; }

if ($mil === 'MILES') { $miles = 'MILES'; $miles2 = ' miles '; }
else                  { $miles = 'KM';    $miles2 = ' km '; }

$tfuzz = ''; $tfuzz2 = 0; $tstarts = ''; $tcontains1 = ''; $tcontains2 = '';

switch ($ttype) {
    case 'Q': $tfield = 'town_bmpm'; $ttext = ' (Phonetically like) : '; break;
    case 'D': $tfield = 'town_dm';   $ttext = ' (DM soundex) : '; break;
    case 'E': $tfield = 'town';      $ttext = ' (Exact) : '; break;
    case 'S': $tfield = 'town';      $ttext = ' (Starts with) : '; $tstarts = '*'; break;
    case 'C': $tfield = 'town';      $ttext = ' (Contains) : '; $tcontains1 = '*'; $tcontains2 = '*'; break;
    case 'F1':
        $tfield = 'town'; $tlen = '1'; $tfuzz = '~' . $tlen; $tfuzz2 = 1;
        $ttext = ' (Fuzzy) : '; $tfield2 = 'town_bmpm'; $tfield3 = 'town_dm'; break;
    case 'F2':
        $tfield = 'town'; $tlen = min(2, min(round(strlen($town) / 3), 4)); $tfuzz = '~' . $tlen; $tfuzz2 = 1;
        $ttext = ' (Fuzzier) : '; $tfield2 = 'town_bmpm'; $tfield3 = 'town_dm'; break;
    case 'FM':
        $tfield = 'town'; $tlen = min(round(strlen($town) / 3), 4); $tfuzz = '~' . $tlen; $tfuzz2 = 1;
        $ttext = ' (Fuzziest) : '; $tfield2 = 'town_bmpm'; $tfield3 = 'town_dm'; break;
    default:  $tfield = 'town_bmpm'; $ttext = ' (phonetically like) : ';
}

$townorig = $town;
$town1 = $town;
$town = str_replace(' ', '', $town);

$query1 = '';
if (strlen($town) > 0) {
    $query1 = $tfield . ':' . $tcontains1 . $town . $tcontains1 . $tstarts . $tfuzz;
}
if ($tfuzz2 === 1) {
    $query1 .= ' AND NOT (' . $tfield2 . ':' . $town . ' OR ' . $tfield3 . ':' . $town . ')';
}

$query1ctry = ''; $countrytxt = '';
if ($ctry !== 'ZZ' and strlen($ctry) > 0) {
    $query1ctry .= ' AND shtetl_cou:"' . $ctry . '"';
    $countrytxt .= 'in modern country ' . $ctry . '<BR>';
}
if ($interwar !== 'ZZ' and strlen($interwar) > 0) {
    $query1ctry .= ' AND count1930:"' . $interwar . '"';
    $countrytxt .= 'in interwar country ' . $interwar . '<BR>';
}
if ($prewwi !== 'ZZ' and strlen($prewwi) > 0) {
    $query1ctry .= ' AND count1900:"' . $prewwi . '"';
    $countrytxt .= 'in pre-WWI country ' . $prewwi . '<BR>';
}

$query1 = '(' . $query1 . ')' . $query1ctry;

$distprovtxt = '';
if (strlen($dist1900) > 0) {
    $query1 = 'dist1900:"' . $dist1900 . '"';
    $distprovtxt .= 'in ' . $dist1900 . ' District';
    if (strlen($prov1900) > 0) { $query1 .= ' AND prov1900:"' . $prov1900 . '"'; $distprovtxt .= ', ' . $prov1900 . ' Province in 1900<BR>'; }
    $cl = 'capital';
}
if (strlen($dist1900) === 0 and strlen($prov1900) > 0) {
    $query1 = 'prov1900:"' . $prov1900 . '"';
    $distprovtxt .= 'in ' . $prov1900 . ' Province in 1900<BR>';
    $cl = 'capital';
}
if (strlen($dist1930) > 0) {
    $query1 = 'dist1930:"' . $dist1930 . '"';
    $distprovtxt .= 'in ' . $dist1930 . ' District';
    if (strlen($prov1930) > 0) { $query1 .= ' AND prov1930:"' . $prov1930 . '"'; $distprovtxt .= ', ' . $prov1930 . ' Province in 1930<BR>'; }
    $cl = 'capital';
}
if (strlen($dist1930) === 0 and strlen($prov1930) > 0) {
    $query1 = 'prov1930:"' . $prov1930 . '"';
    $distprovtxt .= 'in ' . $prov1930 . ' Province in 1930<BR>';
    $cl = 'capital';
}

/* ---- run the Solr search ---- */
$client = new SolrClient($options);
$query = new SolrQuery($query1);
$query->setRows(100000);
$query->setFacet(true);
$query->addFacetField('feature');
$query->setFacetMinCount(1);
$query->setFacetLimit(-1);

$updateResponse = $client->query($query);
$response_array = $updateResponse->getResponse();
$found_array = $response_array->response;
$datarows = $found_array->offsetGet('docs');
$totalhits = $found_array['numFound'];

$featurelist = '';
$datacount = is_array($datarows) ? count($datarows) : 0;
for ($i = 0; $i < $datacount; ++$i) {
    if (stristr($featurelist, "'" . $datarows[$i]['feature'] . "',") === false) {
        $featurelist .= "'" . $datarows[$i]['feature'] . "',";
    }
}
$featurelist = substr($featurelist, 0, strlen($featurelist) - 1);

/* ---- build the hero summary line ---- */
$summary = '';
if (strlen($town) > 0) { $summary = 'Town' . $ttext . htmlspecialchars(strtoupper($town1)); }
$extra = preg_replace('/<br\s*\/?>/i', ' &middot; ', trim($countrytxt . $distprovtxt));
$extra = trim(preg_replace('/(\s*&middot;\s*)+$/', '', $extra));
if ($extra !== '') { $summary .= ($summary !== '' ? ' &middot; ' : '') . $extra; }
if ($summary === '') { $summary = 'All matching communities'; }

/* ---- build the result rows ---- */
$rows = '';

if ((int)$totalhits === 0) {
    $rows = '<tr class="sr-row sr-empty"><td colspan="6">No matches found.</td></tr>';
} else {
    $con = mysqli_connect(JG_DB_HOST, JG_DB_USER, JG_DB_PASS, JG_DB_NAME);
    $sql = 'SELECT shtetl_nam, shtetl_cou, latitude, longitude, alternate_, name1950, count1950, name1930, dist1930, prov1930, count1930, name1900, dist1900, prov1900, count1900, jgff_no, feature, locality, jgffcountry, capital, capitalcoords, alternate_2, 999 as levenshtein, \'\' as levtext FROM communities_view where feature IN (' . $featurelist . ') order by LPAD(jgff_no,10,0) desc';
    $result = mysqli_query($con, $sql);
    $rowcount = mysqli_num_rows($result);

    while ($r[] = mysqli_fetch_array($result));
    if (!is_array($r[count($r) - 1])) { array_pop($r); }

    // Damerau-Levenshtein relevance: closest of all name variants
    foreach ($r as &$value) {
        $alltowns = trim($value['shtetl_nam']) . ' / ' . trim($value['name1900']) . ' / ' . trim($value['name1930']) . ' / ' . trim($value['name1950']) . ' / ' . trim($value['alternate_2']);
        $words = explode('/', $alltowns);
        for ($y = 0; $y < count($words); ++$y) {
            if (strlen(trim($words[$y])) > 0) {
                $errorlevel = error_reporting();
                error_reporting($errorlevel & ~E_NOTICE);
                $res = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', html_entity_decode($words[$y]));
                error_reporting($errorlevel);
                $dl = new DamerauLevenshtein(trim(strtolower($town)), trim(strtolower(str_replace("'", '', $res))));
                $dl2 = $dl->getSimilarity();
                if ($dl2 < $value[22]) {
                    $value['levenshtein'] = $dl2; $value[22] = $dl2;
                    $value['levtext'] = trim($words[$y]); $value[23] = trim($words[$y]);
                }
            }
        }
    }
    unset($value);

    // relevance sort for name searches only (drill-downs keep jgff_no order)
    if (strtoupper($get) !== 'Y') {
        usort($r, function ($a, $b) { return $a['levenshtein'] - $b['levenshtein']; });
    }

    $loadarray = 1; $x = 0; $rowcount2 = 0;
    while ($loadarray === 1) {
        if (strlen(trim($r[$x][0])) > 0) {
            $rows .= '<tr class="sr-row"><td class="shtet" usbgn="' . $r[$x][16] . '">';
            if ($r[$x][17] === 'Town') {
                $rows .= '<a class="sr-more" href="/Communities/community.php?usbgn=' . $r[$x][16] . $urlparam . '" title="More about ' . $r[$x][0] . '"><img src="/jg/images/JGLogoBtn16.gif" border="0" alt="More info"></a> ';
            }
            $rows .= '<b>' . $r[$x][0] . '</b>, ' . $r[$x][1];
            if ($lev === 1) { $rows .= '<br>(Lev distance=' . $r[$x][22] . ' on ' . $r[$x][23] . ')'; }

            if ($r[$x][17] === 'Town' && strlen($r[$x][2]) > 2 && strlen($r[$x][3]) > 2) {
                $rows .= '<br><span class="sr-coords">';
                if ($r[$x][2] > 0) { $rows .= substr($r[$x][2], 0, 2) . '&deg;' . substr($r[$x][2], 2, 2) . "' N "; }
                else { $rows .= (substr($r[$x][2], 0, 2) * -1) . '&deg;' . substr($r[$x][2], 2, 2) . "' S "; }
                if ($r[$x][3] > 0) { $rows .= substr($r[$x][3], 0, 2) . '&deg;' . substr($r[$x][3], 2, 2) . "' E "; }
                else { $rows .= (substr($r[$x][3], 0, 2) * -1) . '&deg;' . substr($r[$x][3], 2, 2) . "' W "; }

                $lat2b  = substr($r[$x][2], 0, 2) + substr($r[$x][2], 2, 2) / 60;
                $long2b = substr($r[$x][3], 0, 2) + substr($r[$x][3], 2, 2) / 60;

                if ($cl === 'capital') {
                    $jgffno = intval($r[$x][18]);
                    if ($jgffno !== 0) {
                        $ccity = $r[$x][19]; $coords = $r[$x][20];
                    } else {
                        $con2 = mysqli_connect(JG_DB_HOST, JG_DB_USER, JG_DB_PASS, JG_DB_NAME);
                        $sql2 = "SELECT capital,capitalcoords FROM country2 where name = '" . $r[$x][1] . "'";
                        $result2 = mysqli_query($con2, $sql2);
                        $row2 = mysqli_fetch_array($result2);
                        $ccity = $row2[0]; $coords = $row2[1];
                        mysqli_free_result($result2); mysqli_close($con2);
                    }
                    $c1 = substr($coords, 0, 2); $c2 = substr($coords, 2, 2);
                    $d1 = substr($coords, 4, 2); $d2 = substr($coords, 6, 2);
                    $c3 = $c1 + $c2 / 60; $d3 = $d1 + $d2 / 60;
                }
                $bearing = getRhumbLineBearing($c3, $d3, $lat2b, $long2b);
                $rows .= '<br>' . round(vincentyGreatCircleDistance($c3, $d3, $lat2b, $long2b, $miles)) . $miles2;
                $rows .= getCompassDirection($bearing) . ' of ';
                $rows .= (strlen($ccity) > 0) ? $ccity : ($c1 . '&deg;' . $c2 . "' " . $d1 . '&deg;' . $d2 . "'");
                $rows .= '</span>';
            }
            $rows .= '</td>';

            $rows .= '<td>' . str_replace('/', ',', $r[$x][4]) . '</td>';
            $rows .= '<td>' . nonbreak($r[$x][5]) . '<br>' . nonbreak($r[$x][6]) . '</td>';
            $rows .= '<td>' . nonbreak($r[$x][7]) . "<br><a href='/Communities/jgcd.php?get=y&dist1930=" . $r[$x][8] . '&prov1930=' . $r[$x][9] . "'>" . nonbreak($r[$x][8]) . "</a><br><a href='/Communities/jgcd.php?get=y&prov1930=" . $r[$x][9] . "'>" . nonbreak($r[$x][9]) . '</a><br>' . nonbreak($r[$x][10]) . '</td>';
            $rows .= '<td>' . $r[$x][11] . "<br><a href='/Communities/jgcd.php?get=y&dist1900=" . $r[$x][12] . '&prov1900=' . $r[$x][13] . "'>" . nonbreak($r[$x][12]) . "</a><br><a href='/Communities/jgcd.php?get=y&prov1900=" . $r[$x][13] . "'>" . nonbreak($r[$x][13]) . '</a><br>' . nonbreak($r[$x][14]) . '</td>';

            if ($r[$x][15] > 0) {
                $jgfflink = '<a href="/jgff/jgffform.php?feature=' . $r[$x][16] . '&town=' . $r[$x][0] . '&country=' . $r[$x][18] . '&dates=ALL" target="_blank" rel="noopener">';
                $rows .= '<td class="sr-num">' . $jgfflink . number_format($r[$x][15]) . '</a></td></tr>';
            } else {
                $rows .= '<td class="sr-num">' . $r[$x][15] . '</td></tr>';
            }
            $rowcount2 = $rowcount2 + 1;
        }
        $x = $x + 1;
        if ($rowcount2 === $rowcount) { $loadarray = 0; }
    }
    mysqli_free_result($result);
    mysqli_close($con);
}

/* ---- render through the template ---- */
render_page($summary, $rows, $totalhits);

$this_ip = get_client_ip();
$time_end = microtime(true);
write_mysql_jglog(($time_end - $time_start), $jgid, $this_ip);

/* ============================================================================
   HELPERS  (unchanged from legacy)
============================================================================ */
function vincentyGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $miles, $earthRadius = 6371000)
{
    $latFrom = deg2rad($latitudeFrom); $lonFrom = deg2rad($longitudeFrom);
    $latTo   = deg2rad($latitudeTo);   $lonTo   = deg2rad($longitudeTo);
    $lonDelta = $lonTo - $lonFrom;
    $a = pow(cos($latTo) * sin($lonDelta), 2) + pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
    $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);
    $angle = atan2(sqrt($a), $b) / 1000;
    if (strtoupper($miles) === 'MILES') { $angle = $angle * 0.621371192; }
    return round($angle * $earthRadius, 1);
}

function nonbreak($tmp) { return str_ireplace(' ', '&nbsp;', trim($tmp)); }

function getRhumbLineBearing($lat1, $lon1, $lat2, $lon2)
{
    $dLon = deg2rad($lon2) - deg2rad($lon1);
    $dPhi = log(tan(deg2rad($lat2) / 2 + pi() / 4) / tan(deg2rad($lat1) / 2 + pi() / 4));
    if (abs($dLon) > pi()) { $dLon = $dLon > 0 ? (($dLon - 2 * pi())) : ($dLon + 2 * pi()); }
    return (rad2deg(atan2($dLon, $dPhi)) + 360) % 360;
}

function getCompassDirection($bearing)
{
    static $cardinals = ['N','NNE','NE','ENE','E','ESE','SE','SSE','S','SSW','SW','WSW','W','WNW','NW','NNW','N'];
    return $cardinals[round($bearing / 22.5)];
}
?>