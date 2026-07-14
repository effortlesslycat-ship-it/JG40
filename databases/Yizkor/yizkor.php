<?php
include_once '\\\\jewishgen6\\JEWISHGEN\\wwwroot\\blockscript\\detector.php';

include_once '../cureetc.php';

include '../bootstrap.php';

include '../msbootstrap.php';

$options = [
    'hostname' => SOLR_SERVER_HOSTNAME,
    'login' => SOLR_SERVER_USERNAME,
    'password' => SOLR_SERVER_PASSWORD,
    'port' => SOLR_SERVER_PORT,
    'path' => 'solr/books',
];

date_default_timezone_set('America/Denver');
$time_start = microtime(true);

$townid = !isset($_GET['townid']) ? '' : $_GET['townid'];
$bookid = !isset($_GET['bookid']) ? '' : $_GET['bookid'];

if (strlen($bookid) > 5) { // sql injection?
    exit;
}
if (strlen($townid) > 5) { // sql injection?
    exit;
}

// sometimes the cookie overwrites the bookid so store and reset afterwards.
$mtbookid = $bookid;

$bookid = $mtbookid;

$jgid = require_userinfo();

if (strlen($bookid) > 0) {
    if (strlen($townid) > 0) {
        $shtetlsbooks_sql = "SELECT book__,shtetl__ FROM shtetlsbooks WHERE shtetl__='" . $townid . "' AND book__='" . $bookid . "'";
    } else {
        $shtetlsbooks_sql = "SELECT book__,shtetl__ FROM shtetlsbooks WHERE book__='" . $bookid . "'";
    }
} else {
    $shtetlsbooks_sql = "SELECT book__,shtetl__ FROM shtetlsbooks WHERE shtetl__='" . $townid . "'";
}

if (isset($_POST['town'])) {   // Iff from the Yizkor Book SEARCH form:
    // Search Solr core, and return list of all matching town shtetl__ ids
    // Then reset $shtetlsbooks_sql to select for this range of ids in $townidlist
    // $shtetlsbooks_sql = "SELECT book__ FROM shtetlsbooks WHERE shtetl__ IN '".$townidlist."' ORDER by shtetl__";

    $town = $_POST['town'];

    if (strlen($town) > 25) { // sql injection?
        exit;
    }

    $ttype = $_POST['ttype'];
    $tfuzz = '';
    $tfuzz2 = 0;
    $tfuzz1 = '';

    switch ($ttype) {
        case 'Q':
            $tfield = 'town_bmpm';
            $ttext = ' (Town Phonetically like) : ';

            break;

        case 'D':
            $tfield = 'town_dm';
            $ttext = ' (Town Soundex) : ';

            break;

        case 'E':
            $tfield = 'town';
            $ttext = ' (Exact) : ';

            break;

        case 'S':
            $tfield = 'town';
            $ttext = ' (Town Starts with) : ';
            $tfuzz = '*';

            break;

        case 'C':
            $tfield = 'town';
            $ttext = ' (Town Contains) : ';
            $tfuzz = '*';
            $tfuzz1 = '*';

            break;

        case 'F1':
            $tfield = 'town';
            $tlen = '1';
            $tfuzz = '~' . $tlen;
            $tfuzz2 = 1;
            $ttext = ' (Town Fuzzy) : ';
            $tfield2 = 'town_bmpm';
            $tfield3 = 'town_dm';

            break;

        case 'F2':
            $tfield = 'town';
            $tlen = min(2, min(round(strlen($town) / 3), 4));
            $tfuzz = '~' . $tlen;
            $tfuzz2 = 1;
            $ttext = ' (Town Fuzzier) : ';
            $tfield2 = 'town_bmpm';
            $tfield3 = 'town_dm';

            break;

        case 'FM':
            $tfield = 'town';
            $tlen = min(round(strlen($town) / 3), 4);
            $tfuzz = '~' . $tlen;
            $tfuzz2 = 1;
            $ttext = ' (Town Fuzziest) : ';
            $tfield2 = 'town_bmpm';
            $tfield3 = 'town_dm';

            break;

        case 'X':
            $tfield = 'alltext';
            $ttext = ' (Text Contains) : ';

            break;

        default:
            $tfield = 'town_bmpm';
            $ttext = ' (Town Phonetically like) : ';
    }

    $townorig = $town;
    $town1 = $town;
    $town = str_replace(' ', '', $town);

    $query1 = '';
    if (strlen($town) > 0) {
        $query1 = $tfield . ':' . $tfuzz1 . $town . $tfuzz;
    }

    if ($tfuzz2 === 1) {  // remove exact and bmpm and dm matches
        $query1 .= ' AND NOT (' . $tfield2 . ':' . $town . ' OR ' . $tfield3 . ':' . $town . ')';
    }

    // echo $query1."<BR>";

    $client = new SolrClient($options);
    $query = new SolrQuery($query1);
    $query->setRows(100000);
    $query->setFacet(true);
    $query->addFacetField('sbid');
    $query->setFacetMinCount(1);
    $query->setFacetLimit(-1);

    $updateResponse = $client->query($query);

    $response_array = $updateResponse->getResponse();

    // print_r($response_array) ;

    $found_array = $response_array->response;

    // print_r($found_array) ;

    $datarows = $found_array->offsetGet('docs');

    // print_r($datarows) ;

    $totalhits = $found_array['numFound'];
    // print_r($totalhits) ;

    $shtetlsummary = $response_array['facet_counts']['facet_fields'];
    $shtetlrows = $shtetlsummary->offsetGet('sbid');
    // print_r($shtetlrows) ;

    $shtetllist = '';
    $datacount = count($datarows);

    $shtetls = 0;
    for ($i = 0; $i < $datacount; ++$i) {
        if ($ttype !== 'X') {
            if (stristr($shtetllist, "'" . $datarows[$i]['sbid'] . "',") === false) {
                $shtetllist .= "'" . $datarows[$i]['sbid'] . "',";
                $shtetls = $shtetls + 1;
            }
        } else {
            if (stristr($shtetllist, "'" . $datarows[$i]['bid'] . "',") === false) {
                $shtetllist .= "'" . $datarows[$i]['bid'] . "',";
                $shtetls = $shtetls + 1;
            }
        }
    }
    $shtetllist = substr($shtetllist, 0, strlen($shtetllist) - 1);
    // echo $shtetllist."<BR>";
    if ($ttype !== 'X') {
        $shtetlsbooks_sql = 'SELECT book__,shtetl__ FROM  shtetlsbooks where shtetl__ IN (' . $shtetllist . ') ORDER by shtetl__';
    } else {
        $shtetlsbooks_sql = 'SELECT book__,Shtetl__ FROM  shtetlsbooks where book__ IN (' . $shtetllist . ') ORDER by book__';
        $shtetlsbooks_sql2 = 'SELECT distinct book__ FROM  shtetlsbooks where book__ IN (' . $shtetllist . ') ';
    }
}

// echo $shtetlsbooks_sql."<BR>";

$html = '<html>';

$html .= "<HEAD><meta http-equiv='Content-Type' content='text/html; charset=utf-8'><BASE REF='HTTPS://www.jewishgen.org/'><TITLE>The Yizkor Book Database</TITLE></HEAD><BODY>";

echo $html;

include '../../jg/headsection.txt';

include '../../jg/header.txt';

echo '<BR>';

$html = '<H1 ALIGN="CENTER">The Yizkor Book Database</H1>';
echo $html;

$html = '';

// look up town in shtetlbooks database
// SELECT * FROM jewishgen.shtetlsbooks where shtetl__='85';
// // then generate a list of the books and search the yizkor books
//
// SELECT * FROM jewishgen.books where book__ in ('85','457','1086');
//
// and get library info from librarybooks database
// SELECT * FROM jewishgen.librarybooks where book__ in ('85','457','1086');

$con = mysqli_connect('jewishgen13:3306', 'mtobias', 'nt732b#$', 'jewishgen');

$shtetlsbooks = mysqli_query($con, $shtetlsbooks_sql);
if ($ttype !== 'X') {
    $shtetlbookcount = mysqli_num_rows($shtetlsbooks);
} else {
    $shtetlsbooks2 = mysqli_query($con, $shtetlsbooks_sql2);
    $shtetlbookcount = mysqli_num_rows($shtetlsbooks2);
}
// echo "ShtetlBookCount: " . $shtetlbookcount . "<BR>";

if (!isset($_POST['town'])) {   // NOT from Yizkor Book SEARCH form
    // Get info about the Town, from the Communities Database:
    $communityID = $townid;

    // If the URL did NOT provide a TownID, then use this book's Primary Shtetl:
    if (strlen($townid) <= 0) {
        $bookprimary_sql = "SELECT primary_sh FROM books WHERE book__ = '" . $bookid . "'";
        $bookprimary = mysqli_query($con, $bookprimary_sql);
        $bookprimary_row = mysqli_fetch_array($bookprimary);
        $communityID = $bookprimary_row['primary_sh'];
    }

    $communities_sql = "SELECT * FROM communities WHERE shtetl__='" . $communityID . "'";
    $community = mysqli_query($con, $communities_sql);
    $community_row = mysqli_fetch_array($community);
}

echo '<CENTER><TABLE BGCOLOR="#E8E1D1" CELLPADDING=3 CELLSPACING=2 BORDER=2 WIDTH=95% >';
if (strlen($bookid) === 0) {
    if (isset($_POST['town'])) {   // Yizkor Book Search form
        echo '<TR><TH COLSPAN=2 ALIGN="CENTER"><H2>  Searching for ' . $ttext . $townorig . '<BR>';
    } else {
        echo '<TR><TH COLSPAN=2 ALIGN="CENTER"><H2>  Searching for Town ' . $community_row['shtetl_nam'] . '<BR>';
    }
    echo '<SMALL><SMALL>Number of Books: ' . $shtetlbookcount . '<BR>Run on ' . date(DATE_RFC2822) . '</SMALL></SMALL><BR></H2></TH></TR>';
}

$lastbook = 9999999999999;
while ($shtetlsbooks_row = mysqli_fetch_array($shtetlsbooks)) {
    if ($shtetlsbooks_row['book__'] !== $lastbook) {	// If we haven't already done this book
        $lastbook = $shtetlsbooks_row['book__'];

        $primary_text = '';
        $namesrow = '';

        if (isset($_POST['town'])) {  // IFF from Yizkor Book Search form:
            $communities_sql = "SELECT * FROM communities where shtetl__ = '" . $shtetlsbooks_row['shtetl__'] . "'";
            $community = mysqli_query($con, $communities_sql);
            $community_row = mysqli_fetch_array($community);
        }

        // Check the BOOKS database, to see if this is the Primary shtetl covered by the book:
        $bookprimary_sql = "SELECT primary_sh FROM books WHERE book__ = '" . $shtetlsbooks_row['book__'] . "'";
        $bookprimary = mysqli_query($con, $bookprimary_sql);
        $bookprimary_row = mysqli_fetch_array($bookprimary);

        // Get a list of all the Shtetls covered by this book:
        $shtetlsinbook_sql = "SELECT shtetl__ FROM shtetlsbooks WHERE book__='" . $shtetlsbooks_row['book__'] . "'";
        $shtetlsinbook = mysqli_query($con, $shtetlsinbook_sql);
        $shtetlsinbookcount = mysqli_num_rows($shtetlsinbook);

        if ($shtetlsinbookcount > 1) {  // more than 1 town covered, so create list of shtetls, w mouse-overs
            $shtetlsinbook_list = '';
            while ($shtetlsinbook_row = mysqli_fetch_array($shtetlsinbook)) {
                $shtetlsinbook_list .= "'" . $shtetlsinbook_row['shtetl__'] . "',";
            }

            $shtetlsinbook_list = substr($shtetlsinbook_list, 0, strlen($shtetlsinbook_list) - 1);
            $namesinbook_sql = 'SELECT shtetl_nam,feature FROM communities where shtetl__ in (' . $shtetlsinbook_list . ') order by remove_accents(HTML_UnEncode(shtetl_nam))';
            // echo $namesinbook_sql;
            $namesinbook = mysqli_query($con, $namesinbook_sql);
            // $namesinbookcount=mysqli_num_rows($namesinbook);
            // echo "namecount=".$namesinbookcount."<BR>";

            while ($namesinbook_row = mysqli_fetch_array($namesinbook)) {
                $namesrow .= '<SPAN class="shtet" usbgn="' . $namesinbook_row['feature'] . '">' . $namesinbook_row['shtetl_nam'] . '</SPAN>, ';
            }
            $namesrow = substr($namesrow, 0, strlen($namesrow) - 2);
        }

        // echo "1: ShtetlID: " . $shtetlsbooks_row["shtetl__"] . "<BR>";
        // echo "2: Primary ShtetlID: " . $bookprimary_row["primary_sh"] . "<BR>";

        // For NON-Primary shtetls, get town info for the Primary shtetl, from Communities DB:
        if ($shtetlsbooks_row['shtetl__'] !== $bookprimary_row['primary_sh']) {
            $communities2_sql = "SELECT * FROM communities WHERE shtetl__ = '" . $bookprimary_row['primary_sh'] . "'";
            $community2 = mysqli_query($con, $communities2_sql);
            $community2_row = mysqli_fetch_array($community2);

            if ($ttype !== 'X') {
                $primary_text = '<BR><BR>included in<BR>';
            }

            if (strlen($community2_row['feature']) > 0) {
                $primary_text .= '<H2 class="shtet" usbgn="' . $community2_row['feature'] . '"><A HREF="/Communities/community.php?usbgn=' . $community2_row['feature'] . '" target="_new"><img src="/jg/images/JGLogoBtn16.gif" border=0 Title="More info about ' . $community2_row['shtetl_nam'] . '"></A> ';
            } else {
                $primary_text .= '<H2>';
            }

            $primary_text .= $community2_row['shtetl_nam'] . ', ' . $community2_row['shtetl_cou'] . '</H2>';

            if (!empty($community2_row['latitude'] . $community2_row['longitude'])) {
                $primary_text .= '<H3>' . latlon($community2_row['latitude'], $community2_row['longitude']) . '</H3>';
            }

            $primary_text .= $community2_row['shtetl_not'];
        }

        // Print info about the book's Shtetl, from the Communities DB:

        echo '<TR BGCOLOR="#BEC49F"><TH COLSPAN=2 ALIGN="CENTER">';
        if (strlen($community_row['feature']) > 0) {
            echo '<H2 class="shtet" usbgn="' . $community_row['feature'] . '"><A HREF="/Communities/community.php?usbgn=' . $community_row['feature'] . '" target="_new"><img src="/jg/images/JGLogoBtn16.gif" border=0 Title="More info about ' . $community_row['shtetl_nam'] . '"></A> ';
        } else {
            echo '<H2>';
        }
        if ($ttype !== 'X') {
            echo $community_row['shtetl_nam'] . ', ' . $community_row['shtetl_cou'] . '</H2>';
        }

        $latlon2 = latlon($community_row['latitude'], $community_row['longitude']);
        echo '<H3>' . $latlon2 . '</H3>';

        echo $community_row['shtetl_not'];

        // If this is NOT the book's primary Shtetl, then print info about the Primary Shtetl
        if (strlen($townid) > 0) {
            // if ( ! empty($primary_text) )
            echo $primary_text;
        }
        echo '</TH></TR>';

        // Print info about the Book, from the BOOKS DB:
        // echo "book=".$shtetlsbooks_row["book__"]."<BR>";

        $books_sql = "SELECT * FROM jewishgen.books where book__ ='" . $shtetlsbooks_row['book__'] . "'";
        $books_info = mysqli_query($con, $books_sql);
        while ($books_row = mysqli_fetch_array($books_info)) {	// should only be one
            
            // echo "title=".$books_row["english_ti"]."<BR>";
            echo '<TR><TD NOWRAP>Original Title: </TD><TD><I>' . $books_row['original_t'] . '</I></TD></TR>';
            echo '<TR><TD NOWRAP>English Title: </TD><TD>' . $books_row['english_ti'] . '</TD></TR>';
            echo '<TR><TD NOWRAP>Editor: </TD><TD>' . $books_row['authors'] . '</TD></TR>';
            echo '<TR><TD NOWRAP>Published: </TD><TD>' . $books_row['pub_place'] . ' ' . $books_row['pub_date'] . '</TD></TR>';
            echo '<TR><TD NOWRAP>Publisher: </TD><TD>' . $books_row['publisher'] . '</TD></TR>';

            $tmp = '';
            if ($books_row['volumes'] > 1) {
                $tmp = 'Volumes: ' . $books_row['volumes'] . '&nbsp; ';
            }
            echo '<TR><TD>&nbsp;</TD><TD>' . $tmp . 'Pages: ' . $books_row['pages'] . '&nbsp; Languages: ' . $books_row['languages'] . '</TD></TR>';

            if (strlen($books_row['book_notes']) > 0) {
                echo '<TR><TD NOWRAP>Notes: </TD><TD>' . $books_row['book_notes'] . '</TD></TR>';
            }

            if (strlen($namesrow) > 0) {
                echo '<TR><TD NOWRAP>Places Included: </TD><TD>' . $namesrow . '</TD></TR>';
            }

            if (strlen($books_row['url']) > 0) {
                echo '<TR><TD NOWRAP>Translation: </TD><TD><A HREF="' . $books_row['url'] . '">' . $books_row['url'] . '</A></TD></TR>';
            }
            echo '<TR><TH colspan=2>&nbsp;</TH></TR>';

            $nypl_link = '';
            $nypl_id = $books_row['nypl_id'];

            if (strlen($nypl_id) > 0) {
                $nypl_link = '<TR><TD COLSPAN=2 ALIGN="CENTER">See Yizkor Book images online at New York Public Library <A HREF="http://yizkor.nypl.org/index.php?id=' . $nypl_id . '"><IMG SRC="/images/bookicon.jpg"></A></TD></TR>';
            }

            $librarybooks_sql = "SELECT * FROM jewishgen.librarybooks where book__ ='" . $shtetlsbooks_row['book__'] . "'";
            $librarybooks_info = mysqli_query($con, $librarybooks_sql);
            $numlibraries = mysqli_num_rows($librarybooks_info);
            if ($numlibraries > 0) {
                echo '<TR BGCOLOR="#BEC49F"><TH COLSPAN=2 ALIGN="CENTER">Libraries</TH></TR>';
                echo $nypl_link;
                if (strlen($books_row['oclc']) > 0) {
                    echo '<TR><TD COLSPAN=2 ALIGN="CENTER">See WorldCat site: <A HREF="' . $books_row['oclc_link'] . '">' . $books_row['oclc_link'] . '</A></TD></TR>';
                    echo '<TR><TH colspan=2>&nbsp;</TH></TR>';
                }

                while ($librarybooks_row = mysqli_fetch_array($librarybooks_info)) {
                    // echo "library=".$librarybooks_row["library_co"]." => ";
                    $library_sql = "SELECT * FROM jewishgen.libraries where library_co ='" . $librarybooks_row['library_co'] . "'";
                    $library_info = mysqli_query($con, $library_sql);
                    $library_row = mysqli_fetch_array($library_info);

                    echo '<TR><TD COLSPAN=2>' . $library_row['library_na'] . ', ' . $library_row['city'] . ', ' . $library_row['state'] . ', ' . $library_row['country'] . ',  Call No: ' . $librarybooks_row['call_numbe'] . '</TD></TR>';
                }
                echo '<TR><TH colspan=2>&nbsp;</TH></TR>';
            } else {
                echo '<TR BGCOLOR="#BEC49F"><TH COLSPAN=2 ALIGN="CENTER">No Libraries</TH></TR>';
                echo $nypl_link;
                if (strlen($books_row['oclc']) > 0) {
                    echo '<TR><TD COLSPAN=2 ALIGN="CENTER">See WorldCat site: <A HREF="' . $books_row['oclc_link'] . '">' . $books_row['oclc_link'] . '</A></TD></TR>';
                    echo '<TR><TH colspan=2>&nbsp;</TH></TR>';
                }
            }
        }  // end for each book
    }
}

echo '</TABLE></CENTER>';

mysqli_close($con);

include '../../yizkor/srchfootphp.txt';

echo '<SCRIPT src="/Ajax/popUpHypertext.js" type="text/javascript"></SCRIPT>';
echo '<SCRIPT src="/Jewishgen/js/HTMLentities.js"   type="text/javascript"></SCRIPT>';

include '../../jg/footer.txt';

$ip = get_client_ip();

$time_end = microtime(true);
$duration = ($time_end - $time_start);

$this_ip = get_client_ip();

// if dont want savesearch version of logging then call write_mysql_jg2log instead
write_mysql_jglog($duration, $jgid, $this_ip);

function latlon($t, $g)
{
    // format coordinates
    $t2 = intval($t / 100);
    $g2 = intval($g / 100);
    $t3 = $t % 100;
    $g3 = $g % 100;

    $ll2 = '';

    if ($t2 + $g2 + $t3 + $g3 !== 0) {
        $temp = $t2 . '&deg;' . $t3 . "' N, ";
        $temp .= $g2 . '&deg;' . $g3 . "' E";
        $t = $t2 + ($t3 / 60);
        $g = $g2 + ($g3 / 60);
        $ll2 = '<A HREF="/maps/mapdist8.asp?lat=' . $t . '&long=' . $g . '" target="_new">' . $temp . '</A>';
    }

    return $ll2;
}

function latlon_format($t, $g)
{
    // format coordinates lat=53.1833&long=22.0833
    $t2 = intval($t / 100);
    $g2 = intval($g / 100);
    $t3 = $t % 100;
    $g3 = $g % 100;

    $t = $t2 + $t3 / 60;
    $g = $g2 + $g3 / 60;

    return 'lat=' . $t . '&long=' . $g;
}
?>

