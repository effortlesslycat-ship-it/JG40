<?php
// jgff-list.php -- CHW JG40 redesign
// Replaces: jgfflist.php
// Displays logged-in researcher's JGFF entries in the new design.
// Backend logic preserved from jgfflist.php unchanged.

include_once '../databases/cureetc.php';
include '../databases/bootstrap.php';
include '../databases/msbootstrap.php';
include '_bootstrap.php';

$time_start = microtime(true);

$jgid = require_userinfo();

$code = !isset($_GET['code']) ? '' : $_GET['code'];
if (strlen($code) === 0) {
    $code = $jgid;
}

$system = 'JGFF';
$access = check_permission($system, $jgid);

if ($access !== 'A' and $access !== 'R' and $access !== 'E' and $access !== 'L' and $jgid !== $code) {
    return;
}

// Get researcher name and VAS status from GoldMine
$gconn = sqlsrv_connect($MSserverName, $optionsGoldmine);
if (!$gconn) {
    echo "Connection could not be established.\n";
    exit(print_r(sqlsrv_errors(), true));
}

$gquery = "SELECT dbo.CONTACT1.CONTACT AS name, dbo.CONTACT1.KEY3 AS code,
           ISNULL(dbo.CONTACT2.UJGFFALERT, '') AS gmalert,
           ISNULL(dbo.CONTACT2.UJGFFDATE,  '') AS gmalertdate
           FROM dbo.CONTACT1
           INNER JOIN dbo.CONTACT2 ON dbo.CONTACT1.ACCOUNTNO = dbo.CONTACT2.ACCOUNTNO
           WHERE (dbo.Contact1.key3 = ?)";
$gparam = array($code);
$stmt   = sqlsrv_query($gconn, $gquery, $gparam);
if ($stmt === false) {
    echo "Error in executing query.\n";
    exit(print_r(sqlsrv_errors(), true));
}
$row          = sqlsrv_fetch_array($stmt);
$name         = $row['name'];
$gmalertdate  = $row['gmalertdate'];
$vasok        = 0;
$today_dt     = new DateTime(date('Y-m-d'));
if ($gmalertdate >= $today_dt) { $vasok = 1; }
sqlsrv_free_stmt($stmt);
sqlsrv_close($gconn);

// Fetch entries from MySQL
$con = mysqli_connect(MYSQL_SERVER_HOSTNAME, MYSQL_SERVER_USERNAME, MYSQL_SERVER_PASSWORD, MYSQL_SERVER_DATABASE);
$sql = "SELECT jewishgen.jgffdata.code, surname, town,
        jewishgen.jgfftowns.country,
        if(jewishgen.jgfftowns.id='432','Any',name) as name,
        date(lastchange) as changedate,
        jewishgen.jgfftowns.id as townid,
        jewishgen.jgfftowns.usbgn as usbgn,
        ifnull(jewishgen.jgfftowns.communitypage,'No') as communitypage,
        jewishgen.jgfftowns.country
        FROM jewishgen.jgffdata
        JOIN jewishgen.jgfftowns ON jewishgen.jgffdata.townid = jewishgen.jgfftowns.id
        LEFT JOIN jewishgen.country2 ON jewishgen.jgfftowns.country = jewishgen.country2.country
        WHERE jewishgen.jgffdata.code = '" . $code . "'
        ORDER BY surname";

$result = $con->query($sql);
$rows   = array();
while ($row = mysqli_fetch_row($result)) {
    $rows[] = $row;
}
$ctr = count($rows);
mysqli_close($con);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your Family Finder Entries &ndash; JewishGen</title>
<meta name="description" content="View all of your JewishGen Family Finder (JGFF) surname and town entries.">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="/jg-global.css">
<style>
/* == JGFF List page styles -- CHW ========================== */
.page-title-band {
    background-color: #09497a;
    padding: 44px 50px;
    text-align: center;
}
body.dark-mode .page-title-band { background-color: #0d2a45; }
.page-title-band h1 {
    margin: 0 0 8px 0;
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 2.25rem;
    font-weight: normal;
    line-height: 1.15;
    color: #ffffff;
}
.page-title-band p.hero-subtitle {
    margin: 0 auto;
    font-size: 0.9375rem;
    color: rgba(255,255,255,0.85);
    max-width: 560px;
    line-height: 1.6;
}
.jgff-ecru-wrap { background-color: var(--ecru); padding: 3rem 2rem; flex: 1; }
.jgff-ecru-inner { max-width: 1100px; margin: 0 auto; }
.jgff-grid {
    display: grid;
    grid-template-columns: minmax(0, 7fr) minmax(0, 3fr);
    gap: 28px;
    align-items: start;
}
.jgff-content-card {
    background-color: var(--white);
    border: 1px solid #d1caba;
    border-radius: 8px;
    padding: 22px 24px;
}
body.dark-mode .jgff-content-card { background-color: #1e1e1e; border-color: #333; }
.jgff-content-card h2 {
    font-size: 1.125rem;
    font-family: Georgia, 'Times New Roman', serif;
    font-weight: bold;
    color: var(--navy);
    margin: 0 0 16px 0;
    padding-bottom: 8px;
    border-bottom: 1px solid #d1caba;
    position: relative;
}
body.dark-mode .jgff-content-card h2 { color: #e0e0e0; border-bottom-color: #333; }
.jgff-content-card h2::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    width: 40px;
    height: 3px;
    background-color: var(--sage);
    border-radius: 2px;
}
.jgff-entry-meta { font-size: 0.875rem; color: var(--charcoal); margin-bottom: 16px; }
body.dark-mode .jgff-entry-meta { color: #a0a0a0; }
.jgff-entry-meta a { color: var(--navy); font-weight: bold; text-decoration: underline; }
.jgff-entry-meta a:hover { color: var(--sage); }
body.dark-mode .jgff-entry-meta a { color: #a8b361; }
.jgff-table-wrap { overflow-x: auto; }
.jgff-entries-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
.jgff-entries-table thead tr { background-color: #09497a; color: #ffffff; }
body.dark-mode .jgff-entries-table thead tr { background-color: #0d2a45; }
.jgff-entries-table th {
    padding: 10px 12px;
    text-align: left;
    font-weight: bold;
    font-size: 0.8125rem;
    letter-spacing: 0.3px;
    white-space: nowrap;
}
.jgff-entries-table td {
    padding: 9px 12px;
    border-bottom: 1px solid #e8e1d1;
    color: var(--charcoal);
    vertical-align: middle;
}
body.dark-mode .jgff-entries-table td { border-bottom-color: #2a2a2a; color: #a0a0a0; }
.jgff-entries-table tbody tr:last-child td { border-bottom: none; }
.jgff-entries-table tbody tr:hover td { background-color: rgba(147,155,81,0.06); }
.jgff-town-link { color: var(--navy); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
.jgff-town-link:hover { color: var(--sage); text-decoration: underline; }
body.dark-mode .jgff-town-link { color: #a8b361; }
.jgff-town-icon { width: 16px; height: 16px; vertical-align: middle; flex-shrink: 0; }
.jgff-vas-form { margin: 0; }
.jgff-vas-btn {
    background-color: #09497a;
    color: #ffffff;
    border: none;
    border-radius: 4px;
    padding: 4px 10px;
    font-size: 0.75rem;
    font-weight: bold;
    cursor: pointer;
    font-family: inherit;
    white-space: nowrap;
    transition: filter 0.15s;
}
.jgff-vas-btn:hover { filter: brightness(1.15); }
body.dark-mode .jgff-vas-btn { background-color: #0d2a45; }
.jgff-num-col { color: #888; font-size: 0.8125rem; }
body.dark-mode .jgff-num-col { color: #555; }
.jgff-date-col { white-space: nowrap; font-size: 0.8125rem; color: #666; }
.jgff-sidebar { position: sticky; top: 20px; display: flex; flex-direction: column; gap: 20px; }
@media (max-width: 768px) {
    .jgff-grid { grid-template-columns: 1fr; }
    .jgff-sidebar { position: static; }
    .page-title-band { padding: 32px 24px; }
    .page-title-band h1 { font-size: 1.75rem; }
    .jgff-ecru-wrap { padding: 2rem 1rem; }
}
</style>
</head>
<body>

<div id="site-header"></div>
<div class="page-title-band" role="banner">
    <span class="tagline">JewishGen Family Finder</span>
    <h1>Your Family Finder Entries</h1>
    <p class="hero-subtitle">
        <?php echo htmlspecialchars($name); ?> &mdash; Researcher #<?php echo htmlspecialchars($code); ?>
    </p>
</div>

<div class="jgff-ecru-wrap">
    <div class="jgff-ecru-inner">
        <div class="jgff-grid">

            <div>
                <div class="jgff-content-card">
                    <div style="display:flex;
				justify-content:space-between;
				align-items:baseline;
				margin:0 0 14px 0;
				padding-bottom:8px;
				border-bottom:1px solid #d1caba;
				position:relative;">
    		    <h2 style="margin:0;padding:0;border:none;">Surnames &amp; Towns</h2>
    		    <a href="/JGFF/jgff-addmodify.php" 
			 style="font-size:0.8125rem;
			 	font-weight:bold;
				color:var(--navy);
				text-decoration:underline;
				white-space:nowrap;">Edit entries &rarr;</a>
 	   </div>

                    <p class="jgff-entry-meta">
                        <?php echo $ctr; ?> <?php echo ($ctr === 1 ? 'entry' : 'entries'); ?> &mdash;
                    </p>
                    <div class="jgff-table-wrap">
                        <table class="jgff-entries-table" aria-label="Your JGFF entries">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Surname</th>
                                    <th scope="col">Town</th>
                                    <th scope="col">Country</th>
                                    <th scope="col">Last Updated</th>
                                    <?php if ($vasok === 1) { echo '<th scope="col">Search</th>'; } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $ctr2 = 0;
                                foreach ($rows as $row) {
                                    $ctr2++;
                                    $dat2 = date('d M Y', strtotime($row[5]));
                                    if ($dat2 === '31 Dec 1996') { $dat2 = 'Before 1997'; }
                                    $communitypage = $row[8];
                                    $usbgn  = trim($row[7]);
                                    $townid = trim($row[6]);
                                    $town   = trim($row[2]);
                                    $surname   = trim($row[1]);
                                    $country   = trim($row[4]);
                                    $countrycode = trim($row[9]);
                                    echo '<tr>';
                                    echo '<td class="jgff-num-col">' . $ctr2 . '</td>';
                                    echo '<td>' . htmlspecialchars($surname) . '</td>';
                                    echo '<td>';
                                    if ($communitypage !== 'No') {
                                        echo '<a class="jgff-town-link" href="/communities/community.php?usbgn=' . htmlspecialchars($usbgn) . '">';
                                        echo '<img class="jgff-town-icon" src="/jg/images/JGLogoBtn16.gif" alt="Communities DB record available" title="More info about ' . htmlspecialchars($town) . '">';
                                        echo htmlspecialchars($town) . '</a>';
                                    } else {
                                        echo htmlspecialchars($town);
                                    }
                                    echo '</td>';
                                    echo '<td>' . htmlspecialchars($country) . '</td>';
                                    echo '<td class="jgff-date-col">' . $dat2 . '</td>';
                                    if ($vasok === 1) {
                                        echo '<td>';
                                        echo '<form class="jgff-vas-form" action="/jgff/jgffform.php" target="_blank" method="GET">';
                                        echo '<input type="hidden" name="surname" value="' . htmlspecialchars($surname) . '">';
                                        echo '<input type="hidden" name="town"    value="' . htmlspecialchars($town) . '">';
                                        echo '<input type="hidden" name="country" value="' . htmlspecialchars($countrycode) . '">';
                                        echo '<input type="hidden" name="dates"   value="ALL">';
                                        echo '<input type="hidden" name="ttype"   value="exact">';
                                        echo '<button type="submit" class="jgff-vas-btn">Search &rsaquo;</button>';
                                        echo '</form>';
                                        echo '</td>';
                                    }
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <aside class="jgff-sidebar" aria-label="JGFF navigation">
                <div id="jgff-subnav" data-jgff-page="list"></div>
            </aside>

        </div>
    </div>
</div>

<div id="site-footer"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/Ajax/popUpHypertext.js"></script>
<script src="/jg/scripts/jg-jgff.js"></script>
<script>
function loadComponent(id, file) {
    return fetch(file)
        .then(function(r) { if (!r.ok) throw new Error('Could not load ' + file); return r.text(); })
        .then(function(html) { document.getElementById(id).innerHTML = html; })
        .catch(function(err) { console.warn(err); });
}
Promise.all([
    loadComponent('site-header', '/Header_NavBar.html'),
    loadComponent('site-footer', '/Footer.html'),
    loadComponent('jgff-subnav', '/JGFF/jgff-subnav.html')
]).then(function() {
    initJgffSubnav();
    document.querySelectorAll('.jg-nav .dropbtn').forEach(function(btn) {
        btn.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                var expanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', String(!expanded));
                var menu = document.getElementById(this.getAttribute('aria-controls'));
                if (menu) menu.style.display = expanded ? 'none' : 'block';
            }
            if (e.key === 'Escape') {
                this.setAttribute('aria-expanded', 'false');
                var menu = document.getElementById(this.getAttribute('aria-controls'));
                if (menu) menu.style.display = 'none';
            }
        });
    });
});
</script>

</body>
</html>
<?php
$this_ip  = get_client_ip();
$time_end = microtime(true);
$duration = $time_end - $time_start;
write_mysql_jglog($duration, $jgid, $this_ip);
?>
