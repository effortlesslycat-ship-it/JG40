<?php
/* =====================================================
   /JOS/jossound.php -- JOS Soundex Calculator (TEMPLATE)
   Pattern for all JOS tool pages:
     header include -> title band -> toolbar include ->
     intro + "read more" -> tool card -> #about (full
     ported InfoFile) -> footer include.
   Soundex math: NARA is the inline function below;
   Daitch-Mokotoff comes from the JG-hosted stevemorse.org
   scripts (dm.js / dmlat.js), which define soundex().
   CHW
   ===================================================== */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Soundex Calculator &mdash; JOS &mdash; JewishGen</title>
<!-- 1. Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<!-- 2. JewishGen Global Design System -->
<link rel="stylesheet" href="/jg-global.css">
<!-- 3. JOS section styles -->
<link rel="stylesheet" href="/jg-jos.css?v=1">
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
        <h1>Soundex Calculator</h1>
    </div>

    <?php
    $josActive = 'jossound';
    include $_SERVER['DOCUMENT_ROOT'] . '/JOS/JOS_SubNav.php';
    ?>

    <div class="jos-outer">
    <div class="jos-shell">

        <!-- Intro snippet + read more -->
        <p class="jos-intro">
            Soundex codes the <em>sound</em> of a name rather than its
            spelling, so that variants like Scherman, Schurman, and Sherman
            all share one code. That makes it a powerful way to find records
            despite the wide spelling variation common in historical
            documents.
            <br>
            <a class="jos-readmore" href="#about">Read more about Soundex
            <span class="jos-arrow">&darr;</span></a>
        </p>

        <!-- Tool card -->
        <div class="jos-tool-card">
            <h2>Calculate Soundex Codes</h2>
            <p class="jos-tool-note">Start typing a surname &mdash; codes
            update as you type.</p>

            <p class="jos-output" id="output" aria-live="polite">Start typing the name</p>

            <div style="text-align:center;">
                <label for="word">Name:
                    <input type="text" name="word" id="word" size="25"
                           maxlength="25" autocomplete="off" autofocus>
                </label>
            </div>
        </div>

        <!-- ================= ABOUT (ported InfoFile) ================= -->
        <section class="jos-about" id="about" tabindex="-1"
                 aria-labelledby="about-heading">
            <p class="jos-about__eyebrow">About This Tool</p>
            <h2 id="about-heading">Soundex Coding</h2>

            <p>With Soundex, the &ldquo;sound&rdquo; of names &mdash; the
            phonetic sound, to be exact &mdash; is coded. This is of great
            help, since it avoids most problems of misspellings or alternate
            spellings.</p>

            <p>For example: Scherman, Schurman, Sherman, Shireman, and Shurman
            are indexed together as NARA Soundex code S655. Surname Soundex
            indexing is not alphabetical, but is listed by the letter-and-number
            code. If several surnames have the same code, their index cards are
            arranged alphabetically by given name &mdash; for example, S655
            Arthur, S655 Betsy, S655 Charles.</p>

            <h3>I. Russell (NARA) Soundex Coding</h3>

            <p>The Russell Soundex system is used by many indexes at the U.S.
            National Archives and Records Administration (NARA), including
            indexes to Census Records, Passenger Lists, and Naturalization
            Records.</p>

            <p>In the 1930s, the Work Projects Administration (WPA) produced a
            complete Soundex index of the 1880, 1900, 1910 (partial), 1920, and
            1930 (partial) censuses. The census information was copied onto file
            cards, alphabetically coded, and filed by state.</p>

            <p>NARA Soundex coding rules:</p>
            <ol>
                <li>Coding consists of a letter followed by three numerals
                (for example L123, C472, S160).</li>
                <li>The first letter of a surname is not coded; it is retained
                as the initial letter.</li>
                <li>A, E, I, O, U, Y, W, and H are not coded.</li>
                <li>Double letters are coded as one letter (as in Lloyd).</li>
                <li>Prefixes such as van, Von, Di, de, le, D, dela, or du are
                sometimes disregarded in coding.</li>
                <li>Code the following letters to three digits, using 0 at the
                end if needed.</li>
            </ol>

            <table>
                <thead>
                    <tr><th>Letter</th><th>Code</th></tr>
                </thead>
                <tbody>
                    <tr><td>B P F V</td><td>1</td></tr>
                    <tr><td>C S K G J Q X Z</td><td>2</td></tr>
                    <tr><td>D T</td><td>3</td></tr>
                    <tr><td>L</td><td>4</td></tr>
                    <tr><td>M N</td><td>5</td></tr>
                    <tr><td>R</td><td>6</td></tr>
                </tbody>
            </table>

            <p>For additional Russell Soundex information, see <em>The Source: A
            Guidebook of American Genealogy</em>, by Arlene Eakle and Johni
            Cerny.</p>

            <h3>II. Daitch-Mokotoff Soundex Coding</h3>

            <p>The Daitch-Mokotoff Soundex system was created by Randy Daitch
            and Gary Mokotoff of the Jewish Genealogical Society (New York),
            who concluded that the system developed by Robert Russell in 1918
            &mdash; still used today by NARA &mdash; does not apply well to
            many Slavic and Yiddish surnames. Daitch-Mokotoff Soundex also
            includes refinements that are independent of ethnic considerations.
            The rules for converting surnames into D-M code numbers are listed
            below, followed by the coding chart.</p>

            <ol>
                <li>Names are coded to six digits, each digit representing a
                sound listed in the coding chart.</li>
                <li>When a name lacks enough coded sounds for six digits, use
                zeros to fill to six digits. GOLDEN, which has only four coded
                sounds (G-L-D-N), is coded 583600.</li>
                <li>The letters A, E, I, O, U, J, and Y are always coded at the
                beginning of a name, as in Alpert (087930). In any other
                position they are ignored, except when two of them form a pair
                and the pair comes before a vowel, as in Breuer (791900) but not
                Freud.</li>
                <li>The letter H is coded at the beginning of a name, as in
                Haber (579000), or preceding a vowel, as in Manheim (665600);
                otherwise it is not coded.</li>
                <li>When adjacent sounds can combine to form a larger sound,
                they are given the code number of the larger sound. Mintz is not
                coded MIN-T-Z but MIN-TZ (664000).</li>
                <li>When adjacent letters have the same code number, they are
                coded as one sound: TOPF is not coded TO-P-F (377000) but TO-PF
                (370000). Exceptions are the combinations MN and NM, whose
                letters are coded separately, as in Kleinman (586660, not
                586600).</li>
                <li>When a surname consists of more than one word, it is coded
                as if one word: &ldquo;Ben Aron&rdquo; is treated as
                &ldquo;Benaron.&rdquo;</li>
                <li>Several letters and letter combinations may sound in one of
                two ways. The letters and combinations CH, CK, C, J, and RS are
                each assigned two possible code numbers.</li>
            </ol>

            <h3>The Daitch-Mokotoff Soundex Coding Chart</h3>
            <?php
            /* The chart and examples remain server-side includes of the
               existing InfoFiles data files. They render inside .jos-about,
               inheriting its table styling. */
            $dmChart = $_SERVER['DOCUMENT_ROOT'] . '/InfoFiles/soundex_dmtable.txt';
            if (file_exists($dmChart)) { include $dmChart; }
            ?>

            <h3>Examples of Daitch-Mokotoff Soundex Coding</h3>
            <?php
            $dmEx = $_SERVER['DOCUMENT_ROOT'] . '/InfoFiles/soundex_dmexamples.txt';
            if (file_exists($dmEx)) { include $dmEx; }
            ?>

            <p>For additional Daitch-Mokotoff Soundex information, see <em>Where
            Once We Walked</em> by Gary Mokotoff and Sallyann Amdur Sack
            (Avotaynu, 2002), pages 567&ndash;569; or Gary Mokotoff's article
            &ldquo;Soundexing and Genealogy&rdquo; on the
            <a href="http://www.avotaynu.com/soundex.html">Avotaynu website</a>.</p>

            <p class="jos-about__attribution">Adapted from the JewishGen
            Soundex InfoFile.</p>

            <a class="jos-backtop" href="#main-content">&uarr; Back to the
            calculator</a>
        </section>

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

<!-- Daitch-Mokotoff engine (JG-hosted, stevemorse.org) -->
<script src="https://stevemorse.org/census/dm.js"></script>
<script src="https://stevemorse.org/census/dmlat.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* NARA (Russell) Soundex -- inline, self-contained */
function soundexNARA(name) {
    name = name.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    if (!name.length) return "";
    var firstLetter = name[0].toUpperCase();
    var mappings = {
        'BFPV': '1',
        'CGJKQSXZ': '2',
        'DT': '3',
        'L': '4',
        'MN': '5',
        'R': '6'
    };
    var charToCode = function(ch) {
        for (var key in mappings) {
            if (key.indexOf(ch) !== -1) return mappings[key];
        }
        return '';
    };
    var code = name.toUpperCase().split('').map(charToCode);
    var rest = code.slice(1).filter(function(ch, index) {
        return ch !== '' && ch !== 'H' && ch !== 'W' && ch !== code[index];
    }).join('');
    return (firstLetter + rest + '0000').slice(0, 4);
}

var wordEl = document.getElementById('word');
var outEl = document.getElementById('output');
wordEl.addEventListener('input', function() {
    var val = this.value.trim();
    if (val.length) {
        var dm = (typeof soundex === 'function') ? soundex(val) : '(unavailable)';
        outEl.innerHTML = 'NARA Soundex: ' + soundexNARA(val) +
            '<br>Daitch-Mokotoff: ' + dm;
    } else {
        outEl.textContent = 'Start typing the name';
    }
});
</script>
</body>
</html>