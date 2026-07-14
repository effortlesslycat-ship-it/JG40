<?php
// include_once("\\\jewishgen6\JEWISHGEN\wwwroot\blockscript\detector.php");
include_once '../databases/cureetc.php';

include '../databases/bootstrap.php';

include '../databases/msbootstrap.php';

include '_bootstrap.php';

$time_start = microtime(true);

$jgid = require_userinfo();

$code = !isset($_GET['code']) ? '' : $_GET['code'];
$add = !isset($_GET['add']) ? 'N' : $_GET['add'];
if (strlen($code) === 0) {
    $code = $jgid;
}

// echo $code;

// Check for admin user to view any user's entries:
$system = 'JGFF';
$access = check_permission($system, $jgid);

if ($access !== 'A' and $access !== 'E' and $code !== $jgid) {
    return;
}

$html = '<HTML>';
$html .= "<HEAD>\r\n<META http-equiv='Content-Type' content='text/html; charset=windows-1250'>";
$html .= "<TITLE>JewishGen Family Finder</TITLE></HEAD>\r\n<BODY>";

echo $html;

include '../JG/headsection.txt';

include '../JG/header.txt';

// include "jgffadvert.htm";

$html = '';
$html .= '<CENTER><H1>Your JewishGen Family Finder data</H1>';
$html .= "<H2>Researcher Number {$code}</H2></CENTER>\r\n";
echo $html;
$html = '';

// Get country list, format into a <SELECT> pulldown:
$con = mysqli_connect(MYSQL_SERVER_HOSTNAME, MYSQL_SERVER_USERNAME, MYSQL_SERVER_PASSWORD, MYSQL_SERVER_DATABASE);
$country2_sql = 'SELECT country,name FROM country2 order by name';
$country2 = mysqli_query($con, $country2_sql);
$country_list = '<option value="NONE">Select Country</option>';
// $country_list = '<option value="Any">Any Country</option>';
while ($country2_row = mysqli_fetch_row($country2)) {
    $country_list .= '<option value="' . trim($country2_row[0]) . '">' . $country2_row[1] . '</option>';
}
$country_list = rtrim($country_list) . '</select>';
mysqli_close($con);

include 'jgffviewmysqltopadd.htm';

$html .= '<P><CENTER><TABLE BGCOLOR="#E8E1D1" CELLPADDING=3 CELLSPACING=2 BORDER=2 WIDTH=95%>';
$html .= '<TR BGCOLOR="#BEC49F"><TH>Entry</TH><TH>Surname</TH><TH colspan=2>Town, Country</TH></TR>';

if ($add !== 'Y') { // EDIT existing entries:
    // Fetch existing entries:
    $con = mysqli_connect(MYSQL_SERVER_HOSTNAME, MYSQL_SERVER_USERNAME, MYSQL_SERVER_PASSWORD, MYSQL_SERVER_DATABASE);
    $sql = "SELECT 
		jewishgen.jgffdata.code,surname,town,
		jewishgen.jgfftowns.country,
		jewishgen.jgfftowns.id as townid,
		jewishgen.jgfftowns.usbgn as usbgn,
		jewishgen.jgfftowns.communitypage,
		jewishgen.jgffdata.id 
		FROM jewishgen.jgffdata 
		JOIN jewishgen.jgfftowns on jewishgen.jgffdata.townid=jewishgen.jgfftowns.id 
		WHERE jewishgen.jgffdata.code='" . $code . "' order by surname";

    // Execute query:
    $result = $con->query($sql);

    $ctr = 0;
    while ($row = mysqli_fetch_row($result)) {
        $ctr = $ctr + 1;

        $dataid = trim($row[7]);

        $html .= '<TR><TD align="CENTER">';
        //		if ($jgid==='1173') {
        //			$html .= $dataid . " &nbsp; ";
        //		}
        $html .= $ctr . '<input name="id' . $ctr . '" value="' . $dataid . '" type ="hidden"></TD>';
        $html .= '<TD align="CENTER"><input type="text" name="sur' . $dataid . '" maxlength="20" size="14" value="' . trim($row[1]) . '" ></TD>';
        $html .= '<TD colspan=2 ALIGN=CENTER><SPAN ID="town' . $ctr . '_P" class="shtet" usbgn="-1">';
        $html .= '<A><img WIDTH="16" HEIGHT="16" border="0" ID="town' . $ctr . '_I" style="visibility:hidden"></A> ';
        $html .= '<INPUT TYPE="text" NAME="town' . $dataid . '" ID="town' . $ctr . '_T" class="jgffi" maxlength="40" size="17" VALUE="' . trim($row[2]) . '" onchange="JGFFDataChanged(this)">,&nbsp;';

        $ctry = str_replace(
            'option value="' . trim($row[3]) . '">',
            'option value="' . trim($row[3]) . '" SELECTED>',
            $country_list
        );

        $html .= '<SELECT name="Count' . $dataid . '" ID="town' . $ctr . '_C" onchange="JGFFDataChanged(this)" onBlur="JGFFDataChanged(this)" size=1>' . $ctry . ' </SPAN>';

        if ($row[6] === 'No') {
            $html .= '<INPUT name="origusbgn' . $dataid . '" value="NO" type="hidden">';
        } else {
            $html .= '<INPUT name="origusbgn' . $dataid . '" value="' . trim($row[5]) . '" type="hidden">';
        }
        $html .= '<INPUT name="origsur' . $dataid . '" value="' . trim($row[1]) . '" type="hidden">';
        $html .= '<INPUT name="origtown' . $dataid . '" value="' . trim($row[2]) . '" type="hidden">';
        $html .= '<INPUT name="origCount' . $dataid . '" value="' . trim($row[3]) . '" type="hidden">';

        $html .= "</TD></TR>\r\n";
    }

    mysqli_close($con);
    $html .= '</table>';
    $html .= '<P><H2>Press the button below to submit your modified entries to the JGFF</H2>';
    if ($ctr === 0) { // no entries
        // Why use JavaScript instead of Header()???
        echo '<script type="text/javascript">window.location = "https://www.jewishgen.org/jgff/jgffviewadd.php?add=Y" </script>';

        return;

        exit;
    }
} else {	// ADD new entries:
    //   Let's present 15 blank entry rows:

    $ctr = 0;
    while ($ctr < 15) {
        $ctr = $ctr + 1;

        $html .= '<TR><TD align="CENTER">' . $ctr . '</TD>';
        $html .= '<TD align="CENTER"><input type="text" name="sur' . trim($ctr) . '" maxlength="20" size="14" value=""></TD>';
        $html .= '<TD colspan=2 ALIGN=CENTER><SPAN ID="town' . $ctr . '_P" class="shtet" usbgn="-1"> ';
        $html .= '<A><img WIDTH="16" HEIGHT="16" border="0" ID="town' . $ctr . '_I" style="visibility:hidden"></A> ';
        $html .= '<INPUT TYPE="text" NAME="town' . $ctr . '" ID="town' . $ctr . '_T" class="jgffi" maxlength="40" size="17" VALUE="" onchange="JGFFDataChanged(this)">,&nbsp;';

        $html .= '<SELECT name="Count' . $ctr . '" ID="town' . $ctr . '_C" onchange="JGFFDataChanged(this)" onBlur="JGFFDataChanged(this)" size=1>' . $country_list . ' </SPAN>';

        $html .= "</TD></TR>\r\n";
    }
    $html .= '</table>';
    $html .= '<P><H2>Press the button below to submit your new entries to the JGFF</H2>';
}

$html .= '<INPUT TYPE="hidden" name="code" value="' . $code . '">';	// JGID#
$html .= '<INPUT TYPE="hidden" name="recs" value="' . $ctr . '">';	// Total # of entries
$html .= '<INPUT TYPE="hidden" name="add"  value="' . $add . '">';	// "Y" for Adding New entries

$html .= '<TABLE BORDER="0"><TR VALIGN="top">';
$html .= '<TD><INPUT TYPE="Submit" VALUE="Submit"></FORM></TD>';
$html .= '<TD> &nbsp; &nbsp; &nbsp; </TD>';
$html .= '<TD><FORM Method="POST" Action="/jgff/">';
$html .= '<INPUT TYPE="Submit" VALUE="Cancel">';
$html .= '</FORM></TD>';
$html .= '</TR></TABLE>';
$html .= '</CENTER><HR>';

echo $html;

echo '<SCRIPT src="/Ajax/popUpHypertext.js" type="text/javascript"></SCRIPT>';
echo '<SCRIPT src="/Ajax/JGFFTownEntry.js"  type="text/javascript"></SCRIPT>';

include 'footer.txt';

include '../JG/footer.txt';

$this_ip = get_client_ip();
$time_end = microtime(true);
$duration = $time_end - $time_start;

// if dont want savesearch version of logging then call write_mysql_jg2log instead
write_mysql_jglog($duration, $jgid, $this_ip);

?>

