<?php
// jgffcontactform.php -- CHW JG40 redesign
// Contact form for JGFF researchers. Opens in _blank from search results.
// Backend logic preserved from original unchanged.

include_once '../databases/cureetc.php';
include_once '../databases/bootstrap.php';
include_once '../databases/msbootstrap.php';
include '_bootstrap.php';
include '_jgff_messages.php';

$jgffjgid  = $_GET['code'] ?? 'NONENONE';
$initsin   = substr($jgffjgid, strlen($jgffjgid) - 2, 2);
$jgffjgid  = substr($jgffjgid, 0, strlen($jgffjgid) - 2);

$jgid = require_userinfo($userinfo);
$inst = 0;

$time_start = microtime(true);

if ($inst === 0) {
    $myname  = $userinfo['fullname'];
    $myemail = $userinfo['email'];
}

$gconn = sqlsrv_connect($MSserverName, $optionsGoldmine);
if (!$gconn) {
    echo "Connection to GoldMine could not be established.\n";
    exit(print_r(sqlsrv_errors(), true));
}

$gquery  = 'SELECT dbo.CONTACT1.KEY3, dbo.CONTACT1.CONTACT, dbo.CONTACT2.UEMAIL_PRI, '
         . ' dbo.CONTACT2.UJGFFDISPL, dbo.CONTACT2.USTATRES, dbo.CONTACT2.ULASTNAME '
         . ' FROM dbo.CONTACT1 '
         . ' INNER JOIN CONTACT2 ON CONTACT1.ACCOUNTNO = CONTACT2.ACCOUNTNO '
         . ' WHERE dbo.CONTACT1.KEY3 = ? ';
$gparams = array($jgffjgid);
$stmt    = sqlsrv_query($gconn, $gquery, $gparams);
if (false === $stmt) {
    writeAuthLog('jgffcontactform', 'Error 340FF line ' . __LINE__ . ' ' . print_r(sqlsrv_errors(), true));
    echo 'Unable to get needed data 340FF. Please hit Back button and try again.';
    exit(print_r(sqlsrv_errors(), true));
}

$row         = sqlsrv_fetch_array($stmt);
$theirname   = $row['CONTACT'];
$theiremail  = $row['UEMAIL_PRI'];
$theirflag   = $row['UJGFFDISPL'];
$theirstatus = $row['USTATRES'];
$theirlast   = $row['ULASTNAME'];
$inits       = strtoupper(substr(trim($theirname), 0, 1) . substr(trim($theirlast), 0, 1));

sqlsrv_free_stmt($stmt);
sqlsrv_close($gconn);

$isDI = false;
if (strlen($userinfo['statres']) > 2 && substr($userinfo['statres'], 0, 2) === 'DI') {
    $isDI = true;
}

// Determine content state
$state = 'ok';
if ($initsin !== $inits) {
    $state = 'bad_inits';
} elseif (strlen($myname) < 2 || strlen($myemail) < 4) {
    $state = 'incomplete_profile';
} elseif (!$userinfo['validated']) {
    $state = 'not_validated';
} elseif ($isDI) {
    $state = 'di';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact JGFF Researcher &ndash; JewishGen</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="/jg-global.css">
<style>
/* == JGFF Contact Form -- CHW ============================== */
.page-title-band {
    background-color: #09497a;
    padding: 36px 50px;
    text-align: center;
}
body.dark-mode .page-title-band { background-color: #0d2a45; }
.page-title-band h1 {
    margin: 0;
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 1.75rem;
    font-weight: normal;
    color: #ffffff;
}
.jgff-ecru-wrap { background-color: var(--ecru); padding: 2.5rem 2rem; min-height: 60vh; }
.jgff-ecru-inner { max-width: 740px; margin: 0 auto; }
.jgff-card {
    background-color: var(--white);
    border: 1px solid #d1caba;
    border-radius: 8px;
    padding: 28px 32px;
}
body.dark-mode .jgff-card { background-color: #1e1e1e; border-color: #333; }
.jgff-card h2 {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 1.125rem;
    font-weight: bold;
    color: var(--navy);
    margin: 0 0 6px 0;
    padding-bottom: 8px;
    border-bottom: 1px solid #d1caba;
    position: relative;
}
body.dark-mode .jgff-card h2 { color: #e0e0e0; border-bottom-color: #333; }
.jgff-card h2::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    width: 40px;
    height: 3px;
    background-color: var(--sage);
    border-radius: 2px;
}
.jgff-to-from {
    font-size: 0.875rem;
    color: var(--charcoal);
    margin: 16px 0;
    line-height: 1.7;
}
body.dark-mode .jgff-to-from { color: #a0a0a0; }
.jgff-to-from strong { color: var(--navy); }
body.dark-mode .jgff-to-from strong { color: #e0e0e0; }
.jgff-disclaimer {
    background-color: var(--cream);
    border-left: 3px solid var(--sage);
    border-radius: 0 5px 5px 0;
    padding: 10px 14px;
    font-size: 0.8125rem;
    color: var(--charcoal);
    line-height: 1.6;
    margin-bottom: 20px;
}
body.dark-mode .jgff-disclaimer { background-color: #1a1a1a; color: #a0a0a0; border-left-color: #a8b361; }
.jgff-disclaimer a { color: var(--navy); }
.jgff-field-group { margin-bottom: 16px; }
.jgff-field-group label {
    display: block;
    font-size: 0.8125rem;
    font-weight: bold;
    color: var(--navy);
    margin-bottom: 5px;
}
body.dark-mode .jgff-field-group label { color: #e0e0e0; }
.jgff-field-group label span { color: #c0392b; }
.jgff-field-group input[type="text"],
.jgff-field-group textarea {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #c5bca8;
    border-radius: 5px;
    font-size: 0.9375rem;
    font-family: inherit;
    background-color: var(--white);
    color: var(--charcoal);
    transition: border-color 0.2s, box-shadow 0.2s;
}
body.dark-mode .jgff-field-group input[type="text"],
body.dark-mode .jgff-field-group textarea {
    background-color: #2a2a2a;
    color: #e0e0e0;
    border-color: #444;
}
.jgff-field-group input[type="text"]:focus,
.jgff-field-group textarea:focus {
    border-color: var(--sage);
    outline: none;
    box-shadow: 0 0 0 2px rgba(147,155,81,0.2);
}
.jgff-field-group textarea { resize: vertical; min-height: 100px; }
.jgff-copy-row {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.875rem;
    color: var(--charcoal);
    margin-bottom: 20px;
}
body.dark-mode .jgff-copy-row { color: #a0a0a0; }
.jgff-submit-btn {
    background-color: #09497a;
    color: #ffffff;
    border: none;
    border-radius: 5px;
    padding: 10px 28px;
    font-size: 1rem;
    font-weight: bold;
    cursor: pointer;
    font-family: inherit;
    transition: filter 0.2s;
}
.jgff-submit-btn:hover { filter: brightness(1.1); }
.jgff-submit-btn:focus { outline: 3px solid var(--sage); outline-offset: 2px; }
body.dark-mode .jgff-submit-btn { background-color: #0d2a45; }
.jgff-status-msg {
    background-color: var(--cream);
    border-left: 4px solid var(--sage);
    border-radius: 0 6px 6px 0;
    padding: 16px 20px;
    font-size: 0.9375rem;
    color: var(--charcoal);
    line-height: 1.7;
}
body.dark-mode .jgff-status-msg { background-color: #1a1a1a; color: #a0a0a0; }
.jgff-status-msg a { color: var(--navy); font-weight: bold; }
.jgff-status-msg p { margin: 0 0 10px 0; }
.jgff-status-msg p:last-child { margin-bottom: 0; }
.jgff-deceased { color: #c0392b; font-weight: bold; font-size: 0.875rem; margin-top: 4px; }
</style>
</head>
<body>

<div id="site-header"></div>

<div class="page-title-band" role="banner">
    <h1>Contact JGFF Researcher</h1>
</div>

<div class="jgff-ecru-wrap">
    <div class="jgff-ecru-inner">
        <div class="jgff-card">

        <?php if ($state === 'bad_inits') { ?>
            <div class="jgff-status-msg">
                <p>Bad message format. Error: 341.</p>
                <p><a href="/JGFF/jgff-search.html">&larr; Return to Search</a></p>
            </div>

        <?php } elseif ($state === 'incomplete_profile') { ?>
            <div class="jgff-status-msg">
                <p>Something has gone wrong with your account. Please contact the
                <a href="mailto:support@jewishgen.org?subject=<?php echo $jgid; ?>+357FF">JewishGen Support Team</a>
                and quote reference <strong>357FF</strong> and your ID <strong><?php echo htmlspecialchars($jgid); ?></strong>.</p>
            </div>

        <?php } elseif ($state === 'not_validated') { ?>
            <div class="jgff-status-msg">
                <p><?php echo JGFF_MESSAGE_VALIDATION; ?></p>
                <p><?php echo JGFF_MESSAGE_NOT_VALIDATED . ' ' . JGFF_MESSAGE_ACTION_EMAIL; ?></p>
                <p><?php echo JGFF_MESSAGE_CONTACT_SUPPORT; ?></p>
            </div>

        <?php } elseif ($state === 'di') { ?>
            <div class="jgff-status-msg">
                <p><?php echo JGFF_MESSAGE_VALIDATION; ?></p>
                <p><?php echo JGFF_MESSAGE_DATA_INCOMPLETE . ' ' . JGFF_MESSAGE_ACTION_EMAIL; ?></p>
                <p><?php echo JGFF_MESSAGE_CONTACT_SUPPORT; ?></p>
            </div>

        <?php } else { ?>

            <h2>Send a Message</h2>

            <div class="jgff-to-from">
                <div><strong>From:</strong> <?php echo htmlspecialchars($myname); ?> (JewishGen ID #<?php echo htmlspecialchars($jgid); ?>)</div>
                <div style="margin-top:4px">
                    <strong>To:</strong>
                    <?php if ($theirflag === 2) { ?>
                        JewishGen ID #<?php echo htmlspecialchars($jgffjgid); ?>
                    <?php } else { ?>
                        <?php echo htmlspecialchars($theirname); ?> (JewishGen ID #<?php echo htmlspecialchars($jgffjgid); ?>)
                    <?php } ?>
                    <?php if ($theirstatus === 'RD Researcher Deceased') { ?>
                        <div class="jgff-deceased"><?php echo htmlspecialchars($theirstatus); ?></div>
                    <?php } ?>
                </div>
            </div>

            <div class="jgff-disclaimer">
                <strong>Reminder:</strong> This database is for your personal research only.
                Contact emails may not be used to solicit funding for non-JewishGen projects
                or for any commercial purpose. Abusers will be denied access to JewishGen.
                See the <a href="/JewishGen/disclaimer.html">JewishGen disclaimer</a> or
                <a href="/JewishGen/Support.htm">contact Support</a> for clarification.
            </div>

            <form method="post" action="jgffcontact.php">

                <div class="jgff-field-group">
                    <label for="jgff-messhead">Message heading <span aria-hidden="true">*</span></label>
                    <input type="text"
                           id="jgff-messhead"
                           name="messhead"
                           value="The JewishGen Family Finder:"
                           maxlength="120"
                           required
                           aria-required="true">
                </div>

                <div class="jgff-field-group">
                    <label for="jgff-message">Your message <span aria-hidden="true">*</span></label>
                    <textarea id="jgff-message"
                              name="message"
                              rows="6"
                              required
                              aria-required="true"></textarea>
                </div>

                <div class="jgff-copy-row">
                    <input type="checkbox" name="sendcopy" value="ON" id="jgff-sendcopy">
                    <label for="jgff-sendcopy">Send me a copy</label>
                </div>

                <input type="hidden" name="jgffjgid" value="<?php echo htmlspecialchars($jgffjgid); ?>">

                <button type="submit" class="jgff-submit-btn">Send message</button>
            </form>

        <?php } ?>

        </div>
    </div>
</div>

<div id="site-footer"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function loadComponent(id, file) {
    return fetch(file)
        .then(function(r){ if(!r.ok) throw new Error('Cannot load '+file); return r.text(); })
        .then(function(h){ document.getElementById(id).innerHTML=h; })
        .catch(function(e){ console.warn(e); });
}
Promise.all([
    loadComponent('site-header', '/Header_NavBar.html'),
    loadComponent('site-footer', '/Footer.html')
]).then(function() {
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
