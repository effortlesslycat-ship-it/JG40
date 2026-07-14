<?php
// include_once("\\\jewishgen6\JEWISHGEN\wwwroot\blockscript\detector.php");
include_once '../databases/cureetc.php';

include '../databases/bootstrap.php';

include '../databases/msbootstrap.php';

include '_bootstrap.php';

$time_start = microtime(true);

$code = $_POST['code'];
$recs = $_POST['recs'];
$add = !isset($_POST['add']) ? 'N' : $_POST['add'];
// echo "recs=".$recs."<BR>";

if (empty($_POST)) { // called incorrectly
    $html = '<html>';

    $html .= "<HEAD><meta http-equiv='Content-Type' content='text/html; charset=windows-1250'><TITLE>JewishGen Family Finder</TITLE></HEAD><BODY>";

    echo $html;

    include '../jg/headsection.txt';

    include '../jg/header.txt';

    echo '<BR>No data entered. Please try again.';

    include 'footer.txt';

    include '../jg/footer.txt';

    return;

    exit;
}

$jgid = require_userinfo();

if (strlen($code) === 0) {
    $code = $jgid;
}

// echo $code;

$admin = 'N';
$system = 'JGFF';

$access = check_permission($system, $jgid);

if ($access !== 'A' and $code !== $jgid) {
    return;
}
if ($access === 'A' or $access === 'E') {
    $admin = 'Y';
}

// Get user information from GoldMine database:
$gconn = sqlsrv_connect($MSserverName, $optionsGoldmine);
if (!$gconn) {
    echo "Connection to GoldMine could not be established.\n";

    exit(print_r(sqlsrv_errors(), true));
}

$gquery = "SELECT dbo.CONTACT1.CONTACT AS name, dbo.CONTACT1.KEY3 AS code, ISNULL(dbo.CONTACT2.UEMAIL_PRI, '') AS email, ISNULL(dbo.CONTACT1.KEY5, '') AS donortypes " .
            ' FROM dbo.CONTACT1 INNER JOIN dbo.CONTACT2 ON dbo.CONTACT1.ACCOUNTNO = dbo.CONTACT2.ACCOUNTNO WHERE dbo.Contact1.key3 = ? ';
$gparam = [$code];
$stmt = sqlsrv_query($gconn, $gquery, $gparam);
if ($stmt === false) {
    echo "Error in executing GoldMine query.\n";

    exit(print_r(sqlsrv_errors(), true));
}
$row = sqlsrv_fetch_array($stmt);
$email = $row['email'];  // email confirmation here
$name = $row['name'];
$donortypes = $row['donortypes']; // if doesn't include 'J', then add 'J' after adding JGFF data records

// Free statement and connection resources:
sqlsrv_free_stmt($stmt);
// sqlsrv_close( $gconn);

// echo $email."<BR>";
// echo $donortypes."<BR>";

$con = mysqli_connect(MYSQL_SERVER_HOSTNAME, MYSQL_SERVER_USERNAME, MYSQL_SERVER_PASSWORD, MYSQL_SERVER_DATABASE);
$spaces_sql = 'SELECT allowed FROM spaces';
$spaces = mysqli_query($con, $spaces_sql);
$spaces_list = ',';
while ($space_row = mysqli_fetch_row($spaces)) {
    $spaces_list .= $space_row[0] . ',';
}

// Get the list of all countries from the database;
//   Create an associative array, for quick lookups.
//   (See function "ctryname", below).
$country2_sql = 'SELECT country,name FROM country2 order by country';
$country2 = mysqli_query($con, $country2_sql);
while ($country2_row = mysqli_fetch_row($country2)) {
    $country_id = trim($country2_row[0]);
    $countries[$country_id] = $country2_row[1];
}

$jgffrejects_sql = 'SELECT country as inputcountry,area,title,shtetls as shtetlsfilter FROM jgffrejects';
$jgffrejects = mysqli_query($con, $jgffrejects_sql);
$jgffrejects_list = '';
while ($jgffrejects_row = mysqli_fetch_row($jgffrejects)) {
    $jgffrejects_list .= '[country]' . $jgffrejects_row[0] . '[/country][area]' . $jgffrejects_row[1] . '[/area][title]' . $jgffrejects_row[2] . '[/title][shtetlsfilter]' . $jgffrejects_row[3] . '[/shtetlsfilter]';
}

// mysqli_close($con);

// echo $jgffrejects_list."<BR>";

// echo $spaces_list;  // list of partial surnames that are allowed to contain spaces

// add javascript validation to view form to set min length surname 2 and dont allow wildcards in surnames or towns.
// also dont allow blank rows - no surname and no town
// also dont allow a town to be entered with 'Any Country'

// also dont allow a dupe surname/town/country

// can we do VAS JGFF match check? Perhaps we have to run a batch job every hour/day that checks all records added/edited in past hour/day?
// we could do VAS by calling Solr JGFF system. We also need to do this to create list of possible matches where town name not recognised.
// but what about Communities database - we really need to search that too for possibles.... do we need to setup a Solr index for that too?
// and need NRT update of Solr when communties sql data changes. Of course need NRT jgff updates too.
//
// we dont need NRT.  we just update BOTH mySQL AND Solr in parallel. You can edit Solr, Add and delete via php.
// would prob want to do a full Solr index from MySQL regularly in case things have gone out of synch.
// can then do proper DELETES not just blanks.  For ADDs (not yet implemented) we have slight problem. Would need to add to MySQL, then query MySQL to
// get id (autoinc!) so we can then add to Solr with correct id. Solr php adds need overwrite set to yes for updates.
//

// for emails
// Always set content-type when sending HTML email
$headers0 = 'MIME-Version: 1.0' . "\r\n";
$headers0 .= 'Content-type:text/html;charset=UTF-8' . "\r\n";
$headers = 'From: JGFF <jgffhelp@jewishgen.org>' . "\r\n";
$headers .= 'X-Mailer: PHP/' . phpversion();
$Foxheaders = 'From: JGFFALERT <jgffalert@jewishgen.org>' . "\r\n";
$Foxheaders .= 'Cc: JGFFAlertAdmin@jewishgen.org' . "\r\n" . 'X-Mailer: PHP/' . phpversion();

ini_set('max_execution_time', 300); // 300 seconds = 5 minutes

$html = '<html>';

$html .= "<HEAD><meta http-equiv='Content-Type' content='text/html; charset=windows-1250'><TITLE>JewishGen Family Finder</TITLE></HEAD><BODY>\r\n";

echo $html;

include '../jg/headsection.txt';

include '../jg/header.txt';

$html = '';

echo '<BR>';

// include "jgffadvert.htm";

$html .= '<h1><CENTER>Your JewishGen Family Finder data</CENTER></h1>';
$html .= '<H2><CENTER>Researcher Number ' . $code . '</CENTER></H2>';
echo $html;
$html = '';

$html .= '<P><CENTER><TABLE BGCOLOR="#E8E1D1" CELLPADDING=3 CELLSPACING=2 BORDER=2 WIDTH=95% >' . "\r\n";
$html .= '<TR BGCOLOR="#BEC49F"><TH><B>Entry</B></TH><TH><B>Surname</B></TH><TH><B>Town</B></TH><TH><B>Country</B></TH><TH><B>Result</B></TH></TR>' . "\r\n";

// $con=mysqli_connect("jewishgen13:3306","mtobias","nt732b#$","jewishgen");
$client_JGFF = new SolrClient($options_JGFF);
$client_communities = new SolrClient($options_communities);

$rejects = 0;
$surncount = 0;

for ($x = 1; $x <= $recs; ++$x) {
    if ($add === 'Y') {
        $rn = $x; // need the autoid value once added - reset later
        $origs = '!A!A!A!A!';
        $origt = '!A!A!A!A!';
        $origc = '!A!A!A!A!';
        $origu = '!A!A!A!A!';
        $u = $origu; // need to lookup new usbgn at some point
    } else {
        $rn = $_POST['id' . $x];
        $origs = str_replace("'", '', iconv('UTF-8', 'ASCII//TRANSLIT', trim($_POST['origsur' . $rn])));
        $origt = str_replace("'", '', iconv('UTF-8', 'ASCII//TRANSLIT', trim($_POST['origtown' . $rn])));
        $origc = str_replace("'", '', iconv('UTF-8', 'ASCII//TRANSLIT', trim($_POST['origCount' . $rn])));
        $origu = str_replace("'", '', iconv('UTF-8', 'ASCII//TRANSLIT', trim($_POST['origusbgn' . $rn])));
        $u = $origu; // need to lookup new usbgn at some point
    }

    $s = ucwords(strtolower(str_replace("'", '', iconv('UTF-8', 'ASCII//TRANSLIT', trim($_POST['sur' . $rn])))));
    $t = ucwords(strtolower(str_replace("'", '', iconv('UTF-8', 'ASCII//TRANSLIT', trim($_POST['town' . $rn])))));
    // check no double spaces entered for USA towns/states
    $t = str_replace(',  ', ', ', $t);
    // check spaces before comma entered for USA towns/states
    $t = str_replace(' , ', ', ', $t);

    if ($jgid === '5200') {
        echo $_POST['sur' . $rn] . ' -> ' . $s . '<BR>';
    }

    if (strlen($t) === 0) {
        $t = 'Any';
    }
    $c = str_replace("'", '', iconv('UTF-8', 'ASCII//TRANSLIT', trim($_POST['Count' . $rn])));

    if (strlen($s) > 0) { // not blank surname in edit or add
        ++$surncount;

        // check for synonym - do i need this? WB ajax appears to auto-correct these!
        $t2 = '';
        $c2 = '';
        $t1a = '';
        $c1a = '';
        $synonym = 0;

        if ($origt !== $t || $origc !== $c) { // at least 1 of town/country has changed
            $con99 = mysqli_connect(MYSQL_SERVER_HOSTNAME, MYSQL_SERVER_USERNAME, MYSQL_SERVER_PASSWORD, MYSQL_SERVER_DATABASE);
            $syn_sql = "SELECT townid FROM jgffsynonyms where (town ='" . $t . "' AND country ='" . $c . "')";
            $synid = mysqli_query($con99, $syn_sql);
            // echo mysqli_num_rows($synid);

            if (mysqli_num_rows($synid) === 1) { // found synonym
                $syn_row = mysqli_fetch_row($synid);
                $syn_id = $syn_row[0];
                $syn_sql = "SELECT town, country FROM jgfftowns where id ='" . $syn_id . "'";
                $synid = mysqli_query($con99, $syn_sql);
                $syn_row = mysqli_fetch_row($synid);
                $t1a = $t;
                $c1a = $c;
                $t1b = $syn_row[0];
                $c1b = $syn_row[1];
                $synonym = 1;
            }
            mysqli_close($con99);
        }

        // need to lookup townid and also new usbgn after update
        $changed = 0;
        if ($origs !== $s || $origt !== $t || $origc !== $c || $add === 'Y') { // at least 1 of surname/town/country has changed
            // if ($code='5200') {
            //	echo "origs=".$origs." news=".$s." origt=".$origt." newt=".$t." origc=".$origc." newc=".$c."<BR>";
            // }
            $changed = 1;
            $u_new = '';
            $jgff_sql = "SELECT id, usbgn, town FROM jgfftowns where (town ='" . $t . "' AND country ='" . $c . "')";
            // echo $jgff_sql."<BR>";
            $jgffid = mysqli_query($con, $jgff_sql);
            // if ($code==='5200') {
            //	echo "sql=".$jgff_sql."<BR>";
            //	echo "rows=".mysqli_num_rows($jgffid)."<BR>";
            // }
            $found = 0;
            if (mysqli_num_rows($jgffid) === 1) { // found entry
                $jgff_row = mysqli_fetch_row($jgffid);
                $jgff_id = $jgff_row[0];
                $u = $jgff_row[1];
                $t = $jgff_row[2];	// change case to precise entry in jgfftowns
                // if had found a synonym entry (probably accented but same spelling) then ignore
                $found = 1;
            } else { // previously unseen JGFF entry - need to check for possible alternatives
                $jgff_id = 'unknown';
                $u = '';
            }
            if ($synonym === 1 and $found === 0) { // apply synonym if town.country not in JGFF
                $t = $t1b;
                $c = $c1b;
                // and look up ids of synonym
                $jgff_sql = "SELECT id, usbgn FROM jgfftowns where (town ='" . $t1b . "' AND country ='" . $c1b . "')";
                $jgffid = mysqli_query($con, $jgff_sql);
                if (mysqli_num_rows($jgffid) === 1) { // found entry
                    $jgff_row = mysqli_fetch_row($jgffid);
                    $jgff_id = $jgff_row[0];
                    $u = $jgff_row[1];
                }
            }
            if ($synonym === 1 and $found === 1) { // ignore synonym
                $synonym = 0;
            }
        } else { // if not changed still need townid!
            $jgff_sql = "SELECT id, usbgn, town FROM jgfftowns where (town ='" . $origt . "' AND country ='" . $origc . "')";
            // echo $jgff_sql."<BR>";
            $jgffid = mysqli_query($con, $jgff_sql);
            $jgff_row = mysqli_fetch_row($jgffid);
            $jgff_id = $jgff_row[0];
            $t = $jgff_row[2];	// change case to precise entry in jgfftowns
        }

        $sql = 'No Change';

        $html2 = '<TR><TD><B>' . $x . '</B></TD><TD><B>';
        if (strtoupper($s) === 'DELETE') {
            // if ($add!=="Y") {
            $html2 .= $origs . ' &rarr; <BR>' . $s;
        } else {
            $html2 .= $s;
        }
        $html2 .= '</B></TD>' . "\r\n";

        // $html2 = '<TR><TD>'.$s.'<BR>(orig '.$origs.')</TD>';
        // if ($u !== "NO" && $t !=="Any" && strlen($u) !== 0) {
        if ($t !== 'Any' && strlen($u) !== 0) {
            if ($u !== 'NO') {
                $html2 .= '<TD class="shtet" usbgn="' . $u . '"><A href="https://www.jewishgen.org/jgff/jgfft.php?jgffid=' . $jgff_id;
                $html2 .= '"><img SRC="https://www.jewishgen.org/jg/images/JGLogoBtn16.gif" border=0 Title="More info about ' . $t . '"></A> ' . "\r\n";
            } else {
                $html2 .= '<TD class="shtet" usbgn="JGFFT' . $jgff_id . '">';
                // $html2 .='"><img SRC="/jg/images/JGLogoBtn16.gif" border=0 Title="More info about '.$t.'"></A> '."\r\n";
            }
        } else {
            $html2 .= '<TD>' . "\r\n";
        }
        $html2 .= '<B>' . $t . '</B></TD>' . "\r\n";
        // $html2 .= $t.'<BR>(orig '.$origt;
        // if ($origt !==$t || $origc !== $c) {
        //	$html2 .= ' new id='.$jgff_id.' new usbgn='.$u;
        // }
        // html2 .= '</TD>';
        $html2 .= '<TD><B>' . ctryname($c) . "\r\n";

        if ($changed === 1) { // at least 1 of surname/town/country has changed
            // add code here to test spaces and wildcards in surnames and / in surnames and surnames of single letters and reject
            // also town entered and any country
            // also wildcards in towns
            // $html2 contains orig entered entry
            // add to $rejects
            $dataproblem = 0;

			// not allowed in Surname
			$haystack = '¬' . $s;
			$rejectChars = ['*', '?', '%', '<', '>', '/', '\\', '[', ']', '(', ')', '{', '}', '+', '"'];
			foreach($rejectChars as $rc) {
				if (stripos($haystack, $rc) > 0) {
					++$dataproblem;
					++$rejects;
					$sql = 'Rejected - Surname cannot include wildcards, slashes, quote ("), < or >, [ or ], ( or ), { or }';
					break;
				}
            }
			// not allowed in Town
			if ($dataproblem == 0) {
				$haystack = '¬' . $t;
				$rejectChars = ['*', '?', '%', '<', '>', '/', '\\', '[', ']', '{', '}', '+', '"'];
				foreach($rejectChars as $rc) {
					if (stripos($haystack, $rc) > 0) {
						++$dataproblem;
						++$rejects;
						$sql = 'Rejected - Surname cannot include wildcards, slashes, quote ("), < or >, [ or ], ( or ), { or }';
						break;
					}
				}
			}
            // if (strtoupper($t) !=="ANY" AND $c ==="Any") {  // cannot enter a town but no country
            //	$dataproblem++;
            //	$sql="Rejected - Cannot enter a town but no country";
            //	$rejects++;
            // }
            if (strlen(trim($s)) < 2) {
                ++$dataproblem;
                $sql = 'Rejected - Surname is too short';
                ++$rejects;
            }
            // echo $spaces_list;
            if (stripos(trim($s), ' ') > 0) {
                $s2 = substr(trim($s), 0, stripos(trim($s), ' '));
                // if ($code==='5200') {
                //	echo "s2=".$s2."<BR>";
                //	echo "space at ".stripos("¬".strtoupper($spaces_list),",".strtoupper($s2).",")."<BR>";
                // }

                if (stripos('¬' . strtoupper($spaces_list), ',' . strtoupper($s2) . ',') > 0) {
                    // ok
                } else {
                    ++$dataproblem;
                    $sql = 'Rejected - Space not allowed in surname';
                    ++$rejects;
                }
            }

            $sqlsyn = '';
            if ($synonym === 1) {
                $sqlsyn = 'Town/Country automatically changed from ' . $t1a . ' / ' . ctryname($c1a) . ': ';
            }

            if ($dataproblem > 0) {
                $html2 .= '</B></TD><TD><B>' . $sql . '</B></TD></TR>' . "\r\n";
            }

            if ($dataproblem === 0) {
                if ($jgff_id !== 'unknown') {
                    $dupe = 0;
                    $doc = new SolrInputDocument();
                    if (strtoupper($s) === 'DELETE') {
                        $sqlcommand = "DELETE from jewishgen.jgffdata where id='" . $rn . "'";
                        // echo $sqlcommand."<BR>";
                        $jgffupdt = mysqli_query($con, $sqlcommand);
                        $sql = 'Delete - ACCEPTED!';
                        $updateResponse = $client_JGFF->deleteById($rn);
                        $result = $client_JGFF->commit();
                    } else {
                        if ($add !== 'Y') {
                            // first check this aint a dupe
                            $sqlcommand = "SELECT id from jewishgen.jgffdata where code='" . $code . "' AND surname='" . $s . "' AND townid=" . $jgff_id . ' AND id !=' . $rn;
                            // echo $sqlcommand."<BR>";
                            $testupdt = mysqli_query($con, $sqlcommand);

                            if (mysqli_num_rows($testupdt) > 0) { // found data!
                                // echo "Reject! Dupe! ".$s." ".$t." ".$c."<BR>";
                                $sql = 'Rejected - Duplicate Record';
                                $dupe = 1;
                            }
                            if ($dupe !== 1) {
                                if ($origt !== $t || $origc !== $c) { // at least 1 of town/country has changed
                                    $sqlcommand = "UPDATE jewishgen.jgffdata set surname='" . $s . "', townid='" . $jgff_id . "', lastchange='" . date('Y-m-d') . "' where id='" . $rn . "'";
                                } else { // only surname changed
                                    $sqlcommand = "UPDATE jewishgen.jgffdata set surname='" . $s . "', lastchange='" . date('Y-m-d') . "' where id='" . $rn . "'";
                                }
                                $jgffupdt = mysqli_query($con, $sqlcommand);
                                $sql = 'Update - ACCEPTED!';
                            }
                        } else {
                            // first check this aint a dupe
                            $sqlcommand = "SELECT id from jewishgen.jgffdata where code='" . $code . "' AND surname='" . $s . "' AND townid=" . $jgff_id;
                            $testupdt = mysqli_query($con, $sqlcommand);
                            if (mysqli_num_rows($testupdt) > 0) { // found data!
                                // echo "Reject! Dupe! ".$s." ".$t." ".$c."<BR>";
                                $sql = 'Rejected - Duplicate Record';
                                $dupe = 1;
                            }
                            if ($dupe !== 1) {
                                $sqlcommand = "INSERT into jewishgen.jgffdata (surname, townid, lastchange, code) VALUES ('" . $s . "','" . $jgff_id . "','" . date('Y-m-d') . "','" . $code . "')";
                                $jgffupdt = mysqli_query($con, $sqlcommand);
                                $sqlcommand = "SELECT id from jewishgen.jgffdata WHERE (surname='" . $s . "' AND townid='" . $jgff_id . "' AND lastchange='" . date('Y-m-d') . "')";
                                $result = $con->query($sqlcommand);
                                $row = mysqli_fetch_row($result);
                                $rn = $row[0];
                                // $html2=str_replace('REPLACEME!',$rn,$html2); not needed as this is townid not sql record id
                                $sql = 'Update - ACCEPTED!';
                            }
                        }

                        if ($dupe !== 1) {
                            $doc->addField('id', $rn);
                            $doc->addField('code', $code);
                            $doc->addField('surname', $s);
                            $doc->addField('town', $t);
                            $doc->addField('country', $c);
                            $doc->addField('recdate', date('Y-m-d') . 'T00:00:00Z');
                            $doc->addField('countryname', ctryname($c));
                            if (strlen($u) !== 0) {
                                $doc->addField('usbgn', $u);
                            }
                            $doc->addField('townid', $jgff_id);

                            $updateResponse = $client_JGFF->addDocument($doc, true, 1000);  // autocommit within 1 second
                            // print_r($updateResponse->getResponse());
                        }
                    }

                    $html2 .= '</B></TD><TD><B>' . $sqlsyn . $sql . '</B></TD></TR>' . "\r\n";
                    // having updated/added a record and not deleted we must check for VAS members data
                    // Solr search the jgff for phonetic surname and exact town/country and not this jgid
                    // grab jgids
                    // loop through jgids grabbing CURE information - email address and alert settings
                    // if VAS is active then creating email message for this surname/town/country
                    // email to VAS email address

                    if ($dupe !== 1) {
                        $client_JGFF = new SolrClient($options_JGFF);
                        if (stripos(trim($s), ' ') > 0) {
                            $query1 = 'surname_bmpm:"' . $s . '"';
                        } else {
                            $query1 = 'surname_bmpm:' . $s;
                        }
                        if (stripos(trim($t), ' ') > 0) {
                            $query1 .= ' AND town:"' . $t . '"';
                        } else {
                            $query1 .= ' AND town:' . $t;
                        }
                        if (stripos(trim($c), ' ') > 0) {
                            $query1 .= ' AND country:"' . $c . '"';
                        } else {
                            $query1 .= ' AND country:' . $c;
                        }
                        $query1 .= ' AND NOT code:' . $code;
                        // echo $query1."<BR>";
                        $query = new SolrQuery($query1);
                        $query->setRows(0);
                        $query->setFacet(true);
                        $query->addFacetField('code');
                        $query->setFacetSort(1);
                        $query->setFacetMinCount(1);
                        $query->setFacetLimit(-1);
                        $updateResponse = $client_JGFF->query($query);
                        $response_array = $updateResponse->getResponse();
                        $found_array = $response_array->response;
                        $datarows = $found_array->offsetGet('docs');
                        $totalhits = $found_array['numFound'];
                        $ressummary = $response_array['facet_counts']['facet_fields'];
                        $resrows = $ressummary->offsetGet('code');
                        $PropertyArray = $resrows->getPropertyNames();
                        $pCount = count($PropertyArray);

                        $reslist = '';
                // echo "reslist=".$reslist."<BR>";
                        for ($i = 0; $i < $pCount; ++$i) { // researcher loop
                            $reslist .= "'" . trim($PropertyArray[$i]) . "'";
                            if ($i !== $pCount - 1) {
                                $reslist .= ',';
                            }
                        }
                        if ($pCount > 0) { // found other researchers with this data
                            // $gconn = sqlsrv_connect( $MSserverName, $optionsGoldmine);
                            // if( $gconn )
                            // {

                            $gquery = 'SELECT dbo.CONTACT1.CONTACT AS name,dbo.CONTACT1.KEY3 AS code2,dbo.CONTACT2.UEMAIL_PRI AS email, dbo.CONTACT2.UJGFFALERT AS gmalert, dbo.CONTACT2.UJGFFDATE AS gmalertdate FROM dbo.CONTACT1 INNER JOIN dbo.CONTACT2 ON dbo.CONTACT1.ACCOUNTNO = dbo.CONTACT2.ACCOUNTNO WHERE dbo.Contact1.key3 IN (' . $reslist . ')';

                            // echo $gquery."<BR>";

                            $stmt = sqlsrv_query($gconn, $gquery);

                            if ($stmt === false) {
                                echo "Error in executing query.\n";

                                exit(print_r(sqlsrv_errors(), true));
                            }

                            while ($row = sqlsrv_fetch_array($stmt)) { // loop through researchers checking VAS status
                                $code2 = $row['code2'];
                                $resemail = $row['email'];
                                $gmalert = $row['gmalert'];
                                $gmalertdate = $row['gmalertdate'];
                                $vasok = 0;
                                $today_dt = new DateTime(date('Y-m-d'));
                                if ($gmalertdate >= $today_dt) {
                                    // echo "VAS valid<BR>";
                                    $vasok = 1;
                                }
                                if ($vasok === 1) { // this researcher has VAS so we need to notify him of this
                                    $to = $resemail;
                                    $subject = 'JGFF Alert';
                                    $headers3 = $headers0 . 'To: ' . $to . "\r\n" . $Foxheaders;
                                    // echo "s=".$s." t=".$t." c=".$c." code2 =".$code2." VAS = ".$vasok."<BR>";

                                    $alertmess = 'Dear JGFF Researcher ' . $code2 . ", <BR><BR>\r\n";

                                    $alertmess .= 'JGFF Researcher ' . $code . ' has just added/amended a data entry for the surname ' . $s . ' in the town of ' . $t . ". \r\n";
                                    $alertmess .= "Could this be a match for one of your entries?<BR><BR> \r\n";
                                    $alertmess .= 'To find the contact information for JGFF Researcher ' . $code . ", click on the following \r\n";

                                    $alertmess .= '<A href="https://www.jewishgen.org/jgff/jgffform.php?surname=' . $s . '&town=' . $t . '&country=' . $c . '&dates=ALL&ttype=exact">link</A><BR><BR>' . "\r\n";
                                    $alertmess .= ' or go to the JGFF at <A href="https://www.jewishgen.org/jgff/jgffweb.asp">https://www.jewishgen.org/jgff/jgffweb.asp</A> and enter Surname: ' . $s . ' and Town: ' . $t . ' and Country: ' . ctryname($c) . ' into the search form.<BR><BR><BR>' . "\r\n";

                                    $alertmess .= "Please let us know if this alert has been successful by emailing us at JGFFAlertAdmin@jewishgen.org .<BR><BR>\r\n";

                                    $alertmess .= 'You are subscribed to JGFFAlert at your email address of ' . $resemail . "<BR><BR>\r\n";
                                    // echo $alertmess."<BR><BR>";

                                    mail($to, $subject, $alertmess . "\r\n", $headers3);
                                }
                            }

                            // Free statement and connection resources.
                            sqlsrv_free_stmt($stmt);
                        } // pCount>0
                    } // not dupe
                } else { // unknown
                    ++$rejects;

                    // do something
                    // do we use dm or bmpm for these searchs for possiblities?
                    // $sql2 = "Solr code to search JGFF for similar towns in correct area<BR>";
                    // $sql2.= "town_bmpm:".$t." AND (country:".str_replace(","," OR country:",rejects($c,$jgffrejects_list,"area")).") then search MySQL for the IDs<BR>";
                    // $sql2.= "Use townid field for facetting like http://jewishgen14:8888/solr/JGFFproto/select?q=town_bmpm:lomza&rows=0&wt=json&indent=true&facet=true&facet.field=townid&facet.mincount=1<BR><BR>";
                    // $sql2.= "Solr code to search Comunities for similar towns in correct area (Solr collection not setup yet)<BR>";
                    // $sql2.= "town_bmpm:".$t." AND feature:[* TO *]";
                    // $sfilt=rejects($c,$jgffrejects_list,"shtetlsfilter");
                    // if (strlen($sfilt) >0) {
                    //	$sql2.= " AND ".$sfilt.")<BR>";
                    // }
                    // $sql2 .='<BR>The title for possible matches is "'.rejects($c,$jgffrejects_list,"title").'"<BR>';
                    // in communities panel when add or edit or delete update relevant fields for Solr index

                    if ($c !== 'Any') {
                        $query1 = 'town_dm:' . $t . ' AND (country:' . str_replace(',', ' OR country:', rejects($c, $jgffrejects_list, 'area')) . ')';
                    } else {
                        $query1 = 'town_bmpm:' . $t;
                    }

                    $client_JGFF = new SolrClient($options_JGFF);
                    $query = new SolrQuery($query1);
                    $query->setRows(250); // was 100k, GSandler 19-Apr-2024
                    $query->setFacet(true);
                    $query->addFacetField('townid');
                    $query->setFacetSort(1);
                    $query->setFacetMinCount(1);
                    $query->setFacetLimit(-1);
                    $updateResponse = $client_JGFF->query($query);
                    $response_array = $updateResponse->getResponse();
                    // print_r($response_array) ;
                    $found_array = $response_array->response;
                    // print_r($found_array) ;
                    $datarows = $found_array->offsetGet('docs');
                    // print_r($datarows) ;
                    $totalhits = $found_array['numFound'];
                    // print_r($totalhits) ;
                    // echo "<BR>";
                    $townsummary = $response_array['facet_counts']['facet_fields'];
                    $townrows = $townsummary->offsetGet('townid');
                    // print_r($townsummary) ;
                    // print_r($townrows) ;
                    // echo "<BR>";
                    $PropertyArray = $townrows->getPropertyNames();
                    // print_r($PropertyArray);
                    // echo "<BR>";
                    $pCount = count($PropertyArray);
					if ($pCount > 0) {
						$idlist = '';
						for ($i = 0; $i < $pCount; ++$i) {
							$idlist .= "'" . $PropertyArray[$i] . "'";
							if ($i !== $pCount - 1) {
								$idlist .= ',';
							}
						}
						$idlist = '(' . $idlist . ')';
						// echo $idlist."<BR>";

						// SELECT town,countryname,count(*) as count FROM jewishgen.jgffdata_view where townid IN ('16098','2980','3467','15916','18487','21961','22351') group by townid order by count desc;
						$jgff2_sql = 'SELECT town, countryname, count(*) as count, usbgn, townid, communitypage, notes FROM jewishgen.jgffdata_view where townid IN ' . $idlist . ' group by townid order by count desc';
						$jgffid2 = mysqli_query($con, $jgff2_sql);
						// echo $jgff2_sql."<BR>";
						$posscount = 0;
						$possible = '<CENTER><TABLE CELLPADDING=3 CELLSPACING=2 BORDER=2><TR><TH>Town</TH><TH>Country</TH><TH>Notes</TH><TH>JGFF<BR>Entries</TH></TR>' . "\r\n";
						while ($jgffid2_row = mysqli_fetch_row($jgffid2)) {
							++$posscount;
							// $html .= '<TD class="shtet" usbgn="'.$u.'"><A HREF="/wconnect/wc.dll?jg~jgsys~jgfft~'.$rn.'"><img SRC="/jg/images/JGLogoBtn16.gif" border=0 Title="More info about '.$t.'"></A> ';
							if ($jgffid2_row[5] !== 'No') {
								$possible .= '<TR><TD class="shtet" usbgn="' . $jgffid2_row[3] . '"><A href="https://www.jewishgen.org/jgff/jgfft.php?jgffid=' . $jgffid2_row[4] . '"><img SRC="https://www.jewishgen.org/jg/images/JGLogoBtn16.gif" border=0 Title="More info about ' . $jgffid2_row[0] . '"></A> ' . $jgffid2_row[0] . '</TD><TD>' . $jgffid2_row[1] . '</TD><TD>' . $jgffid2_row[6] . '</TD><TD align=right>' . $jgffid2_row[2] . '</TD></TR>' . "\r\n";
							} else {
								$possible .= '<TR><TD class="shtet" usbgn="JGFFT' . $jgffid2_row[4] . '">' . $jgffid2_row[0] . '</TD><TD>' . $jgffid2_row[1] . '</TD><TD>' . $jgffid2_row[6] . '</TD><TD align=right>' . $jgffid2_row[2] . '</TD></TR>' . "\r\n";
							}
							// $possible .= "<TR><TD>".$jgffid2_row[0]."</TD><TD>".$jgffid2_row[1].'</TD><TD>'.$jgffid2_row[6]."</TD><TD>".$jgffid2_row[2]."</TD></TR>";
						}

						// for ($i = 0; $i < $pCount; $i++) {
						//	$possible .= "<TR><TD>".$PropertyArray[$i]."</TD><TD>&nbsp;</TD><TD>".$townrows[$PropertyArray[$i]]."</TD></TR>";
						// }
						$possible .= '</TABLE></CENTER>' . "\r\n";
					} else {
						$posscount = 0;
						$possible = '';
					}

                    // now look for communities database matches
                    $query1 = 'town_dm:' . $t . ' AND NOT jgffid:""';
                    $sfilt = '';
                    if ($c !== 'Any') {
                        $sfilt = rejects($c, $jgffrejects_list, 'shtetlsfilter');
                    } else {
                        $query1 = 'town_bmpm:' . $t . ' AND NOT jgffid:""';
                    }
                    if (strlen($sfilt) > 0) {
                        $query1 .= ' AND ' . $sfilt;
                    }
                    // echo $query1."<BR>";
                    $client_communities = new SolrClient($options_communities);
                    $query = new SolrQuery($query1);
                    $query->setRows(250); // was 100k, GSandler 19-Apr-2024
                    $query->addSortField('jgff_no', SolrQuery::ORDER_DESC);
                    // $query->setFacet(true);
                    // $query->addFacetField('townid');
                    // $query->setFacetSort(1);
                    // $query->setFacetMinCount(1);
                    // $query->setFacetLimit(-1);
                    $updateResponse = $client_communities->query($query);
                    $response_array = $updateResponse->getResponse();
                    $found_array = $response_array->response;
                    $totalhits = $found_array['numFound'];
                    // print_r($found_array) ;
                    $datarows = $found_array->offsetGet('docs');
                    // echo $datarows["0"]["shtetl_nam"]."<BR>" ;
                    // echo $datarows["0"]["shtetl_cou"]."<BR>" ;
                    // echo $datarows["0"]["alternate_"]."<BR>" ;
                    $possible_communities = '';
                    $posshead_communities = '';
                    // echo $totalhits;

                    if ($totalhits > 0) {
                        $posshead_communities = '<P>For your assistance, we show below the current acceptable entries in the <A HREF="https://www.jewishgen.org/Communities/Search.asp">JewishGen Communities Database</A> that match by ';
                        if ($c === 'Any') {
                            $posshead_communities .= 'Beider-Morse Phonetic Matching for ';
                        } else {
                            $posshead_communities .= 'Daitch-Mokotoff Soundex for ';
                        }

                        $posshead_communities .= '&ldquo;' . $t . '&rdquo;:' . "\r\n";
                        $possible_communities = '<P><CENTER><TABLE CELLPADDING=3 CELLSPACING=2 BORDER=2>';
                        $possible_communities .= '<TR><TH>Town</TH><TH>Country</TH><TH>Alternate Names</TH><TH>JGFF<BR>Entries</TH></TR>' . "\r\n";
                        $idcount = 0;
                        for ($i = 0; $i < $totalhits; ++$i) {
                    // echo "pos= ".strpos(','.$idlist.',', ','.$datarows[$i]["jgffid"].',')."<BR>";
                            if (strpos(',' . $idlist . ',', ',' . $datarows[$i]['jgffid'] . ',') === false) { // not in JGFF list
                                ++$idcount;
                                $possible_communities .= '<TR><TD class="shtet" usbgn="' . $datarows[$i]['feature'] . '"><A href="https://www.jewishgen.org/jgff/jgfft.php?jgffid=' . $datarows[$i]['jgffid'] . '"><img SRC="https://www.jewishgen.org/jg/images/JGLogoBtn16.gif" border=0 Title="More info about ' . $datarows[$i]['shtetl_nam'] . '"></A> ' . $datarows[$i]['shtetl_nam'] . '</TD><TD>' . $datarows[$i]['shtetl_cou'] . '</TD><TD>' . $datarows[$i]['alternate_orig'] . '</TD><TD ALIGN=right>' . $datarows[$i]['jgff_no'] . '</TD></TR>' . "\r\n";
                            }
                        }
                        $possible_communities .= "</TABLE></CENTER><br>\r\n";
                    }
                    // echo $possible_communities;
                    if ($idcount === 0) {
                        $possible_communities = '';
                        $posshead_communities = '';
                    }

                    $cname = ctryname($c);
                    $posshead = 'This entry has been rejected because the Town/Country name is questionable.&nbsp; ';
                    $posshead .= 'This town has never been entered in the JGFF database before.&nbsp; ' . "\r\n";
                    $posshead .= 'The JGFF uses only <a href="https://www.jewishgen.org/jgff/FAQ/#q4.2"><B>modern</B> town and country names</A>.</br>';
                    $posshead .= '<br>If &ldquo;' . $t . '&rdquo;, &ldquo;' . $cname . '&rdquo; is ';
                    $posshead .= 'the official modern name of the town ';
                    $posshead .= 'and you feel that this entry is correct, please complete our ';
                    $posshead .= '<a href="https://www.jewishgen.org/jgff/TownQ.asp?town=' . $t . '&country=' . $cname . '">Questionable Town Query Form</A>.&nbsp;' . "\r\n";
                    $possfoot = '';
                    if ($posscount > 0) {
                        $posshead .= ' For your assistance, we show below the current acceptable entries in the JGFF that match by ';
                        if ($c !== 'Any') {
                            $posshead .= 'Daitch-Mokotoff Soundex for ';
                        } else {
                            $posshead .= 'Beider-Morse Phonetic Matching for ';
                        }
                        $posshead .= '&ldquo;' . $t . '&rdquo; in ';
                        if ($c !== 'Any') {
                            $posshead .= rejects($c, $jgffrejects_list, 'title');
                        } else {
                            $posshead .= 'Any Country';
                        }
                        $posshead .= '.<br>' . "\r\n";
                        $possfoot = '<P>Note: It is possible that a few invalid entries have slipped through into the JGFF.&nbsp; Treat any towns with only a handful of entries with some caution!</P>' . "\r\n";
                    } else {
                        $possible = '<br>' . "\r\n";
                    }
                    $possfoot2 = '';
                    if ($add !== 'Y') {
                        $possfoot2 = '<P>Your existing entry has been left as:</P>' . "\r\n";
                    }

                    $sql2 = '<BR>' . $posshead . $possible . $possfoot . $posshead_communities . $possible_communities . $possfoot2;
                    $sql = '';

                    $html .= '<TR><TD><B>' . $x . '</B></TD><TD><B>' . $s . '</B></TD>' . "\r\n";
                    $html .= '<TD><B>' . $t . '</B></TD>' . "\r\n";
                    $html .= '<TD><B>' . ctryname($c) . '</B></TD><TD><B>REJECTED!</B></TD></TR>' . "\r\n";

                    $html .= '<TR><TD COLSPAN=5>' . $sql2 . '</TD></TR>' . "\r\n";
                    // that is JGFF possibilities - now need to look for Communities database possiblities!

                    $html2 = '';
                    if ($add !== 'Y') {
                        $html2 = '<TR><TD><B>' . $x . '</B></TD><TD><B>' . $origs . '</B></TD>' . "\r\n";
                        if ($origu !== 'NO' && $origt !== 'Any' && strlen($origu) !== 0) {
                            $html2 .= '<TD class="shtet" usbgn="' . $origu . '"><A href="https://www.jewishgen.org/jgff/jgfft.php?jgffid=' . $jgff_id . '"><img SRC="https://www.jewishgen.org/jg/images/JGLogoBtn16.gif" border=0 Title="More info about ' . $origt . '"></A> ' . "\r\n";
                        } else {
                            $html2 .= '<TD>' . "\r\n";
                        }
                        $html2 .= '<B>' . $origt . '</B></TD>' . "\r\n";
                        $html2 .= '<TD><B>' . ctryname($origc) . '</B></TD><TD><B>Left Unchanged!</B></TD></TR>' . "\r\n";
                    }
                }
            } // of $dataproblem===0
        } else {  // not changed
            $html2 .= '</TD><TD><B>' . $sql . '</B></TD></TR>' . "\r\n";
        }

        // $html2 .= '<TD>'.ctryname($c).'<BR>(orig '.ctryname($origc).')</TD><TD>'.$sql.'</TD></TR>';

        $html .= $html2 . "\r\n";
    }  // strlen surname > 0
}

if ($surncount === 0) {  // no surnames entered
    $html .= '<BR><B>NOTE: You have not entered any surnames.<BR><BR>' . "\r\n";
}

// if now have JGFF data and donortypes didnt have J before then add it. If no longer have JGFF data then remove J
$sqlcommand = "SELECT id from jewishgen.jgffdata where code='" . $code . "'";
$testupdt = mysqli_query($con, $sqlcommand);
if (mysqli_num_rows($testupdt) > 0) { // found data!
    if (strpos($donortypes, 'J') === false) {
        $donortypes .= ' J';
        $donortypes = trim(str_replace('  ', ' ', $donortypes));
        $gquery = 'update dbo.CONTACT1 set KEY5 = ?, U_KEY5 = ? WHERE key3 = ? ';
        $gparam = [$donortypes, $donortypes, $code];
        $stmt = sqlsrv_query($gconn, $gquery, $gparam);
    }
} else { // no data!
    if (strpos($donortypes, 'J') !== false) {
        $donortypes = str_replace('J', '', $donortypes);
        $donortypes = trim(str_replace('  ', ' ', $donortypes));
        $gquery = 'update dbo.CONTACT1 set KEY5 = ?, U_KEY5 = ? WHERE key3 = ? ';
        $gparam = [$donortypes, $donortypes, $code];
        $stmt = sqlsrv_query($gconn, $gquery, $gparam);
    }
}
sqlsrv_close($gconn);
mysqli_close($con);
$html .= '</table></center>' . "\r\n";

if ($rejects > 0) {
    $html .= '<BR><B>NOTE: Some of the data you tried to enter has been REJECTED.</B><BR><BR>' . "\r\n";
}

$html .= '<BR><H3>Click <A href="/JGFF/jgff-addmodify.php">here</A> to edit your entries, ' . "\r\n";
$html .= 'or click <A href="/JGFF/jgff-addmodify.php?add=Y">here</A> to add more entries</H3>' . "\r\n";

$html .= "<BR><HR>\r\n";

echo $html;

// Send emails:
//
// NOTE!!!!!! the php mailer adds in random spaces to message body if the message is long.
// this can screw up the appearance of html emails when the spaces occur in the middle of HTML TAGS.
// the solution is to add ."\r\n" at the end of every line building up the message text.
// you need double quotes " not single as the \r\n is not handled correctly if not double quotes
//
$to = $email;
$subject = 'Your JGFF Data: Researcher #' . $code;
$headers3 = $headers0 . 'To: ' . $to . "\r\n" . $headers;

$mess2 = 'Researcher: ' . $name . ' (#' . $code . '), <NOBR>' . $to . "</NOBR><BR><BR>\r\n";
$mess2 .= "Thank you for adding/amending your data in the JewishGen Family Finder.<BR>\r\n";
$mess2 .= "Here is a summary of the data you entered:<BR><BR>\r\n";

mail($to, $subject, $mess2 . $html . "\r\n", $headers3);

// Now send an email to JGFF administrators:
$email = 'jghelp@jewishgen.org';
$to = $email;
if ($add !== 'Y') {
    $subject = 'JGFF EDIT Data : Researcher #' . $code;
} else {
    $subject = 'JGFF NEW Data : Researcher #' . $code;
}
$headers3 = $headers0 . 'To: ' . $to . "\r\n" . $headers;
$ip = get_client_ip();
$mess2 = $ip . '  <NOBR>(' . gethostbyaddr($ip) . ')</NOBR><BR><BR>' . $mess2;

if ($admin === 'Y') {
    $mess2 = 'NOTE: This change was made by master user #' . $jgid . ' at IP = ' . $ip . '<BR><BR>' . $mess2 . "\r\n";
}

mail($to, $subject, $mess2 . $html . "\r\n", $headers3);

include 'footer.txt';

echo '<SCRIPT src="/Ajax/popUpHypertext.js" type="text/javascript"></SCRIPT>';
echo '<SCRIPT src="/Jewishgen/js/HTMLentities.js"   type="text/javascript"></SCRIPT>';

include '../jg/footer.txt';

$time_end = microtime(true);
$duration = ($time_end - $time_start);

// if dont want savesearch version of logging then call write_mysql_jg2log instead
write_mysql_jglog($duration, $code, $ip);

// function remove_accents($string) 
// {
//	return iconv('UTF-8', 'ASCII//TRANSLIT', $string) ;
// }

function ctryname($country_id)
{
    global $countries;  // Initialized above.

    $country_name = $countries[trim($country_id)];

    // In case of bizarre error:
    if ($country_name === null) {
        $country_name = $country_id;
    }

    return $country_name;
}

function rejects($country, $jgffrejectslist, $param)
{
    $reject1 = stripos($jgffrejectslist, '[country]' . $country);
    $reject2 = substr($jgffrejectslist, $reject1 + 1, 999);
    $reject1 = stripos($reject2, '[country]');
    $reject2 = '[' . substr($reject2, 0, $reject1);

    $reject3 = stripos($reject2, '[' . $param . ']');
    $reject4 = substr($reject2, $reject3, 999);
    $reject3 = stripos($reject4, '[/' . $param . ']');
    $reject4 = substr($reject4, 0, $reject3);

    $reject4 = str_replace('[' . $param . ']', '', $reject4);
    if ($param === 'area') {
        $reject4 = substr($reject4, 1, strlen($reject4) - 2);
    }

    return $reject4;
}

?>

