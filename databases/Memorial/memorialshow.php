<?php

date_default_timezone_set('America/Denver');
$time_start = microtime(true);

// setup lengths of fields for editing
$fieldlength = [
    ['cemeteryid', '10'],
    ['oldid', '10'],
    ['cem_name', '100'],
    ['landsman', '120'],
    ['insystem', '6'],
    ['status1', '14'],
    ['status2', '13'],
    ['status3', '10'],
    ['country', '20'],
    ['city', '25'],
    ['state', '2'],
    ['street', '50'],
    ['usbgn_code', '12'],
    ['region', '20'],
    ['land_city', '25'],
    ['land_ctry', '20'],
    ['land_usbgn', '15'],
    ['land_regio', '25'],
    ['donor_id', '6'],
    ['donor_name', '80'],
    ['donor_emai', '60'],
    ['burials', '10'],
    ['photos', '10'],
    ['comments', '999'],
    ['instructs', '999'],
    ['date_prop', '20'],
    ['date_donor', '20'],
    ['date_recd', '20'],
    ['date_data', '20'],
    ['date_image', '20'],
    ['date_test', '20'],
    ['date_live', '20'],
    ['admin_note', '999'],
    ['entity', '11'],
    ['download', '1'],
    ['fulllist', '1'],
];

function reglookup($reg)
{
    $reg = strtoupper($reg);
    $newreg = $reg;

    switch ($reg) {
        case 'AL':
            $newreg = 'Alabama';

            break;

        case 'AK':
            $newreg = 'Alaska';

            break;

        case 'AZ':
            $newreg = 'Arizona';

            break;

        case 'AR':
            $newreg = 'Arkansas';

            break;

        case 'CA':
            $newreg = 'California';

            break;

        case 'CO':
            $newreg = 'Colorado';

            break;

        case 'CT':
            $newreg = 'Connecticut';

            break;

        case 'DE':
            $newreg = 'Delaware';

            break;

        case 'DC':
            $newreg = 'District of Columbia';

            break;

        case 'FL':
            $newreg = 'Florida';

            break;

        case 'GA':
            $newreg = 'Georgia';

            break;

        case 'HI':
            $newreg = 'Hawaii';

            break;

        case 'ID':
            $newreg = 'Idaho';

            break;

        case 'IL':
            $newreg = 'Illinois';

            break;

        case 'IN':
            $newreg = 'Indiana';

            break;

        case 'IA':
            $newreg = 'Iowa';

            break;

        case 'KS':
            $newreg = 'Kansas';

            break;

        case 'KY':
            $newreg = 'Kentucky';

            break;

        case 'LA':
            $newreg = 'Louisiana';

            break;

        case 'ME':
            $newreg = 'Maine';

            break;

        case 'MD':
            $newreg = 'Maryland';

            break;

        case 'MA':
            $newreg = 'Massachusetts';

            break;

        case 'MI':
            $newreg = 'Michigan';

            break;

        case 'MN':
            $newreg = 'Minnesota';

            break;

        case 'MS':
            $newreg = 'Mississippi';

            break;

        case 'MO':
            $newreg = 'Missouri';

            break;

        case 'MT':
            $newreg = 'Montana';

            break;

        case 'NE':
            $newreg = 'Nebraska';

            break;

        case 'NV':
            $newreg = 'Nevada';

            break;

        case 'NH':
            $newreg = 'New Hampshire';

            break;

        case 'NJ':
            $newreg = 'New Jersey';

            break;

        case 'NM':
            $newreg = 'New Mexico';

            break;

        case 'NY':
            $newreg = 'New York';

            break;

        case 'NC':
            $newreg = 'North Carolina';

            break;

        case 'ND':
            $newreg = 'North Dakota';

            break;

        case 'OH':
            $newreg = 'Ohio';

            break;

        case 'OK':
            $newreg = 'Oklahoma';

            break;

        case 'OR':
            $newreg = 'Oregon';

            break;

        case 'PA':
            $newreg = 'Pennsylvania';

            break;

        case 'PR':
            $newreg = 'Puerto Rico';

            break;

        case 'RI':
            $newreg = 'Rhode Island';

            break;

        case 'SC':
            $newreg = 'South Carolina';

            break;

        case 'SD':
            $newreg = 'South Dakota';

            break;

        case 'TN':
            $newreg = 'Tennessee';

            break;

        case 'TX':
            $newreg = 'Texas';

            break;

        case 'UT':
            $newreg = 'Utah';

            break;

        case 'VT':
            $newreg = 'Vermont';

            break;

        case 'VA':
            $newreg = 'Virginia';

            break;

        case 'WA':
            $newreg = 'Washington';

            break;

        case 'WI':
            $newreg = 'Wisconsin';

            break;

        case 'WV':
            $newreg = 'West Virginia';

            break;

        case 'WY':
            $newreg = 'Wyoming';

            break;

        case 'VET-NONVA':
            $newreg = 'Veteran Burials - Other than Federal or State Cemeteries';

            break;

        case 'AB':
            $newreg = 'Alberta';

            break;

        case 'BC':
            $newreg = 'British Columbia';

            break;

        case 'MB':
            $newreg = 'Manitoba';

            break;

        case 'NB':
            $newreg = 'New Brunswick';

            break;

        case 'NL':
            $newreg = 'Newfoundland and Labrador';

            break;

        case 'NS':
            $newreg = 'Nova Scotia';

            break;

        case 'NT':
            $newreg = 'Northwest Territories';

            break;

        case 'NU':
            $newreg = 'Nunavut';

            break;

        case 'ON':
            $newreg = 'Ontario';

            break;

        case 'PE':
            $newreg = 'Prince Edward Island';

            break;

        case 'QC':
            $newreg = 'Québec';

            break;

        case 'SK':
            $newreg = 'Saskatchewan';

            break;

        case 'YT':
            $newreg = 'Yukon';

            break;
    }

    return $newreg;
}

if (isset($_GET['id'])) {
    $id = trim($_GET['id']);
}
if (isset($_GET['ID'])) {
    $id = trim($_GET['ID']);
}

if (stripos($id, 'script')) {
    exit;
}
if (stripos($id, '<')) {
    exit;
}
if (strlen($id) > 10) {
    exit;
}

$num_rows = 0;

if ($id !== '') {
    // Create connection
    $con = mysqli_connect('jewishgen13:3306', 'mtobias', 'nt732b#$', 'jewishgen');

    // Check connection
    if (mysqli_connect_errno()) {
        echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
    }
    $table = 'memorial';
    $control_row = "SELECT * FROM {$table} where cemeteryid='" . $id . "'";

    $control = mysqli_query($con, $control_row);
    $num_rows = mysqli_num_rows($control);
    $row = mysqli_fetch_array($control);
    mysqli_close($con);
}

if ($num_rows === 0) {
    exit;
}

$html = "<HTML>\r\n<HEAD>\r\n";
$html .= '   <TITLE>JewishGen Memorial Display - ' . $id . "</TITLE> \r\n";

echo $html;

include '../../JG/HeadSection.txt';

$html = "</HEAD> \r\n\r\n<BODY> \r\n";
echo $html;

include '../../JG/Header.txt';

?>

<CENTER>
<H1><A href="/databases/Memorial/" STYLE="text-decoration: none">JewishGen Memorial Plaque Database</A></H1>
<H2>Synagogue/Society Information</H2>
</CENTER>


<CENTER>
<TABLE BGCOLOR="#E8E1D1" CELLPADDING="3" CELLSPACING="2" BORDER="2" WIDTH="95%"
       RULES="rows">

<TR BGCOLOR="#BEC49F">
<TH COLSPAN="2">Synagogue/Society Identification</TH>
</TR>

<TR><TD WIDTH="25%"><B>Synagogue/Society ID</B>: </TD>
<?php
if ($id !== '') {
    echo '<TD>' . $row[$fieldlength[0][0]];
}
?>

</TD></TR>

<TR><TD><B>Synagogue/Society Name</B>: </TD>
    <TD>
<?php
echo $row[$fieldlength[2][0]];
?>
</TD></TR>

<TR><TD><B>Section</B>: </TD>
    <TD>
<?php
echo $row[$fieldlength[3][0]];
?>
</TD></TR>

<TR BGCOLOR="#BEC49F">
<TH COLSPAN="2">Synagogue/Society Location</TH>
</TR>
<TR><TD><B>Country</B>: </TD>
<TD>
<?php
echo $row[$fieldlength[8][0]];
?>
</TD></TR>

<?php
if ($row[$fieldlength[10][0]] !== '') {
    echo '<TR><TD><B>State</B>:</TD><TD>' . reglookup($row[$fieldlength[10][0]]) . '</TD></TR>';
}

?>


<TR><TD><B>City</B>: </TD>

<?php
$smlink = '<TD>' . $row[$fieldlength[9][0]];
if ($row[$fieldlength[12][0]] !== '') {
    $smlink = '<TD class="shtet" usbgn="' . $row[$fieldlength[12][0]] . '">';
    $smlink .= $row[$fieldlength[9][0]] . ' &nbsp; <A HREF="/Communities/community.php?usbgn=';
    $smlink .= $row[$fieldlength[12][0]] . '"><img src="/images/favicon.gif" border="0" ';
    $smlink .= 'Title="More Info about ' . $row[$fieldlength[9][0]] . '"></A>';
}

echo $smlink;
?>
</TD></TR>

<TR><TD><B>Street</B>: </TD>
    <TD>
<?php
echo $row[$fieldlength[11][0]];
?>
</TD></TR>



<TR BGCOLOR="#BEC49F">
<TH COLSPAN="2">Synagogue/Society Details</TH>
</TR>

<TR><TD><B>Number of Memorials</B>: </TD>
    <TD>
<?php
echo $row[$fieldlength[21][0]];
?>
</TD></TR>

<TR><TD><B>Number of Photographs</B>: </TD>
    <TD>
<?php
echo $row[$fieldlength[22][0]];
?>
</TD></TR>

<TR><TD><B>Synagogue/Society Description</B>:</TD>
    <TD>
<?php
echo $row[$fieldlength[23][0]];
?>
</TD></TR>




<TR><TD><B>Data last updated</B>: </TD>
    <TD>
<?php
// date("j M Y", strtotime("2011/07/01"))
echo date('m/d/Y', strtotime(str_replace('0000-00-00', '', str_replace('1899-12-30', '', str_replace(' 00:00:00', '', $row[$fieldlength[31][0]])))));
?>
</TD></TR>



</TABLE>
</CENTER>

<SCRIPT src="/Ajax/popUpHypertext.js" type="text/javascript"></SCRIPT>
<HR>

<?php
include 'footer.txt';

include '../../JG/Footer.txt';
?>



