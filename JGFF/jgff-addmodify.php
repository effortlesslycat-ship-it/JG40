<?php
// jgff-addmodify.php -- CHW JG40 redesign
// Replaces: jgffviewadd.php
// Add or modify JGFF entries in the new design.
// Backend logic preserved from jgffviewadd.php unchanged.
// Form still posts to jgffupdtadd.php unchanged.

include_once '../databases/cureetc.php';
include '../databases/bootstrap.php';
include '../databases/msbootstrap.php';
include '_bootstrap.php';

$time_start = microtime(true);

$jgid = require_userinfo();

$code = !isset($_GET['code']) ? '' : $_GET['code'];
$add  = !isset($_GET['add'])  ? 'N' : $_GET['add'];
if (strlen($code) === 0) { $code = $jgid; }

$system = 'JGFF';
$access = check_permission($system, $jgid);
if ($access !== 'A' and $access !== 'E' and $code !== $jgid) { return; }

// Build country list for select dropdowns
$con = mysqli_connect(MYSQL_SERVER_HOSTNAME, MYSQL_SERVER_USERNAME, MYSQL_SERVER_PASSWORD, MYSQL_SERVER_DATABASE);
$country2_sql = 'SELECT country,name FROM country2 order by name';
$country2     = mysqli_query($con, $country2_sql);
$country_list = '<option value="NONE">Select Country</option>';
while ($country2_row = mysqli_fetch_row($country2)) {
    $country_list .= '<option value="' . trim($country2_row[0]) . '">' . $country2_row[1] . '</option>';
}
$country_list = rtrim($country_list) . '</select>';
mysqli_close($con);

// Fetch existing entries (modify mode)
$rows = array();
$ctr  = 0;
if ($add !== 'Y') {
    $con = mysqli_connect(MYSQL_SERVER_HOSTNAME, MYSQL_SERVER_USERNAME, MYSQL_SERVER_PASSWORD, MYSQL_SERVER_DATABASE);
    $sql = "SELECT
        jewishgen.jgffdata.code, surname, town,
        jewishgen.jgfftowns.country,
        jewishgen.jgfftowns.id as townid,
        jewishgen.jgfftowns.usbgn as usbgn,
        jewishgen.jgfftowns.communitypage,
        jewishgen.jgffdata.id
        FROM jewishgen.jgffdata
        JOIN jewishgen.jgfftowns ON jewishgen.jgffdata.townid = jewishgen.jgfftowns.id
        WHERE jewishgen.jgffdata.code='" . $code . "'
        ORDER BY surname";
    $result = $con->query($sql);
    while ($row = mysqli_fetch_row($result)) {
        $rows[] = $row;
        $ctr++;
    }
    mysqli_close($con);

    // If no entries exist, redirect to add mode
    if ($ctr === 0) {
        header('Location: /JGFF/jgff-addmodify.php?add=Y');
        exit;
    }
} else {
    $ctr = 15; // 15 blank rows for add mode
}

// Page title switches on mode
$page_title    = ($add === 'Y') ? 'Add Family Finder Entries'      : 'Modify Your Family Finder Entries';
$page_subtitle = ($add === 'Y') ? 'Add new ancestral surnames and towns to the database.' : 'Edit or update your existing surname and town entries.';
$form_heading  = ($add === 'Y') ? 'Add New Surnames &amp; Towns'    : 'Modify Your Surnames &amp; Towns';
$submit_label  = ($add === 'Y') ? 'Submit new entries'              : 'Submit changes';
$subnav_page   = ($add === 'Y') ? 'add'                             : 'modify';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($page_title); ?> &ndash; JewishGen</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="/jg-global.css">
<style>
/* == JGFF Add/Modify page styles -- CHW ==================== */
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
    margin-bottom: 20px;
}
body.dark-mode .jgff-content-card { background-color: #1e1e1e; border-color: #333; }
.jgff-content-card h2 {
    font-size: 1.125rem;
    font-family: Georgia, 'Times New Roman', serif;
    font-weight: bold;
    color: var(--navy);
    margin: 0 0 14px 0;
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
.jgff-instructions {
    border: 1px solid #d1caba;
    border-radius: 6px;
    overflow: hidden;
    margin-bottom: 20px;
}
body.dark-mode .jgff-instructions { border-color: #333; }
.jgff-instructions summary {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background-color: var(--cream);
    cursor: pointer;
    font-size: 0.9375rem;
    font-weight: bold;
    color: var(--navy);
    list-style: none;
    user-select: none;
}
.jgff-instructions summary::-webkit-details-marker { display: none; }
body.dark-mode .jgff-instructions summary { background-color: #1a1a1a; color: #e0e0e0; }
.jgff-instructions summary:hover { background-color: #ddd6c7; }
body.dark-mode .jgff-instructions summary:hover { background-color: #222; }
.jgff-instructions-chevron { font-size: 0.75rem; color: var(--sage); transition: transform 0.2s; flex-shrink: 0; }
.jgff-instructions[open] .jgff-instructions-chevron { transform: rotate(180deg); }
.jgff-instructions-body {
    padding: 16px 18px;
    background-color: var(--white);
    font-size: 0.875rem;
    line-height: 1.7;
    color: var(--charcoal);
}
body.dark-mode .jgff-instructions-body { background-color: #1e1e1e; color: #a0a0a0; }
.jgff-instr-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 12px 0; }
.jgff-instr-item {
    background-color: var(--ecru);
    border-radius: 5px;
    padding: 10px 12px;
    font-size: 0.8125rem;
    line-height: 1.55;
}
body.dark-mode .jgff-instr-item { background-color: #1a1a1a; }
.jgff-instr-item strong { color: var(--navy); }
body.dark-mode .jgff-instr-item strong { color: #e0e0e0; }
.jgff-instr-item code { font-size: 0.8125rem; color: var(--navy); font-family: inherit; font-weight: bold; background-color: var(--light-blue); border-radius: 3px; padding: 1px 5px; }
body.dark-mode .jgff-instr-item code { background-color: #2a2a2a; color: #a8b361; }
.jgff-instr-example {
    margin-top: 10px;
    background-color: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 8px 12px;
    font-family: monospace;
    font-size: 0.8125rem;
    color: var(--charcoal);
    white-space: pre;
}
body.dark-mode .jgff-instr-example { background-color: #1a1a1a; border-color: #333; color: #a0a0a0; }
.jgff-instr-faq-link { margin-top: 12px; font-size: 0.8125rem; }
.jgff-instr-faq-link a { color: var(--navy); font-weight: bold; }
.jgff-instr-faq-link a:hover { color: var(--sage); }
body.dark-mode .jgff-instr-faq-link a { color: #a8b361; }
.jgff-form-intro { font-size: 0.875rem; color: var(--charcoal); margin-bottom: 12px; line-height: 1.6; }
body.dark-mode .jgff-form-intro { color: #a0a0a0; }
.jgff-form-intro strong { color: var(--navy); }
body.dark-mode .jgff-form-intro strong { color: #e0e0e0; }
.jgff-delete-note {
    background-color: var(--cream);
    border-left: 4px solid var(--sage);
    border-radius: 0 5px 5px 0;
    padding: 8px 12px;
    font-size: 0.8125rem;
    color: var(--charcoal);
    margin-bottom: 14px;
}
body.dark-mode .jgff-delete-note { background-color: #1a1a1a; color: #a0a0a0; border-left-color: #a8b361; }
.jgff-delete-note code { font-size: 0.8125rem; color: var(--navy); font-family: inherit; font-weight: bold; }
body.dark-mode .jgff-delete-note code { background-color: #2a2a2a; color: #a8b361; }
.jgff-table-wrap { overflow-x: auto; }
.jgff-entry-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
.jgff-entry-table thead tr { background-color: #09497a; color: #ffffff; }
body.dark-mode .jgff-entry-table thead tr { background-color: #0d2a45; }
.jgff-entry-table th { padding: 10px 12px; text-align: left; font-weight: bold; font-size: 0.8125rem; white-space: nowrap; }
.jgff-entry-table td { padding: 7px 8px; border-bottom: 1px solid #e8e1d1; vertical-align: middle; }
body.dark-mode .jgff-entry-table td { border-bottom-color: #2a2a2a; }
.jgff-entry-table tbody tr:last-child td { border-bottom: none; }
.jgff-entry-table input[type="text"],
.jgff-entry-table select {
    width: 100%;
    padding: 6px 8px;
    border: 1px solid #c5bca8;
    border-radius: 4px;
    font-size: 0.875rem;
    font-family: inherit;
    background-color: var(--white);
    color: var(--charcoal);
    transition: border-color 0.2s, box-shadow 0.2s;
}
body.dark-mode .jgff-entry-table input[type="text"],
body.dark-mode .jgff-entry-table select { background-color: #2a2a2a; color: #e0e0e0; border-color: #444; }
.jgff-entry-table input[type="text"]:focus,
.jgff-entry-table select:focus { border-color: var(--sage); outline: none; box-shadow: 0 0 0 2px rgba(147,155,81,0.2); }
.jgff-num-col { color: #888; font-size: 0.8125rem; white-space: nowrap; }
body.dark-mode .jgff-num-col { color: #555; }
.jgff-submit-row { display: flex; gap: 12px; align-items: center; margin-top: 18px; flex-wrap: wrap; }
.jgff-submit-btn {
    background-color: #09497a;
    color: #ffffff;
    border: none;
    border-radius: 5px;
    padding: 10px 32px;
    font-size: 1rem;
    font-weight: bold;
    cursor: pointer;
    font-family: inherit;
    letter-spacing: 0.5px;
    transition: filter 0.2s;
}
.jgff-submit-btn:hover { filter: brightness(1.1); }
.jgff-submit-btn:focus { outline: 3px solid var(--sage); outline-offset: 2px; }
body.dark-mode .jgff-submit-btn { background-color: #0d2a45; }
.jgff-cancel-btn {
    background-color: transparent;
    color: var(--charcoal);
    border: 1px solid #c5bca8;
    border-radius: 5px;
    padding: 10px 20px;
    font-size: 0.9375rem;
    cursor: pointer;
    font-family: inherit;
    text-decoration: none;
    display: inline-block;
    transition: border-color 0.15s, color 0.15s;
}
.jgff-cancel-btn:hover { border-color: var(--navy); color: var(--navy); }
body.dark-mode .jgff-cancel-btn { color: #a0a0a0; border-color: #444; }
.jgff-sidebar { position: sticky; top: 20px; display: flex; flex-direction: column; gap: 20px; }
@media (max-width: 768px) {
    .jgff-grid { grid-template-columns: 1fr; }
    .jgff-sidebar { position: static; }
    .page-title-band { padding: 32px 24px; }
    .page-title-band h1 { font-size: 1.75rem; }
    .jgff-ecru-wrap { padding: 2rem 1rem; }
    .jgff-instr-grid { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<div id="site-header"></div>

<div class="page-title-band" role="banner">

    <span class="tagline">JewishGen Family Finder</span>

    <h1><?php echo htmlspecialchars($page_title); ?></h1>
    <p class="hero-subtitle"><?php echo htmlspecialchars($page_subtitle); ?></p>
</div>

<div class="jgff-ecru-wrap">
    <div class="jgff-ecru-inner">
        <div class="jgff-grid">

            <div>
                <!-- Instructions -->
                <details class="jgff-instructions" open>
                    <summary>
                        Instructions &amp; formatting rules
                        <span class="jgff-instructions-chevron" aria-hidden="true">&#9660;</span>
                    </summary>
                    <div class="jgff-instructions-body">
                        <p>Each entry consists of <strong>one</strong> surname associated with <strong>one</strong> town within <strong>one</strong> country.</p>
                        <div class="jgff-instr-example" aria-label="Correct and incorrect entry examples">Cohen         Warszawa       Poland       &lt;-- CORRECT
Kahn          Warszawa       Poland       &lt;-- CORRECT
Cohen/Kahn    Warszawa       Poland       &lt;-- INCORRECT</div>
                        <div class="jgff-instr-grid">
                            <div class="jgff-instr-item">
                                <strong>Surname spelling</strong><br>
                                You do not need to enter variant spellings. The Daitch-Mokotoff Soundex search finds all variants automatically.
                            </div>
                            <div class="jgff-instr-item">
                                <strong>Town names</strong><br>
                                Use the <em>modern contemporary</em> native spelling: <code>Warszawa</code> not Warsaw; <code>Vilnius</code> not Vilna; <code>Wien</code> not Vienna.
                            </div>
                            <div class="jgff-instr-item">
                                <strong>USA &amp; Canada</strong><br>
                                Include the two-letter state or province abbreviation: <code>New York, NY</code> or <code>Toronto, ON</code>.
                            </div>
                            <div class="jgff-instr-item">
                                <strong>Country names</strong><br>
                                Enter the country in which the town is located <em>today</em>, regardless of historical borders.
                            </div>
                            <div class="jgff-instr-item">
                                <strong>Unknown town</strong><br>
                                If you know the country but not the specific town, use <code>Any</code> in the Town field (e.g. <code>Any, Poland</code>).
                            </div>
                            <div class="jgff-instr-item">
                                <strong>Characters</strong><br>
                                Latin letters only. No slashes, dashes, hyphens, brackets, numbers, or accented characters. Use spaces for hyphens: <code>Frankfurt am Main</code>.
                            </div>
                        </div>
                        <p class="jgff-instr-faq-link">
                            <a href="/JGFF/FAQ/">Full instructions and examples in the JGFF FAQ &rarr;</a>
                        </p>
                    </div>
                </details>

                <!-- Entry form -->
                <div class="jgff-content-card">
                    <h2><?php echo $form_heading; ?></h2>
                    <p class="jgff-form-intro">
                        Enter only <strong>one</strong> surname and <strong>one</strong> town per record.
                        Do <strong>not</strong> use: <strong>/ \ + - ( ) { } [ ] ?</strong> or any accented characters.
                    </p>
                    <div class="jgff-delete-note" role="note">
                        To <strong>delete</strong> a record, change the surname to <code>DELETE</code>
                    </div>

                    <form method="POST" action="/jgff/jgffupdtadd.php">
                        <div class="jgff-table-wrap">
                            <table class="jgff-entry-table" aria-label="Surname and town entry form">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Surname</th>
                                        <th scope="col">Town</th>
                                        <th scope="col">Country</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                if ($add !== 'Y') {
                                    // MODIFY mode -- pre-populated rows
                                    $rownum = 0;
                                    foreach ($rows as $row) {
                                        $rownum++;
                                        $dataid      = trim($row[7]);
                                        $surname_val = trim($row[1]);
                                        $town_val    = trim($row[2]);
                                        $country_val = trim($row[3]);
                                        $usbgn_val   = trim($row[5]);
                                        $community   = $row[6];

                                        $ctry = str_replace(
                                            'option value="' . $country_val . '">',
                                            'option value="' . $country_val . '" selected>',
                                            $country_list
                                        );

                                        echo '<tr>';
                                        echo '<td class="jgff-num-col">';
                                        echo $rownum;
                                        echo '<input type="hidden" name="id' . $rownum . '" value="' . htmlspecialchars($dataid) . '">';
                                        echo '</td>';
                                        echo '<td><input type="text" name="sur' . $dataid . '" maxlength="20" value="' . htmlspecialchars($surname_val) . '" aria-label="Surname for entry ' . $rownum . '"></td>';
                                        echo '<td>';
                                        echo '<span id="town' . $rownum . '_P" class="shtet" usbgn="-1">';
                                        echo '<a><img width="16" height="16" border="0" id="town' . $rownum . '_I" alt="" style="visibility:hidden"></a>';
                                        echo '<input type="text" name="town' . $dataid . '" id="town' . $rownum . '_T" class="jgffi" maxlength="40" value="' . htmlspecialchars($town_val) . '" onchange="JGFFDataChanged(this)" aria-label="Town for entry ' . $rownum . '">';
                                        echo '</span>';
                                        echo '</td>';
                                        echo '<td>';
                                        echo '<select name="Count' . $dataid . '" id="town' . $rownum . '_C" onchange="JGFFDataChanged(this)" onblur="JGFFDataChanged(this)" aria-label="Country for entry ' . $rownum . '">';
                                        echo str_replace('</select>', '', $ctry);
                                        if ($community === 'No') {
                                            echo '<input type="hidden" name="origusbgn' . $dataid . '" value="NO">';
                                        } else {
                                            echo '<input type="hidden" name="origusbgn' . $dataid . '" value="' . htmlspecialchars($usbgn_val) . '">';
                                        }
                                        echo '<input type="hidden" name="origsur'   . $dataid . '" value="' . htmlspecialchars($surname_val)  . '">';
                                        echo '<input type="hidden" name="origtown'  . $dataid . '" value="' . htmlspecialchars($town_val)     . '">';
                                        echo '<input type="hidden" name="origCount' . $dataid . '" value="' . htmlspecialchars($country_val)  . '">';
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    // ADD mode -- 15 blank rows
                                    for ($i = 1; $i <= 15; $i++) {
                                        echo '<tr>';
                                        echo '<td class="jgff-num-col">' . $i . '</td>';
                                        echo '<td><input type="text" name="sur' . $i . '" maxlength="20" value="" aria-label="Surname for entry ' . $i . '"></td>';
                                        echo '<td>';
                                        echo '<span id="town' . $i . '_P" class="shtet" usbgn="-1">';
                                        echo '<a><img width="16" height="16" border="0" id="town' . $i . '_I" alt="" style="visibility:hidden"></a>';
                                        echo '<input type="text" name="town' . $i . '" id="town' . $i . '_T" class="jgffi" maxlength="40" value="" onchange="JGFFDataChanged(this)" aria-label="Town for entry ' . $i . '">';
                                        echo '</span>';
                                        echo '</td>';
                                        echo '<td>';
                                        echo '<select name="Count' . $i . '" id="town' . $i . '_C" onchange="JGFFDataChanged(this)" onblur="JGFFDataChanged(this)" aria-label="Country for entry ' . $i . '">';
                                        echo str_replace('</select>', '', $country_list);
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>

                        <input type="hidden" name="code" value="<?php echo htmlspecialchars($code); ?>">
                        <input type="hidden" name="recs" value="<?php echo $ctr; ?>">
                        <input type="hidden" name="add"  value="<?php echo htmlspecialchars($add); ?>">

                        <div class="jgff-submit-row">
                            <button type="submit" class="jgff-submit-btn"><?php echo htmlspecialchars($submit_label); ?></button>
                            <a href="/JGFF/" class="jgff-cancel-btn">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            <aside class="jgff-sidebar" aria-label="JGFF navigation">
                <div id="jgff-subnav" data-jgff-page="<?php echo $subnav_page; ?>"></div>
            </aside>

        </div>
    </div>
</div>

<div id="site-footer"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/Ajax/popUpHypertext.js"></script>
<script src="/Ajax/JGFFTownEntry.js"></script>
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
