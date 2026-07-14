<?php
include_once '\\\\jewishgen6\\JEWISHGEN\\wwwroot\\blockscript\\detector.php';
// include_once "../cureetc.php";
// include "../bootstrap.php";

$hebrewmonths = ['', 'Nisan ', 'Iyyar ', 'Sivan ', 'Tammuz ', 'Av ', 'Elul ', 'Tishri ', 'Heshvan ', 'Kislev ', 'Tevet ', 'Shevat ', 'Adar (I) ', 'Adar II '];
$gregorianmonths = ['', 'January ', 'February ', 'March ', 'April ', 'May ', 'June ', 'July ', 'August ', 'September ', 'October ', 'November ', 'December '];

$Gday = $_POST['Gday'];
$Gmonth = $_POST['Gmonth'];
$Gcent = $_POST['Gcent'];
$Gdec = $_POST['Gdec'];
$Gyear = $_POST['Gyear'];

$Jday = $_POST['Jday'];
$Jmonth = $_POST['Jmonth'];
$Jcent = $_POST['Jcent'];
$Jdec = $_POST['Jdec'];
$Jyear = $_POST['Jyear'];

$multiple = $_POST['Multiple'];
$fragment = ((isset($_POST['format']) && $_POST['format'] === 'fragment')
          || (isset($_GET['format'])  && $_GET['format']  === 'fragment'));
/*
echo "Gday=".$Gday."<BR>";
echo "Gmonth=".$Gmonth."<BR>";
echo "Gcent=".$Gcent."<BR>";
echo "Gdec=".$Gdec."<BR>";
echo "Gyear=".$Gyear."<BR>";
echo "Jday=".$Jday."<BR>";
echo "Jmonth=".$Jmonth."<BR>";
echo "Jcent=".$Jcent."<BR>";
echo "Jdec=".$Jdec."<BR>";
echo "Jyear=".$Jyear."<BR>";
*/

$Gdate = str_pad($Gmonth, 2, '0', STR_PAD_LEFT) . str_pad($Gday, 2, '0', STR_PAD_LEFT) . $Gcent . $Gdec . $Gyear;
$Hdate = str_pad($Jmonth, 2, '0', STR_PAD_LEFT) . str_pad($Jday, 2, '0', STR_PAD_LEFT) . $Jcent . $Jdec . $Jyear;

$today = date('mdY');

$html = '<html>';

$html .= "<HEAD><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><TITLE>JewishGen - JOS Calendar Conversion Results</TITLE></HEAD><BODY>";

if (!$fragment) { echo $html; }

    if (!$fragment) {
        include '../jg/headsection.txt';
        include '../jg/header.txt';
    }

$con = mysqli_connect('jewishgen13:3306', 'mtobias', 'nt732b#$', 'jewishgen');

$sql = "SELECT gregorian,hebrew FROM josdates where gregorian='" . $today . "'";

$result = mysqli_query($con, $sql);
// $rowcount=mysqli_num_rows($result);
// echo "rowcount=".$rowcount."<BR>";
$row = mysqli_fetch_array($result);

echo '<CENTER><H1>JOS Calendar Conversion Results</H1></CENTER>';

echo '<HR>';
echo 'Today is ' . date('l j F Y') . ' => ';
echo substr($row['hebrew'], 2, 2) . ' ';
$hm = $hebrewmonths[intval(substr($row['hebrew'], 0, 2))];
$jyear = intval(substr($row['hebrew'], 4, 4));
$jleap = 'N';
$jyear2 = ',' . trim(strval(fmod($jyear, 19))) . ',';
if (stripos(',0,3,6,8,11,14,17,', $jyear2)) {
    $jleap = 'Y';
}
if ($jleap === 'Y') {
    $hm = str_replace('Adar (I)', 'Adar I', $hm);
} else {
    $hm = str_replace('Adar (I)', 'Adar', $hm);
}
echo $hm . ' ' . $jyear;

// while ($row=mysqli_fetch_array($result)) {
// echo $row["gregorian"]." => ".$row["hebrew"];
// }

echo '<P><h3>Gregorian to Jewish Date</h3><BR>';
$baddates = ',0230,0231,0431,0631,0931,1131,';
$gleapyear = 'N';
$gleap = fmod(intval($Gcent . $Gdec . $Gyear), 4);
if ($gleap == 0) { // divides by 4
    $gleap2 = fmod(intval($Gcent . $Gdec . $Gyear), 400);
    $gleap3 = fmod(intval($Gcent . $Gdec . $Gyear), 100);
    // echo "gyear=".intval($Gcent.$Gdec.$Gyear)."<BR>";
    // echo "gleap2=".$gleap2."<BR>";
    if ($gleap3 == 0 and $gleap2 > 0) { // does not divide by 400
        $baddates .= '0229,';
    // echo "1<BR>";
    } else {
        $gleapyear = 'Y';
        // echo "2<BR>";
    }
} else {
    // echo "3<BR>";
    $baddates .= '0229,';
}
// echo "4<BR>";

if (stripos($baddates, substr($Gdate, 0, 4) . ',') > 0) {
    // invalid date
    echo $Gdate;
    echo ' = Invalid Date';
} else {
    $sql = "SELECT gregorian,hebrew FROM josdates where gregorian='" . $Gdate . "'";
    // echo $sql."<BR>";

    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result);
    $d = $row['gregorian'];
    $dr = date_create_from_format('mdY', $d);
    echo $dr->format('l j F Y');
    echo ' => ';
    echo substr($row['hebrew'], 2, 2) . ' ';
    $hm = $hebrewmonths[intval(substr($row['hebrew'], 0, 2))];
    $jyear = intval(substr($row['hebrew'], 4, 4));
    $jleap = 'N';
    $jyear2 = ',' . trim(strval(fmod($jyear, 19))) . ',';
    if (stripos(',0,3,6,8,11,14,17,', $jyear2)) {
        $jleap = 'Y';
    }
    if ($jleap === 'Y') {
        $hm = str_replace('Adar (I)', 'Adar I', $hm);
    } else {
        $hm = str_replace('Adar (I)', 'Adar', $hm);
    }
    echo $hm . ' ' . $jyear;
}

echo '<BR><HR><P>';

echo '<P><h3>Jewish to Gregorian Date(s)</h3><BR>';
$hlist = '';
// if starting point is a leap year then need to check all future years in loop and convert to non-leap year month Adar I where appropriate
$jleapstart = 'N';
$invalid = 0;

if ($Jmonth === '13' or $Jmonth = '12') {
    $jyear = intval(substr($Hdate, 4, 4));
    $jyear2 = ',' . trim(strval(fmod($jyear, 19))) . ',';
    if (stripos(',0,3,6,8,11,14,17,', $jyear2)) {
        $jleapstart = 'Y';
    } else { // invalid date, Adar II in non leap year
        if ($Jmonth === '13') {
            $invalid = 1;
        }
    }
}

if ($invalid === 0) {
    for ($i = 0; $i < $multiple; ++$i) { // years loop
        $tmpyear = str_pad($Hdate + $i, 8, '0', STR_PAD_LEFT);
        if ($jleapstart === 'Y') { // starting from leap year
            $jyear = intval(substr($tmpyear, 4, 4));
            $jleap = 'N';
            $jyear2 = ',' . trim(strval(fmod($jyear, 19))) . ',';
            if (stripos(',0,3,6,8,11,14,17,', $jyear2)) {
                $jleap = 'Y';
            }
            if ($jleap === 'N' and $Jmonth === '13') { // convert month if Adar
                // echo "subyear=".substr($tmpyear,2,6)."<BR>";
                $tmpyear = '12' . substr($tmpyear, 2, 6);
            }
        }
        $sql = "SELECT gregorian,hebrew FROM josdates where hebrew ='" . $tmpyear . "'";
        // echo $sql."<BR>";

        $result = mysqli_query($con, $sql);
        $rowcount = mysqli_num_rows($result);
        if ($rowcount > 0) {
            $row = mysqli_fetch_array($result);
            echo strval(intval(substr($row['hebrew'], 2, 2))) . ' ';
            $hm = $hebrewmonths[intval(substr($row['hebrew'], 0, 2))];

            $jyear = intval(substr($row['hebrew'], 4, 4));
            $jyear2 = ',' . trim(strval(fmod($jyear, 19))) . ',';
            if (stripos(',0,3,6,8,11,14,17,', $jyear2)) {
                $hm = str_ireplace('Adar (I) ', 'Adar I ', $hm);
            } else {
                // if ($jleapstart==="Y") { //starting from leap year
                $hm = str_ireplace('Adar (I) ', 'Adar ', $hm);
            }
            $jyear = intval(substr($row['hebrew'], 4, 4));
            echo $hm . ' ' . $jyear;
            echo ' => ';
            $d = $row['gregorian'];
            $dr = date_create_from_format('mdY', $d);
            echo $dr->format('l j F Y');
            echo '<BR>';
        } else {
            // try again
            if ($Jday === '30') { // leap year problems
                if ($i == 0) { // FIRST LINE
                    echo strval(intval(substr($tmpyear, 2, 2))) . ' ';
                    $hm = $hebrewmonths[intval(substr($tmpyear, 0, 2))];
                    $jyear = intval(substr($tmpyear, 4, 4));
                    echo $hm . ' ' . $jyear;
                    echo ' = Invalid Date (not a leap year) so taking previous day in non-leap years<BR><BR>';
                }
                if (substr($tmpyear, 0, 2) === '12') { // Adar I so not a leap year so use last day of Shevat
                    $tmpyear2 = '1130' . substr($tmpyear, 4, 4);
                } else {
                    $tmpyear2 = substr($tmpyear, 0, 2) . '29' . substr($tmpyear, 4, 4);
                }
                $sql = "SELECT gregorian,hebrew FROM josdates where hebrew ='" . $tmpyear2 . "'";
                // echo $sql."<BR>";

                $result = mysqli_query($con, $sql);
                $rowcount = mysqli_num_rows($result);
                if ($rowcount > 0) {
                    $row = mysqli_fetch_array($result);
                    echo strval(intval(substr($row['hebrew'], 2, 2))) . ' ';
                    $hm = $hebrewmonths[intval(substr($row['hebrew'], 0, 2))];

                    $jyear = intval(substr($row['hebrew'], 4, 4));
                    $jyear2 = ',' . trim(strval(fmod($jyear, 19))) . ',';
                    if (stripos(',0,3,6,8,11,14,17,', $jyear2)) {
                        $hm = str_ireplace('Adar (I) ', 'Adar I ', $hm);
                    } else {
                        // if ($jleapstart==="Y") { //starting from leap year
                        $hm = str_ireplace('Adar (I) ', 'Adar ', $hm);
                    }
                    $jyear = intval(substr($row['hebrew'], 4, 4));
                    echo $hm . ' ' . $jyear;
                    echo ' => ';
                    $d = $row['gregorian'];
                    $dr = date_create_from_format('mdY', $d);
                    echo $dr->format('l j F Y');
                    echo '<BR>';
                }
            } else {
                echo strval(intval(substr($tmpyear, 2, 2))) . ' ';
                $hm = $hebrewmonths[intval(substr($tmpyear, 0, 2))];
                $jyear = intval(substr($tmpyear, 4, 4));
                echo $hm . ' ' . $jyear;
                echo ' = Invalid Date<BR>';
            }
        }
    }
} else { // invalid
    echo strval(intval(substr($Hdate, 2, 2))) . ' ';
    $hm = $hebrewmonths[intval(substr($Hdate, 0, 2))];
    $jyear = intval(substr($Hdate, 4, 4));
    echo $hm . ' ' . $jyear;
    echo ' = Invalid Date';
}

echo '<HR><P>';

$Pageft = '<A HREF="/jos/josdates.htm"><IMG SRC="https://www.jewishgen.org/images/ArrowBack.gif" BORDER=0  WIDTH=42 HEIGHT=42 ALIGN=CENTER>Another Calculation</a>';
$Pageft .= '<BR><A HREF="https://www.jewishgen.org"><IMG SRC="https://www.jewishgen.org/images/ArrowHome.gif" BORDER=0  WIDTH=42 HEIGHT=42 ALIGN=CENTER>Back to the JewishGen Home Page</a>';
$Pageft .= '<BR><BR><CITE>JOS Conceived by Bernard Israelite Kouchel, programmed by Michael Tobias<BR>';
   
 if (!$fragment) {
        echo $Pageft;
        include 'tradefoot.txt';
        include '../jg/footer.txt';
    }

mysqli_close($con);

?>

