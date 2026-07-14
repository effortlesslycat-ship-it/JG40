<?php
/* =====================================================
   /JOS/mjyear.php -- JOS Hebrew Year Converter
   Numerals <-> Hebrew letters, plus leap-year info.
   POSTs to the existing backend /cgi-bin/m_jyear.pl.
   Field names (Type, Jcent, Jdec, Jyear, one, ten, hun)
   and their values are preserved EXACTLY so the working
   Perl backend still parses them. The legacy letter GIFs
   are replaced with Unicode Hebrew (numeric entities,
   ASCII-safe) per decision.
   CHW
   ===================================================== */

/* Radio option lists: value => Hebrew letter(s) as entities.
   Order and values match the legacy form so the backend is
   unaffected. '' renders as "(none)". */
$onesCol = array(
    '0'  => '', '1' => '&#1488;', '2' => '&#1489;', '3' => '&#1490;',
    '4'  => '&#1491;', '5' => '&#1492;', '6' => '&#1493;', '7' => '&#1494;',
    '8'  => '&#1495;', '9' => '&#1496;',
    '15' => '&#1493;&#1496;', '16' => '&#1494;&#1496;'
);
$tensCol = array(
    '0' => '', '1' => '&#1497;', '2' => '&#1499;', '3' => '&#1500;',
    '4' => '&#1502;', '5' => '&#1504;', '6' => '&#1505;', '7' => '&#1506;',
    '8' => '&#1508;', '9' => '&#1510;'
);
$hunsCol = array(
    '0' => '', '1' => '&#1511;', '2' => '&#1512;', '3' => '&#1513;', '4' => '&#1514;',
    '5' => '&#1511;&#1514;', '6' => '&#1512;&#1514;', '7' => '&#1513;&#1514;', '8' => '&#1514;&#1514;'
);
function mjyRadios($name, $col, $checked) {
    $out = '';
    foreach ($col as $val => $glyph) {
        $lab = ($glyph === '') ? '<span class="mjy-none">(none)</span>'
                               : '<span class="jos-hebrew" lang="he">' . $glyph . '</span>';
        $star = ($val == '15' || $val == '16') ? ' <span class="mjy-star">*</span>' : '';
        $isChecked = ($val === $checked) ? ' checked' : '';
        $out .= '<label class="mjy-radio"><input type="radio" name="' . $name .
                '" value="' . $val . '"' . $isChecked . '> ' . $lab . $star . '</label>';
    }
    return $out;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hebrew Year Converter &mdash; JOS &mdash; JewishGen</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="/jg-global.css">
<link rel="stylesheet" href="/jg-jos.css?v=1">
<style>
/* Page-specific: two-form layout + letter-picker columns */
.mjy-cols { display: flex; gap: 28px; flex-wrap: wrap; }
.mjy-col { flex: 1; min-width: 240px; }
.mjy-col h3 {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 1.15rem; color: var(--navy); margin: 0 0 10px; text-align: center;
}
body.dark-mode .mjy-col h3 { color: #8ab4d4; }
.mjy-picker { display: flex; gap: 10px; justify-content: center; }
.mjy-picker__group {
    border: 1px solid #c5bca8; border-radius: 6px; padding: 6px;
    display: flex; flex-direction: column; gap: 2px; min-width: 78px;
}
body.dark-mode .mjy-picker__group { border-color: #444; }
.mjy-picker__cap {
    font-size: 10px; font-weight: bold; text-transform: uppercase;
    letter-spacing: 0.5px; color: var(--sage); text-align: center; margin-bottom: 2px;
}
body.dark-mode .mjy-picker__cap { color: #a8b361; }
.mjy-radio {
    display: flex; align-items: center; gap: 6px;
    font-size: 1.05rem; padding: 2px 4px; border-radius: 4px; cursor: pointer;
}
.mjy-radio:hover { background-color: rgba(9,73,122,0.06); }
body.dark-mode .mjy-radio:hover { background-color: rgba(138,180,212,0.12); }
.mjy-none { font-size: 0.8rem; color: var(--charcoal); opacity: 0.6; }
body.dark-mode .mjy-none { color: #a0a0a0; }
.mjy-star { color: var(--sage); font-weight: bold; }
body.dark-mode .mjy-star { color: #a8b361; }
.mjy-note { font-size: 12px; color: var(--charcoal); opacity: 0.75; margin-top: 10px; }
body.dark-mode .mjy-note { color: #a0a0a0; opacity: 1; }
.mjy-divider { width: 1px; background-color: var(--cream); align-self: stretch; }
body.dark-mode .mjy-divider { background-color: #333; }
@media (max-width: 640px) { .mjy-divider { display: none; } }
</style>
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
        <h1>Hebrew Year Converter</h1>
    </div>

    <?php $josActive = 'mjyear'; include $_SERVER['DOCUMENT_ROOT'] . '/JOS/JOS_SubNav.php'; ?>

    <div class="jos-outer">
    <div class="jos-shell">

        <p class="jos-intro">
            Jewish years are often written in Hebrew letters using their
            numerical values. Convert a year from numerals to Hebrew letters or
            back, and find out whether it is a leap year &mdash; useful for
            yahrzeit and birthday anniversaries.
            <br>
            <a class="jos-readmore" href="#about">Read more about Hebrew year
            notation <span class="jos-arrow">&darr;</span></a>
        </p>

        <div class="jos-tool-card" style="max-width:900px;">
            <div class="mjy-cols">

                <!-- Numbers to Letters -->
                <div class="mjy-col">
                    <h3>Numbers to Letters</h3>
                    <form method="post" action="/cgi-bin/m_jyear.pl">
                        <input type="hidden" name="Type" value="N">
                        <p style="text-align:center;">Select a Jewish year:</p>
                        <p style="text-align:center;">
                            <label for="Jcent" class="visually-hidden">Century</label>
                            <select id="Jcent" name="Jcent" aria-label="Year, century">
                                <?php foreach (array(53,54,55,56,57,58,59) as $c) {
                                    echo '<option' . ($c == 57 ? ' selected' : '') . '>' . $c . '</option>';
                                } ?>
                            </select>
                            <select name="Jdec" aria-label="Year, decade">
                                <?php for ($n = 0; $n <= 9; $n++) { echo '<option' . ($n == 6 ? ' selected' : '') . '>' . $n . '</option>'; } ?>
                            </select>
                            <select name="Jyear" aria-label="Year, digit">
                                <?php for ($n = 0; $n <= 9; $n++) { echo '<option' . ($n == 5 ? ' selected' : '') . '>' . $n . '</option>'; } ?>
                            </select>
                        </p>
                        <div style="text-align:center;">
                            <button type="submit" class="jos-submit">Convert to Hebrew Letters</button>
                        </div>
                    </form>
                </div>

                <div class="mjy-divider" aria-hidden="true"></div>

                <!-- Letters to Numbers -->
                <div class="mjy-col">
                    <h3>Letters to Numbers</h3>
                    <form method="post" action="/cgi-bin/m_jyear.pl">
                        <input type="hidden" name="Type" value="L">
                        <p style="text-align:center; font-size:13px;">Mark one letter
                        in each column. If a column has no letter, mark
                        &ldquo;(none)&rdquo; at the top.</p>
                        <div class="mjy-picker">
                            <div class="mjy-picker__group">
                                <span class="mjy-picker__cap">Hundreds</span>
                                <?php echo mjyRadios('hun', $hunsCol, '7'); ?>
                            </div>
                            <div class="mjy-picker__group">
                                <span class="mjy-picker__cap">Tens</span>
                                <?php echo mjyRadios('ten', $tensCol, '5'); ?>
                            </div>
                            <div class="mjy-picker__group">
                                <span class="mjy-picker__cap">Ones</span>
                                <?php echo mjyRadios('one', $onesCol, '8'); ?>
                            </div>
                        </div>
                        <p class="mjy-note"><span class="mjy-star">*</span> These two
                        combinations do not co-occur with a letter in the Tens column;
                        if one is selected, mark &ldquo;(none)&rdquo; in the Tens
                        column.</p>
                        <div style="text-align:center;">
                            <button type="submit" class="jos-submit">Convert to Numbers</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

        <!-- Short about (this tool has no full InfoFile) -->
        <section class="jos-about" id="about" tabindex="-1" aria-labelledby="about-heading">
            <p class="jos-about__eyebrow">About This Tool</p>
            <h2 id="about-heading">Jewish Years in Hebrew Letters</h2>

            <p>Jewish years are frequently written with Hebrew letters, each of
            which carries a numerical value (gematria). When the year is written
            this way, the thousands (5000) are usually omitted &mdash; the
            &ldquo;small count&rdquo; &mdash; so, for example, the letters
            <span class="jos-hebrew" lang="he">&#1496;&#1513;&#1504;&#1493;</span>
            add up to 756, short for 5756. The civil equivalent of a small-count
            year can be found by adding 1240 (756 + 1240 = 1996).</p>

            <p>This tool converts in both directions: enter a numeral year to see
            its Hebrew-letter form, or select the letters of a year to read its
            numeric value. It also reports whether the year is a leap year, which
            matters for anniversaries &mdash; someone born in Adar of a common
            year celebrates in Adar II in leap years, while yahrzeit for a death
            in Adar of a common year is observed in Adar I.</p>

            <p>For the broader picture &mdash; how months, years, and leap years
            fit together &mdash; see the
            <a href="/JOS/josdates.php#about">Introduction to the Jewish
            Calendar</a>. To convert full dates between the Jewish and civil
            calendars, use the <a href="/JOS/josdates.php">Calendar
            Converter</a>.</p>

            <p class="jos-about__attribution">Copyright &copy;1997 Joachim
            Mugdan, all rights reserved.</p>

            <a class="jos-backtop" href="#main-content">&uarr; Back to the tool</a>
        </section>

    </div>
    </div>

</main>

<?php
$footerPath = $_SERVER['DOCUMENT_ROOT'] . '/Footer.html';
if (file_exists($footerPath)) { echo '<div id="site-footer">'; include $footerPath; echo '</div>'; }
else { echo '<div id="site-footer"></div>'; }
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
