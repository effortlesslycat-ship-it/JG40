<?php
// include_once("\\\jewishgen6\JEWISHGEN\wwwroot\blockscript\detector.php");
include_once '../databases/cureetc.php';

include '../databases/bootstrap.php';

include '../databases/msbootstrap.php';

include '_bootstrap.php';

$time_start = microtime(true);

$jgffid = $_GET['jgffid'] ?? '';
$jgffid = (int) $jgffid;
if ((!is_int($jgffid)) or ($jgffid === 0)) {
    exit("Invalid JGFF TownID: '{$jgffid}'.");
}

$con = mysqli_connect(MYSQL_SERVER_HOSTNAME, MYSQL_SERVER_USERNAME, MYSQL_SERVER_PASSWORD, MYSQL_SERVER_DATABASE);
$sql = 'SELECT * from jgfftowns where id=' . $jgffid;
$towninfo = mysqli_query($con, $sql);
if (!$towninfo) {
    exit('MySQL SELECT error: ' . mysqli_error($con));
}
$rowcount = mysqli_num_rows($towninfo);

if ($rowcount != 1) {
    exit("Row Count is {$rowcount}");
}

$tinfo = mysqli_fetch_array($towninfo);

if (strlen($tinfo['communitypage']) > 2) {   // Town has a "JewishGen Communities Database" page:
    $redir = 'http://www.jewishgen.org/Communities/community.php?usbgn=' . $tinfo['usbgn'];
    header('Location: ' . $redir);

    exit('');
}

if ($tinfo['gazetteer'] !== 'Y') {   // Town's country is not in Gazetteer:
    exit("Localities in country '" . $tinfo['country'] . "' are not in the JewishGen Gazetteer.");
}

// Yes, country is in the JewishGen Gazetteer:
if ($tinfo['usbgn']) {
    // Redirect to Gazetteer, using USBGN code as parameter:
    $redir = 'https://www.jewishgen.org/databases/gazetteer/gazetteer.php?feature=' . $tinfo['usbgn'];
    header('Location: ' . $redir);

    exit('');
}

// Redirect to Gazetteer, displaying ALL exact-spelling town matches:
// First, truncate the town name after a comma ",":
$temptown = $tinfo['town'];
if (strpos($temptown, ',') > 0) {
    $temptown = substr($temptown, 0, strpos($temptown, ','));
}

// Get full country name:
$countrysql = "SELECT * from country2 where country='" . $tinfo['country'] . "'";
$countryinfo = mysqli_query($con, $countrysql);
$cinfo = mysqli_fetch_array($countryinfo);

$redir = 'https://www.jewishgen.org/databases/gazetteer/gazetteer.php';
$redir .= '?Town=' . $temptown . '&Country=' . $cinfo['name'] . '&cl=capital';
header('Location: ' . $redir);

exit('');

// Probably should not reach here, because redirected before now...
mysqli_close($con);

// If don't want savesearch version of logging, then call write_mysql_jg2log() instead.
$this_ip = get_client_ip();
$time_end = microtime(true);
$duration = $time_end - $time_start;
write_mysql_jglog($duration, $jgid, $this_ip);

?>

