<?php
/* ============================================================================
   JewishGen Communities Database  -  Search Form  (replaces legacy Search.asp)
   Posts to jgcd.php with the exact field names the engine expects.
   City-picker reads its data from /Communities/Cities.js (setCities/selCity).
   Validation / digits-only / radio-enable are reimplemented inline so the form
   does not depend on the ASP /JG/Scripts tree.  ASCII-safe (HTML entities only).
============================================================================ */
$town    = isset($_GET['town']) ? htmlspecialchars($_GET['town'], ENT_QUOTES) : '';
$selCtry = isset($_GET['ctry']) ? $_GET['ctry'] : '';
$selType = isset($_GET['stype']) ? $_GET['stype'] : 'Q';
function sel($val, $opt) { return ((string)$val === (string)$opt) ? ' selected' : ''; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>JewishGen Communities Database &mdash; Search</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="/jg-global.css">
<link rel="stylesheet" href="/Communities/jg-community.css?v=8">
</head>
<body>

<div id="site-header"></div>

<main>
    <div class="jg-community-hero">
        <div class="jg-community-hero__inner">
            <span class="jg-tag-sage" style="margin-bottom: 15px;">JewishGen Communities Database</span>
            <h1>Search the Communities Database</h1>
            <p class="coords">More than 6,500 Jewish communities across Europe, North Africa &amp; the Middle East</p>
        </div>
    </div>

    <div class="cf-layout">
        <p class="cf-intro">Each community is recorded with its names in various languages and its political
        jurisdictions across different time periods. Enter a community name below &mdash; on the results page,
        click a community&rsquo;s icon for a full page of maps, population figures, and JewishGen resources.</p>

        <form id="cf-form" class="jg-town-card cf-card" method="POST" action="/Communities/jgcd.php">
            <div class="jg-town-card-body">

                <!-- Town + method -->
                <div class="cf-section">
                    <label class="cf-label" for="Town">Jewish community name</label>
                    <div class="cf-town-row">
                        <input class="cf-input" type="text" id="Town" name="Town" value="<?= $town ?>" autocomplete="off" placeholder="e.g. Krakow">
                        <select class="cf-select" id="ttype" name="ttype" aria-label="Search method">
                            <option value="Q"<?= sel($selType,'Q') ?>>Phonetically like</option>
                            <option value="D"<?= sel($selType,'D') ?>>Sounds like (D-M Soundex)</option>
                            <option value="S"<?= sel($selType,'S') ?>>Starts with</option>
                            <option value="C"<?= sel($selType,'C') ?>>Contains</option>
                            <option value="E"<?= sel($selType,'E') ?>>Is exactly</option>
                            <option value="F1"<?= sel($selType,'F1') ?>>Fuzzy match</option>
                            <option value="F2"<?= sel($selType,'F2') ?>>Fuzzier match</option>
                            <option value="FM"<?= sel($selType,'FM') ?>>Fuzziest match</option>
                        </select>
                    </div>
                </div>

                <!-- Country filters -->
                <div class="cf-section">
                    <span class="cf-section-title">Narrow by country (optional)</span>
                    <div class="cf-grid3">
                        <div>
                            <label class="cf-label" for="Country">Modern country</label>
                            <select class="cf-select" id="Country" name="Country">
                                <option value="ZZ"<?= sel($selCtry,'') . sel($selCtry,'ZZ') ?>>All countries</option>
                                <option value="Algeria"<?= sel($selCtry,'Algeria') ?>>Algeria</option>
                                <option value="Armenia"<?= sel($selCtry,'Armenia') ?>>Armenia*</option>
                                <option value="Austria"<?= sel($selCtry,'Austria') ?>>Austria</option>
                                <option value="Azerbaijan"<?= sel($selCtry,'Azerbaijan') ?>>Azerbaijan*</option>
                                <option value="Belarus"<?= sel($selCtry,'Belarus') ?>>Belarus</option>
                                <option value="Belgium"<?= sel($selCtry,'Belgium') ?>>Belgium*</option>
                                <option value="Bosnia-Herzegovina"<?= sel($selCtry,'Bosnia-Herzegovina') ?>>Bosnia-Herzegovina</option>
                                <option value="Bulgaria"<?= sel($selCtry,'Bulgaria') ?>>Bulgaria</option>
                                <option value="Croatia"<?= sel($selCtry,'Croatia') ?>>Croatia</option>
                                <option value="Czech Republic"<?= sel($selCtry,'Czech Republic') ?>>Czech Republic</option>
                                <option value="Denmark"<?= sel($selCtry,'Denmark') ?>>Denmark*</option>
                                <option value="Egypt"<?= sel($selCtry,'Egypt') ?>>Egypt*</option>
                                <option value="Estonia"<?= sel($selCtry,'Estonia') ?>>Estonia</option>
                                <option value="France"<?= sel($selCtry,'France') ?>>France</option>
                                <option value="Georgia"<?= sel($selCtry,'Georgia') ?>>Georgia*</option>
                                <option value="Germany"<?= sel($selCtry,'Germany') ?>>Germany</option>
                                <option value="Greece"<?= sel($selCtry,'Greece') ?>>Greece</option>
                                <option value="Hungary"<?= sel($selCtry,'Hungary') ?>>Hungary</option>
                                <option value="Iran"<?= sel($selCtry,'Iran') ?>>Iran</option>
                                <option value="Iraq"<?= sel($selCtry,'Iraq') ?>>Iraq</option>
                                <option value="Italy"<?= sel($selCtry,'Italy') ?>>Italy</option>
                                <option value="Kazakhstan"<?= sel($selCtry,'Kazakhstan') ?>>Kazakhstan*</option>
                                <option value="Kosovo"<?= sel($selCtry,'Kosovo') ?>>Kosovo*</option>
                                <option value="Latvia"<?= sel($selCtry,'Latvia') ?>>Latvia</option>
                                <option value="Lebanon"<?= sel($selCtry,'Lebanon') ?>>Lebanon*</option>
                                <option value="Libya"<?= sel($selCtry,'Libya') ?>>Libya</option>
                                <option value="Lithuania"<?= sel($selCtry,'Lithuania') ?>>Lithuania</option>
                                <option value="Moldova"<?= sel($selCtry,'Moldova') ?>>Moldova</option>
                                <option value="Morocco"<?= sel($selCtry,'Morocco') ?>>Morocco</option>
                                <option value="Netherlands"<?= sel($selCtry,'Netherlands') ?>>Netherlands</option>
                                <option value="Macedonia"<?= sel($selCtry,'Macedonia') ?>>North Macedonia</option>
                                <option value="Poland"<?= sel($selCtry,'Poland') ?>>Poland</option>
                                <option value="Romania"<?= sel($selCtry,'Romania') ?>>Romania</option>
                                <option value="Russia"<?= sel($selCtry,'Russia') ?>>Russia</option>
                                <option value="Serbia"<?= sel($selCtry,'Serbia') ?>>Serbia</option>
                                <option value="Slovakia"<?= sel($selCtry,'Slovakia') ?>>Slovakia</option>
                                <option value="Slovenia"<?= sel($selCtry,'Slovenia') ?>>Slovenia</option>
                                <option value="Switzerland"<?= sel($selCtry,'Switzerland') ?>>Switzerland</option>
                                <option value="Syria"<?= sel($selCtry,'Syria') ?>>Syria*</option>
                                <option value="Tajikistan"<?= sel($selCtry,'Tajikistan') ?>>Tajikistan*</option>
                                <option value="Tunisia"<?= sel($selCtry,'Tunisia') ?>>Tunisia</option>
                                <option value="Turkey"<?= sel($selCtry,'Turkey') ?>>Turkey</option>
                                <option value="Turkmenistan"<?= sel($selCtry,'Turkmenistan') ?>>Turkmenistan*</option>
                                <option value="Ukraine"<?= sel($selCtry,'Ukraine') ?>>Ukraine</option>
                                <option value="Uzbekistan"<?= sel($selCtry,'Uzbekistan') ?>>Uzbekistan*</option>
                            </select>
                        </div>
                        <div>
                            <label class="cf-label" for="interwar">Inter-war country</label>
                            <select class="cf-select" id="interwar" name="interwar">
                                <option value="ZZ" selected>All countries</option>
                                <option value="Austria">Austria</option>
                                <option value="Belgium">Belgium*</option>
                                <option value="Bulgaria">Bulgaria</option>
                                <option value="Czechoslovakia">Czechoslovakia</option>
                                <option value="Egypt">Egypt*</option>
                                <option value="Finland">Finland*</option>
                                <option value="France">France</option>
                                <option value="Germany">Germany</option>
                                <option value="Greece">Greece</option>
                                <option value="Hungary">Hungary</option>
                                <option value="Iran">Iran*</option>
                                <option value="Iraq">Iraq*</option>
                                <option value="Italy">Italy</option>
                                <option value="Latvia">Latvia</option>
                                <option value="Lithuania">Lithuania</option>
                                <option value="Netherlands">Netherlands</option>
                                <option value="Poland">Poland</option>
                                <option value="Romania">Romania</option>
                                <option value="Soviet Union">Soviet Union</option>
                                <option value="Switzerland">Switzerland*</option>
                                <option value="Tunisia">Tunisia</option>
                                <option value="Turkey">Turkey</option>
                                <option value="Yugoslavia">Yugoslavia</option>
                            </select>
                        </div>
                        <div>
                            <label class="cf-label" for="prewwi">Pre-WWI country</label>
                            <select class="cf-select" id="prewwi" name="prewwi">
                                <option value="ZZ" selected>All countries</option>
                                <option value="Austrian Empire">Austrian Empire</option>
                                <option value="Belgium">Belgium*</option>
                                <option value="Bulgaria">Bulgaria</option>
                                <option value="Egypt">Egypt*</option>
                                <option value="France">France</option>
                                <option value="Germany">Germany</option>
                                <option value="Greece">Greece</option>
                                <option value="Hungary">Hungary</option>
                                <option value="Italy">Italy</option>
                                <option value="Morocco">Morocco*</option>
                                <option value="Netherlands">Netherlands</option>
                                <option value="Ottoman Empire">Ottoman Empire</option>
                                <option value="Persia">Persia*</option>
                                <option value="Romania">Romania</option>
                                <option value="Russian Empire">Russian Empire</option>
                                <option value="Serbia">Serbia</option>
                                <option value="Switzerland">Switzerland*</option>
                                <option value="Tunisia">Tunisia</option>
                            </select>
                        </div>
                    </div>
                    <p class="cf-note">* limited coverage for these countries.</p>
                </div>

                <!-- Distance / direction -->
                <div class="cf-section">
                    <span class="cf-section-title">Show distance &amp; direction (optional)</span>
                    <div class="cf-row">
                        <label class="cf-label" for="Miles" style="margin:0;">Units</label>
                        <select class="cf-select cf-select-sm" id="Miles" name="Miles">
                            <option value="MILES" selected>miles</option>
                            <option value="KILOM">kilometers</option>
                        </select>
                        <span class="cf-help">measured from:</span>
                    </div>

                    <div class="cf-radio-row">
                        <input type="radio" id="cl-capital" name="cl" value="capital" checked>
                        <label for="cl-capital">The capital city of the relevant country</label>
                    </div>

                    <div class="cf-radio-row">
                        <input type="radio" id="cl-latlon" name="cl" value="latlon">
                        <label for="cl-latlon">A latitude / longitude</label>
                    </div>
                    <div class="cf-coord">
                        Lat
                        <input class="cf-coord-in" id="c1" name="c1" maxlength="2" inputmode="numeric" oninput="jgDigits(this)" aria-label="Latitude degrees">&deg;
                        <input class="cf-coord-in" id="c2" name="c2" maxlength="2" inputmode="numeric" oninput="jgDigits(this)" aria-label="Latitude minutes">&prime;
                        <select class="cf-select cf-select-sm" id="LATHEM" name="LATHEM"><option value="N">N</option><option value="S">S</option></select>
                        &nbsp; Long
                        <input class="cf-coord-in" id="d1" name="d1" maxlength="2" inputmode="numeric" oninput="jgDigits(this)" aria-label="Longitude degrees">&deg;
                        <input class="cf-coord-in" id="d2" name="d2" maxlength="2" inputmode="numeric" oninput="jgDigits(this)" aria-label="Longitude minutes">&prime;
                        <select class="cf-select cf-select-sm" id="LONGHEM" name="LONGHEM"><option value="E">E</option><option value="W">W</option></select>
                    </div>

                    <div class="cf-radio-row">
                        <input type="radio" id="cl-city" name="cl" value="city">
                        <label for="cl-city">A selected city</label>
                    </div>
                    <div class="cf-coord">
                        <select class="cf-select" id="coun" onchange="setCities(this.value)" aria-label="City-picker country">
                            <option value="">Select country</option>
                            <option value="Alg">Algeria</option>
                            <option value="Aus">Austria</option>
                            <option value="Bel">Belarus</option>
                            <option value="Bul">Bulgaria</option>
                            <option value="Cro">Croatia</option>
                            <option value="Cz">Czech Republic</option>
                            <option value="Fra">France</option>
                            <option value="Ger">Germany</option>
                            <option value="Gre">Greece</option>
                            <option value="Hun">Hungary</option>
                            <option value="Irn">Iran</option>
                            <option value="Ita">Italy</option>
                            <option value="Lat">Latvia</option>
                            <option value="Lit">Lithuania</option>
                            <option value="Mac">Macedonia</option>
                            <option value="Mol">Moldova</option>
                            <option value="Mor">Morocco</option>
                            <option value="Net">Netherlands</option>
                            <option value="Pol">Poland</option>
                            <option value="Rom">Romania</option>
                            <option value="Rus">Russia</option>
                            <option value="Ser">Serbia</option>
                            <option value="Slo">Slovakia</option>
                            <option value="Tun">Tunisia</option>
                            <option value="Tur">Turkey</option>
                            <option value="Ukr">Ukraine</option>
                        </select>
                        <select class="cf-select" id="cities" onchange="selCity(this)" aria-label="City">
                            <option value="        ">Select city</option>
                        </select>
                        <input type="hidden" id="ccity" name="ccity" value="">
                    </div>
                </div>

                <button type="submit" class="btn-search-db cf-submit">Start the search <span aria-hidden="true">&rarr;</span></button>

            </div>
        </form>

        <p class="cf-foot">
            <a href="/Communities/Trees/">Jewish Communities Tree</a> &middot;
            <a href="/Communities/About.htm">About the Database</a> &middot;
            <a href="/Communities/LocTown.asp">JewishGen Gazetteer</a>
        </p>
    </div>
</main>

<div id="site-footer"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/Communities/Cities.js"></script>
<script>
/* component loader (JG40 header/footer) */
function loadComponent(id, file) {
    return fetch(file).then(r => { if (!r.ok) throw new Error(file); return r.text(); })
        .then(h => { document.getElementById(id).innerHTML = h; }).catch(e => console.warn(e));
}
Promise.all([loadComponent('site-header', '/header_navbar.html'), loadComponent('site-footer', '/footer.html')]);

/* digits-only for coordinate boxes */
function jgDigits(el) { el.value = el.value.replace(/[^0-9]/g, ''); }

/* enable only the inputs for the chosen distance mode */
function jgSensRadio(which) {
    ['c1','c2','LATHEM','d1','d2','LONGHEM'].forEach(function (id) {
        var e = document.getElementById(id); if (e) e.disabled = (which !== 'latlon');
    });
    ['coun','cities'].forEach(function (id) {
        var e = document.getElementById(id); if (e) e.disabled = (which !== 'city');
    });
}

(function () {
    var t = document.getElementById('Town'); if (t) t.focus();
    Array.prototype.forEach.call(document.querySelectorAll('input[name=cl]'), function (r) {
        r.addEventListener('change', function () { jgSensRadio(this.value); });
    });
    var checked = document.querySelector('input[name=cl]:checked');
    jgSensRadio(checked ? checked.value : 'capital');

    document.getElementById('cf-form').addEventListener('submit', function (e) {
        if (document.getElementById('Town').value.trim() === '') {
            e.preventDefault();
            alert('Please enter a town or community name to search for.');
            document.getElementById('Town').focus();
        }
    });
})();
</script>

</body>
</html>