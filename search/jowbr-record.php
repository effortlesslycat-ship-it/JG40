<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>JOWBR Burial Record &mdash; JewishGen</title>
<!-- 1. Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<!-- 2. JewishGen Global Design System -->
<link rel="stylesheet" href="/jg-global.css">
<!-- 3. Page-specific styles -->
<style>

a { text-decoration: none; color: inherit; }

/* ============================================
   PAGE HERO HEADING - matches record-list.php
============================================ */
.jowbr-hero {
    text-align: center; padding: 12px 0 4px;
}
.jowbr-hero h1 {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 2.25rem; font-weight: normal;
    color: var(--navy); margin: 0;
}
body.dark-mode .jowbr-hero h1 { color: #8ab4d4; }
.jowbr-hero h1 a {
    color: inherit; text-decoration: underline solid;
    text-underline-offset: 4px; text-decoration-thickness: 1px;
}
.jowbr-hero h1 a:hover { text-decoration-thickness: 2px; }
.jowbr-hero .lede {
    font-size: 13px; color: var(--charcoal); opacity: 0.6; margin: 6px 0 0;
}

/* ============================================
   OUTER WRAPPER (ecru background, full width)
============================================ */
.jowbr-outer {
    background-color: var(--ecru);
    min-height: 60vh;
    padding-bottom: 2rem;
}
.jowbr-outer-inner {
    max-width: 1100px; margin: 0 auto; padding: 0 2rem;
}

/* ============================================
   RECORD CARD (white on ecru)
============================================ */
.record-card {
    background: #ffffff; border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    padding: 28px 32px; margin-top: 20px;
}
body.dark-mode .record-card { background: #1e1e1e; box-shadow: none; }

/* ============================================
   NAME BLOCK
============================================ */
.record-name-block h2 {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 2rem; font-weight: normal;
    color: var(--navy); margin: 0 0 4px 0;
}
body.dark-mode .record-name-block h2 { color: #8ab4d4; }
.record-name-block h2 strong { font-weight: bold; }
.alt-names {
    font-size: 13px; color: var(--charcoal); opacity: 0.7;
    margin: 0 0 16px 0;
}
.alt-names span {
    font-weight: bold; color: var(--navy);
    background: rgba(9,73,122,0.06); padding: 1px 6px; border-radius: 3px;
}
body.dark-mode .alt-names span { color: #8ab4d4; background: rgba(138,180,212,0.1); }

/* ============================================
   ACTION BAR
============================================ */
.action-bar {
    display: flex; align-items: center; flex-wrap: wrap;
    gap: 10px; margin-bottom: 20px;
    padding-bottom: 16px; border-bottom: 1px solid #e8edf0;
}
body.dark-mode .action-bar { border-color: #2a2a2a; }
.btn-save {
    padding: 7px 16px; border-radius: 6px; font-size: 13px; font-weight: bold;
    background-color: var(--sage); color: #ffffff; border: none; cursor: not-allowed;
    opacity: 0.5;
}
.btn-print {
    padding: 7px 16px; border-radius: 6px; font-size: 13px; font-weight: bold;
    background: none; color: var(--navy); border: 1.5px solid var(--navy); cursor: pointer;
}
.btn-print:hover { background-color: #09497a; color: #ffffff; }
body.dark-mode .btn-print { color: #8ab4d4; border-color: #8ab4d4; }
body.dark-mode .btn-print:hover { background-color: #8ab4d4; color: #121212; }
.record-id-note {
    margin-left: auto; font-size: 11px; color: var(--charcoal); opacity: 0.5;
}

/* ============================================
   TWO-COLUMN BODY
============================================ */
.record-body {
    display: grid; grid-template-columns: 320px 1fr; gap: 32px;
    align-items: start;
}

/* -- Photo column -- */
.record-photo-col { position: sticky; top: 20px; }
.photo-frame {
    border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;
    background: #f4f4f4;
}
body.dark-mode .photo-frame { border-color: #333; background: #1e1e1e; }
.photo-frame img {
    width: 100%; display: block; cursor: zoom-in;
}
.photo-caption {
    padding: 10px 14px; font-size: 12px; color: var(--charcoal);
    text-align: center; line-height: 1.5;
}
.photo-caption a { color: var(--navy); text-decoration: underline dotted; }
body.dark-mode .photo-caption a { color: #8ab4d4; }

.photo-placeholder {
    border: 2px dashed #d1caba; border-radius: 8px;
    padding: 40px 20px; text-align: center;
    color: var(--charcoal); opacity: 0.6;
}
body.dark-mode .photo-placeholder { border-color: #333; }
.photo-placeholder p { margin: 12px 0 0; font-size: 13px; }

/* -- Search buttons below photo -- */
.search-in-cem {
    margin-top: 16px; display: flex; flex-direction: column; gap: 8px;
}
.search-in-cem a {
    display: block; padding: 9px 14px; border-radius: 6px;
    font-size: 12px; font-weight: bold; text-align: center;
    color: var(--navy); border: 1.5px solid var(--navy);
    transition: background-color 0.15s, color 0.15s;
}
.search-in-cem a:hover {
    background-color: #09497a; color: #ffffff; text-decoration: none;
}
body.dark-mode .search-in-cem a { color: #8ab4d4; border-color: #8ab4d4; }
body.dark-mode .search-in-cem a:hover { background-color: #8ab4d4; color: #121212; }
.search-in-cem .alt-search {
    border-color: var(--sage); color: var(--sage);
}
.search-in-cem .alt-search:hover {
    background-color: var(--sage); color: #ffffff;
}
body.dark-mode .search-in-cem .alt-search { border-color: #a8b361; color: #a8b361; }
body.dark-mode .search-in-cem .alt-search:hover { background-color: #a8b361; color: #121212; }

/* -- Data column -- */
.data-section-label {
    font-size: 12px; font-weight: bold; text-transform: uppercase;
    letter-spacing: 1.5px; color: var(--sage);
    margin: 0 0 8px 0; padding-bottom: 4px;
    border-bottom: 1px solid rgba(147,155,81,0.25);
}
body.dark-mode .data-section-label { color: #a8b361; border-color: rgba(168,179,97,0.2); }
.fact-group { margin-bottom: 24px; }
.fact-row {
    display: flex; align-items: baseline; gap: 12px;
    padding: 6px 0; border-bottom: 1px solid #f0f0f0;
}
body.dark-mode .fact-row { border-color: #222; }
.fact-label {
    flex: 0 0 160px; font-size: 12px; font-weight: bold;
    text-transform: uppercase; letter-spacing: 0.5px;
    color: var(--charcoal);
}
body.dark-mode .fact-label { color: #888; }
.fact-value {
    flex: 1; font-size: 14px; color: var(--charcoal);
}
body.dark-mode .fact-value { color: #c8c0b0; }
.fact-value strong { color: var(--navy); }
body.dark-mode .fact-value strong { color: #8ab4d4; }

.plot-tag {
    display: inline-block; padding: 2px 10px; border-radius: 4px;
    background-color: var(--navy); color: #ffffff;
    font-size: 13px; font-weight: bold; font-family: monospace;
}
body.dark-mode .plot-tag { background-color: #0d2a45; }

.comments-block {
    background: #fafcfd; border: 1px solid #e8edf0;
    border-left: 3px solid var(--sage); border-radius: 0 6px 6px 0;
    padding: 14px 18px; font-size: 14px; line-height: 1.6;
    color: var(--charcoal);
}
body.dark-mode .comments-block {
    background: #1a1a1a; border-color: #333; border-left-color: #a8b361;
    color: #a0a0a0;
}

/* ============================================
   CEMETERY CALLOUT
============================================ */
.cemetery-callout {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 16px;
    margin-top: 32px; padding: 20px 24px; border-radius: 8px;
    background-color: #09497a; color: #ffffff;
}
body.dark-mode .cemetery-callout { background-color: #0d2a45; }
.cem-label {
    font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px;
    color: var(--sage); font-weight: bold; margin-bottom: 2px;
}
body.dark-mode .cem-label { color: #a8b361; }
.cem-name { font-size: 18px; font-weight: bold; }
.cem-location { font-size: 13px; opacity: 0.7; margin-top: 2px; }
.cem-link {
    display: inline-block; padding: 10px 22px; border-radius: 6px;
    background-color: var(--sage); color: #ffffff;
    font-size: 13px; font-weight: bold;
    transition: filter 0.15s;
}
.cem-link:hover { filter: brightness(1.15); text-decoration: none; color: #ffffff; }

/* ============================================
   RECORD METADATA
============================================ */
.record-meta {
    margin-top: 16px; padding: 8px 0; font-size: 11px;
    color: var(--charcoal); opacity: 0.45;
}
.record-meta a { color: inherit; text-decoration: underline; }

/* ============================================
   LIGHTBOX
============================================ */
.jowbr-lightbox {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.88); z-index: 1000;
    align-items: center; justify-content: center; padding: 20px;
}
.jowbr-lightbox.open { display: flex; }
.jowbr-lightbox img {
    max-width: 90vw; max-height: 90vh; border-radius: 4px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.6);
}
.jowbr-lightbox-close {
    position: fixed; top: 20px; right: 28px;
    color: #fff; font-size: 38px; cursor: pointer;
    background: none; border: none; padding: 0; line-height: 1;
}
.jowbr-lightbox-close:hover { color: var(--sage); }

/* ============================================
   LOADING / ERROR STATES
============================================ */
.jowbr-state {
    text-align: center; padding: 80px 24px; color: var(--charcoal);
}
.jowbr-state-title {
    font-size: 1.25rem; font-weight: bold; color: var(--navy);
    margin-bottom: 8px;
}
body.dark-mode .jowbr-state-title { color: #8ab4d4; }
.jowbr-spinner {
    display: inline-block; width: 28px; height: 28px;
    border: 3px solid #e8edf0; border-top-color: var(--navy);
    border-radius: 50%; animation: jowbr-spin 0.7s linear infinite;
    margin-bottom: 12px;
}
@keyframes jowbr-spin { to { transform: rotate(360deg); } }

/* ============================================
   RESPONSIVE
============================================ */
@media (max-width: 768px) {
    .record-body { grid-template-columns: 1fr; gap: 24px; }
    .record-photo-col { position: static; }
    .fact-label { flex: 0 0 120px; }
    .cemetery-callout { flex-direction: column; align-items: flex-start; }
    .action-bar { flex-direction: column; align-items: flex-start; }
    .record-id-note { margin-left: 0; }
}

/* =====================================================
   PRINT STYLES  -- jowbr-record.php
   ===================================================== */

/* Masthead exists for print only -- hidden on screen */

.jg-print-masthead { display: none;
 }


@media print {
 
    /* ---- 1. Hide screen chrome ---- */
    #site-header,
    #site-footer,
    .action-bar,
    .jowbr-lightbox,
    .search-in-cem,
    .jg-datedrawer,
    .jg-datedrawer-backdrop,
    .photo-caption,          /* says "click to enlarge" -- meaningless on paper */
    .cem-link {              /* "View Cemetery Page" button */
        display: none !important;
    }
 
    /* ---- 2. Force light output even if dark mode is on ----
       Broad reset first; branded rules below re-style on
       top of it. Equal specificity, later rules win. */
    body.dark-mode,
    body.dark-mode * {
        background-color: #ffffff !important;
        color: #232b2b !important;
        border-color: #aaaaaa !important;
        box-shadow: none !important;
    }
 
    /* ---- 3. Page basics ---- */
    body { background-color: #ffffff; }
    .jowbr-outer { background-color: #ffffff !important; padding-bottom: 0; }
    .jowbr-outer-inner { max-width: none; padding: 0; }
 
    /* ---- 4. JG masthead: logo + tagline + navy/sage rule ---- */
    .jg-print-masthead {
        display: block;
        text-align: center;
        padding-bottom: 10px;
        border-bottom: 2.5px solid #09497a;   /* navy signature rule */
        margin-bottom: 3px;
    }
    .jg-print-masthead__logo {
        width: 1.6in;
        height: auto;
    }
    .jg-print-masthead__tagline {
        font-family: Georgia, 'Times New Roman', serif;
        font-style: italic;
        font-size: 11px;
        color: #232b2b !important;
        margin: 4px 0 0;
    }
    /* thin sage echo line beneath the navy rule */
    .jg-print-masthead::after {
        content: '';
        display: block;
        border-bottom: 1px solid #939b51;
        margin-top: 3px;
        position: relative;
        top: 3px;
    }
 
    /* ---- 5. Title band: ink block becomes letterhead text ---- */
    .page-title-band {
        background-color: #ffffff !important;
        background-image: none !important;
        color: #232b2b !important;
        text-align: center;
        padding: 10px 0 4px;
        margin: 0;
    }
    .page-title-band .tagline {
        color: #939b51 !important;
        background-color: transparent !important;
        border: none !important;
        padding: 0;
    }
    .page-title-band h1 {
        color: #09497a !important;
        font-size: 1.5rem;
        margin: 2px 0 0;
    }
 
    /* ---- 6. Permalink + print footer line ---- */
    .record-meta { opacity: 1; font-size: 10px; }
    .record-meta::before {
        content: 'Printed from JewishGen.org \2014\0020 JewishGen Online Worldwide Burial Registry';
        display: block;
        font-size: 10px;
        color: #232b2b;
        border-top: 1px solid #939b51;
        padding-top: 6px;
        margin-top: 10px;
    }
 
    /* ---- 7. Record card: borderless in print ----
       A framed card slices open at page breaks on long
       records; branding is carried by the masthead rules,
       sage section labels, and footer line instead. */
    .record-card {
        box-shadow: none;
        border: none !important;
        border-radius: 0;
        padding: 0.1in 0 0;
        margin-top: 0.1in;
        background-color: #ffffff !important;
    }
    .record-name-block h2 { color: #09497a !important; }
    .data-section-label {
        color: #939b51 !important;
        border-bottom-color: #939b51 !important;
    }
 
      /* ---- 8. Photo left, facts beside it; the shorter
       layout pulls the cemetery box onto page 1 ---- */
    .record-body { display: block; }
    .record-photo-col {
        position: static;
        float: left;
        width: 3in;
        max-width: 3in;
        margin: 0 0.25in 0.15in 0;
    }
    .record-data-col {
        overflow: hidden;   /* stops fact-row borders running under the photo */
    }
    .record-body::after {
        content: '';
        display: block;
        clear: both;        /* callout and permalink sit below both columns */
    }
    .photo-frame {
        border: 1px solid #cccccc;
        border-radius: 0;
        page-break-inside: avoid;
    }
 
    /* ---- 9. Data rows: keep intact across page breaks ---- */
    .fact-row { page-break-inside: avoid; }
    .fact-group { page-break-inside: avoid; }
 
    /* ---- 10. Ink-heavy fills become bordered text ---- */
    .plot-tag {
        background-color: transparent !important;
        color: #232b2b !important;
        border: 1px solid #232b2b;
    }
    .comments-block {
        background-color: transparent !important;
        border: 1px solid #cccccc;
        border-left: 3px solid #939b51 !important;
    }
    .cemetery-callout {
        background-color: #ffffff !important;
        color: #232b2b !important;
        border: 1.5px solid #09497a !important;
        border-radius: 0;
        page-break-inside: avoid;
    }
    .cemetery-callout .cem-label {
        color: #939b51 !important;
        opacity: 1;
    }
    .cemetery-callout .cem-name,
    .cemetery-callout .cem-location {
        color: #232b2b !important;
        opacity: 1;
    }
}
 

</style>
</head>
<body>
<div id="site-header"></div>

 <div class="jg-print-masthead" aria-hidden="true">
       <img src="https://www.jewishgen.org/JG/Images/JGlogo.svg"
            alt="JewishGen" class="jg-print-masthead__logo">
       <p class="jg-print-masthead__tagline">The Home of Jewish
           Genealogy</p>
   </div>


    <!-- Hero heading -->
      <div class="page-title-band">
        <span class="tagline">JewishGen Online Worldwide Burial Registry</span>
	<h1 id="jowbr-heading">JOWBR</h1>
     </div>


<div class="jowbr-outer">
<div class="jowbr-outer-inner">


    <main id="main-content">
        <!-- Loading state -->
        <div id="jowbr-loading" class="jowbr-state">
            <div class="jowbr-spinner"></div>
            <div>Loading burial record&hellip;</div>
        </div>
        <!-- Content injected by JS -->
        <div id="jowbr-content" style="display:none;"></div>
    </main>
</div>
</div>

<!-- Lightbox -->
<div class="jowbr-lightbox" id="jowbr-lightbox" tabindex="-1"
     onclick="if(event.target===this)closeLightbox()">
    <button class="jowbr-lightbox-close" onclick="closeLightbox()"
            aria-label="Close enlarged photograph">&times;</button>
    <img id="lightbox-img" src="" alt="">
</div>

<div id="jowbr-datedrawer"></div>

<div id="site-footer"></div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/databases/Cemetery/drawer/jg-hebcal.min.js"></script>
<script src="/databases/Cemetery/drawer/jg-datedrawer.js?v=2"></script>
<script>
/* ============================================================
   JOWBR BURIAL RECORD PAGE
   ============================================================ */

var JOWBR_API = '/databases/jowbrjson.php';

function getParams() { return new URLSearchParams(window.location.search); }

/* Escape for safe HTML insertion, but PRESERVE numeric character
   references. Record data stores Hebrew (and other non-Latin) text as
   HTML entities (&#1499; / &#x5DB;) because the pipeline is ASCII-only.
   A plain textContent escape turns their & into &amp; and the browser
   prints the literal codes (double-escaping). So: escape everything,
   then restore numeric entities so they render. */
function escHtml(s) {
    var d = document.createElement('div');
    d.textContent = s == null ? '' : String(s);
    return d.innerHTML.replace(/&amp;#(x[0-9a-fA-F]+|[0-9]+);/g, '&#$1;');
}

/* Decode numeric entities to real characters - for plain-text sinks
   (document.title, aria-labels) where entities do NOT get parsed. */
function decodeEntities(s) {
    if (s == null) return '';
    return String(s).replace(/&#(x[0-9a-fA-F]+|[0-9]+);/g, function(m, code) {
        var n = (code.charAt(0) === 'x' || code.charAt(0) === 'X')
            ? parseInt(code.slice(1), 16) : parseInt(code, 10);
        return isNaN(n) ? m : String.fromCharCode(n);
    });
}

/* Strip &nbsp;, <hr>, and tags to get plain text */
function plain(s) {
    if (!s) return '';
    return s.replace(/&nbsp;/gi, ' ').replace(/<br\s*\/?>/gi, ' ').replace(/<[^>]+>/g, '').replace(/\s+/g, ' ').trim();
}

/* Split a stacked value by <HR> into [top, bottom] */
function splitHR(s) {
    if (!s) return ['', ''];
    var parts = s.split(/<hr\s*\/?>/i);
    return [plain(parts[0] || ''), plain(parts[1] || '')];
}

/* ---- Render the page ---- */

function renderRecord(data) {
    var rec = data.record;
    var cem = data.cemetery;
    var fields = rec.fields || [];

    // Parse fields by position
    var nameHtml    = fields[0] ? fields[0].html : '';
    var nameText    = plain(fields[0] ? fields[0].value : '');
    var places      = splitHR(fields[1] ? fields[1].html : '');
    var dates       = splitHR(fields[2] ? fields[2].html : '');
    var hebrew      = splitHR(fields[3] ? fields[3].html : '');
    var ageBurial   = splitHR(fields[4] ? fields[4].html : '');
    // fields[5] = photo (already extracted as photo_url)
    var plot        = plain(fields[6] ? fields[6].value : '');
    var spouse      = plain(fields[7] ? fields[7].value : '');
    var famParts    = splitHR(fields[8] ? fields[8].html : '');
    var comments    = plain(fields[9] ? fields[9].value : '');
    // fields[10] = cemetery link (we use structured cemetery data instead)

    // Extract surname and given name from name HTML
    // Format: "<B>SURNAME</B>,&nbsp;Given<BR>OTHER / SURNAMES"
    var surname = '', givenName = '';
    var altNames = [];
    var nameMatch = nameHtml.match(/<B>([^<]+)<\/B>/i);
    if (nameMatch) surname = nameMatch[1].trim();

    // Split name value by <BR> to separate given name from other surnames
    var nameRaw = fields[0] ? fields[0].html : '';
    var nameParts = nameRaw.split(/<br\s*\/?>/i);
    var mainNameText = plain(nameParts[0] || '');
    var commaPos = mainNameText.indexOf(',');
    if (commaPos > -1) givenName = mainNameText.substring(commaPos + 1).trim();

    // Alt surnames: from the second part after <BR>, or from the label parentheses
    if (nameParts.length > 1) {
        var altText = plain(nameParts.slice(1).join(' '));
        if (altText) {
            altNames = altText.split(/[\/,;]/).map(function(s) { return s.trim(); }).filter(Boolean);
        }
    }
    // Also check label for alt names if none found in value
    if (!altNames.length && fields[0]) {
        var labelMatch = (fields[0].label || '').match(/\(([^)]+)\)/);
        if (labelMatch) {
            var labelAlt = plain(labelMatch[1]);
            if (labelAlt && labelAlt.toLowerCase() !== 'other surnames') {
                altNames = labelAlt.split(/[\/,;]/).map(function(s) { return s.trim(); }).filter(Boolean);
            }
        }
    }

    // Update page title
    document.title = decodeEntities(surname ? surname + ', ' + givenName : nameText) + ' - JOWBR - JewishGen';

    // Update heading with dataset title as a link
    var heading = document.getElementById('jowbr-heading');
    if (heading) {
        var dsTitle = data.dataset_title || 'JewishGen Online Worldwide Burial Registry';
        heading.innerHTML = '<a href="https://www.jewishgen.org/databases/Cemetery/">' +
            escHtml(dsTitle) + '</a>';
    }

    // Build cemetery location string
    var cemLocation = '';
    if (cem) {
        var locParts = [];
        if (cem.city) locParts.push(cem.city);
        if (cem.state) locParts.push(cem.state);
        if (cem.country) locParts.push(cem.country);
        cemLocation = locParts.join(', ');
    }

    var html = '<div class="record-card">';

    // -- Name block --
    html += '<div class="record-name-block">';
    html += '<h2><strong>' + escHtml(surname) + '</strong>, ' + escHtml(givenName) + '</h2>';
    if (altNames.length) {
        html += '<p class="alt-names">Also indexed as: ';
        altNames.forEach(function(n) { html += '<span>' + escHtml(n) + '</span> '; });
        html += '</p>';
    }
    html += '</div>';

    // -- Action bar --
    html += '<div class="action-bar">';
    html += '<button class="btn-save" disabled title="Coming soon">&#x1F516; Save to My Research</button>';
    html += '<button class="btn-print" onclick="window.print()">&#x1F5A8; Print Record</button>';
    html += '<span class="record-id-note">Record ID: ' + escHtml(rec.dataid) + '</span>';
    html += '</div>';

    // -- Two-column body --
    html += '<div class="record-body">';

    // LEFT: Photo
    html += '<div class="record-photo-col">';
    if (rec.photo_url) {
        html += '<div class="photo-frame">';
        html += '<img src="' + escHtml(rec.photo_url) + '" alt="Gravestone photograph"';
        html += ' onclick="openLightbox(this.src, this.alt)" style="cursor:zoom-in">';
        html += '<div class="photo-caption">Gravestone photograph &mdash; click to enlarge</div>';
        html += '</div>';
    } else {
        html += '<div class="photo-placeholder">';
        html += '<p>No photograph of this gravestone is currently on file.</p>';
        html += '</div>';
    }

    // Search buttons below photo
    html += '<div class="search-in-cem">';
    var cemName = cem ? (cem.name || '') : '';
    var cemCity = cem ? (cem.city || '') : '';
    // Primary surname: search in this city + search all
    if (surname && cemCity) {
        html += '<a href="/search-results.php?srch1=' + encodeURIComponent(surname) +
            '&srch1v=S&srch1t=E&srch2=' + encodeURIComponent(cemCity) +
            '&srch2v=T&srch2t=E&allcountry=01jowbr">Search other <strong>' +
            escHtml(surname) + '</strong> in ' + escHtml(cemName || cemCity) + '</a>';
    }
    if (surname) {
        html += '<a href="/search-results.php?srch1=' + encodeURIComponent(surname) +
            '&srch1v=S&srch1t=E&allcountry=01jowbr">Search all <strong>' +
            escHtml(surname) + '</strong> burials</a>';
    }
    // Alt surnames: search in this city + search all
    altNames.forEach(function(alt) {
        if (cemCity) {
            html += '<a class="alt-search" href="/search-results.php?srch1=' + encodeURIComponent(alt) +
                '&srch1v=S&srch1t=E&srch2=' + encodeURIComponent(cemCity) +
                '&srch2v=T&srch2t=E&allcountry=01jowbr">Search <strong>' +
                escHtml(alt) + '</strong> in ' + escHtml(cemName || cemCity) + '</a>';
        }
        html += '<a class="alt-search" href="/search-results.php?srch1=' + encodeURIComponent(alt) +
            '&srch1v=S&srch1t=E&allcountry=01jowbr">Search all <strong>' +
            escHtml(alt) + '</strong> burials</a>';
    });
    html += '</div>';
    html += '</div>'; // /photo col

    // RIGHT: Data
    html += '<div class="record-data-col">';

    // Vital Information
    html += '<div class="fact-group"><p class="data-section-label">Vital Information</p>';
    if (hebrew[0]) html += factRow('Hebrew Name', hebrew[0]);
    if (dates[0])  html += factRow('Date of Birth', dates[0]);
    if (dates[1])  html += factRow('Date of Death', dates[1]);
    if (hebrew[1]) html += factRow('Hebrew Date', hebrew[1]);
    if (ageBurial[0]) html += factRow('Age at Death', ageBurial[0]);
    if (ageBurial[1]) html += factRow('Burial Date', ageBurial[1]);
    if (places[0]) html += factRow('Place of Birth', places[0]);
    if (places[1]) html += factRow('Place of Death', places[1]);
    html += '</div>';

    // Family
    if (spouse || famParts[0] || famParts[1]) {
        html += '<div class="fact-group"><p class="data-section-label">Family</p>';
        if (spouse) html += factRow('Spouse', spouse);
        if (famParts[0]) html += factRow('Father', famParts[0]);
        if (famParts[1]) html += factRow('Mother', famParts[1]);
        html += '</div>';
    }

    // Burial Location
    if (plot) {
        html += '<div class="fact-group"><p class="data-section-label">Burial Location</p>';
        html += '<div class="fact-row"><span class="fact-label">Plot</span>' +
            '<span class="fact-value"><span class="plot-tag">' + escHtml(plot) + '</span></span></div>';
        html += '</div>';
    }

    // Notes
    if (comments) {
        html += '<div class="fact-group"><p class="data-section-label">Notes</p>';
        html += '<div class="comments-block">' + escHtml(comments) + '</div>';
        html += '</div>';
    }

    html += '</div>'; // /data col
    html += '</div>'; // /record-body
    html += '</div>'; // /record-card

    // -- Cemetery callout --
    if (cem) {
        html += '<div class="cemetery-callout">';
        html += '<div class="cem-info">';
        html += '<div class="cem-label">Cemetery</div>';
        html += '<div class="cem-name">' + escHtml(cem.name || '') + '</div>';
        html += '<div class="cem-location">' + escHtml(cemLocation) +
            ' &middot; Cemetery ID: ' + escHtml(cem.id || '') + '</div>';
        html += '</div>';
        html += '<a class="cem-link" href="/search/cemetery.php?id=' +
            encodeURIComponent(cem.id) + '">View Cemetery Page &rarr;</a>';
        html += '</div>';
    }

    // -- Permalink --
    html += '<div class="record-meta">Permalink: <a href="/databases/cemetery/jowbr.php?rec=' +
        escHtml(rec.dataid) + '">jewishgen.org/databases/cemetery/jowbr.php?rec=' +
        escHtml(rec.dataid) + '</a></div>';

    document.getElementById('jowbr-loading').style.display = 'none';
    var content = document.getElementById('jowbr-content');
    content.style.display = '';
    content.innerHTML = html;
}

function factRow(label, value) {
    return '<div class="fact-row"><span class="fact-label">' + escHtml(label) +
        '</span><span class="fact-value">' + escHtml(value) + '</span></div>';
}

function renderError(msg) {
    document.getElementById('jowbr-loading').innerHTML =
        '<div class="jowbr-state-title">Record not found</div>' +
        '<p>' + escHtml(msg) + '</p>' +
        '<p><a href="/JOWBR/search.php" style="color:var(--navy);text-decoration:underline;">Back to JOWBR Search</a></p>';
}

/* ---- Lightbox ---- */
function openLightbox(src, alt) {
    var lb = document.getElementById('jowbr-lightbox');
    document.getElementById('lightbox-img').src = src;
    document.getElementById('lightbox-img').alt = alt || '';
    lb.classList.add('open');
    lb.focus();
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('jowbr-lightbox').classList.remove('open');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('jowbr-lightbox').classList.contains('open')) {
        closeLightbox();
    }
});

/* ---- Load ---- */
async function loadRecord() {
    var rec = getParams().get('rec');
    if (!rec) { renderError('No record ID provided.'); return; }
    try {
        var resp = await fetch(JOWBR_API + '?rec=' + encodeURIComponent(rec));
        if (!resp.ok) throw new Error('Server returned ' + resp.status);
        var data = await resp.json();
        if (data.error) throw new Error(data.error.message);
        renderRecord(data);
    } catch (e) {
        renderError(e.message);
    }
}

/* ---- Component loader ---- */
function loadComponent(id, file) {
    return fetch(file)
        .then(function(r) { if (!r.ok) throw new Error('Could not load ' + file); return r.text(); })
        .then(function(html) { document.getElementById(id).innerHTML = html; })
        .catch(function(err) { console.warn(err); });
}

 Promise.all([
        loadComponent('site-header', '/Header_NavBar.html'),
        loadComponent('site-footer', '/Footer.html'),
        loadComponent('jowbr-datedrawer', '/databases/Cemetery/drawer/JOWBR_DateDrawer.html')
    ]).then(function() {
    jgDateDrawerInit();
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

loadRecord();
</script>
</body>
</html>