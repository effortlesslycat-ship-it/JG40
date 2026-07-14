<?php
include_once '\\\\jewishgen6\\JEWISHGEN\\wwwroot\\blockscript\\detector.php';

include_once '../cureetc.php';

include '../bootstrap.php';

$Miles = 'MILES';

if (isset($_GET['usbgn'])) {
    $usbgn = $_GET['usbgn'];
} else {
    $usbgn = $_POST['usbgn'];
}
if (strlen($usbgn) > 10) {
    exit;
}

if (isset($_GET['miles'])) {
    $Miles = strtoupper($_GET['miles']);
} else {
    $Miles = strtoupper($_POST['miles']);
}
if (strlen($Miles) > 10) {
    exit;
}

if (isset($_GET['lat1'])) {
    $lat1 = $_GET['lat1'];
    $lat = $lat1;
} else {
    $lat1 = $_POST['lat1'];
}
if (isset($_POST['lat2'])) {
    $lat2 = $_POST['lat2'];
    $lat = $lat1 + ($lat2 / 60);
}
if (strlen($lat) > 10) {
    exit;
}

if (isset($_GET['lon1'])) {
    $lon1 = $_GET['lon1'];
    $long = $lon1;
} else {
    $lon1 = $_POST['lon1'];
}
if (isset($_POST['lon2'])) {
    $lon2 = $_POST['lon2'];
    $long = $lon1 + ($lon2 / 60);
}
if (strlen($long) > 10) {
    exit;
}

if (isset($_GET['radius'])) {
    $radius = $_GET['radius'];
} else {
    $radius = $_POST['radius'];
}
if (strlen($radius) > 1) {
    exit;
}

if ($Miles === 'MILES') {
    $unit = ' mile';
} else {
    $unit = ' kilometer';
}

// echo "lat=".$lat." and long=".$long."<BR>";

$html = '<html>';

$html .= "<HEAD><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><TITLE>LDS Microfilm Master</TITLE></HEAD><BODY>";

echo $html;

include '../../jg/headsection.txt';

include '../../jg/header.txt';

$con = mysqli_connect('jewishgen13:3306', 'mtobias', 'nt732b#$', 'jewishgen');

if ($radius === 'Y') {
    $radius2 = 100;
    $fact = '3959';
    if ($Miles === 'KILOMETERS') {
        $radius2 = 150;
        $fact = '6371';
    }

    $sql = 'SELECT distinct f1,f13,f14,f10, ( ' . $fact . ' * acos( cos( radians(' . $lat . ') ) * cos( radians( f13 ) ) * cos( radians( f14 ) - radians(' . $long . ') ) + sin( radians(' . $lat . ") ) * sin(radians(f13)) ) ) AS distance FROM  jgdata_notglue where dataid like 'LDSNEW%' having distance<=" . $radius2 . ' ORDER BY distance';

    // echo "sql=".$sql."<BR>";

    $result = mysqli_query($con, $sql);
    // $rowcount=mysqli_num_rows($result);
    // echo "rowcount=".$rowcount."<BR>";

    echo '<CENTER><H1><a href="http://jri-poland.org/ldsdist.htm">LDS Microfilm Master</A></H1></CENTER>';

    echo '<BR>';

    echo '<CENTER><TABLE id="myTable" BGCOLOR="#E8E1D1" CELLPADDING=3 CELLSPACING=2 BORDER=2 WIDTH=100% ><THEAD>';

    $coords1 = latlonformat($lat, $long);

    echo '<TR><TH COLSPAN=7 ALIGN="CENTER"><H2>Searching for LDS Polish records within<BR>' . $radius2 . ' ' . $unit . 's of ' . $coords1 . ' <BR>';
    echo '<small><small>Run on ' . date(DATE_RFC2822) . '</small></small></H2></TH></TR>';

    echo '<TR BGCOLOR="#BEC49F"><TH>Town</TH><TH>Re-base</TH><TH>Films</TH><TH NOWRAP>Distance/Direction<BR>from ' . $coords1 . '</TH></TR></THEAD><TBODY>';

    while ($row = mysqli_fetch_array($result)) {
        echo '<TR><TD>' . $row['f1'] . '</TD>';
        echo '<TD><A HREF="https://www.jewishgen.org/databases/poland/ldsfilms.php?lat1=' . $row['f13'] . '&lon1=' . $row['f14'] . '&radius=Y&miles=' . $Miles . '">Re-base</A></TD>';
        echo '<TD><A HREF="https://www.jewishgen.org/databases/poland/ldsfilms.php?usbgn=' . $row['f10'] . '">Films</A></TD>';

        $dist = '<TD>' . number_format($row['distance'], 1) . ' ' . $unit . 's  ';
        $bearing = getRhumbLineBearing($lat, $long, $row['f13'], $row['f14']);
        $dist .= getCompassDirection($bearing) . '</TD>';
        echo $dist;

        echo '</TR>';
    }

    echo '</TABLE></CENTER>';
} else {  // town search
    $sql = "SELECT f1,f2,f3,f4,f5,f9 FROM jgdata_notglue where dataid like 'LDSNEW%' AND f10='" . $usbgn . "'";
    // echo "sql=".$sql."<BR>";

    $result = mysqli_query($con, $sql);
    $rowcount = mysqli_num_rows($result);
    $row = mysqli_fetch_array($result);

    echo '<CENTER><H1><a href="http://jri-poland.org/ldsdist.htm">LDS Microfilm Master</a></H1></CENTER><P><CENTER>';
    echo '<TABLE id="myTable" BGCOLOR="#E8E1D1" CELLPADDING=3 CELLSPACING=2 BORDER=1 WIDTH=90% ><TR><TH COLSPAN=2 ALIGN="CENTER"><h2>Searching for Town : ' . $row['f1'] . '<BR>';
    echo $rowcount . ' matching records found.<BR>';
    echo '<small><small>Run on ' . date(DATE_RFC2822) . '</small></small></H2></TH></TR></TABLE>';

    echo '<TABLE BGCOLOR=#E8E1D1 CELLPADDING=3 CELLSPACING=2 BORDER=1 WIDTH=90%><THEAD><TR BGCOLOR="#BEC49F"><TH>Town</TH><TH>Gubernia</TH><TH>Film</TH><TH>Year(s)</TH><TH>Record Types</TH><TH>JRI-Poland Town Page</TH></TR></THEAD><TBODY>';
    echo '<TR><TH COLSPAN=6 BGCOLOR="#BEC49F" BORDER=0 CELLPADDING=0 CELLSPACING=0><IMG SRC="/images/jgspace.gif"></TH></TR>';

    // already read row to get town name from usbgn code, so display first row and then loop through rest
    echo '<TR><TD>' . $row['f1'] . '</TD><TD>' . $row['f2'] . '</TD><TD>' . $row['f5'] . '</TD><TD>' . $row['f4'] . '</TD><TD>' . $row['f3'] . '</TD><TD>' . $row['f9'] . '</TD></TR>';
    echo '<TR><TH COLSPAN=6 BGCOLOR="#BEC49F" BORDER=0 CELLPADDING=0 CELLSPACING=0><IMG SRC="/images/jgspace.gif"></TH></TR>';

    while ($row = mysqli_fetch_array($result)) {
        echo '<TR><TD>' . $row['f1'] . '</TD><TD>' . $row['f2'] . '</TD><TD>' . $row['f5'] . '</TD><TD>' . $row['f4'] . '</TD><TD>' . $row['f3'] . '</TD><TD>' . $row['f9'] . '</TD></TR>';
        echo '<TR><TH COLSPAN=6 BGCOLOR="#BEC49F" BORDER=0 CELLPADDING=0 CELLSPACING=0><IMG SRC="/images/jgspace.gif"></TH></TR>';
    }
    echo '</TABLE></CENTER>';
}

echo '<script src="/ajax/popUpHypertext.js" type="text/javascript"></script>';

include '../../jg/footer.txt';

mysqli_close($con);

function getRhumbLineBearing($lat1, $lon1, $lat2, $lon2)
{
    // difference in longitudinal coordinates
    $dLon = deg2rad($lon2) - deg2rad($lon1);

    // difference in the phi of latitudinal coordinates
    $dPhi = log(tan(deg2rad($lat2) / 2 + pi() / 4) / tan(deg2rad($lat1) / 2 + pi() / 4));

    // we need to recalculate $dLon if it is greater than pi
    if (abs($dLon) > pi()) {
        if ($dLon > 0) {
            $dLon = (2 * pi() - $dLon) * -1;
        } else {
            $dLon = 2 * pi() + $dLon;
        }
    }
    // return the angle, normalized
    return (rad2deg(atan2($dLon, $dPhi)) + 360) % 360;
}

function getCompassDirection($bearing)
{
    static $cardinals = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW', 'N'];

    return $cardinals[round($bearing / 22.5)];
}

function latlonformat($lat, $long)
{
    $epsilon = 0.0000000001;
    if ($lat > 0) {
        $lt1 = ($lat - intval($lat)) * 60;
        if (abs(round($lt1) - 60) < $epsilon) {
            $coords1 = round(intval($lat) + 1) . "&deg;00' N ";
        } else {
            $coords1 = round(intval($lat)) . '&deg;' . str_pad(round($lt1), 2, '0', STR_PAD_LEFT) . "' N ";
        }
    } else {
        $lt1 = (intval($lat) - $lat) * 60;
        if (abs(round($lt1) - 60) < $epsilon) {
            $coords1 = round(intval(-$lat) + 1) . "&deg;00' S ";
        } else {
            $coords1 = round(intval(-$lat)) . '&deg;' . str_pad(round($lt1), 2, '0', STR_PAD_LEFT) . "' S ";
        }
    }
    if ($long > 0) {
        $lg1 = ($long - intval($long)) * 60;
        if (abs(round($lg1) - 60) < $epsilon) {
            $coords1 .= round(intval($long) + 1) . "&deg;00' E";
        } else {
            $coords1 .= round(intval($long)) . '&deg;' . str_pad(round($lg1), 2, '0', STR_PAD_LEFT) . "' E";
        }
    } else {
        $lg1 = (intval($long) - $long) * 60;
        if (abs(round($lg1) - 60) < $epsilon) {
            $coords1 .= round(intval(-$long) + 1) . "&deg;00' W";
        } else {
            $coords1 .= round(intval(-$long)) . '&deg;' . str_pad(round($lg1), 2, '0', STR_PAD_LEFT) . "' W";
        }
    }

    return $coords1;
}

?>

