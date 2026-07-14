<?php
// jgffcontact.php -- CHW JG40 redesign
// Handles contact form submission, sends email, shows confirmation.
// Backend logic preserved from original unchanged.

include_once '../databases/cureetc.php';
include_once '../databases/bootstrap.php';
include_once '../databases/msbootstrap.php';
include '_bootstrap.php';
include '_jgff_messages.php';

$sendcopy       = $_POST['sendcopy'] ?? '';
$jgffmessage    = $_POST['message'] ?? '';
$jgffmessagehead = $_POST['messhead'] ?? '';
$jgffjgid       = $_POST['jgffjgid'] ?? '';

$jgid = require_userinfo($userinfo);
$inst = 0;

$isDI = false;
if (strlen($userinfo['statres']) > 2 && substr($userinfo['statres'], 0, 2) === 'DI') {
    $isDI = true;
}

$time_start = microtime(true);
ini_set('max_execution_time', 300);

$myname  = $userinfo['fullname'];
$myemail = $userinfo['email'];

$content = '';

if (strlen($jgffmessagehead) < 2 || strlen($jgffjgid) < 3 || strlen($jgffmessage) < 3) {
    $content = 'error_incomplete_form';
} elseif (strlen($myname) < 2 || strlen($myemail) < 4) {
    writeAuthLog('jgffcontact', 'Error 358FF - incomplete profile - for user ' . $jgid . ' @ ' . $myemail . ' seeking to send to ' . $jgffjgid);
    $content = 'error_profile';
} elseif ($userinfo['validated'] && !$isDI) {

    $gconn = sqlsrv_connect($MSserverName, $optionsGoldmine);
    if (!$gconn) { echo "Connection to GoldMine could not be established.\n"; exit(print_r(sqlsrv_errors(), true)); }

    $gquery  = 'SELECT dbo.CONTACT1.CONTACT, dbo.CONTACT2.UEMAIL_PRI, dbo.CONTACT2.UJGFFDISPL '
             . ' FROM dbo.CONTACT1 INNER JOIN CONTACT2 ON CONTACT1.ACCOUNTNO = CONTACT2.ACCOUNTNO '
             . ' WHERE dbo.CONTACT1.KEY3 = ? ';
    $gparams = array($jgffjgid);
    $stmt    = sqlsrv_query($gconn, $gquery, $gparams);
    if (false === $stmt) {
        writeAuthLog('jgffcontact', 'Error 351 line ' . __LINE__ . ' ' . print_r(sqlsrv_errors(), true));
        echo 'Unable to get needed data 351FF. Please hit Back button and try again.';
        exit(print_r(sqlsrv_errors(), true));
    }
    $row = sqlsrv_fetch_array($stmt);
    if (is_array($row)) {
        $theirname  = $row['CONTACT'];
        $theiremail = $row['UEMAIL_PRI'];
        $theirflag  = $row['UJGFFDISPL'];
    } else {
        writeAuthLog('jgffcontact', 'Error 352 line ' . __LINE__ . ' ' . print_r(sqlsrv_errors(), true));
        exit('Unable to get needed data 352FF. Please hit Back button and try again.');
    }
    sqlsrv_free_stmt($stmt);

    $isJgffAbuser = '0';
    if ($inst === 0) {
        $gquery  = 'SELECT dbo.CONTACT2.USERDEF02 '
                 . ' FROM dbo.CONTACT1 INNER JOIN CONTACT2 ON CONTACT1.ACCOUNTNO = CONTACT2.ACCOUNTNO '
                 . ' WHERE dbo.CONTACT1.KEY3 = ? ';
        $gparams = array($jgid);
        $stmt    = sqlsrv_query($gconn, $gquery, $gparams);
        if (false === $stmt) {
            writeAuthLog('jgffcontact', 'Error 353 line ' . __LINE__ . ' ' . print_r(sqlsrv_errors(), true));
            echo 'Unable to get needed data 353FF. Please hit Back button and try again.';
            exit(print_r(sqlsrv_errors(), true));
        }
        $row = sqlsrv_fetch_array($stmt);
        if (is_array($row)) { $isJgffAbuser = $row['USERDEF02']; }
        else { writeAuthLog('jgffcontact', 'Error 354 line ' . __LINE__ . ' ' . print_r(sqlsrv_errors(), true)); exit('Unable to get needed data 354FF. Please hit Back button and try again.'); }
    }
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($gconn);

    if (strlen($theirname) < 2 || strlen($theiremail) < 4) {
        writeAuthLog('jgffcontact', "Error 356FF - incomplete profile for user {$jgffjgid} '{$theirname}' @ {$theiremail} user {$jgid} seeking to send to them");
        $content = 'error_their_profile';
        $error_code = '356FF';
        $error_jgid = $jgffjgid;
    } else {
        $message  = '<TABLE border=0><TR><TD>From : ' . $myname . ', (researcher code ' . $jgid . ') ' . $myemail . '</TD></TR>';
        $message .= '<TR><TD>To : ' . $theirname . ', (researcher code ' . $jgffjgid . ')</TD></TR>';
        $message .= '<TR><TD>Subject : ' . $jgffmessagehead . '</TD></TR></TABLE>';
        $message .= '<HR><P>' . $jgffmessage . '</P>';

        $footer       = file_get_contents('EmailFooter.inc');
        $message      = nl2br($message);
        $messagestore = $message;
        $message      = $message . $footer;

        $headers_top  = 'MIME-Version: 1.0' . "\r\n" . 'Content-type:text/html;charset=UTF-8' . "\r\n";
        $headers      = 'From: JGFF-Comms@jewishgen.org (' . $myname . ')' . "\r\n";
        $headersa     = $headers . 'Bcc: JGFF-Comms@jewishgen.org' . "\r\n" . 'X-Mailer: PHP/' . phpversion();
        $headers     .= 'X-Mailer: PHP/' . phpversion();
        $headers_bottom = $headers;
        $subject      = $jgffmessagehead;

        $to_other = $theiremail;
        if ($isJgffAbuser === '1') {
            $to_other        = 'jg_admin@jewishgen.org,bobmar37@aol.com,jgffabuse@jewishgen.org,garysandler42@gmail.com';
            $message_to_other = 'WARNING: ABUSER - this email has been redirected to ' . $to_other . '<BR><BR>It was originally sent by ' . $myname . ', ' . $myemail . ' intended for but has not yet been sent to ' . $theiremail . '<BR><BR>' . $message;
            $headers_to_other = '';
        } else {
            $headers_to_other = 'Reply-To: ' . $myemail . ' (' . $myname . ')' . "\r\n";
            $message_to_other = $message;
        }
        $headers_to_other = $headers_top . 'To: ' . $to_other . "\r\n" . $headers_to_other . $headersa;

        $headerscopy = $headers_to_other;
        $headerscopy = str_replace('From: ', 'From: ' . $myname . ' (researcher code ' . $jgid . ' ) ', $headerscopy);
        $headerscopy = str_replace('Reply-To:', 'Reply-To', $headerscopy);
        $headerscopy = str_replace('To: ', 'To: ' . $theirname . ' (researcher code ' . $jgffjgid . ' ) ', $headerscopy);
        $headerscopy = str_replace('Reply-To', 'Reply-To:', $headerscopy);

        $dt   = date('Y-m-d H:i:s');
        $msg  = $headerscopy . "\r\n" . $subject . "\r\n========================\r\n"
              . str_replace('<BR><BR>', "\r\n========================\r\n", remove_emoji($messagestore));

        $con  = mysqli_connect(MYSQL_SERVER_HOSTNAME, MYSQL_SERVER_USERNAME, MYSQL_SERVER_PASSWORD, MYSQL_SERVER_DATABASE);
        $stmt = mysqli_prepare($con, "INSERT into jewishgen.blindcontact (jgid, datetime, message) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $jgid, $dt, $msg);
        if (!mysqli_stmt_execute($stmt)) {
            $hdr = 'From: JGFF-Comms@jewishgen.org' . "\r\n" . 'X-Mailer: PHP/' . phpversion();
            mail('garysandler42@gmail.com', 'JGFFContact Insert Error', $dt . "\r\n" . $jgid . "\r\n" . $subject . "\r\n" . $messagestore . "\r\n" . mysqli_error($con), $hdr);
        }
        mysqli_close($con);

        $this_ip  = get_client_ip();
        $time_end = microtime(true);
        $duration = $time_end - $time_start;
        write_mysql_jglog($duration, $jgid, $this_ip);

        if ($sendcopy === 'ON') {
            $headers_copy_to_self = $headers_top . 'To: ' . $myemail . "\r\n" . $headers_bottom;
            mail($myemail, $subject, 'Copy of Message for Your Records - Do Not Reply to This Copy<BR><BR>' . $message, $headers_copy_to_self);
        }

        mail($to_other, $subject, $message_to_other, $headers_to_other);

        if ($isJgffAbuser === '2') {
            $to   = 'jg_admin@jewishgen.org,garysandler42@gmail.com';
            $warn = 'WARNING - this email is from a potential abuser.<BR><BR>It was NOT redirected and was sent by ' . $myname . ', ' . $myemail . ' to ' . $theiremail . '<BR><BR>' . $message;
            $h1   = $headers_top . 'To: ' . $to . "\r\n" . 'Reply-To: ' . $myemail . "\r\n" . $headersa;
            mail($to, $subject, $warn, $h1);
        }

        $content = 'success';
        $recipient_name = $myname;
    }
} elseif (!$userinfo['validated']) {
    $content = 'not_validated';
} else {
    $content = 'di';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Message Sent &ndash; JewishGen Family Finder</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="/jg-global.css">
<style>
/* == JGFF Contact Confirmation -- CHW ====================== */
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
    text-align: center;
}
body.dark-mode .jgff-card { background-color: #1e1e1e; border-color: #333; }
.jgff-success-icon {
    width: 48px;
    height: 48px;
    background-color: #09497a;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    color: #ffffff;
    font-size: 22px;
}
body.dark-mode .jgff-success-icon { background-color: #0d2a45; }
.jgff-card h2 {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 1.25rem;
    font-weight: normal;
    color: var(--navy);
    margin: 0 0 12px 0;
}
body.dark-mode .jgff-card h2 { color: #e0e0e0; }
.jgff-card p {
    font-size: 0.9375rem;
    color: var(--charcoal);
    line-height: 1.7;
    margin: 0 0 12px 0;
}
body.dark-mode .jgff-card p { color: #a0a0a0; }
.jgff-card p:last-child { margin-bottom: 0; }
.jgff-card a { color: var(--navy); font-weight: bold; }
.jgff-close-btn {
    display: inline-block;
    background-color: #09497a;
    color: #ffffff;
    border-radius: 5px;
    padding: 8px 24px;
    font-size: 0.875rem;
    font-weight: bold;
    text-decoration: none;
    margin-top: 8px;
    border: none;
    cursor: pointer;
    font-family: inherit;
    transition: filter 0.2s;
}
.jgff-close-btn:hover { filter: brightness(1.1); color: #ffffff; }
body.dark-mode .jgff-close-btn { background-color: #0d2a45; }
.jgff-status-msg {
    background-color: var(--cream);
    border-left: 4px solid var(--sage);
    border-radius: 0 6px 6px 0;
    padding: 16px 20px;
    font-size: 0.9375rem;
    color: var(--charcoal);
    line-height: 1.7;
    text-align: left;
}
body.dark-mode .jgff-status-msg { background-color: #1a1a1a; color: #a0a0a0; }
.jgff-status-msg p { margin: 0 0 10px 0; }
.jgff-status-msg p:last-child { margin-bottom: 0; }
</style>
</head>
<body>

<div id="site-header"></div>

<div class="page-title-band" role="banner">
    <span class="tagline">JewishGen Family Finder</span>
    <h1>Contact JGFF Researcher</h1>
</div>

<div class="jgff-ecru-wrap">
    <div class="jgff-ecru-inner">
        <div class="jgff-card">

        <?php if ($content === 'success') { ?>
            <div class="jgff-success-icon" aria-hidden="true">
                <i class="ti ti-mail"></i>
            </div>
            <h2>Message sent, <?php echo htmlspecialchars($recipient_name); ?></h2>
            <p>Your message has been forwarded to the researcher.</p>
            <p>
                If you have a success story due to the JewishGen Family Finder,
                we would love to hear it!<br>
                Please write to us at
                <a href="mailto:jgffhelp@jewishgen.org">jgffhelp@jewishgen.org</a>.
            </p>
            <button class="jgff-close-btn" onclick="window.close();">Close this window</button>

        <?php } elseif ($content === 'error_incomplete_form') { ?>
            <div class="jgff-status-msg">
                <p><strong>Your form was incomplete.</strong> Please go back and fill in all required fields.</p>
                <p><button onclick="history.go(-1)" style="background:none;border:none;color:var(--navy);font-weight:bold;cursor:pointer;padding:0;font-family:inherit;font-size:inherit;">&larr; Go back</button></p>
                <p>For help, contact the <a href="mailto:support@jewishgen.org?subject=<?php echo htmlspecialchars($jgid); ?>+357FF">JewishGen Support Team</a>.</p>
            </div>

        <?php } elseif ($content === 'error_profile') { ?>
            <div class="jgff-status-msg">
                <p>Something has gone wrong with your account. Please contact the
                <a href="mailto:support@jewishgen.org?subject=<?php echo htmlspecialchars($jgid); ?>+358FF">JewishGen Support Team</a>
                and quote reference <strong>358FF</strong>.</p>
            </div>

        <?php } elseif ($content === 'error_their_profile') { ?>
            <div class="jgff-status-msg">
                <p>Something has gone wrong. Please contact the
                <a href="mailto:support@jewishgen.org?subject=<?php echo htmlspecialchars($error_jgid); ?>+356FF">JewishGen Support Team</a>
                and quote reference <strong>356FF</strong>.</p>
            </div>

        <?php } elseif ($content === 'not_validated') { ?>
            <div class="jgff-status-msg">
                <p><?php echo JGFF_MESSAGE_VALIDATION; ?></p>
                <p><?php echo JGFF_MESSAGE_NOT_VALIDATED . ' ' . JGFF_MESSAGE_ACTION_EMAIL; ?></p>
                <p><?php echo JGFF_MESSAGE_CONTACT_SUPPORT; ?></p>
            </div>

        <?php } elseif ($content === 'di') { ?>
            <div class="jgff-status-msg">
                <p><?php echo JGFF_MESSAGE_VALIDATION; ?></p>
                <p><?php echo JGFF_MESSAGE_DATA_INCOMPLETE . ' ' . JGFF_MESSAGE_ACTION_EMAIL; ?></p>
                <p><?php echo JGFF_MESSAGE_CONTACT_SUPPORT; ?></p>
            </div>
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
]);
</script>

</body>
</html>
<?php
function remove_emoji($string) {
    $symbols = "\x{1F100}-\x{1F1FF}\x{1F300}-\x{1F5FF}\x{1F600}-\x{1F64F}"
             . "\x{1F680}-\x{1F6FF}\x{1F900}-\x{1F9FF}\x{1FA70}-\x{1FAFF}"
             . "\x{1D400}-\x{1D7FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}";
    $string2 = preg_replace('/['. $symbols . ']+/u', '~', $string);
    return $string2;
}
?>
