<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Memorial Plaque Record &mdash; JewishGen</title>
<!-- 1. Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<!-- 2. JewishGen Global Design System -->
<link rel="stylesheet" href="/jg-global.css">
<!-- 3. Page-specific styles -->
<style>

a { text-decoration: none; color: inherit; }

/* ============================================================
   OUTER WRAPPER (ecru background)
   Cloned from the JOWBR burial record page. Class names kept
   identical so a future extraction to a shared stylesheet
   covers both record pages at once.  CHW + JG40
============================================================ */
.jowbr-outer {
    background-color: var(--ecru);
    min-height: 60vh;
    padding-bottom: 2rem;
}
.jowbr-outer-inner {
    max-width: 1100px; margin: 0 auto; padding: 0 2rem;
}

/* -- Hero heading -- */
.jowbr-hero { text-align: center; padding: 12px 0 4px; }
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

/* -- Record card -- */
.record-card {
    background: #ffffff; border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    padding: 28px 32px; margin-top: 20px;
}
body.dark-mode .record-card { background: #1e1e1e; box-shadow: none; }

/* -- Name block -- */
.record-name-block h2 {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 2rem; font-weight: normal;
    color: var(--navy); margin: 0 0 4px 0;
}
body.dark-mode .record-name-block h2 { color: #8ab4d4; }
.record-name-block h2 strong { font-weight: bold; }
.alt-names {
    font-size: 13px; color: var(--charcoal); opacity: 0.7; margin: 0 0 16px 0;
}
.alt-names span {
    font-weight: bold; color: var(--navy);
    background: rgba(9,73,122,0.06); padding: 1px 6px; border-radius: 3px;
}
body.dark-mode .alt-names span { color: #8ab4d4; background: rgba(138,180,212,0.1); }

/* -- Action bar -- */
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

/* -- Two-column body -- */
.record-body {
    display: grid; grid-template-columns: 320px 1fr; gap: 32px; align-items: start;
}

/* -- Photo column -- */
.record-photo-col { position: sticky; top: 20px; }
.photo-frame {
    border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; background: #f4f4f4;
}
body.dark-mode .photo-frame { border-color: #333; background: #1e1e1e; }
.photo-frame img { width: 100%; display: block; cursor: zoom-in; }
.photo-caption {
    padding: 10px 14px; font-size: 12px; color: var(--charcoal);
    text-align: center; line-height: 1.5;
}
.photo-caption a { color: var(--navy); text-decoration: underline dotted; }
body.dark-mode .photo-caption a { color: #8ab4d4; }
/* Permanent filename: monospace so it reads as a technical identifier.
   This is the value researchers/admins cite for broken-link fixes. */
.photo-filename {
    display: block; margin-top: 6px;
    font-family: monospace; font-size: 11px;
    color: var(--charcoal); opacity: 0.7; word-break: break-all;
}
body.dark-mode .photo-filename { color: #999; }

.photo-placeholder {
    border: 2px dashed #d1caba; border-radius: 8px;
    padding: 40px 20px; text-align: center; color: var(--charcoal); opacity: 0.6;
}
body.dark-mode .photo-placeholder { border-color: #333; }
.photo-placeholder p { margin: 12px 0 0; font-size: 13px; }

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
    flex: 0 0 200px; font-size: 12px; font-weight: bold;
    text-transform: uppercase; letter-spacing: 0.5px; color: var(--charcoal);
}
body.dark-mode .fact-label { color: #888; }
.fact-value { flex: 1; font-size: 14px; color: var(--charcoal); }
body.dark-mode .fact-value { color: #c8c0b0; }
.fact-value strong { color: var(--navy); }
body.dark-mode .fact-value strong { color: #8ab4d4; }

/* -- Cemetery/Synagogue callout -- */
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
    font-size: 13px; font-weight: bold; transition: filter 0.15s;
}
.cem-link:hover { filter: brightness(1.15); text-decoration: none; color: #ffffff; }

/* -- Lightbox -- */
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

/* -- Loading / error states -- */
.jowbr-state { text-align: center; padding: 80px 24px; color: var(--charcoal); }
.jowbr-state-title {
    font-size: 1.25rem; font-weight: bold; color: var(--navy); margin-bottom: 8px;
}
body.dark-mode .jowbr-state-title { color: #8ab4d4; }
.jowbr-spinner {
    display: inline-block; width: 28px; height: 28px;
    border: 3px solid #e8edf0; border-top-color: var(--navy);
    border-radius: 50%; animation: jowbr-spin 0.7s linear infinite; margin-bottom: 12px;
}
@keyframes jowbr-spin { to { transform: rotate(360deg); } }

/* -- Responsive -- */
@media (max-width: 768px) {
    .record-body { grid-template-columns: 1fr; gap: 24px; }
    .record-photo-col { position: static; }
    .fact-label { flex: 0 0 130px; }
    .cemetery-callout { flex-direction: column; align-items: flex-start; }
    .action-bar { flex-direction: column; align-items: flex-start; }
}

@media print {
    .action-bar, .jowbr-lightbox { display: none !important; }
}

</style>
</head>
<body>
<div id="site-header"></div>

<div class="jowbr-outer">
<div class="jowbr-outer-inner">
    <div class="jowbr-hero">
        <h1 id="mem-heading">JewishGen Memorial Plaques Database</h1>
        <p class="lede">Memorial Plaques</p>
    </div>

    <main id="main-content">
        <div id="mem-loading" class="jowbr-state">
            <div class="jowbr-spinner"></div>
            <div>Loading memorial record&hellip;</div>
        </div>
        <div id="mem-content" style="display:none;"></div>
    </main>
</div>
</div>

<!-- Lightbox -->
<div class="jowbr-lightbox" id="mem-lightbox" tabindex="-1"
     onclick="if(event.target===this)closeLightbox()">
    <button class="jowbr-lightbox-close" onclick="closeLightbox()"
            aria-label="Close enlarged photograph">&times;</button>
    <img id="lightbox-img" src="" alt="">
</div>

<div id="site-footer"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ============================================================
   MEMORIAL PLAQUE RECORD PAGE
   Consumes /databases/memjson.php?rec=...
   Renders generically from the fields[] array (label/value),
   since Memorial column layouts vary per collection.
   ============================================================ */

var MEM_API = '/databases/memjson.php';

function getParams() { return new URLSearchParams(window.location.search); }

function escHtml(s) {
    var d = document.createElement('div');
    d.textContent = s == null ? '' : s;
    return d.innerHTML;
}

/* The first field is the name cell: "<B>SURNAME</B>,&nbsp;Given<BR>ALT".
   Parse surname / given / alt names out of its html. */
function parseName(field) {
    var out = { surname: '', given: '', alts: [] };
    if (!field) return out;
    var html = field.html || '';
    var mSur = html.match(/<B>([^<]+)<\/B>/i);
    if (mSur) out.surname = mSur[1].trim();
    var parts = html.split(/<br\s*\/?>/i);
    var mainText = (function(s){
        var d = document.createElement('div'); d.innerHTML = s;
        return (d.textContent || '').replace(/\s+/g, ' ').trim();
    })(parts[0] || '');
    var comma = mainText.indexOf(',');
    if (comma > -1) out.given = mainText.substring(comma + 1).trim();
    if (parts.length > 1) {
        var d = document.createElement('div'); d.innerHTML = parts.slice(1).join(' ');
        var altText = (d.textContent || '').replace(/\s+/g, ' ').trim();
        if (altText) {
            out.alts = altText.split(/[\/,;]/).map(function(s){ return s.trim(); }).filter(Boolean);
        }
    }
    return out;
}

function renderRecord(data) {
    var rec = data.record;
    var syn = data.synagogue;
    var fields = rec.fields || [];

    var nameInfo = parseName(fields[0]);
    var surname = nameInfo.surname;
    var given = nameInfo.given;
    var alts = nameInfo.alts;

    document.title = (surname ? surname + ', ' + given : 'Memorial Record') + ' - Memorial Plaques - JewishGen';

    var heading = document.getElementById('mem-heading');
    if (heading) {
        var dsTitle = data.dataset_title || 'JewishGen Memorial Plaques Database';
        heading.innerHTML = '<a href="/databases/Memorial/">' + escHtml(dsTitle) + '</a>';
    }

    var synLocation = '';
    if (syn) {
        synLocation = [syn.city, syn.state, syn.country].filter(Boolean).join(', ');
    }

    var html = '<div class="record-card">';

    // -- Name block --
    html += '<div class="record-name-block">';
    if (surname || given) {
        html += '<h2><strong>' + escHtml(surname) + '</strong>' +
                (given ? ', ' + escHtml(given) : '') + '</h2>';
    } else {
        html += '<h2>Memorial Record</h2>';
    }
    if (alts.length) {
        html += '<p class="alt-names">Also indexed as: ';
        alts.forEach(function(n){ html += '<span>' + escHtml(n) + '</span> '; });
        html += '</p>';
    }
    html += '</div>';

    // -- Action bar (no Record # - it changes on updates and misleads researchers) --
    html += '<div class="action-bar">';
    html += '<button class="btn-save" disabled title="Coming soon">&#x1F516; Save to My Research</button>';
    html += '<button class="btn-print" onclick="window.print()">&#x1F5A8; Print Record</button>';
    html += '</div>';

    // -- Two-column body --
    html += '<div class="record-body">';

    // LEFT: photo
    html += '<div class="record-photo-col">';
    if (rec.photo_url) {
        html += '<div class="photo-frame">';
        html += '<img src="' + escHtml(rec.photo_url) + '" alt="Memorial plaque photograph"';
        html += ' onclick="openLightbox(this.src, this.alt)">';
        html += '<div class="photo-caption">Memorial plaque photograph &mdash; click to enlarge';
        // Permanent filename caption (Nolan: used to resolve broken links)
        if (rec.photo_file) {
            html += '<span class="photo-filename">File: ' + escHtml(rec.photo_file) + '</span>';
        }
        html += '</div>';
        html += '</div>';
    } else {
        html += '<div class="photo-placeholder">';
        html += '<p>No photograph of this memorial plaque is currently on file.</p>';
        html += '</div>';
    }
    html += '</div>'; // /photo col

    // RIGHT: all populated fields, generic label/value.
    // Skip fields[0] (the name, already shown as the heading) and any
    // field whose value is empty or is just the synagogue link cell.
    html += '<div class="record-data-col">';
    html += '<div class="fact-group"><p class="data-section-label">Record Details</p>';

    var shownAny = false;
    for (var i = 1; i < fields.length; i++) {
        var f = fields[i];
        if (!f) continue;
        var val = (f.value || '').trim();
        if (!val) continue;
        // Skip the synagogue/society cell - it's rendered as the callout below
        if (f.html && /memorialshow\.php/i.test(f.html)) continue;
        var label = (f.label || '').trim() || 'Detail';
        html += '<div class="fact-row"><span class="fact-label">' + escHtml(label) +
                '</span><span class="fact-value">' + escHtml(val) + '</span></div>';
        shownAny = true;
    }
    if (!shownAny) {
        html += '<p style="font-size:14px;color:var(--charcoal);opacity:0.6;">No additional details recorded for this plaque.</p>';
    }
    html += '</div>'; // /fact-group
    html += '</div>'; // /data col

    html += '</div>'; // /record-body
    html += '</div>'; // /record-card

    // -- Synagogue / Society callout --
    if (syn) {
        html += '<div class="cemetery-callout">';
        html += '<div class="cem-info">';
        html += '<div class="cem-label">Synagogue / Society</div>';
        html += '<div class="cem-name">' + escHtml(syn.name || '') + '</div>';
        html += '<div class="cem-location">' + escHtml(synLocation) +
                ' &middot; ID: ' + escHtml(syn.id || '') + '</div>';
        html += '</div>';
        html += '<a class="cem-link" href="/search/synagogue.php?id=' +
                encodeURIComponent(syn.id) + '">View Synagogue / Society Page &rarr;</a>';
        html += '</div>';
    }

    document.getElementById('mem-loading').style.display = 'none';
    var content = document.getElementById('mem-content');
    content.style.display = '';
    content.innerHTML = html;
}

function renderError(msg) {
    document.getElementById('mem-loading').innerHTML =
        '<div class="jowbr-state-title">Record not found</div>' +
        '<p>' + escHtml(msg) + '</p>' +
        '<p><a href="/databases/Memorial/" style="color:var(--navy);text-decoration:underline;">Back to Memorial Plaques Search</a></p>';
}

/* -- Lightbox -- */
function openLightbox(src, alt) {
    var lb = document.getElementById('mem-lightbox');
    document.getElementById('lightbox-img').src = src;
    document.getElementById('lightbox-img').alt = alt || '';
    lb.classList.add('open');
    lb.focus();
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('mem-lightbox').classList.remove('open');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('mem-lightbox').classList.contains('open')) {
        closeLightbox();
    }
});

/* -- Load -- */
async function loadRecord() {
    var rec = getParams().get('rec');
    if (!rec) { renderError('No record ID provided.'); return; }
    try {
        var resp = await fetch(MEM_API + '?rec=' + encodeURIComponent(rec));
        if (!resp.ok) throw new Error('Server returned ' + resp.status);
        var data = await resp.json();
        if (data.error) throw new Error(data.error.message);
        renderRecord(data);
    } catch (e) {
        renderError(e.message);
    }
}

function loadComponent(id, file) {
    return fetch(file)
        .then(function(r) { if (!r.ok) throw new Error('Could not load ' + file); return r.text(); })
        .then(function(html) { document.getElementById(id).innerHTML = html; })
        .catch(function(err) { console.warn(err); });
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

loadRecord();
</script>
</body>
</html>
