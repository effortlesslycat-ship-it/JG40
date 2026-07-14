<?php

include_once '\\\\jewishgen6\\JEWISHGEN\\wwwroot\\BlockScript/detector.php';

include_once '..\\cureetc.php';

date_default_timezone_set('America/Denver');

$jgid = require_userinfo();

/*
Override user's ID from query string? No way GSandler 14 Aug 2021 TODO
if (isset($_GET['jgid']))  {
    $jgid=$_GET['jgid'];
}
*/

if (1 === 1) {
    // id = log id to make a favorite, rid = log id to remove favored status
    $id = $_GET['id'];
    $rid = $_GET['rid'];

    $con = mysqli_connect('jewishgen13:3306', 'mtobias', 'nt732b#$', '');

    // Check database connection
    if (($con instanceof mysqli) === false) {
        return [status => false, message => 'MySQL connection is invalid'];
    }

    if (strlen($id) > 0) {
        // first check that this search was done by this researcher - so jgid must match or allow blank.
        $sql = 'select jgid from jewishgen.jg_log where log_id =' . $id;
        // Execute query and save data
        $result = mysqli_query($con, $sql);
        $row = mysqli_fetch_row($result);
        $jgidrow = $row[0];
        if ($jgid === $jgidrow or strlen($jgidrow) === 0) {
            $sql = "UPDATE jewishgen.jg_log set fave=1, jgid='" . $jgid . "' where log_id =" . $id;
            // Execute query and save data
            $result = mysqli_query($con, $sql);
        }
    }
    if (strlen($rid) > 0) {
        // first check that this search was done by this researcher - so jgid must match (cannot be blank as would be set when saved)
        $sql = 'select jgid from jewishgen.jg_log where log_id =' . $rid;
        // Execute query and save data
        $result = mysqli_query($con, $sql);
        $row = mysqli_fetch_row($result);
        $jgidrow = $row[0];
        if ($jgid === $jgidrow) {
            $sql = 'UPDATE jewishgen.jg_log set fave=0 where log_id =' . $rid;
            // Execute query and save data
            $result = mysqli_query($con, $sql);
        }
    }

    $sql2 = "SELECT savedtitle, savedsearch, log_id, log_date FROM jewishgen.jg_log where jgid ='" . $jgid . "' and fave>0 order by log_date desc";

    // Execute query and save data
    $result = mysqli_query($con, $sql2);
    $rowcount = mysqli_num_rows($result);

    mysqli_close($con);

    echo '<HTML><HEAD><TITLE>JewishGen Saved Searches</TITLE>';

    include '../../JG/HeadSection.txt';

    echo '</HEAD><BODY>';

    include '../../JG/Header.txt';

    if ($rowcount > 0) {
        $display = '<CENTER><H1>Here is a list of your ' . $rowcount . ' saved search' . ($rowcount > 1 ? 'es</H1>' : '</H1>');

        $display .= '<SCRIPT language="JavaScript" src="/JewishGen/js/HTMLentities.js"></SCRIPT>';
        $display .= '<SCRIPT language="JavaScript" src="/JewishGen/js/TableSort.js"></SCRIPT>';

        $display .= "<P><TABLE class='jgtable' ID='favTable' CELLPADDING='3' CELLSPACING='2' BORDER='1'>";

        $display .= '<THEAD><TR><TH>Search</TH><TH>Date/Time</TH><TH>Link</TH><TH>Remove from List</TH></TR></THEAD><TBODY>';

        while ($row = mysqli_fetch_array($result)) {
            $tmprw = $row['savedtitle'];
            $rw = $tmprw;
            $rw1 = '<I>' . substr($rw, 0, stripos($rw, '<BR>'));
            // echo $rw1."<BR>";
            $rw2 = substr($rw, stripos($rw, '<BR>') + 4);
            // echo $rw2."<BR>";
            // if (stripos($rw2,'Searching for ')===false) {
            //		$rw2="<BR>Searching for ".$rw2;
            // }
            // else {
            //		$rw2="<BR>".$rw2;
            // }
            $rw2 = '<BR>' . str_ireplace('Searching for ', '', $rw2);
            $rw = $rw1 . '</I>' . $rw2;
            $rw = str_ireplace('</I><BR>All Poland Database', '<BR>All Poland Database</I>', $rw);

            $rwlen1 = strlen($rw);
            $rwlen2 = strlen(str_ireplace(') : ', '', $rw));
            $rwitems = ($rwlen1 - $rwlen2) / 4;
            // echo $rwitems."<BR>";

            for ($i = 0; $i < $rwitems; ++$i) {
                $tmprw1 = stripos($tmprw, ') : ');
                $tmprw = substr($tmprw, $tmprw1 + 4);
                $tmprw3a = stripos($tmprw, '<');
                // $tmprw3b=stripos($tmprw,"<BR>");
                // echo $tmprw1."<BR>";
                // echo $tmprw."<BR>";
                // echo $tmprw3a." a<BR>";
                // echo $tmprw3b." b<BR>";
                // if ($tmprw3b>0 AND $tmprw3a>0 AND $tmprw3b<$tmprw3a) {
                //	$tmprw3=$tmprw3b;
                // }
                // else {
                //	$tmprw3=$tmprw3a;
                // }
                // echo $tmprw3."<BR>";
                if ($i < $rwitems - 1) {
                    $tmprw2 = substr($tmprw, 0, $tmprw3a);
                } else {
                    $tmprw2 = $tmprw;
                }
                if (stripos($tmprw, '<BR>')) {
                    $tmprw2 = substr($tmprw2, 0, stripos($tmprw, '<BR>'));
                }

                if (substr($tmprw2, strlen($tmprw2) - 4, 4) === ' AND') {
                    $tmprw2 = substr($tmprw2, 0, strlen($tmprw2) - 4);
                }
                // echo $tmprw2." ".strlen($tmprw2)." ".substr($tmprw2,strlen($tmprw2)-3,4)."<BR>";
                // $tmprw=substr($tmprw,stripos($tmprw," ")+1);
                $tmprw2a = ') : ' . $tmprw2;
                $tmprw2b = ') : “' . $tmprw2 . '”';
                // echo $tmprw2a."<BR>";
                // echo $tmprw2b."<BR>";
                // echo strlen($rw)."<BR>";
                $rw = str_ireplace($tmprw2a, $tmprw2b, $rw);
                // echo strlen($rw)."<BR>";
                // echo $rw."<BR>";
                // echo ') : '.$tmprw2."<BR>";
                // echo stripos($rw,') : '.$tmprw2)."<BR>";
            }
            // echo $rw."<BR>";

            // $rw="<I>".substr($rw,0,stripos($rw,'<BR>'));
            // $rw="</I>".$rw.substr($rw,stripos($rw,'<BR>'));
            // $rw.substr($row['savedtitle'],stripos($row['savedtitle'],'<BR>'));

            $display .= '<TR><TD>' . $rw . '</TD>';
            $display .= '<TD>' . $row['log_date'] . '</TD>';
            $display .= '<TD ALIGN="CENTER"><A HREF="' . $row['savedsearch'] . '&saved=1" target="_blank">Repeat Search</TD>';
            $display .= '<TD ALIGN="CENTER"><A HREF="/databases/searches/favsearch.php?rid=' . $row['log_id'] . '" target="_blank">Remove Search</TD></TR>';
        }

        $display .= '</TBODY></TABLE></center>';

        $display .= "<SCRIPT>initSortTable('favTable', Array('S','N',false,false)); </SCRIPT>";

        echo $display;
    } else { // no records
        echo '<CENTER><H1>You have no saved searches</H1>';
    }

    include '../../databases/footer.new.txt';

    include '../../JG/footer.txt';
}
