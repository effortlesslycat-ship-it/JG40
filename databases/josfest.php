<?php
include_once '\\\\jewishgen6\\JEWISHGEN\\wwwroot\\blockscript\\detector.php';
// include_once "../cureetc.php";
    $fragment = ((isset($_POST['format']) && $_POST['format'] === 'fragment')
              || (isset($_GET['format'])  && $_GET['format']  === 'fragment'));
// include "../bootstrap.php";

$Pageft = '<A HREF="/jos/josfest.htm"><IMG SRC="https://www.jewishgen.org/images/ArrowBack.gif" BORDER=0  WIDTH=42 HEIGHT=42 ALIGN=CENTER>Another Calculation</a>';
$Pageft .= '<BR><A HREF="https://www.jewishgen.org"><IMG SRC="https://www.jewishgen.org/images/ArrowHome.gif" BORDER=0  WIDTH=42 HEIGHT=42 ALIGN=CENTER>Back to the JewishGen Home Page</a>';
$Pageft .= '<BR><BR><CITE>JOS Conceived by Bernard Israelite Kouchel, programmed by Michael Tobias<BR>';

$hebrewmonths = ['', 'Nisan ', 'Iyyar ', 'Sivan ', 'Tammuz ', 'Av ', 'Elul ', 'Tishri ', 'Heshvan ', 'Kislev ', 'Tevet ', 'Shevat ', 'Adar (I) ', 'Adar II '];
$gregorianmonths = ['', 'January ', 'February ', 'March ', 'April ', 'May ', 'June ', 'July ', 'August ', 'September ', 'October ', 'November ', 'December '];

// festival,day,month,cmonth,shabbat
$festivals = [
    ['Rosh Hashana', '01', '07', 'Tishri', 0],
    ['Tzom Gedalya', '03', '07', 'Tishri', 1],
    ['Yom Kippur', '10', '07', 'Tishri', 0],
    ['Sukkot (first day)', '15', '07', 'Tishri', 0],
    ['Chanukka (first day)', '25', '09', 'Kislev', 0],
    ["Asara b'Tevet", '10', '10', 'Tevet', 0],
    ["Ta'anit Ester", '13', '13', 'Adar II', -2],
    ['Purim', '14', '13', 'Adar II', 0],
    ['Pesach (first day)', '15', '01', 'Nisan', 0],
    ['Shavuot', '06', '03', 'Sivan', 0],
    ["Shiv'a Asar b'Tammuz", '17', '04', 'Tammuz', 1],
    ["Tish'a b'Av", '09', '05', 'Av', 1],
];

if (isset($_GET['caltype'])) {
    $caltype = $_GET['caltype']; // jyear, gyear
    $jyear = str_pad($_GET['jyear'], 4, '0', STR_PAD_LEFT);
} else {
    $caltype = $_POST['caltype']; // jyear, gyear
    $jyear = str_pad($_POST['jyear'], 4, '0', STR_PAD_LEFT);
}

$jyearorig = $jyear;

$today = date('mdY');

$html = '<html>';

$html .= "<HEAD><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><TITLE>JewishGen - JOS Festival Results</TITLE></HEAD><BODY>";

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

echo '<CENTER><H1>JOS Festival Results</H1></CENTER>';

echo '<HR>';
echo '<CENTER>Today is ' . date('l j F Y') . ' => ';
echo substr($row['hebrew'], 2, 2) . ' ';
$hm = $hebrewmonths[intval(substr($row['hebrew'], 0, 2))];
$jyear1 = intval(substr($row['hebrew'], 4, 4));
$jleap = 'N';
$jyear2 = ',' . trim(strval(fmod($jyear1, 19))) . ',';
if (stripos(',0,3,6,8,11,14,17,', $jyear2)) {
    $jleap = 'Y';
}
if ($jleap === 'Y') {
    $hm = str_replace('Adar (I)', 'Adar I', $hm);
} else {
    $hm = str_replace('Adar (I)', 'Adar', $hm);
}
echo $hm . ' ' . $jyear1;

$jyearorig = $jyear;
if ($caltype === 'gyear') {
    $gyear = $jyear;
    $jyear = strval(intval($jyear) + 3760);
    $jyear2 = $jyear;
} else {
    $gyear = '';
}
if (intval($jyear) < 5362 or intval($jyear) > 6059) { // not valid years
    echo '<P>The Jewish Year must be between 5362 and 6059 (Gregorian Years between 1600 and 2299)!<P>';
    echo $Pageft;

    include 'tradefoot.txt';

    include '../jg/footer.txt';
    mysqli_close($con);

    exit;
}

if (intval($gyear) > 0) {
    echo '<h2>Jewish Festival Dates in the Gregorian Year ' . $gyear . '</h2><BR>';
} else {
    echo '<h2>Jewish Festival Dates in the Jewish Year ' . $jyear . '</h2><BR>';
}

echo '<TABLE BGCOLOR="#E8E1D1" CELLPADDING=3 CELLSPACING=2 BORDER=2 WIDTH=95% ><TR BGCOLOR="#BEC49F"><TH>Festival / Fast</TH><TH>Jewish Date</TH><TH>Day of Week</TH><TH>Gregorian Date</TH></TR>';

$lcdouble = 0;
$html = '';
$html2 = '';

if (intval($gyear) === 0) { // jewish year
    $istart = 0;
    $iend = 12;
} else {
    $istart = 5;
    $iend = 17;
}
for ($ii = $istart; $ii < $iend; ++$ii) { // festivals loop
    if ($ii > 11) {
        $i = $ii - 12;
        $jyear = $jyear2 + 1;
    } else {
        $i = $ii;
    }
    $problemyear = 0;

    $tmp = $festivals[$i][2] . $festivals[$i][1] . $jyear;
    $sql = "SELECT gregorian,hebrew FROM josdates where hebrew='" . $tmp . "'";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result);
    $rowcount = mysqli_num_rows($result);
    if ($rowcount === 0) {  // must be Adar II not present
        $tmp = '12' . $festivals[$i][1] . $jyear;
        $sql = "SELECT gregorian,hebrew FROM josdates where hebrew='" . $tmp . "'";
        $result = mysqli_query($con, $sql);
        $row = mysqli_fetch_array($result);
        $jdate2 = substr($row['hebrew'], 2, 2) . ' Adar ' . substr($row['hebrew'], 4, 4);
    } else {
        $jdate2 = substr($row['hebrew'], 2, 2) . ' ' . $festivals[$i][3] . ' ' . substr($row['hebrew'], 4, 4);
    }

    if (substr($jdate2, 0, 1) === '0') { // leading 0
        $jdate2 = substr($jdate2, 1, 20);
    }
    $res3 = substr($row['gregorian'], 0, 2) . '/' . substr($row['gregorian'], 2, 2) . '/' . substr($row['gregorian'], 4, 4);

    $skiprow = 0;
    if ($ii === $istart and substr($row['gregorian'], 4, 4) < intval($gyear)) {
        // first entry in gregorian year is actually in previous year so need skip this entry and add extra run in loop
        $iend = $iend + 1;
        $skiprow = 1;
    }
    if ($ii === $iend - 1 and $gyear > 0 and substr($row['gregorian'], 4, 4) > intval($gyear)) {
        // last entry in gregorian year is actually in following year so need skip this entry
        $skiprow = 1;
    }

    $res3 = strtotime($res3);
    $dayofweek = date('l', $res3);
    // echo "day=".$dayofweek."<BR>";
    if ($dayofweek === 'Saturday' and $festivals[$i][4] != 0) {
        $tmp = $festivals[$i][2];
        $tmp .= str_pad(strval($festivals[$i][1] + $festivals[$i][4]), 2, '0', STR_PAD_LEFT);
        $tmp .= $jyear;
        // echo "tmp=".$tmp."<BR>";
        $sql = "SELECT gregorian,hebrew FROM josdates where hebrew='" . $tmp . "'";
        $result = mysqli_query($con, $sql);
        $row = mysqli_fetch_array($result);

        $jdate2 = substr($row['hebrew'], 2, 2) . ' ' . $festivals[$i][3] . ' ' . substr($row['hebrew'], 4, 4);
        if (substr($jdate2, 0, 1) === '0') { // leading 0
            $jdate2 = substr($jdate2, 1, 20);
        }
        $res3 = substr($row['gregorian'], 0, 2) . '/' . substr($row['gregorian'], 2, 2) . '/' . substr($row['gregorian'], 4, 4);
        $res3 = strtotime($res3);
    }
    $res4 = date('j F Y', $res3);

    if ($skiprow === 0) {
        $html = $html . '<TR><TD>' . $festivals[$i][0] . '</TD><TD>' . $jdate2 . '</TD><TD>';
        $html = $html . date('l', $res3) . '</TD><TD>' . $res4 . '</TD></TR>';
    }
}

if (strlen($html) > 0) {
    $html = substr($html, 0, strlen($html) - 10) . '<BR></TD></TR>';
}
echo $html;

echo '</TABLE></CENTER>';

echo '<CENTER><TABLE><TR><TD COLSPAN=2 NOWRAP ALIGN="RIGHT"><A HREF="/databases/josfest.php?caltype=';
echo $caltype . '&jyear=' . ($jyearorig - 1) . '"><IMG SRC="/images/ArrowBack.gif" BORDER=0 ALIGN=absmiddle>';
echo '<BR>' . ($jyearorig - 1) . '<BR>Previous Year</A></TD><TD COLSPAN=2 NOWRAP ALIGN="LEFT">';
echo '<A HREF="/databases/josfest.php?caltype=';
echo $caltype . '&jyear=' . ($jyearorig + 1) . '"><IMG SRC="/images/ArrowNext.gif" BORDER=0 ALIGN=absmiddle>';
echo '<BR>' . ($jyearorig + 1) . '<BR>Next Year</A></TD></TR></TABLE></CENTER>';

echo '<CENTER>Note that Jewish holidays begin at sundown of the previous day</CENTER><HR>';

    if (!$fragment) {
        echo $Pageft;
        include 'tradefoot.txt';
        include '../jg/footer.txt';
    }

mysqli_close($con);

?>

