<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Testimony Record &mdash; USC Shoah Foundation &mdash; JewishGen</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="/jg-global.css">
<style>

a { text-decoration: none; color: inherit; }

/* ============================================
   PAGE LAYOUT
============================================ */
.shoah-outer {
    background-color: var(--ecru);
    min-height: 60vh;
}
.shoah-inner {
    max-width: 1100px; margin: 0 auto; padding: 0 2rem 2rem;
}

/* ============================================
   TITLE BAND - matches record-list.php hero
============================================ */
.shoah-hero {
    padding: 12px 0 4px;
}
.shoah-hero h1 {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 2.25rem; font-weight: normal;
    color: var(--navy); text-align: center; margin: 0;
}
body.dark-mode .shoah-hero h1 { color: #8ab4d4; }
.shoah-hero h1 a {
    color: inherit; text-decoration: underline solid;
    text-underline-offset: 4px; text-decoration-thickness: 1px;
}
.shoah-hero h1 a:hover { text-decoration-thickness: 2px; }
.shoah-hero .lede {
    text-align: center; font-size: 13px;
    color: var(--charcoal); opacity: 0.6; margin: 6px 0 0;
}

/* ============================================
   INTERVIEW METADATA CARD
============================================ */
.interview-card {
    background: #ffffff; border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    margin: 24px 0; overflow: hidden;
    border-top: 3px solid var(--navy);
}
body.dark-mode .interview-card { background: #1e1e1e; box-shadow: none; }

.interview-header {
    display: flex; align-items: stretch; flex-wrap: wrap;
    border-bottom: 1px solid #e8edf0;
}
body.dark-mode .interview-header { border-color: #2a2a2a; }

.interview-meta-main {
    flex: 1; min-width: 280px; padding: 20px 24px;
    display: flex; flex-wrap: wrap; gap: 16px 32px;
    align-items: flex-start;
}
.meta-group { display: flex; flex-direction: column; gap: 3px; }
.meta-label {
    font-size: 10px; font-weight: bold; text-transform: uppercase;
    letter-spacing: 0.7px; color: var(--navy);
}
body.dark-mode .meta-label { color: #8ab4d4; }
.meta-value {
    font-size: 14px; color: var(--charcoal);
    font-family: Georgia, serif;
}
body.dark-mode .meta-value { color: #e0e0e0; }
.meta-value.subject-name {
    font-size: 1.5rem; font-weight: bold; color: var(--navy);
}
body.dark-mode .meta-value.subject-name { color: #8ab4d4; }

.interview-actions {
    display: flex; flex-direction: column; align-items: flex-end;
    justify-content: center; gap: 10px;
    padding: 20px 24px; border-left: 1px solid #e8edf0;
    flex-shrink: 0;
}
body.dark-mode .interview-actions { border-color: #2a2a2a; }

.btn-testimony {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 13px; font-weight: bold; color: #ffffff;
    background-color: #09497a; border: none; border-radius: 6px;
    padding: 10px 20px; white-space: nowrap;
    transition: background-color 0.15s;
}
.btn-testimony:hover { background-color: var(--sage); color: #ffffff; }
body.dark-mode .btn-testimony { background-color: #0d2a45; }

.btn-back {
    display: inline-block; font-size: 12px; font-weight: bold;
    color: var(--navy); border: 1.5px solid var(--navy);
    border-radius: 20px; padding: 4px 14px; white-space: nowrap;
}
.btn-back:hover { background-color: #09497a; border-color: #09497a; color: #ffffff; }
body.dark-mode .btn-back { color: #a8b361; border-color: #a8b361; }

/* ============================================
   SUBJECT FIELDS: ALIASES + PLACES
============================================ */
.subject-fields {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 0; border-bottom: 1px solid #e8edf0;
}
body.dark-mode .subject-fields { border-color: #2a2a2a; }
@media (max-width: 640px) { .subject-fields { grid-template-columns: 1fr; } }

.subject-field {
    padding: 16px 24px;
    border-right: 1px solid #e8edf0;
}
.subject-field:last-child { border-right: none; }
body.dark-mode .subject-field { border-color: #2a2a2a; }

.field-label {
    font-size: 10px; font-weight: bold; text-transform: uppercase;
    letter-spacing: 0.7px; color: var(--navy); margin-bottom: 8px;
}
body.dark-mode .field-label { color: #8ab4d4; }

.alias-tag {
    display: inline-block; background-color: #eaf2f6;
    border: 1px solid #c8d8e2; border-radius: 3px;
    padding: 2px 8px; margin: 2px 3px 2px 0;
    font-size: 11px; color: var(--charcoal);
}
body.dark-mode .alias-tag { background-color: #1a2533; border-color: #2a4060; color: #c8c0b0; }

.place-item {
    display: block; font-size: 12px; color: var(--charcoal);
    padding: 3px 0; border-bottom: 1px solid #f0f0f0; line-height: 1.4;
}
.place-item:last-child { border-bottom: none; }
body.dark-mode .place-item { color: #c8c0b0; border-color: #2a2a2a; }
.place-item a { color: var(--navy); text-decoration: underline dotted; }
body.dark-mode .place-item a { color: #8ab4d4; }

/* ============================================
   FAMILY TABLE
============================================ */
.family-section-label {
    font-size: 12px; font-weight: bold; text-transform: uppercase;
    letter-spacing: 1.5px; color: var(--sage);
    padding: 16px 24px 8px; margin: 0;
}
body.dark-mode .family-section-label { color: #a8b361; }

.family-table-wrap {
    overflow-x: auto; -webkit-overflow-scrolling: touch;
}
@media (min-width: 1025px) { .family-table-wrap { overflow-x: visible; } }

.family-table {
    width: 100%; border-collapse: collapse; font-size: 13px;
    table-layout: auto; word-wrap: break-word;
}

.family-table thead th {
    background-color: #09497a; color: #ffffff;
    font-size: 10px; font-weight: bold; text-transform: uppercase;
    letter-spacing: 0.5px; padding: 8px 10px; text-align: left;
    border-right: 1px solid rgba(255,255,255,0.25);
    position: sticky; top: 0; z-index: 2;
}
.family-table thead th:last-child { border-right: none; }
body.dark-mode .family-table thead th { background-color: #0d2a45; }

.family-table tbody { background-color: #f4f8fa; }
body.dark-mode .family-table tbody { background-color: #161c20; }

.family-table tbody tr { border-bottom: 1px solid #e8edf0; }
body.dark-mode .family-table tbody tr { border-color: #2a2a2a; }
.family-table tbody tr:hover { background-color: #e6f0f5; }
body.dark-mode .family-table tbody tr:hover { background-color: #1a2533; }
.family-table tbody tr:nth-child(even) { background-color: #eaf2f6; }
body.dark-mode .family-table tbody tr:nth-child(even) { background-color: #131a1f; }

.family-table td {
    padding: 8px 10px; vertical-align: top; color: var(--charcoal);
    border-right: 1px solid #c8d8e2;
}
.family-table td:last-child { border-right: none; }
body.dark-mode .family-table td { color: #c8c0b0; border-color: #2a2a2a; }

/* Self row highlight */
.family-table tr.self-row { background-color: rgba(9,73,122,0.06) !important; }
body.dark-mode .family-table tr.self-row { background-color: rgba(138,180,212,0.08) !important; }

/* Holocaust-related death */
.holocaust-cause {
    display: inline-block; font-size: 11px; font-weight: bold;
    color: #8b0000; background: rgba(139,0,0,0.06);
    padding: 1px 6px; border-radius: 3px; margin-top: 2px;
}
body.dark-mode .holocaust-cause { color: #e87070; background: rgba(232,112,112,0.1); }

/* Surname emphasis */
.surname { font-weight: bold; }

/* ============================================
   FOOTER ACTIONS
============================================ */
.record-footer {
    padding: 16px 24px; border-top: 1px solid #e8edf0;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 12px;
}
body.dark-mode .record-footer { border-color: #2a2a2a; }
.record-footer-note { font-size: 11px; color: var(--charcoal); opacity: 0.4; }
.footer-btns { display: flex; gap: 10px; flex-wrap: wrap; }
.footer-btn {
    display: inline-block; font-size: 12px; font-weight: bold;
    color: var(--navy); border: 1.5px solid var(--navy);
    border-radius: 6px; padding: 6px 16px; cursor: pointer;
    transition: background-color 0.15s, color 0.15s;
}
.footer-btn:hover { background-color: #09497a; color: #ffffff; }
body.dark-mode .footer-btn { color: #e0e0e0; border-color: #e0e0e0; }

/* ============================================
   STATES
============================================ */
.shoah-state { text-align: center; padding: 80px 24px; color: var(--charcoal); }
.shoah-spinner {
    display: inline-block; width: 28px; height: 28px;
    border: 3px solid #e8edf0; border-top-color: var(--navy);
    border-radius: 50%; animation: shoah-spin 0.7s linear infinite;
    margin-bottom: 12px;
}
@keyframes shoah-spin { to { transform: rotate(360deg); } }

/* ============================================
   RESPONSIVE
============================================ */
@media (max-width: 768px) {
    .interview-header { flex-direction: column; }
    .interview-actions { flex-direction: row; border-left: none; border-top: 1px solid #e8edf0; }
    .subject-fields { grid-template-columns: 1fr; }
    .subject-field { border-right: none; border-bottom: 1px solid #e8edf0; }
    .record-footer { flex-direction: column; align-items: flex-start; }
}

@media print {
    .interview-actions, .footer-btns { display: none !important; }
}

</style>
</head>
<body>
<div id="site-header"></div>

<div class="shoah-outer">
<div class="shoah-inner">
    <div class="shoah-hero">
        <h1 id="shoah-heading">USC Shoah Foundation &mdash; Survivor Interviews</h1>
        <p class="lede">Testimony records accessed through JewishGen</p>
    </div>
    <div id="shoah-loading" class="shoah-state">
        <div class="shoah-spinner"></div>
        <div>Loading testimony record&hellip;</div>
    </div>
    <div id="shoah-content" style="display:none;"></div>
</div>
</div>

<div id="site-footer"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>

var API_URL = '/databases/recordlistjson.php';

function esc(s) {
    var d = document.createElement('div');
    d.textContent = s || '';
    return d.innerHTML;
}

function clean(s) {
    if (!s) return '';
    return String(s)
        .replace(/\0/g, '')
        .replace(/\uFFFD/g, '')
        .replace(/[\u25C6\u25C7\u2666\u25C8\u2B25\u2B26\u25CF\u25CB\u2022]/g, ' ')
        .replace(/&nbsp;/gi, ' ')
        .replace(/&#xa0;/gi, ' ')
        .replace(/&#160;/gi, ' ')
        .replace(/&#x([0-9a-f]+);/gi, function(m, hex) {
            var code = parseInt(hex, 16);
            return code === 160 ? ' ' : String.fromCharCode(code);
        })
        .replace(/\s{2,}/g, ' ')
        .trim();
}

function splitHR(s) {
    if (!s) return [];
    return s.split(/<hr\s*\/?>/i).map(function(p) { return clean(p); });
}

function stripTags(s) {
    return (s || '').replace(/<[^>]+>/g, '');
}

/* ---- Parse the interview metadata from the rowspan'd first cell ---- */
function parseInterviewMeta(cellHtml) {
    var parts = splitHR(cellHtml);
    var meta = {
        testimonyUrl: '',
        testimonyLabel: 'View Testimony',
        experienceGroup: parts[1] || '',
        interviewDate: parts[2] || '',
        language: parts[3] || '',
        country: parts[4] || '',
        state: parts[5] || '',
        city: parts[6] || ''
    };

    // Extract testimony URL from first part
    var linkMatch = (parts[0] || '').match(/href="([^"]+)"/i);
    if (!linkMatch) linkMatch = (cellHtml || '').match(/href="([^"]+)"/i);
    if (linkMatch) meta.testimonyUrl = linkMatch[1];

    // Build location string
    var locParts = [meta.city, meta.state, meta.country].filter(Boolean);
    meta.location = locParts.join(', ');

    return meta;
}

/* ---- Parse aliases and places from the rowspan'd last cell ---- */
function parseAliasesPlaces(cellHtml) {
    var hrParts = (cellHtml || '').split(/<hr\s*\/?>/i);
    var aliasesRaw = hrParts[0] || '';
    var placesRaw = hrParts[1] || '';

    // Aliases: split by <br>
    var aliases = aliasesRaw.split(/<br\s*\/?>/i)
        .map(function(a) { return clean(stripTags(a)); })
        .filter(Boolean);

    // Places: split by <br>, preserve links
    var places = placesRaw.split(/<br\s*\/?>/i)
        .map(function(p) { return p.trim(); })
        .filter(function(p) { return clean(stripTags(p)) !== ''; });

    return { aliases: aliases, places: places };
}

/* ---- Parse a family member row ---- */
function parseFamilyMember(cells) {
    var name = clean(stripTags(cells[0] ? cells[0].html : ''));
    var gender = clean(cells[1] ? cells[1].html : '');
    var relationship = clean(cells[2] ? cells[2].html : '');
    var survivor = clean(cells[3] ? cells[3].html : '');

    var birthParts = splitHR(cells[4] ? cells[4].html : '');
    var deathParts = splitHR(cells[5] ? cells[5].html : '');

    // Check for Holocaust-related
    var deathText = cells[5] ? stripTags(cells[5].html) : '';
    var isHolocaust = /holocaust[- ]related/i.test(deathText);

    // Parse cause from death date line
    var deathDateLine = deathParts[0] || '';
    var cause = '';
    if (isHolocaust) {
        cause = 'Holocaust-related';
        deathDateLine = deathDateLine.replace(/\s*holocaust[- ]related\s*/i, '').trim();
    } else {
        var causeMatch = deathDateLine.match(/\s+(natural death|[a-z]+ death|suicide|murdered|killed)/i);
        if (causeMatch) {
            cause = causeMatch[1];
            deathDateLine = deathDateLine.replace(causeMatch[0], '').trim();
        }
    }

    // Parse surname from name (pattern: "Surname Given" or bolded)
    var nameParts = name.split(/\s+/);
    var surname = nameParts[0] || '';
    var givenName = nameParts.slice(1).join(' ');

    return {
        name: name,
        surname: surname,
        givenName: givenName,
        gender: gender,
        relationship: relationship,
        survivor: survivor,
        dob: birthParts[0] || '',
        pob: birthParts[1] || '',
        dod: deathDateLine,
        cause: cause,
        pod: deathParts[1] || '',
        isHolocaust: isHolocaust,
        isSelf: relationship.toLowerCase() === 'self'
    };
}

/* ---- RENDER ---- */

function renderRecord(data) {
    var groups = data.groups || [];
    if (!groups.length || !groups[0].rows || !groups[0].rows.length) {
        renderError('No data found for this testimony.');
        return;
    }

    var firstRow = groups[0].rows[0];
    var cells = firstRow.cells || [];

    // The first cell (rowspan'd) has interview metadata
    var metaCell = cells[0] || {};
    var meta = parseInterviewMeta(metaCell.html || '');

    // The last cell (rowspan'd) has aliases + places
    var lastCellIdx = cells.length - 1;
    var apCell = cells[lastCellIdx] || {};
    var ap = parseAliasesPlaces(apCell.html || '');

    // The "Self" row data (columns 2-7 of first row, indices 1-6)
    var selfCells = cells.slice(1, lastCellIdx);
    var selfMember = parseFamilyMember(selfCells);

    // Subject name from first row
    var subjectName = selfMember.name || 'Unknown Subject';

    document.title = subjectName + ' - USC Shoah Foundation - JewishGen';

    // Make heading a link to the dataset description page
    var heading = document.getElementById('shoah-heading');
    if (heading && data.dataset && data.dataset.info_url) {
        heading.innerHTML = '<a href="' + esc(data.dataset.info_url) + '">' +
            esc(data.dataset.title || 'USC Shoah Foundation - Survivor Interviews') + '</a>';
    }

    var html = '';

    // ---- Interview card ----
    html += '<div class="interview-card">';

    // Header: metadata + actions
    html += '<div class="interview-header">';
    html += '<div class="interview-meta-main">';

    // Subject name (large)
    html += '<div class="meta-group" style="flex-basis:100%">';
    html += '<span class="meta-label">Subject</span>';
    html += '<span class="meta-value subject-name">' + esc(subjectName) + '</span>';
    html += '</div>';

    if (meta.experienceGroup) {
        html += '<div class="meta-group"><span class="meta-label">Experience Group</span>';
        html += '<span class="meta-value">' + esc(meta.experienceGroup) + '</span></div>';
    }
    if (meta.interviewDate) {
        html += '<div class="meta-group"><span class="meta-label">Interview Date</span>';
        html += '<span class="meta-value">' + esc(meta.interviewDate) + '</span></div>';
    }
    if (meta.language) {
        html += '<div class="meta-group"><span class="meta-label">Language</span>';
        html += '<span class="meta-value">' + esc(meta.language) + '</span></div>';
    }
    if (meta.location) {
        html += '<div class="meta-group"><span class="meta-label">Interview Location</span>';
        html += '<span class="meta-value">' + esc(meta.location) + '</span></div>';
    }
    if (selfMember.dob || selfMember.pob) {
        html += '<div class="meta-group"><span class="meta-label">Born</span>';
        html += '<span class="meta-value">';
        if (selfMember.dob) html += esc(selfMember.dob);
        if (selfMember.dob && selfMember.pob) html += ' &mdash; ';
        if (selfMember.pob) html += esc(selfMember.pob);
        html += '</span></div>';
    }

    html += '</div>'; // /interview-meta-main

    // Actions
    html += '<div class="interview-actions">';
    if (meta.testimonyUrl) {
        html += '<a class="btn-testimony" href="' + esc(meta.testimonyUrl) +
            '" target="_blank" rel="noopener">View Testimony &#x2197;</a>';
    }
    html += '<a class="btn-back" href="#" onclick="goBack();return false;">&larr; Back to Results</a>';
    html += '</div>';

    html += '</div>'; // /interview-header

    // ---- Aliases + Places ----
    if (ap.aliases.length || ap.places.length) {
        html += '<div class="subject-fields">';

        // Aliases
        html += '<div class="subject-field">';
        html += '<div class="field-label">Aliases &amp; Other Names</div>';
        if (ap.aliases.length) {
            ap.aliases.forEach(function(a) {
                html += '<span class="alias-tag">' + esc(a) + '</span>';
            });
        } else {
            html += '<span style="font-size:12px;opacity:0.5;">None recorded</span>';
        }
        html += '</div>';

        // Places
        html += '<div class="subject-field">';
        html += '<div class="field-label">Places</div>';
        if (ap.places.length) {
            ap.places.forEach(function(p) {
                // Preserve Google Maps links
                var placeName = clean(stripTags(p));
                if (p.indexOf('<a') > -1) {
                    html += '<span class="place-item">' + p + '</span>';
                } else {
                    html += '<span class="place-item">' + esc(placeName) + '</span>';
                }
            });
        } else {
            html += '<span style="font-size:12px;opacity:0.5;">None recorded</span>';
        }
        html += '</div>';

        html += '</div>'; // /subject-fields
    }

    // ---- Family Table ----
    html += '<p class="family-section-label">Family &amp; Related Persons</p>';

    // Parse all family members (skip first row's rowspan cells, use remaining rows)
    var members = [selfMember]; // Self is first
    for (var r = 1; r < groups[0].rows.length; r++) {
        var rowCells = groups[0].rows[r].cells || [];
        members.push(parseFamilyMember(rowCells));
    }

    html += '<div class="family-table-wrap">';
    html += '<table class="family-table" aria-label="Family and related persons">';
    html += '<thead><tr>';
    html += '<th>Name</th><th>Gender</th><th>Relationship</th><th>Survivor</th>';
    html += '<th>Date of Birth<br>Place of Birth</th>';
    html += '<th>Date of Death<br>Place of Death</th>';
    html += '</tr></thead><tbody>';

    members.forEach(function(m) {
        var cls = m.isSelf ? ' class="self-row"' : '';
        html += '<tr' + cls + '>';

        // Name with surname emphasis
        html += '<td><span class="surname">' + esc(m.surname) + '</span> ' + esc(m.givenName) + '</td>';
        html += '<td>' + esc(m.gender) + '</td>';
        html += '<td>' + esc(m.relationship) + '</td>';
        html += '<td>' + esc(m.survivor) + '</td>';

        // Birth
        html += '<td>';
        if (m.dob) html += esc(m.dob);
        if (m.pob) html += '<br>' + esc(m.pob);
        html += '</td>';

        // Death
        html += '<td>';
        if (m.dod) html += esc(m.dod);
        if (m.isHolocaust) html += '<br><span class="holocaust-cause">Holocaust-related</span>';
        else if (m.cause) html += '<br><span style="font-size:11px;opacity:0.7;">' + esc(m.cause) + '</span>';
        if (m.pod) html += '<br>' + esc(m.pod);
        html += '</td>';

        html += '</tr>';
    });

    html += '</tbody></table></div>';

    // ---- Footer ----
    html += '<div class="record-footer">';
    html += '<span class="record-footer-note">' + esc(data.dataset ? data.dataset.title : '') +
        ' &mdash; Record ID: ' + esc(data.groups[0].glue_id || '') + '</span>';
    html += '<div class="footer-btns">';
    html += '<button class="footer-btn" onclick="window.print()">Print Record</button>';
    html += '<a class="footer-btn" href="/">New Search</a>';
    html += '</div>';
    html += '</div>';

    html += '</div>'; // /interview-card

    document.getElementById('shoah-loading').style.display = 'none';
    var content = document.getElementById('shoah-content');
    content.style.display = '';
    content.innerHTML = html;
}

function goBack() {
    if (window.history.length <= 1) {
        window.location.href = '/';
    } else {
        window.history.back();
    }
}

function renderError(msg) {
    document.getElementById('shoah-loading').innerHTML =
        '<div style="font-size:1.25rem;font-weight:bold;color:var(--navy);margin-bottom:8px;">Record not found</div>' +
        '<p>' + esc(msg) + '</p>' +
        '<p><a href="/" style="color:var(--navy);text-decoration:underline;">&larr; New Search</a></p>';
}

async function loadRecord() {
    var params = new URLSearchParams(window.location.search);
    var rec = params.get('rec');
    if (!rec) { renderError('No record ID provided.'); return; }
    try {
        var resp = await fetch(API_URL + '?rec=' + encodeURIComponent(rec));
        if (!resp.ok) throw new Error('Server returned ' + resp.status);
        var data = await resp.json();
        if (data.error) throw new Error(data.error.message || data.error);
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