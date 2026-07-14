<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Synagogue &amp; Society Information &mdash; Memorial Plaques &mdash; JewishGen</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="/jg-global.css">
<style>
/* ============================================================
   SYNAGOGUE / SOCIETY PAGE - Memorial Plaques Database
   Cloned from cemetery.php (JOWBR). Class names kept identical
   so any future extraction to a shared stylesheet covers both.
   CHW + JG40
   ============================================================ */

a { text-decoration: none; color: inherit; }

/* -- Page wrapper (warm ecru background, full width) -- */
.page-wrapper {
    background-color: var(--ecru);
    padding: 0 2rem 2rem;
}
.page-inner {
    max-width: 1100px; margin: 0 auto;
}

/* -- ID tag -- */
.cem-id-tag {
    display: inline-block; padding: 2px 10px; border-radius: 4px;
    border: 1px solid var(--charcoal); font-family: monospace; font-size: 12px;
    color: var(--charcoal); margin-left: 8px;
}
body.dark-mode .cem-id-tag { border-color: #555; color: #aaa; }

/* -- Stats bar (white cards on ecru, sage accent) -- */
.stats-bar {
    display: grid; grid-template-columns: 1fr 1fr; gap: 16px;
    margin-bottom: 24px;
}
.stat-block {
    text-align: center; padding: 24px 16px;
    background: #ffffff; border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    border-top: 3px solid var(--sage);
}
body.dark-mode .stat-block { background: #1e1e1e; box-shadow: 0 2px 10px rgba(0,0,0,0.3); border-top-color: #a8b361; }
.stat-number {
    display: block; font-size: 2rem; font-weight: bold;
    color: var(--navy); font-family: Georgia, serif;
}
body.dark-mode .stat-number { color: #8ab4d4; }
.stat-label {
    font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px;
    color: var(--charcoal); opacity: 0.6; margin-top: 2px;
}

/* -- Search section (navy, matching cemetery callout style) -- */
.search-section {
    background: #09497a; border-radius: 10px;
    padding: 24px 28px; margin-bottom: 32px; color: #ffffff;
}
body.dark-mode .search-section { background: #0d2a45; }
.search-label {
    font-size: 13px; font-weight: bold; text-transform: uppercase;
    letter-spacing: 1.5px; color: var(--sage); margin-bottom: 6px;
}
body.dark-mode .search-label { color: #a8b361; }
.search-description { font-size: 13px; color: #ffffff; opacity: 0.75; margin: 0 0 12px; }
.search-note { font-size: 11px; color: #ffffff; opacity: 0.45; margin: 8px 0 0; font-style: italic; }
.search-row-cem { display: flex; gap: 8px; }
.search-row-cem input {
    flex: 1; padding: 11px 16px; border: 1.5px solid #d1caba;
    border-radius: 6px; font-size: 14px; background: #ffffff;
}
body.dark-mode .search-row-cem input { background: #1a1a1a; border-color: #444; color: #e0e0e0; }
.btn-cem-search {
    padding: 11px 24px; border: none; border-radius: 6px;
    background-color: var(--sage); color: #ffffff;
    font-size: 13px; font-weight: bold; letter-spacing: 0.5px;
    cursor: pointer; text-transform: uppercase;
}
.btn-cem-search:hover { filter: brightness(1.1); }

/* -- Content body -- */
.content-body {
    display: grid; grid-template-columns: 1fr 300px; gap: 32px;
}
.content-left {
    background: #ffffff; border-radius: 8px; padding: 24px;
    box-shadow: 0 1px 6px rgba(0,0,0,0.04);
    border-left: 3px solid var(--sage);
}
body.dark-mode .content-left { background: #1e1e1e; box-shadow: none; border-left-color: #a8b361; }

/* -- Section labels -- */
.section-label {
    font-size: 12px; font-weight: bold; text-transform: uppercase;
    letter-spacing: 1.5px; color: var(--sage); margin: 0 0 12px;
}
body.dark-mode .section-label { color: #a8b361; }

/* -- Description block -- */
.description-text {
    font-size: 14px; line-height: 1.7; color: var(--charcoal);
}
body.dark-mode .description-text { color: #a0a0a0; }
.description-text a { color: var(--navy); text-decoration: underline dotted; }
body.dark-mode .description-text a { color: #8ab4d4; }
.description-text.collapsed {
    max-height: 100px; overflow: hidden;
    -webkit-mask-image: linear-gradient(to bottom, black 50%, transparent 100%);
    mask-image: linear-gradient(to bottom, black 50%, transparent 100%);
}
.description-text.expanded { max-height: none; mask-image: none; -webkit-mask-image: none; }
.btn-show-more {
    display: inline-block; margin-top: 8px; padding: 5px 14px;
    font-size: 12px; font-weight: bold; color: var(--navy);
    background: none; border: 1.5px solid var(--navy); border-radius: 5px;
    cursor: pointer; transition: background-color 0.15s, color 0.15s;
}
.btn-show-more:hover { background-color: #09497a; color: #ffffff; }
body.dark-mode .btn-show-more { color: #8ab4d4; border-color: #8ab4d4; }
body.dark-mode .btn-show-more:hover { background-color: #8ab4d4; color: #121212; }

/* -- Sidebar details (stacked: label above value) -- */
.detail-group { margin-bottom: 28px; }
.detail-row {
    padding: 10px 0; border-bottom: 1px solid #eee;
}
body.dark-mode .detail-row { border-color: #222; }
.detail-row-label {
    display: block; font-size: 10px; font-weight: bold; text-transform: uppercase;
    letter-spacing: 1px; color: var(--charcoal); opacity: 0.5; margin-bottom: 2px;
}
body.dark-mode .detail-row-label { color: #777; }
.detail-row-value {
    display: block; font-size: 14px; color: var(--charcoal);
}
body.dark-mode .detail-row-value { color: #c8c0b0; }

/* -- Landsmanshaft -- */
.land-group { margin-bottom: 24px; }

/* -- Record meta -- */
.record-meta-cem {
    font-size: 11px; color: var(--charcoal); opacity: 0.4; margin-top: 12px;
}

/* -- Back links (simple, matching burial record style) -- */
.back-links {
    display: flex; gap: 20px; flex-wrap: wrap;
    margin-top: 28px; padding-top: 16px;
    border-top: 1px solid rgba(147,155,81,0.3);
}
.back-links a {
    font-size: 13px; font-weight: bold; color: var(--navy);
    text-decoration: underline dotted; text-underline-offset: 3px;
}
.back-links a:hover { color: var(--sage); }
body.dark-mode .back-links a { color: #8ab4d4; }
body.dark-mode .back-links { border-color: #333; }

/* -- Loading state -- */
.cem-state { text-align: center; padding: 80px 24px; color: var(--charcoal); }
.cem-spinner {
    display: inline-block; width: 28px; height: 28px;
    border: 3px solid #e8edf0; border-top-color: var(--navy);
    border-radius: 50%; animation: cem-spin 0.7s linear infinite; margin-bottom: 12px;
}
@keyframes cem-spin { to { transform: rotate(360deg); } }

/* -- Responsive -- */
@media (max-width: 768px) {
    .content-body { grid-template-columns: 1fr; }
    .stats-bar { grid-template-columns: 1fr 1fr; }
    .search-row-cem { flex-direction: column; }
}

@media print {
    .search-section { display: none !important; }
}

</style>
</head>
<body>
<div id="site-header"></div>

<div class="page-title-band">
    <span class="tagline">JewishGen Memorial Plaques Database</span>
    <h1 id="syn-title">Loading&hellip;</h1>
    <p class="hero-subtitle" id="syn-sub"></p>
    <span class="stat-line" id="syn-loc"></span>
</div>

<main id="main-content" class="page-wrapper">
    <div class="page-inner">
    <div id="syn-loading" class="cem-state">
        <div class="cem-spinner"></div>
        <div>Loading synagogue &amp; society information&hellip;</div>
    </div>
    <div id="syn-content" style="display:none;"></div>
    </div><!-- /page-inner -->
</main>

<div id="site-footer"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
var SYN_API = '/databases/synjson.php';

function escHtml(s) {
    var d = document.createElement('div');
    d.textContent = s || '';
    return d.innerHTML;
}

function renderSynagogue(data) {
    var c = data.synagogue;
    document.title = (c.name || 'Synagogue') + ' - Memorial Plaques - JewishGen';

    var locationParts = [];
    if (c.street) locationParts.push(c.street);
    if (c.city) locationParts.push(c.city);
    if (c.state) locationParts.push(c.state);
    var locationStr = locationParts.join(', ');


    // Populate the title band
    document.getElementById('syn-title').textContent = c.name || 'Synagogue / Society';
    var sub = document.getElementById('syn-sub');
    sub.textContent = c.section || '';
    sub.style.display = c.section ? '' : 'none';
    document.getElementById('syn-loc').textContent =
        [locationStr, c.country].filter(Boolean).join(', ') + ' \u00B7 Synagogue/Society ID: ' + (c.id || '');

    var html = '';

    // Stats bar
    html += '<div class="stats-bar">';
    html += '<div class="stat-block"><span class="stat-number">' +
        (c.burials || 0).toLocaleString() + '</span><span class="stat-label">Memorials</span></div>';
    html += '<div class="stat-block"><span class="stat-number">' +
        (c.photos || 0).toLocaleString() + '</span><span class="stat-label">Photographs</span></div>';
    html += '</div>';

    // Search this synagogue/society
    html += '<div class="search-section">';
    html += '<div class="search-label">Search Records From This Synagogue or Society</div>';
    html += '<p class="search-description">Enter a surname to search memorial plaques from ' +
        escHtml(c.name) + (c.section ? ' &mdash; ' + escHtml(c.section) : '') + '.</p>';
    html += '<div class="search-row-cem">';
    html += '<input type="text" id="syn-search-input" placeholder="Enter surname&hellip;" onkeydown="if(event.key===\'Enter\')searchThisSynagogue()">';
    html += '<button class="btn-cem-search" onclick="searchThisSynagogue()">Search</button>';
    html += '</div>';
    html += '<p class="search-note">Results will open in the Memorial Plaques search results page filtered to this city.</p>';
    html += '</div>';

    // Two-column content
    html += '<div class="content-body">';

    // LEFT: Description
    html += '<div class="content-left">';
    if (c.comments) {
        html += '<p class="section-label">About This Synagogue or Society</p>';
        html += '<div class="description-text collapsed" id="desc-text">' + c.comments + '</div>';
        html += '<button class="btn-show-more" id="desc-toggle" onclick="toggleDesc()">';
        html += 'Show more &darr;</button>';
    } else {
        html += '<p class="section-label">About This Synagogue or Society</p>';
        html += '<p style="font-size:14px;color:var(--charcoal);opacity:0.6;">No description available.</p>';
    }
    html += '</div>';

    // RIGHT: Sidebar
    html += '<div>';
    html += '<div class="detail-group"><p class="section-label">Synagogue &amp; Society Details</p>';
    if (c.street) html += detailRow('Street', c.street);
    if (c.city) html += detailRow('City', c.city);
    // "Region" is the neutral label across all geographies (state, province,
    // gubernia, etc.) - avoids "State" reading wrong for Canadian/European rows.
    var regionParts = [c.state, c.country].filter(Boolean);
    if (regionParts.length) html += detailRow('Region', regionParts.join(', '));
    if (c.section) html += detailRow('Section', c.section);
    html += '</div>';

    // Landsmanshaft
    if (c.land_city || c.land_ctry) {
        html += '<div class="land-group"><p class="section-label">Landsmanshaft Info</p>';
        var landLoc = [c.land_city, c.land_ctry].filter(Boolean).join(', ');
        html += detailRow('Town &amp; Country of Origin', landLoc);
        html += '</div>';
    }

    if (c.last_updated) {
        html += '<div class="record-meta-cem">Data last updated: ' + escHtml(c.last_updated) + '</div>';
    }
    html += '</div>'; // /sidebar

    html += '</div>'; // /content-body

    // Back links
    html += '<div class="back-links">';
    html += '<a href="/databases/Memorial/">&larr; Memorial Plaques Search</a>';
    html += '<a href="/databases/Memorial/Submit.htm">&larr; About the Memorial Plaques Database</a>';
    html += '</div>';

    // Show content
    document.getElementById('syn-loading').style.display = 'none';
    var content = document.getElementById('syn-content');
    content.style.display = '';
    content.innerHTML = html;

    // Store synagogue data for search function
    window._synData = c;
}

function detailRow(label, value) {
    return '<div class="detail-row"><span class="detail-row-label">' + label +
        '</span><span class="detail-row-value">' + escHtml(value) + '</span></div>';
}

function renderError(msg) {
    document.getElementById('syn-title').textContent = 'Synagogue / Society not found';
    document.getElementById('syn-loading').innerHTML =
        '<div style="font-size:1.25rem;font-weight:bold;color:var(--navy);margin-bottom:8px;">Synagogue / Society not found</div>' +
        '<p>' + escHtml(msg) + '</p>';
}

function toggleDesc() {
    var el = document.getElementById('desc-text');
    var btn = document.getElementById('desc-toggle');
    if (!el || !btn) return;
    var expanded = el.classList.contains('expanded');
    el.classList.toggle('collapsed', expanded);
    el.classList.toggle('expanded', !expanded);
    btn.innerHTML = expanded ? 'Show more &darr;' : 'Show less &uarr;';
}

function searchThisSynagogue() {
    var input = document.getElementById('syn-search-input');
    var surname = (input ? input.value : '').trim();
    if (!surname) { if (input) input.focus(); return; }
    var c = window._synData || {};
    var city = c.city || '';
    var url = '/search-results.php?srch1=' + encodeURIComponent(surname) +
        '&srch1v=S&srch1t=Q&allcountry=01memorial';
    if (city) url += '&srch2=' + encodeURIComponent(city) + '&srch2v=T&srch2t=E';
    window.open(url, '_blank');
}

async function loadSynagogue() {
    var params = new URLSearchParams(window.location.search);
    var id = params.get('id') || params.get('ID');
    if (!id) { renderError('No synagogue/society ID provided.'); return; }
    try {
        var resp = await fetch(SYN_API + '?id=' + encodeURIComponent(id));
        if (!resp.ok) throw new Error('Server returned ' + resp.status);
        var data = await resp.json();
        if (data.error) throw new Error(data.error);
        renderSynagogue(data);
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

loadSynagogue();
</script>
</body>
</html>