<?php
/* =====================================================
   /JOS/josdates.php -- JOS Calendar Converter
   Civil <-> Hebrew date conversion + multi-year Yahrzeit.
   POSTs to the existing backend /databases/josdates.php.
   Form field names (Gday, Gmonth, Gcent, Gdec, Gyear,
   Jday, Jmonth, Jcent, Jdec, Jyear, Multiple) are
   preserved EXACTLY so the working backend still parses
   them; only the presentation is restyled.
   CHW
   ===================================================== */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Calendar Converter &mdash; JOS &mdash; JewishGen</title>
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
        <h1>Calendar Converter</h1>
    </div>

    <?php $josActive = 'josdates'; include $_SERVER['DOCUMENT_ROOT'] . '/JOS/JOS_SubNav.php'; ?>

    <div class="jos-outer">
    <div class="jos-shell">

        <p class="jos-intro">
            Convert a civil (Gregorian) date into the Hebrew calendar or the
            reverse, and list Yahrzeit dates across consecutive years. Because
            the Jewish day begins at sunset, a Hebrew date may fall one civil
            day later than you expect.
            <br>
            <a class="jos-readmore" href="#about">Read more about the Jewish
            calendar <span class="jos-arrow">&darr;</span></a>
        </p>

        <div class="jos-tool-card">
            <h2>Convert a Date</h2>

            <form method="post" action="/databases/josdates.php">

                <p><strong>Civil (Gregorian) date</strong> to convert into the
                Jewish calendar:</p>
                <p>
                    <label for="Gday">Day</label>
                    <select id="Gday" name="Gday">
                        <?php for ($d = 1; $d <= 31; $d++) {
                            $v = sprintf('%02d', $d);
                            echo '<option value="' . $v . '"' . ($d == 1 ? ' selected' : '') . '>' . $d . '</option>';
                        } ?>
                    </select>
                    <label for="Gmonth">Month</label>
                    <select id="Gmonth" name="Gmonth">
                        <?php
                        $gm = array('January','February','March','April','May','June',
                                    'July','August','September','October','November','December');
                        for ($m = 1; $m <= 12; $m++) {
                            $v = sprintf('%02d', $m);
                            echo '<option value="' . $v . '"' . ($m == 1 ? ' selected' : '') . '>' . $m . ' ' . $gm[$m - 1] . '</option>';
                        } ?>
                    </select>
                    <label for="Gcent">Year</label>
                    <select id="Gcent" name="Gcent" aria-label="Civil year, century">
                        <?php foreach (array(16,17,18,19,20,21,22) as $c) {
                            echo '<option value="' . $c . '"' . ($c == 20 ? ' selected' : '') . '>' . $c . '</option>';
                        } ?>
                    </select>
                    <select name="Gdec" aria-label="Civil year, decade">
                        <?php for ($n = 0; $n <= 9; $n++) { echo '<option' . ($n == 0 ? ' selected' : '') . '>' . $n . '</option>'; } ?>
                    </select>
                    <select name="Gyear" aria-label="Civil year, digit">
                        <?php for ($n = 0; $n <= 9; $n++) { echo '<option' . ($n == 5 ? ' selected' : '') . '>' . $n . '</option>'; } ?>
                    </select>
                </p>

                <hr>

                <p><strong>Jewish date</strong> to convert into the civil
                (Gregorian) calendar:</p>
                <p>
                    <label for="Jday">Day</label>
                    <select id="Jday" name="Jday">
                        <?php for ($d = 1; $d <= 30; $d++) { echo '<option' . ($d == 1 ? ' selected' : '') . '>' . $d . '</option>'; } ?>
                    </select>
                    <label for="Jmonth">Month</label>
                    <select id="Jmonth" name="Jmonth">
                        <?php
                        $jm = array('07'=>'Tishri','08'=>'Cheshvan','09'=>'Kislev','10'=>'Tevet',
                                    '11'=>"Sh'vat",'12'=>'Adar','13'=>'Adar II','01'=>'Nisan',
                                    '02'=>'Iyyar','03'=>'Sivan','04'=>'Tammuz','05'=>'Av','06'=>'Elul');
                        $jmNum = array('07'=>7,'08'=>8,'09'=>9,'10'=>10,'11'=>11,'12'=>12,'13'=>13,
                                       '01'=>1,'02'=>2,'03'=>3,'04'=>4,'05'=>5,'06'=>6);
                        foreach ($jm as $val => $nm) {
                            echo '<option value="' . $val . '"' . ($val == '07' ? ' selected' : '') . '>' . $jmNum[$val] . ' ' . $nm . '</option>';
                        } ?>
                    </select>
                    <label for="Jcent">Year</label>
                    <select id="Jcent" name="Jcent" aria-label="Jewish year, century">
                        <?php foreach (array(54,55,56,57,58,59) as $c) {
                            echo '<option value="' . $c . '"' . ($c == 57 ? ' selected' : '') . '>' . $c . '</option>';
                        } ?>
                    </select>
                    <select name="Jdec" aria-label="Jewish year, decade">
                        <?php for ($n = 0; $n <= 9; $n++) { echo '<option' . ($n == 6 ? ' selected' : '') . '>' . $n . '</option>'; } ?>
                    </select>
                    <select name="Jyear" aria-label="Jewish year, digit">
                        <?php for ($n = 0; $n <= 9; $n++) { echo '<option' . ($n == 5 ? ' selected' : '') . '>' . $n . '</option>'; } ?>
                    </select>
                </p>

                <hr>

                <p>
                    <label for="Multiple">Compute the civil date for</label>
                    <select id="Multiple" name="Multiple">
                        <?php for ($n = 1; $n <= 10; $n++) { echo '<option' . ($n == 1 ? ' selected' : '') . '>' . $n . '</option>'; } ?>
                    </select>
                    consecutive Jewish years (beginning with the year chosen
                    above).
                </p>

                <div style="text-align:center;">
                    <button type="submit" class="jos-submit">Convert Dates</button>
                </div>

            </form>
        </div>

        <div id="jos-results" class="jos-results" role="region"
             aria-label="Conversion results" hidden></div>

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
    endpoint: '/databases/josdates.php',
    match: 'josdates.php'
});
</script>
</body>
</html>