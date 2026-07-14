<?php
// include_once '\\\\jewishgen6\\JEWISHGEN\\wwwroot\\blockscript\\detector.php';

include_once '../../databases/cureetc.php';

include '../../databases/bootstrap.php';

include '../../databases/msbootstrap.php';

$time_start = microtime(true);

$jgid = require_userinfo();
$system = 'JGFF';
$access = check_permission($system, $jgid);

if ($access !== 'A' and $access !== 'R' and $access !== 'E' and $access !== 'L') {
    return;
}

$html = '<html>';

$html .= "<HEAD><meta http-equiv='Content-Type' content='text/html; charset=windows-1250'><BASE REF='HTTPs://www.jewishgen.org/'><TITLE>Blind Email Usage</TITLE></HEAD><BODY>";

echo $html;

include '../../jg/headsection.txt';

include '../../jg/header.txt';

echo '<BR><HR>';

$days = $_GET['dys'];
$pc = $_GET['pc'];

// echo "days=".$days." pc=".$pc."<BR>";
// echo "days=".$_POST['dys']." pc=".$_POST['pc']."<BR>";

$con = mysqli_connect('jewishgen13:3306', 'mtobias', 'nt732b#$', 'jewishgen');
$sql = 'SELECT jgid,count(*) as cnt,datetime as dt,message FROM jewishgen.blindcontact where datediff(now(),datetime)<=' . $days . ' group by jgid order by count(*) desc limit ' . $pc;
// Execute query
// echo $sql."<BR>";

$result = $con->query($sql);
$jgidlist = '';
$ct = 0;

while ($row = mysqli_fetch_row($result)) {
    $ct = $ct + 1;
    $tt = $row[0];
    $jgidlist .= "'" . $tt . "',";
    $r[] = ['link' => '<TD><A HREF="https://www.jewishgen.org/cure/jgidview.php?code=' . $tt . '" target="_new">' . $tt . '</A></TD>', 'count' => '<TD>' . $row[1] . '</TD>', 'datetime' => '<TD>' . str_replace(' ', '<BR>', $row[2]) . '</TD>', 'message' => '<TD>' . nl2br($row[3]) . '</TD>', 'jgid' => $tt];
}
$jgidlist = substr($jgidlist, 0, strlen($jgidlist) - 1);
$numrows = $ct;
// echo "jgidlist=".$jgidlist."<BR>";
// print_r($r);

mysqli_close($con);

echo '<CENTER><TABLE BGCOLOR="#DDD6E4" CELLPADDING=3 CELLSPACING=2 BORDER=2 WIDTH=95% >';
echo "<TR><TH COLSPAN=6 ALIGN='CENTER'>Listing the top " . trim($pc) . ' Blind Emailers over the past ' . $days . ' days</TH></TR>';
echo '<TR BGCOLOR="#B3A3C2"><TH>JGID</TH><TH>Contact</TH><TH nowrap>Blind Emails sent<BR>in past ' . $days . ' days</TH><TH>DateTime</TH><TH>Sample Email</TH><TH>Abuser</TH></TR>';

$gquery = "SELECT dbo.CONTACT1.CONTACT AS name,dbo.CONTACT1.KEY3 AS code, ISNULL(dbo.CONTACT2.USERDEF02, '') AS abuser FROM dbo.CONTACT1 INNER JOIN dbo.CONTACT2 ON dbo.CONTACT1.ACCOUNTNO = dbo.CONTACT2.ACCOUNTNO WHERE dbo.Contact1.key3 = '";

for ($x = 0; $x < $numrows; ++$x) {
    $gconn = sqlsrv_connect($MSserverName, $optionsGoldmine);
    if ($gconn) {
        $gquery2 = $gquery . $r[$x]['jgid'] . "'";
        // echo $gquery2."<BR>";
        $stmt = sqlsrv_query($gconn, $gquery2);
        if ($stmt === false) {
            echo "Error in executing query.\n";

            exit(print_r(sqlsrv_errors(), true));
        }
        $row2 = sqlsrv_fetch_array($stmt);

        $contact = '<TD>' . $row2['name'] . '</TD>';
        $abuser = '<TD>' . $row2['abuser'] . '&nbsp;</TD>';
        sqlsrv_free_stmt($stmt);
    } else {
        echo "Connection could not be established.\n";

        exit(print_r(sqlsrv_errors(), true));
    }
    sqlsrv_close($gconn);

    $res = '<TR>';
    $res .= $r[$x]['link'];
    $res .= $contact;
    $res .= $r[$x]['count'];
    $res .= $r[$x]['datetime'];
    $res .= $r[$x]['message'];
    $res .= $abuser;
    $res .= '</TR>';

    if ($abuser === '<TD>1&nbsp;</TD>') {
        $res = str_replace('<TD>', '<TD><B><Font color="RED">', $res);
        $res = str_replace('</TD>', '</font></B></TD>', $res);
    }

    echo $res;
}

echo '</table></CENTER>';

include '../../jg/footer.txt';

$ip = get_client_ip();

$time_end = microtime(true);
$duration = ($time_end - $time_start);

$this_ip = get_client_ip();

// if dont want savesearch version of logging then call write_mysql_jg2log instead
write_mysql_jglog($duration, $jgid, $this_ip);

?>

