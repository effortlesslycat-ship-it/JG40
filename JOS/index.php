<?php
/* =====================================================
   /JOS/index.php -- JewishGen Online Services landing
   Hub for the JOS calculator tools. Server-includes the
   header, the shared JOS toolbar, and the footer.
   CHW
   ===================================================== */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>JewishGen Online Services (JOS) &mdash; JewishGen</title>
<!-- 1. Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<!-- 2. JewishGen Global Design System -->
<link rel="stylesheet" href="/jg-global.css">
<!-- 3. JOS section styles -->
<link rel="stylesheet" href="/jg-jos.css?v=3">
</head>
<body>

<?php
$headerPath = $_SERVER['DOCUMENT_ROOT'] . '/Header_NavBar.html';
if (file_exists($headerPath)) {
    echo '<div id="site-header">';
    include $headerPath;
    echo '</div>';
} else {
    echo '<div id="site-header"></div>';
}
?>

<main id="main-content">

    <div class="page-title-band">
        <span class="tagline">JewishGen Online Services</span>
        <h1>JOS Tools</h1>
    </div>

    <?php
    $josActive = 'index';
    include $_SERVER['DOCUMENT_ROOT'] . '/JOS/JOS_SubNav.php';
    ?>

    <div class="jos-outer">
    <div class="jos-shell">

        <p class="jos-lede">
            A set of free calculators for Jewish genealogical research:
            convert dates between the civil and Hebrew calendars, look up
            Jewish festival dates for any year, generate Soundex codes for
            surnames, and translate Jewish years between numerals and Hebrew
            letters.
        </p>

        <div class="jos-tile-grid">

            <a class="jos-tile" href="/JOS/josdates.php">
                <p class="jos-tile__title">Calendar Converter</p>
                <p class="jos-tile__desc">Convert a civil (Gregorian) date to
                the Hebrew calendar or back, and list Yahrzeit dates across
                consecutive years.</p>
                <span class="jos-tile__cta">Open converter &rarr;</span>
            </a>

            <a class="jos-tile" href="/JOS/josfest.php">
                <p class="jos-tile__title">Festival Dates</p>
                <p class="jos-tile__desc">Display every Jewish holiday for any
                year you choose &mdash; entered as either a civil year or a
                Hebrew year.</p>
                <span class="jos-tile__cta">Show festivals &rarr;</span>
            </a>

            <a class="jos-tile" href="/JOS/jossound.php">
                <p class="jos-tile__title">Soundex Calculator</p>
                <p class="jos-tile__desc">Generate NARA (Russell) and
                Daitch-Mokotoff Soundex codes for a surname to find spelling
                variants in indexes.</p>
                <span class="jos-tile__cta">Calculate codes &rarr;</span>
            </a>

            <a class="jos-tile" href="/JOS/mjyear.php">
                <p class="jos-tile__title">Hebrew Year Converter</p>
                <p class="jos-tile__desc">Translate a Jewish year between
                Arabic numerals and Hebrew letters, and see whether it is a
                leap year.</p>
                <span class="jos-tile__cta">Convert year &rarr;</span>
            </a>

        </div>

    </div>
    </div>

</main>

<?php
$footerPath = $_SERVER['DOCUMENT_ROOT'] . '/Footer.html';
if (file_exists($footerPath)) {
    echo '<div id="site-footer">';
    include $footerPath;
    echo '</div>';
} else {
    echo '<div id="site-footer"></div>';
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>