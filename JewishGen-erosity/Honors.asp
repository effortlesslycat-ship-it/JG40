<%
' /JewishGen-erosity/Honors.asp:
' ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
' User input form for "Wall of Honor".
%>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JewishGen-erosity: Special Honors and Thanks &mdash; JewishGen</title>

    <!-- 1. Bootstrap 5.3.3 (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- 2. Global Styles (Tokens, Dark Mode, Nav, Band) -->
    <link href="/jg-global.css" rel="stylesheet">

    <!-- 3. Page-specific CSS -->
    <style>
        a { color: inherit; }

        .jg-band-content {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 1rem;
        }

	.page-title-band__inner {
    	   position: relative; 
           width: 100%;
           max-width: 1200px;
           margin: 0 auto;
	}


        /* Intro copy above the form */
        .jg-donate-intro {
            max-width: 720px;
            margin: 1.75rem auto 0;
            color: var(--charcoal);
            font-size: 0.95rem;
            line-height: 1.6;
        }
        body.dark-mode .jg-donate-intro { color: #c8c8c8; }
        .jg-donate-intro a { color: var(--navy); text-decoration: underline; }
        .jg-donate-intro a:hover { color: var(--sage); }
        body.dark-mode .jg-donate-intro a { color: #a8b361; }
        .jg-feature-note {
            background-color: var(--light-blue);
            border-left: 4px solid var(--sage);
            padding: 0.8rem 1.1rem;
            border-radius: 4px;
            margin-top: 1rem;
            font-weight: bold;
            font-style: italic;
        }
        .jg-feature-note a { font-weight: bold; }

        /* ------------------------------------------------------------------
           DONATION FORM CARD
           Inputs mirror the JG40 search-card field styling (light-blue fill,
           navy focus ring) for consistency, with matching dark-mode variants.
        ------------------------------------------------------------------ */
        .jg-donate-form {
            background-color: var(--white);   /* white card; flips to #121212 in dark, overridden below */
            border: 1px solid var(--light-blue);
            border-radius: 8px;
            padding: 2rem;
            max-width: 720px;
            margin: 1.5rem auto 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.07);
        }
        body.dark-mode .jg-donate-form {
            background-color: #1e1e1e;
            border-color: #333;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.35);
        }

        .jg-form-section { margin-bottom: 1.75rem; }
        .jg-form-section:last-of-type { margin-bottom: 0; }
        .jg-form-section__title {
            font-family: Georgia, 'Times New Roman', serif;
            color: var(--navy);
            font-size: 1.15rem;
            font-weight: normal;
            margin: 0 0 1.1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--sage);
        }
        body.dark-mode .jg-form-section__title { color: #ffffff; }
        .jg-form-section__sub {
            font-size: 0.85rem;
            color: var(--charcoal);
            font-style: italic;
            margin: -0.7rem 0 1rem;
            opacity: 0.85;
        }
        body.dark-mode .jg-form-section__sub { color: #a0a0a0; }

        .jg-field { margin-bottom: 1.1rem; }
        .jg-field:last-child { margin-bottom: 0; }
        .jg-field > label,
        .jg-field-label {
            display: block;
            font-weight: 600;
            color: var(--charcoal);
            margin-bottom: 0.35rem;
            font-size: 0.95rem;
        }
        body.dark-mode .jg-field > label,
        body.dark-mode .jg-field-label { color: #e0e0e0; }

        .jg-field input[type="text"],
        .jg-field input[type="email"],
        .jg-field textarea {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #c4d0d6;
            border-radius: 4px;
            font-size: 0.95rem;
            color: var(--charcoal);
            background: var(--white);
            font-family: inherit;
            transition: border-color 0.2s, background 0.2s;
        }
        .jg-field input:focus,
        .jg-field textarea:focus {
            border-color: var(--navy);
            outline: none;
            background: var(--white);
            box-shadow: 0 0 0 2px rgba(9, 73, 122, 0.12);
        }
        body.dark-mode .jg-field input,
        body.dark-mode .jg-field textarea {
            background: #2a2a2a;
            border-color: #444;
            color: #e0e0e0;
        }
        body.dark-mode .jg-field input:focus,
        body.dark-mode .jg-field textarea:focus {
            background: #1e1e1e;
            border-color: #a8b361;
        }
        .jg-field textarea { resize: vertical; min-height: 110px; }

        .jg-form-hint {
            display: block;
            font-size: 0.82rem;
            color: var(--charcoal);
            opacity: 0.8;
            margin-top: 0.35rem;
            font-style: italic;
        }
        body.dark-mode .jg-form-hint { color: #a0a0a0; }

        /* Amount: $ [input] .00 */
        .jg-amount-row { display: flex; align-items: center; gap: 0.4rem; }
        .jg-amount-affix { font-weight: 600; color: var(--charcoal); }
        body.dark-mode .jg-amount-affix { color: #e0e0e0; }
        .jg-field input.jg-amount-input {
            width: 96px;
            text-align: right;
        }

        /* Type radios */
        fieldset.jg-radio-group { border: none; margin: 0; padding: 0; }
        .jg-radio-group legend {
            font-weight: 600;
            color: var(--charcoal);
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
            padding: 0;
            float: none;
            width: auto;
        }
        body.dark-mode .jg-radio-group legend { color: #e0e0e0; }
        .jg-radio-option {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            margin-bottom: 0.45rem;
        }
        .jg-radio-option label {
            color: var(--charcoal);
            font-weight: normal;
            margin: 0;
        }
        body.dark-mode .jg-radio-option label { color: #e0e0e0; }
        .jg-radio-option input[type="radio"] {
            width: 1.05rem;
            height: 1.05rem;
            accent-color: #09497a;   /* navy; hardcoded so it survives dark mode */
        }
        body.dark-mode .jg-radio-option input[type="radio"] { accent-color: #a8b361; }

        /* Submit */
        .jg-donate-actions { text-align: center; margin-top: 1.5rem; }
        .jg-donate-submit {
            background-color: #09497a;   /* navy hardcoded; var(--navy) flips */
            color: #ffffff;
            border: none;
            font-size: 0.9375rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 13px 36px;
            border-radius: 8px;
            cursor: pointer;
            transition: filter 0.15s ease, transform 0.15s ease;
        }
        .jg-donate-submit:hover { filter: brightness(1.15); transform: translateY(-1px); }
        .jg-donate-submit:focus { outline: 3px solid var(--light-green); outline-offset: 2px; }
        body.dark-mode .jg-donate-submit { background-color: #0d2a45; }

        /* Back links */
        .jg-donate-backlinks {
            display: flex;
            justify-content: center;
            gap: 2rem;
            padding: 0.5rem 0 2.5rem;
            font-size: 0.9rem;
        }
        .jg-donate-backlinks a { color: var(--navy); }
        .jg-donate-backlinks a:hover { color: var(--sage); }
        body.dark-mode .jg-donate-backlinks a { color: #e0e0e0; }
        body.dark-mode .jg-donate-backlinks a:hover { color: #a8b361; }

        @media (max-width: 700px) {
            .jg-donate-form { padding: 1.5rem 1.25rem; }
        }
    </style>

    <script>
        // JavaScript functions by Henrik Petersen / NetKontoret
        // Explained at www.echoecho.com/jsforms.htm

        function valuevalidation(entered, min, max, alertbox) {
            with (entered) {
                checkvalue = parseInt(value);
                if ( ( parseInt(min) == min && checkvalue < min ) ||
                     ( parseInt(max) == max && checkvalue > max ) ||
                     ( value != checkvalue ) ) {
                    if ( alertbox != "" ) { alert(alertbox); }
                    return false;
                } else {
                    return true;
                }
            }
        }

        function emptyvalidation(entered, alertbox) {
            with (entered) {
                if (value == null || value == "") {
                    if ( alertbox != "" ) { alert(alertbox); }
                    return false;
                } else {
                    return true;
                }
            }
        }

        function formvalidation(thisform) {
            with (thisform) {
                alert_string = "You must make a minimum donation of $10.";
                if (valuevalidation(donate, 10, 10000, alert_string) == false) {
                    donate.focus();
                    return false;
                }
                if (emptyvalidation(Honoree, "You need to enter someone to Honor.") == false) {
                    Honoree.focus();
                    return false;
                }
            }
        }
    </script>
    <script src="/JG/Scripts/DataChecks.js"></script>
</head>
<body>

    <!-- Shared Header Injected Here -->
    <div id="site-header"></div>

    <!-- Page Title Band -->
   <div class="page-title-band">
    <div class="page-title-band__inner"> <a href="/jewishgen-erosity/" class="jg-back-link-light" aria-label="Back to JewishGen-erosity">
            <span class="arrow" aria-hidden="true">&larr;</span> Back to JewishGen-erosity
        </a><br>
        <span class="tagline">JewishGen-erosity</span>
        <h1>Special Honors and Thanks</h1>
        <p class="hero-subtitle">Honor, thank, or remember someone with a gift to JewishGen.</p>
    </div>
</div>

    <div class="container jg-band-content">

        <div class="jg-donate-intro">
            <p>All revenue from these "Special Honors and Thanks" is earmarked for the
            <a href="/JewishGen-erosity/valueadded.html">JewishGen General Fund</a>, so that JewishGen may
            continue to provide all of its programs and projects as a public service,
            free to all who are researching their Jewish ancestry.</p>
            <p class="jg-feature-note">Your contribution will be featured on the
            <a href="/JewishGen-erosity/Honors">JewishGen Wall of Honor</a>.</p>
        </div>

        <form class="jg-donate-form" method="post" action="honors1.asp"
              onsubmit="return formvalidation(this)">

            <div class="jg-form-section">
                <h2 class="jg-form-section__title">Your Dedication</h2>

                <div class="jg-field">
                    <label for="donate">Amount</label>
                    <div class="jg-amount-row">
                        <span class="jg-amount-affix">$</span>
                        <input type="text" id="donate" name="donate" maxlength="4" value=""
                               class="jg-amount-input" required pattern="[1-9][0-9]{1,3}"
                               title="Minimum contribution is $10"
                               onkeypress="return numbersonly(event)">
                        <span class="jg-amount-affix">.00</span>
                    </div>
                    <span class="jg-form-hint">Minimum contribution: $10</span>
                </div>

                <div class="jg-field">
                    <fieldset class="jg-radio-group">
                        <legend>Type (select one)</legend>
                        <div class="jg-radio-option">
                            <input type="radio" id="typeHonor" name="Type" value="Honor" checked>
                            <label for="typeHonor">In Honor Of...</label>
                        </div>
                        <div class="jg-radio-option">
                            <input type="radio" id="typeThanks" name="Type" value="Thanks">
                            <label for="typeThanks">With Special Thanks To...</label>
                        </div>
                        <div class="jg-radio-option">
                            <input type="radio" id="typeMemory" name="Type" value="Memory">
                            <label for="typeMemory">In Memory Of...</label>
                        </div>
                    </fieldset>
                </div>

                <div class="jg-field">
                    <label for="Honoree">Honoree</label>
                    <input type="text" id="Honoree" name="Honoree" required size="40" maxlength="40">
                    <span class="jg-form-hint">Person to honor, thank, or remember.</span>
                </div>

                <div class="jg-field">
                    <label for="ReasonOccasion">Reason or Occasion</label>
                    <textarea id="ReasonOccasion" name="ReasonOccasion" rows="5" cols="40"></textarea>
                    <span class="jg-form-hint">Optional: the reason or occasion for the honor or special thanks.</span>
                </div>
            </div>

            <div class="jg-form-section">
                <h2 class="jg-form-section__title">Honoree Contact Information</h2>
                <p class="jg-form-section__sub">To allow us to inform the honoree.</p>

                <div class="jg-field">
                    <label for="HonoreesEmail">Honoree's Email</label>
                    <input type="email" id="HonoreesEmail" name="HonoreesEmail" size="40" maxlength="40">
                    <span class="jg-form-hint">Email address of the honoree, if known.</span>
                </div>
<%
'  Postal address fields (retained, disabled). To re-enable, port these
'  into .jg-field blocks above and uncomment.
'    If you are not aware of an email address,
'    but do know the Honoree's postal address, then please enter it below:
'    Address Line 1   name="add1"
'    Address Line 2   name="add2"
'    City             name="city"
'    State / Province name="state"
'    Country          name="country"
'    Zip / Postcode   name="zip"
%>
            </div>

            <div class="jg-donate-actions">
                <button type="submit" class="jg-donate-submit">Go to Checkout</button>
            </div>

        </form>


    </div>

    <!-- Shared Footer Injected Here -->
    <div id="site-footer"></div>

    <!-- Component Loading Script -->
    <script>
        function loadComponent(id, file) {
            return fetch(file)
                .then(response => response.text())
                .then(html => { document.getElementById(id).innerHTML = html; })
                .catch(err => console.warn('Something went wrong loading ' + file, err));
        }
        Promise.all([
            loadComponent('site-header', '/Header_NavBar.html'),
            loadComponent('site-footer', '/Footer.html')
        ]).then(function () {
            // header/footer init (if any) runs here, after innerHTML is injected
        });
    </script>
</body>
</html>
