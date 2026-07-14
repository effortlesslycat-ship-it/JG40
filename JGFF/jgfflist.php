<?php
// include_once("\\\jewishgen6\JEWISHGEN\wwwroot\blockscript\detector.php");
include_once '../databases/cureetc.php';

include '../databases/bootstrap.php';

include '../databases/msbootstrap.php';

include '_bootstrap.php';

$time_start = microtime(true);

$jgid = require_userinfo();

$code = !isset($_GET['code']) ? '' : $_GET['code'];
if (strlen($code) === 0) {
    $code = $jgid;
}

// check for admin user to view all entries
$system = 'JGFF';
$access = check_permission($system, $jgid);

if ($access !== 'A' and $access !== 'R' and $access !== 'E' and $access !== 'L' and $jgid !== $code) {
    return;
}

$html = '<html>';

$html .= "<HEAD><meta http-equiv='Content-Type' content='text/html; charset=windows-1250'><BASE REF='HTTPS://www.jewishgen.org/'><TITLE>JewishGen Family Finder</TITLE></HEAD><BODY>";

echo $html;

include '../jg/headsection.txt';

include '../jg/header.txt';

$html = '<HR>';

echo '<BR>';

// include "jgffadvert.htm";

echo $html;

$html = '';

$gconn = sqlsrv_connect($MSserverName, $optionsGoldmine);
if ($gconn) {
    $gquery = "SELECT dbo.CONTACT1.CONTACT AS name,dbo.CONTACT1.KEY3 AS code,ISNULL(dbo.CONTACT2.UJGFFALERT, '') AS gmalert, ISNULL(dbo.CONTACT2.UJGFFDATE, '') AS gmalertdate FROM dbo.CONTACT1 INNER JOIN dbo.CONTACT2 ON dbo.CONTACT1.ACCOUNTNO = dbo.CONTACT2.ACCOUNTNO WHERE (dbo.Contact1.key3 = ?)";
    // echo $gquery;
    $gparam = [$code];

    $stmt = sqlsrv_query($gconn, $gquery, $gparam);
    if ($stmt === false) {
        echo "Error in executing query.\n";

        exit(print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmt);
    $name = $row['name'];
    $gmalert = $row['gmalert'];
    $gmalertdate = $row['gmalertdate'];
    $vasok = 0;
    $today_dt = new DateTime(date('Y-m-d'));
    if ($gmalertdate >= $today_dt) {
        // echo "VAS valid<BR>";
        $vasok = 1;
    }

    // Free statement and connection resources.
    sqlsrv_free_stmt($stmt);

    sqlsrv_close($gconn);

// $row[9] in main data listing is JGFF country code
// jgffform.php?surname=katz&town=lodz&country=Pol&dates=ALL&ttype=exact
// searches phonetic (default) surname, exact town
} else {
    echo "Connection could not be established.\n";

    exit(print_r(sqlsrv_errors(), true));
}

$cols = 5;
if ($vasok === 1) {
    $cols = 6;
}

$html .= '<CENTER><TABLE BGCOLOR="#E8E1D1" CELLPADDING=3 CELLSPACING=2 BORDER=2 WIDTH=95% >';
$html .= '<TR><TH COLSPAN=' . $cols . ' ALIGN="CENTER"><H2>JewishGen Family Finder<BR>Surnames/Towns for ' . $name . ' (#' . $code . ')</H2></TH></TR>';
if ($vasok === 1) {
    $html .= '<TR BGCOLOR="#BEC49F"><TH>Entry</TH><TH>Surname</TH><TH>Town</TH><TH>Country</TH><TH>Last<BR>Updated</TH><TH>Value Added<BR>Search</TH></TR>';
} else {
    $html .= '<TR BGCOLOR="#BEC49F"><TH>Entry</TH><TH>Surname</TH><TH>Town</TH><TH>Country</TH><TH>Last<BR>Updated</TH></TR>';
}

$con = mysqli_connect(MYSQL_SERVER_HOSTNAME, MYSQL_SERVER_USERNAME, MYSQL_SERVER_PASSWORD, MYSQL_SERVER_DATABASE);
$sql = "SELECT jewishgen.jgffdata.code, surname, town, jewishgen.jgfftowns.country, if(jewishgen.jgfftowns.id='432','Any',name) as name, date(lastchange) as changedate, jewishgen.jgfftowns.id as townid, jewishgen.jgfftowns.usbgn as usbgn, ifnull(jewishgen.jgfftowns.communitypage,'No') as communitypage, jewishgen.jgfftowns.country FROM jewishgen.jgffdata join jewishgen.jgfftowns on jewishgen.jgffdata.townid = jewishgen.jgfftowns.id left join jewishgen.country2 on jewishgen.jgfftowns.country = jewishgen.country2.country WHERE jewishgen.jgffdata.code = '" . $code . "' order by surname";

// Execute query
$ctr = 0;
$result = $con->query($sql);
while ($row = mysqli_fetch_row($result)) {
    $ctr = $ctr + 1;
    $dat2 = date('d M Y', strtotime($row[5]));
    if ($dat2 === '31 Dec 1996') {
        $dat2 = 'Before 1997';
    }
    $html .= '<TR><TD>' . $ctr . '</TD><TD>' . trim($row[1]) . '</TD><TD';
    if ($row[8] === 'No') {
        $html .= ' class="shtet" usbgn="JGFFT' . trim($row[6]) . '">';
    } else {
        $html .= ' class="shtet" usbgn="' . trim($row[7]) . '"><A HREF="/communities/community.php?usbgn=' . trim($row[7]) . '"><img src="/jg/images/JGLogoBtn16.gif" border=0 Title="More info about ' . $row[2] . '"></A> ';
    }
    $html .= trim($row[2]) . '</TD><TD>' . trim($row[4]) . '</TD><TD>' . $dat2 . '</TD>';
    if ($vasok === 1) { // add submit form eventually
        //		$html .= '<TD ALIGN="center"><A HREF="jgffform.php?surname='.$row[1].'&town='.$row[2].'&country='.$row[9]. '&dates=ALL&ttype=exact" target="_blank">Search</A></TD>';
        $html .= '<TD ALIGN="center"><FORM action="jgffform.php" target="_blank" STYLE="margin:0">';
        $html .= '<INPUT name="surname" type="hidden" value="' . $row[1] . '">';
        $html .= '<INPUT name="town" type="hidden" value="' . $row[2] . '">';
        // $html .= '<INPUT name="Coun1" type="hidden" value="'.$row[9].'">';
        $html .= '<INPUT name="country" type="hidden" value="' . $row[9] . '">'; // tried variable Coun1 but jgffform didnt work
        $html .= '<INPUT name="dates" type="hidden" value="ALL">';
        $html .= '<INPUT name="ttype" type="hidden" value="exact">';
        $html .= '<INPUT name="submit" type="submit" value="Search">';
        $html .= '</FORM></TD>';
    }
    $html .= '</TR>';
}
mysqli_close($con);

$html .= '</table></CENTER>';

$html .= '<h2>' . $ctr . ' data entries</h2><p></p><h2>To modify your Surname/Town information <a href="jgffviewadd.php?code=' . $code . '">click here</a>.</h2><br>';

echo $html;

include 'footer.txt';

echo '<script src="/Ajax/popUpHypertext.js" type="text/javascript"></script>';

include '../jg/footer.txt';

$ip = get_client_ip();

$time_end = microtime(true);
$duration = ($time_end - $time_start);

$this_ip = get_client_ip();

// if dont want savesearch version of logging then call write_mysql_jg2log instead
write_mysql_jglog($duration, $jgid, $this_ip);

?>

