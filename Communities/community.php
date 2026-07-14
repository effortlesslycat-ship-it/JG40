<?php
/* ============================================================================
   JewishGen Communities Database - Locality (Town) Page  [JG40 rebuild]
   Renders communitypage.html (the new tabbed template) by str_replace token
   substitution.  Reads communities_view by ?usbgn=.

   ASCII-SAFE BUILD: no literal non-ASCII characters in this source (dev SFTP
   corrupts them).  Visible symbols use HTML entities; the degree sign is built
   at runtime via html_entity_decode().

   CHANGES FROM THE LEGACY community.php
   - Single template: loads communitypage.html only.  The old
     communitypage1.txt + ../jg/headsection.txt / header.txt / footer.txt
     chain is gone; header/footer are fetched JG40 components in the template.
   - One DB connection, opened once and passed to the section builders.
     *** CHW: put real credentials in the constants below, or swap in your
     config include. ***
   - Section builders now emit the new card markup (was LI/UL).
   - Era tabs fed by a json_encode()d ERA_DATA object (eradata token).
   - Alternate names parsed into .lang-tag chips (altnames token).
   - Resources hide when empty; if ALL are empty, one "No resources" line.
   - JGFF always shows; zero matches -> register CTA to /jgff/.

   FLAGS
   - Search endpoints (/databases/jgform.php, /databases/all/) carry the same
     "confirm with Gary" status as the rest of the JG40 search wiring.
   - Landsmanshaft section grouping (Section 1 / 2 ...) from the mockup is NOT
     done here - the live data is a flat list; grouping needs a data check.
============================================================================ */

// ---- credentials ----
define('JG_DB_HOST', 'jewishgen13:3306');
define('JG_DB_USER', 'mtobias');
define('JG_DB_PASS', 'nt732b#$');
define('JG_DB_NAME', 'jewishgen');

// ODBC DSN for JewishGen-erosity (funding) projects
define('JG_GEN_DSN',  'Generosity');
define('JG_GEN_USER', 'Generosity');
define('JG_GEN_PASS', 'p1mpl3');

include_once '../databases/cureetc.php';
include_once '../databases/bootstrap.php';
include_once '../databases/msbootstrap.php';

$time_start = microtime(true);

$jgid = require_userinfo();

$usbgn = isset($_GET['usbgn']) ? $_GET['usbgn'] : '';
if (strlen($usbgn) > 10) { // crude injection guard (kept from legacy)
    exit;
}
$scale = isset($_GET['scale']) ? $_GET['scale'] : '';

$con = mysqli_connect(JG_DB_HOST, JG_DB_USER, JG_DB_PASS, JG_DB_NAME);
mysqli_set_charset($con, 'utf8');

$sql = "SELECT * FROM communities_view where feature = '" . $usbgn . "'";
$result = mysqli_query($con, $sql);
$rowcount = mysqli_num_rows($result);
$row = mysqli_fetch_array($result);

/* ===========================  RECORD FOUND  ============================== */
if ($rowcount > 0) {

    $deg = html_entity_decode('&deg;', ENT_QUOTES, 'UTF-8'); // degree sign at runtime

    $shtetl_nam = $row['shtetl_nam'];
    $shtetl_cou = $row['shtetl_cou'];
    $shtetl__   = $row['shtetl__'];
    $feature    = $row['feature'];
    $gub        = $row['guberniya'];
    $sn         = $row['shtetl_not'];   // public notes only (never shtetl_hid)
    $pp1900     = $row['pop1900'];

    $n1900 = $row['name1900']; $n1930 = $row['name1930'];
    $n1950 = $row['name1950']; $n2000 = $row['name2000'];

    $c1900 = $row['count1900']; $c1930 = $row['count1930']; $c1950 = $row['count1950'];

    // raw district/province (for ERA_DATA) + linked anchors (for the glance grid)
    $dist1900 = $row['dist1900']; $prov1900 = $row['prov1900'];
    $dist1930 = $row['dist1930']; $prov1930 = $row['prov1930'];

    $d1900u = $dist1900 ? '/Communities/jgcd.php?get=y&dist1900=' . $dist1900 . ($prov1900 ? '&prov1900=' . $prov1900 : '') : '';
    $p1900u = $prov1900 ? '/Communities/jgcd.php?get=y&prov1900=' . $prov1900 : '';
    $d1930u = $dist1930 ? '/Communities/jgcd.php?get=y&dist1930=' . $dist1930 . ($prov1930 ? '&prov1930=' . $prov1930 : '') : '';
    $p1930u = $prov1930 ? '/Communities/jgcd.php?get=y&prov1930=' . $prov1930 : '';

    $d1900 = $dist1900 ? '<a href="' . $d1900u . '" target="_blank" rel="noopener">' . $dist1900 . '</a>' : '&nbsp;';
    $p1900 = $prov1900 ? '<a href="' . $p1900u . '" target="_blank" rel="noopener">' . $prov1900 . '</a>' : '&nbsp;';
    $d1930 = $dist1930 ? '<a href="' . $d1930u . '" target="_blank" rel="noopener">' . $dist1930 . '</a>' : '&nbsp;';
    $p1930 = $prov1930 ? '<a href="' . $p1930u . '" target="_blank" rel="noopener">' . $prov1930 . '</a>' : '&nbsp;';

    // ---- coordinates (legacy DMS-digit parsing) -> $coorddisplay + $loc + centre
    $latitude = $row['latitude']; $longitude = $row['longitude'];
    $lenlong = strlen($longitude);
    $loclink = '<A HREF="/databases/gazetteer/gazetteer3.php?feature=' . $feature . '" TITLE="JewishGen Gazetteer">';
    $coorddisplay = ''; $latcentre = ''; $longcentre = '';

    if ($latitude >= 0 && $longitude >= 0) {
        $coorddisplay = substr($latitude,0,2).$deg.substr($latitude,2,2)."' N, ".substr($longitude,0,$lenlong-2).$deg.substr($longitude,$lenlong-2,2)."' E";
        $latcentre = substr($latitude,0,2) + substr($latitude,2,2)/60;
        $longcentre = substr($longitude,0,$lenlong-2) + substr($longitude,$lenlong-2,2)/60;
    } elseif ($latitude < 0 && $longitude >= 0) {
        $coorddisplay = substr($latitude,1,2).$deg.substr($latitude,3,2)."' S, ".substr($longitude,0,$lenlong-2).$deg.substr($longitude,$lenlong-2,2)."' E";
        $latcentre = substr($latitude,0,3) - substr($latitude,3,2)/60;
        $longcentre = substr($longitude,0,$lenlong-2) + substr($longitude,$lenlong-2,2)/60;
    } elseif ($latitude >= 0 && $longitude < 0) {
        $coorddisplay = substr($latitude,0,2).$deg.substr($latitude,2,2)."' N, ".substr($longitude,1,$lenlong-2).$deg.substr($longitude,$lenlong-2,2)."' W";
        $latcentre = substr($latitude,0,2) + substr($latitude,2,2)/60;
        $longcentre = substr($longitude,0,$lenlong-2) - substr($longitude,$lenlong-2,2)/60;
    } elseif ($latitude < 0 && $longitude < 0) {
        $coorddisplay = substr($latitude,1,2).$deg.substr($latitude,3,2)."' S, ".substr($longitude,1,$lenlong-2).$deg.substr($longitude,$lenlong-2,2)."' W";
        $latcentre = substr($latitude,0,3) - substr($latitude,3,2)/60;
        $longcentre = substr($longitude,0,$lenlong-2) - substr($longitude,$lenlong-2,2)/60;
    }
    // HTML version (entity degree) wrapped in the gazetteer link, for the title band
    $loc = $loclink . str_replace($deg, '&deg;', $coorddisplay) . '</A>';

    // ---- ERA_DATA (flattened for cards-as-toggle + Leaflet renderer; decoded to real UTF-8)
    function _d($s) { return html_entity_decode((string)$s, ENT_QUOTES, 'UTF-8'); }
    $coords = ['lat' => $latcentre, 'lng' => $longcentre];   // town is fixed across eras
    $era = [
        'prewwi' => [
            'label' => 'Pre-WWI (c. 1900)', 'town' => _d($n1900),
            'district' => _d($dist1900), 'province' => _d($prov1900), 'country' => _d($c1900),
            'coords' => $coords, 'mapType' => 'leaflet', 'border' => '1900',
            'sourceNote' => 'Historical borders, c. 1900',
        ],
        'interwar' => [
            'label' => 'Interwar (c. 1930)', 'town' => _d($n1930),
            'district' => _d($dist1930), 'province' => _d($prov1930), 'country' => _d($c1930),
            'coords' => $coords, 'mapType' => 'leaflet', 'border' => '1930',
            'sourceNote' => 'Historical borders, c. 1930',
        ],
        'postwar' => [
            'label' => 'Post War (c. 1960)', 'town' => _d($n1950),   // borders = 1960; names/country from 1950 cols
            'district' => '', 'province' => '', 'country' => _d($c1950),
            'coords' => $coords, 'mapType' => 'leaflet', 'border' => '1960',
            'sourceNote' => 'Historical borders, c. 1960',
        ],
        'present' => [
            'label' => 'Present Day', 'town' => _d($n2000),
            'district' => '', 'province' => '', 'country' => _d($shtetl_cou),
            'coords' => $coords, 'mapType' => 'google',
            'sourceNote' => 'Map source: Google Maps, present day',
        ],
    ];
    $eradata = json_encode($era, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    // ---- assembled token blobs
    $altnames = format_altnames($row['alternate_']);
    $mtnearby = nearby_items($con, $scale, $latcentre, $longcentre, $usbgn, $shtetl_cou, 5);
    $resources = build_resources($con, $usbgn, $shtetl__, (int)$row['YBS'], $shtetl_nam, $shtetl_cou);

    // JGFF count + always-on card
    $sqljgff = "SELECT count(*) as recs FROM jewishgen.jgffdata where townid='" . $row['jgffid'] . "'";
    $rowjgff = mysqli_fetch_array(mysqli_query($con, $sqljgff));
    $jgffcard = jgff_card((int)$rowjgff['recs'], $feature);

    $additionalinfo = additionalinfo_card($con, $usbgn);
    $projcard = projcard($usbgn);

    list($searchcard_db, $searchcard_web) = build_search_cards($n2000, $n1950, $n1930, $n1900, $shtetl_cou);

    $ajax = '<script src="https://www.jewishgen.org/Ajax/popUpHypertext.js" type="text/javascript"></script>';

    mysqli_free_result($result);
    mysqli_close($con);

    // ---- fill the template ----
    $find = [
        '<%=shtetls.SHTETL_NAM %>', '<%=shtetls.SHTETL_COU %>', '<%= cou %>',
        '<%=gub %>', '<%=loc %>', '<%=altnames %>', '<%=pp1900 %>', '<%=sn %>',
        '<%=n1900 %>', '<%=d1900 %>', '<%=p1900 %>', '<%=c1900 %>',
        '<%=n1930 %>', '<%=d1930 %>', '<%=p1930 %>', '<%=c1930 %>',
        '<%=n1950 %>', '<%=c1950 %>', '<%=n2000 %>',
        '<%=eradata %>', '<%=mtnearby %>', '<%=resources %>',
        '<%=searchcard_db %>', '<%=searchcard_web %>',
        '<%=jgff %>', '<%=additionalinfo %>', '<%=projcard %>', '<%=ajax %>',
    ];
    $repl = [
        $shtetl_nam, $shtetl_cou, $shtetl_cou,
        $gub, $loc, $altnames, $pp1900, $sn,
        $n1900, $d1900, $p1900, $c1900,
        $n1930, $d1930, $p1930, $c1930,
        $n1950, $c1950, $n2000,
        $eradata, $mtnearby, $resources,
        $searchcard_db, $searchcard_web,
        $jgffcard, $additionalinfo, $projcard, $ajax,
    ];

    $html = file_get_contents('communitypage.html');
    echo str_replace($find, $repl, $html);

/* ===========================  NO RECORD  ================================= */
} else {
    mysqli_free_result($result);
    mysqli_close($con);
    $msg = ($usbgn === '') ? 'No community was specified.' : 'No community found for that identifier.';
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">'
       . '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
       . '<title>JewishGen Communities Database</title>'
       . '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">'
       . '<link rel="stylesheet" href="/jg-global.css"><link rel="stylesheet" href="/Communities/jg-community.css"></head><body>'
       . '<div id="site-header"></div><main><div class="page-state"><div class="state-icon" aria-hidden="true"></div>'
       . '<h2>Community Not Found</h2><p>' . $msg . '</p>'
       . '<p><a href="/Communities/search.html">Return to the Communities Database search</a>.</p></div></main>'
       . '<div id="site-footer"></div>'
       . '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>'
       . '<script>function lc(i,f){fetch(f).then(r=>r.text()).then(h=>{document.getElementById(i).innerHTML=h;}).catch(e=>{});}'
       . "lc('site-header','/header_navbar.html');lc('site-footer','/footer.html');</script></body></html>";
}

$time_end = microtime(true);
write_mysql_jglog(($time_end - $time_start), $jgid, get_client_ip());


/* ============================================================================
   HELPERS
============================================================================ */

/* Alternate names: "Name [Code, Code], ..." -> one lang-tag chip per code;
   bracket-less spellings -> "Also known as". CHW */
function format_altnames($alternate)
{
    if (trim((string)$alternate) === '') { return '&nbsp;'; }

    // Split on top-level commas only -- a comma inside [ ... ] is left intact,
    // so "Cracovia [Sp, Ital]" stays as one group.
    $parts = preg_split('/,(?![^\[]*\])/', $alternate);

    $tagged = ''; $extra = [];
    foreach ($parts as $p) {
        $p = trim($p);
        if ($p === '') { continue; }
        if (preg_match('/^(.*?)\s*\[([^\]]+)\]\s*$/', $p, $m)) {
            $name  = trim($m[1]);
            $codes = array_map('trim', explode(',', $m[2]));   // one chip per code
            $chips = '';
            foreach ($codes as $code) {
                if ($code !== '') { $chips .= '<span class="lang-tag">' . $code . '</span> '; }
            }
            $tagged .= $chips . $name . ' &nbsp; ';
        } else {
            $extra[] = $p;
        }
    }

    $out = $tagged;
    if (count($extra) > 0) {
        $out .= '<br><span class="alt-names-extra">Also known as: ' . implode(', ', $extra) . '</span>';
    }
    return $out !== '' ? $out : '&nbsp;';
}

/* ---- resources tab: present cards joined, or a single fallback line ---- */
function build_resources($con, $usbgn, $shtetl__, $ybs, $shtetl_nam, $shtetl_cou)
{
    $cards = '';
    if ($ybs > 0) {
        $cards .= resource_card('', 'Yizkor Books', $ybs, yb_body($con, $ybs, $shtetl__));
    }
    list($klc, $klh) = kl_body($con, $usbgn);
    if ($klc > 0) { $cards .= resource_card(' sage-accent', 'KehilaLinks', $klc, $klh); }

    list($jlc, $jlh) = jowbr_local_body($con, $usbgn);
    if ($jlc > 0) { $cards .= resource_card(' sage-accent', 'Local Cemetery (JOWBR)', $jlc, $jlh); }

    list($lsc, $lsh) = landsmanshaft_body($con, $usbgn);
    if ($lsc > 0) { $cards .= resource_card(' sage-accent', 'Landsmanshaft Cemeteries', $lsc, $lsh); }

    list($mpc, $mph) = memorials_body($con, $usbgn);
    if ($mpc > 0) { $cards .= resource_card(' sage-accent', 'Memorials &amp; Plaques', $mpc, $mph); }

    if ($cards === '') {
        return '<p class="empty-state" style="grid-column:1 / -1; text-align:center; padding:30px 20px;">No resources for '
             . $shtetl_nam . ', ' . $shtetl_cou . '.</p>';
    }
    return $cards;
}

function resource_card($accent, $title, $count, $bodyhtml)
{
    return '<div class="resource-card' . $accent . '">'
         . '<div class="resource-card-header"><span class="res-icon" aria-hidden="true"></span>'
         . '<h3>' . $title . '</h3><span class="res-count">' . $count . '</span></div>'
         . '<div class="resource-card-body">' . $bodyhtml . '</div></div>';
}

/* Yizkor: first 6 books, "more" if >6 */
function yb_body($con, $ybs, $shtetl__)
{
    $sql = "SELECT book__ FROM jewishgen.SHTETLSBOOKS where shtetl__='" . $shtetl__ . "'";
    $r = mysqli_query($con, $sql);
    $list = '';
    while ($row = mysqli_fetch_array($r)) {
        $list .= ($list ? ',' : '') . "'" . $row['book__'] . "'";
    }
    mysqli_free_result($r);
    if ($list === '') { return ''; }

    $sql = 'SELECT book__,original_t,pub_place,pub_date,nypl_id FROM jewishgen.BOOKS where book__ IN (' . $list . ')';
    $r = mysqli_query($con, $sql);
    $body = ''; $ctr = 0;
    while ($row = mysqli_fetch_array($r)) {
        if (++$ctr > 6) { break; }
        $body .= '<div class="yizkor-item">'
              . '<a href="/databases/yizkor/yizkor.php?townid=' . $shtetl__ . '&bookid=' . $row['book__'] . '" target="_blank" rel="noopener"><em>' . $row['original_t'] . '</em></a>';
        if (!empty($row['nypl_id'])) {
            $body .= ' <a href="http://yizkor.nypl.org/index.php?id=' . $row['nypl_id'] . '" target="_blank" rel="noopener" class="yizkor-nypl" title="View digitized book at NYPL" aria-label="View digitized book at NYPL"><img src="/images/bookicon.jpg" alt="" style="height:14px;vertical-align:middle;"></a>';
        }
        $body .= '<div class="yizkor-meta">' . $row['pub_place'] . ' &middot; ' . $row['pub_date'] . '</div></div>';
    }
    mysqli_free_result($r);
    if ($ybs > 6) {
        $body .= '<a href="/databases/yizkor/yizkor.php?townid=' . $shtetl__ . '" target="_blank" rel="noopener" class="yizkor-more">View all ' . $ybs . ' books &rarr;</a>';
    }
    return $body;
}

function kl_body($con, $usbgn)
{
    $r = mysqli_query($con, "SELECT url FROM jewishgen.kehilalinks where feature='" . $usbgn . "'");
    $body = ''; $ctr = 0;
    while ($row = mysqli_fetch_array($r)) {
        $ctr++;
        if (!empty($row['url'])) {
            $body .= '<div class="kehila-item"><a href="' . $row['url'] . '" target="_blank" rel="noopener">KehilaLinks Community Page</a></div>';
        } else {
            $body .= '<div class="kehila-item">In Progress</div>';
        }
    }
    mysqli_free_result($r);
    return [$ctr, $body];
}

function jowbr_local_body($con, $usbgn)
{
    $r = mysqli_query($con, "SELECT cemeteryid,cem_name FROM jewishgen.cemetery where (insystem='live' AND usbgn_code='" . $usbgn . "')");
    $body = ''; $ctr = 0;
    while ($row = mysqli_fetch_array($r)) {
        if (empty($row['cemeteryid'])) { continue; }
        $ctr++;
        $name = trim($row['cem_name']) === '' ? 'Jewish Cemetery' : $row['cem_name'];
        $body .= '<div class="jowbr-item"><a href="/databases/cemetery/jowbrshow.php?id=' . $row['cemeteryid'] . '" target="_blank" rel="noopener">' . $name . '</a></div>';
    }
    mysqli_free_result($r);
    return [$ctr, $body];
}

/* Landsmanshaft: flat list (mockup's Section 1/2 grouping deferred - needs data check) */
function landsmanshaft_body($con, $usbgn)
{
    $r = mysqli_query($con, "SELECT cemeteryid,cem_name,city,country FROM jewishgen.cemetery where (insystem='live' AND land_usbgn='" . $usbgn . "')");
    $body = ''; $ctr = 0;
    while ($row = mysqli_fetch_array($r)) {
        $ctr++;
        $body .= '<div class="landsmanshaft-item"><a href="/databases/cemetery/jowbrshow.php?id=' . $row['cemeteryid'] . '" target="_blank" rel="noopener">' . $row['cem_name'] . '</a>'
              . '<div class="landsmanshaft-meta">' . $row['city'] . ', ' . $row['country'] . '</div></div>';
    }
    mysqli_free_result($r);
    return [$ctr, $body];
}

function memorials_body($con, $usbgn)
{
    $r = mysqli_query($con, "SELECT cemeteryid,cem_name FROM jewishgen.memorial where (insystem='live' AND usbgn_code='" . $usbgn . "')");
    $body = ''; $ctr = 0;
    while ($row = mysqli_fetch_array($r)) {
        if (empty($row['cemeteryid'])) { continue; }
        $ctr++;
        $name = trim($row['cem_name']) === '' ? 'Memorial' : $row['cem_name'];
        $body .= '<div class="jowbr-item"><a href="/databases/memorial/memorialshow.php?id=' . $row['cemeteryid'] . '" target="_blank" rel="noopener">' . $name . '</a></div>';
    }
    mysqli_free_result($r);
    return [$ctr, $body];
}

/* Additional Information (smlt): whole connect-card, or '' when empty */
function additionalinfo_card($con, $usbgn)
{
    // brockhaus exclusion preserved
    $r = mysqli_query($con, "SELECT url,text FROM smlt where url not like '%brockhaus%' and townid='" . $usbgn . "' order by datasetid desc");
    $items = '';
    while ($row = mysqli_fetch_array($r)) {
        if (empty($row['url'])) {
            $items .= '<div class="bib-item">' . $row['text'] . '</div>';
        } else {
            $items .= '<div class="bib-item"><a href="' . $row['url'] . '" target="_blank" rel="noopener">' . $row['text'] . '</a></div>';
        }
    }
    mysqli_free_result($r);
    if ($items === '') { return ''; }

    return '<div class="connect-card"><div class="connect-card-header"><span class="con-icon" aria-hidden="true"></span>'
         . '<h3>Additional Information</h3></div><div class="connect-card-body">' . $items . '</div></div>';
}

/* JGFF: ALWAYS shown.  >0 -> match block; 0 -> register CTA */
function jgff_card($count, $feature)
{
    $intro = '<p style="font-size:13px;margin:0 0 14px 0;color:var(--charcoal);line-height:1.6;">Researchers who have registered surnames connected to this community in the JewishGen Family Finder. Matches are linked via the community&rsquo;s USBGN feature number, covering all historical names for this location.</p>';

    if ($count > 0) {
        $block = '<div class="jgff-block"><div><div class="jgff-number">' . number_format($count) . '</div>'
               . '<div class="jgff-label">' . ($count === 1 ? 'Registered Surname Match' : 'Registered Surname Matches') . '</div></div>'
               . '<a href="/jgff/jgffform.php?feature=' . $feature . '" target="_blank" rel="noopener" class="btn-search-db">View All Matches &rarr;</a></div>';
    } else {
        $block = '<div class="jgff-block"><div><div class="jgff-label" style="font-size:14px;color:#fff;text-transform:none;letter-spacing:0;">No matches found &mdash; register your surnames to get started.</div></div>'
               . '<a href="/jgff/" target="_blank" rel="noopener" class="btn-search-db">Register Surnames &rarr;</a></div>';
    }

    return '<div class="connect-card navy-accent full-width"><div class="connect-card-header">'
         . '<span class="con-icon" aria-hidden="true"></span><h3>JewishGen Family Finder (JGFF)</h3></div>'
         . '<div class="connect-card-body">' . $intro . $block . '</div></div>';
}

/* JewishGen-erosity funding: whole connect-card in Connect & Share, or '' */
function projcard($usbgn)
{
    if (!function_exists('odbc_connect')) { return ''; } // dev may lack the ODBC extension
    $gconn = @odbc_connect(JG_GEN_DSN, JG_GEN_USER, JG_GEN_PASS);
    if (!$gconn) { return ''; }

    $sql = "select cast(ISNULL(project_name,'') as varchar(254)) as projname, ISNULL(projproposal,'') as projproposal from projects where (USBGN = '" . $usbgn . "' and projactive=1)";
    $stmt = odbc_exec($gconn, $sql);
    $items = ''; $ctr = 0;
    if ($stmt) {
        while (odbc_fetch_row($stmt)) {
            $ctr++;
            $name = odbc_result($stmt, 'projname');
            $prop = odbc_result($stmt, 'projproposal');
            $items .= '<div class="bib-item"><a href="' . $prop . '" target="_blank" rel="noopener">' . $name . '</a></div>';
        }
        odbc_free_result($stmt);
    }
    odbc_close($gconn);
    if ($ctr === 0) { return ''; }

    return '<div class="connect-card"><div class="connect-card-header"><span class="con-icon" aria-hidden="true"></span>'
         . '<h3>JewishGen-erosity Projects</h3><span class="res-count">' . $ctr . '</span></div>'
         . '<div class="connect-card-body">' . $items . '</div></div>';
}

/* Two Search-tab cards: country DB (POST jgform.php) + Global Search (GET /databases/all/) */
function build_search_cards($n2000, $n1950, $n1930, $n1900, $shtetl_cou)
{
    // country -> JewishGen DB
    $map = [
        'Poland'=>['00poland','Poland'], 'Belarus'=>['00belarus','Belarus'],
        'Germany'=>['00germany','Germany'], 'Lithuania'=>['00lithuania','Lithuania'],
        'Ukraine'=>['00ukraine','Ukraine'], 'Romania'=>['00romania','Romania'],
        'Moldova'=>['00romania','Romania'], 'Latvia'=>['00latvia','Latvia'],
        'Estonia'=>['00latvia','Latvia'], 'Hungary'=>['00hungary','Hungary'],
        'Slovakia'=>['00hungary','Hungary'], 'Croatia'=>['00hungary','Hungary'],
        'Denmark'=>['00scandinavia','Scandinavia'], 'Sweden'=>['00scandinavia','Scandinavia'],
        'Norway'=>['00scandinavia','Scandinavia'], 'Finland'=>['00scandinavia','Scandinavia'],
        'Austria'=>['00austriaczech','Austria-Czech'], 'Czech'=>['00austriaczech','Austria-Czech'],
        'Czech Republic'=>['00austriaczech','Austria-Czech'],
        'France'=>['00france','France'], 'Belgium'=>['00france','France'],
        'Switzerland'=>['00france','France'], 'Morocco'=>['00france','France'],
        'Tunisia'=>['00france','France'], 'Algeria'=>['00france','France'],
        'Luxembourg'=>['00france','France'],
    ];
	$allc = isset($map[$shtetl_cou][0]) ? $map[$shtetl_cou][0] : '';
	$dbcountry = isset($map[$shtetl_cou][1]) ? $map[$shtetl_cou][1] : '';

    // dedupe spellings (present first), keep originals for display/q, flatten for jgform
    $names = []; $flat = []; $seen = [];
    foreach ([$n2000, $n1950, $n1930, $n1900] as $nm) {
        $nm = trim(html_entity_decode((string)$nm, ENT_QUOTES, 'UTF-8'));
        if ($nm === '') { continue; }
        $key = strtolower(preg_replace('/\s+/', ' ', $nm));   // normalized dedupe key
        if (in_array($key, $seen, true)) { continue; }
        $seen[] = $key;
        $names[] = $nm;
        $flat[] = preg_replace('/[^a-z ]/i', '', iconv('UTF-8', 'ASCII//TRANSLIT', $nm));
    }

    $tags = '';
    foreach ($names as $nm) { $tags .= '<span class="search-name-tag">' . $nm . '</span>'; }

    // --- country DB card (POST jgform.php) - '' when country not mapped ---
    $db = '';
    if ($allc !== '') {
        $hidden = '<input type="hidden" name="allcountry" value="' . $allc . '">';
        $i = 0;
        foreach ($flat as $f) {
            $i++;
            $hidden .= '<input type="hidden" name="srch' . $i . '" value="' . $f . '">'
                     . '<input type="hidden" name="srch' . $i . 'v" value="T">'
                     . '<input type="hidden" name="srch' . $i . 't" value="E">';
        }
        $hidden .= '<input type="hidden" name="SrchBOOL" value="OR">';
        $db = '<form class="search-option-card" method="POST" action="/databases/jgform.php" target="_blank">'
            . $hidden
            . '<h3>JewishGen ' . $dbcountry . ' Database</h3>'
            . '<p>Search the ' . $dbcountry . '-specific database for all records associated with ' . $names[0] . ' and its historical names.</p>'
            . '<div class="search-names-list">' . $tags . '</div>'
            . '<button type="submit" class="btn-search-db">Search ' . $dbcountry . ' Database &rarr;</button></form>';
    }

    // --- Global Search card (GET /databases/all/) - endpoint pending Gary ---
    $web = '<form class="search-option-card" method="GET" action="/databases/all/" target="_blank">'
         . '<input type="hidden" name="q" value="' . implode(' ', $names) . '">'
         . '<h3>JewishGen Global Search</h3>'
         . '<p>Search across all JewishGen databases worldwide for records mentioning ' . $names[0] . ' or any of its alternate names.</p>'
         . '<div class="search-names-list">' . $tags . '</div>'
         . '<button type="submit" class="btn-search-db">Search All Databases &rarr;</button></form>';

    return [$db, $web];
}

/* Nearby communities -> .nearby-list <li> items */
function nearby_items($con, $scale, $latcentre, $longcentre, $usbgn, $startcountry, $min_entries)
{
    if ($scale === 'K') { $scale2 = 6371; $scale3 = 50; } else { $scale2 = 3959; $scale3 = 30; }
    $max = 100;

    $sql = 'SELECT shtetl_nam,shtetl_cou,locality,feature,(lat1+lat2/60) as lat,(long1+long2/60) as lon,( '
         . $scale2 . ' * acos( cos( radians(' . $latcentre . ') ) * cos( radians( lat1+lat2/60 ) ) '
         . '* cos( radians( long1+long2/60 ) - radians(' . $longcentre . ') ) + sin( radians(' . $latcentre . ') ) '
         . '* sin(radians(lat1+lat2/60)) ) ) AS distance FROM communities_view HAVING distance < ' . $max . ' ORDER BY distance ';
    $r = mysqli_query($con, $sql);

    $out = ''; $tow = 0;
    while ($row = mysqli_fetch_array($r)) {
        if ($row['feature'] !== $usbgn && $row['locality'] === 'Town' && (round($row['distance']) <= $scale3 || $tow < $min_entries)) {
            $tow++;
            $href = '/Communities/community.php?usbgn=' . $row['feature'] . ($scale === 'K' ? '&scale=K' : '');
            $name = $row['shtetl_nam'] . ($row['shtetl_cou'] !== $startcountry ? ', ' . $row['shtetl_cou'] : '');
            $dist = round($row['distance']) . ($scale === 'K' ? ' km ' : ' mi ')
                  . getCompassDirection(getRhumbLineBearing($latcentre, $longcentre, $row['lat'], $row['lon']));
            $out .= '<li><a href="' . $href . '">' . $name . '</a> <span class="nearby-dist">' . $dist . '</span></li>';
        }
    }
    mysqli_free_result($r);
    return $out;
}

function getRhumbLineBearing($lat1, $lon1, $lat2, $lon2)
{
    $dLon = deg2rad($lon2) - deg2rad($lon1);
    $dPhi = log(tan(deg2rad($lat2)/2 + pi()/4) / tan(deg2rad($lat1)/2 + pi()/4));
    if (abs($dLon) > pi()) { $dLon = $dLon > 0 ? ($dLon - 2*pi()) : ($dLon + 2*pi()); }
    return (rad2deg(atan2($dLon, $dPhi)) + 360) % 360;
}

function getCompassDirection($bearing)
{
    static $c = ['N','NNE','NE','ENE','E','ESE','SE','SSE','S','SSW','SW','WSW','W','WNW','NW','NNW','N'];
    return $c[round($bearing / 22.5)];
}
?>
