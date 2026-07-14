<?php
/* =====================================================
   /JOS/josfest.php -- JOS Festival Dates
   Lists Jewish holidays for a given civil or Hebrew year.
   POSTs to the existing backend /databases/josfest.php.
   Field names (caltype, jyear) preserved exactly. The
   legacy numbersonly() check (DataChecks.js) is replaced
   with an inline handler so there is no external script
   dependency.
   CHW
   ===================================================== */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Festival Dates &mdash; JOS &mdash; JewishGen</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="/jg-global.css">
<link rel="stylesheet" href="/jg-jos.css?v=4">
</head>
<body>

<?php
$headerPath = $_SERVER['DOCUMENT_ROOT'] . '/Header_NavBar.html';
if (file_exists($headerPath)) { echo '<div id="site-header">'; include $headerPath; echo '</div>'; }
else { echo '<div id="site-header"></div>'; }
?>

<main id="main-content">

    <div class="page-title-band">
        <span class="tagline">JewishGen Online Services</span>
        <h1>Festival Dates</h1>
    </div>

    <?php $josActive = 'josfest'; include $_SERVER['DOCUMENT_ROOT'] . '/JOS/JOS_SubNav.php'; ?>

    <div class="jos-outer">
    <div class="jos-shell">

        <p class="jos-intro">
            Display every Jewish holiday for any year you choose &mdash;
            entered as either a civil (Gregorian) year or a Hebrew year. A
            civil year spans parts of two Hebrew years, so the list is anchored
            to the year type you select.
            <br>
            <a class="jos-readmore" href="#about">Read more about the Jewish
            calendar <span class="jos-arrow">&darr;</span></a>
        </p>

        <div class="jos-tool-card">
            <h2>Show Festival Dates</h2>

            <form method="post" action="/databases/josfest.php">
                <p>
                    <label for="caltype">Calendar Type</label>
                    <select id="caltype" name="caltype">
                        <option value="jyear">Hebrew Calendar</option>
                        <option value="gyear" selected>Gregorian Calendar</option>
                    </select>
                    &nbsp;
                    <label for="jyear">Year</label>
                    <input type="text" id="jyear" name="jyear" size="4"
                           maxlength="4" inputmode="numeric" autocomplete="off"
                           oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                </p>

                <div style="text-align:center;">
                    <button type="submit" class="jos-submit">Show Festival Dates</button>
                </div>
            </form>
        </div>

        <div id="jos-results" class="jos-results" role="region"
             aria-label="Festival date results" hidden></div>

        <?php include $_SERVER['DOCUMENT_ROOT'] . '/JOS/JOS_CalendarAbout.php'; ?>

    </div>
    </div>

</main>

<?php
$footerPath = $_SERVER['DOCUMENT_ROOT'] . '/Footer.html';
if (file_exists($footerPath)) { echo '<div id="site-footer">'; include $footerPath; echo '</div>'; }
else { echo '<div id="site-footer"></div>'; }
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/jg-jos.js?v=1"></script>
<script>
josInlineResults({
    form: '.jos-tool-card form',
    results: '#jos-results',
    endpoint: '/databases/josfest.php',
    match: 'josfest.php'
});
</script>
</body>
</html>