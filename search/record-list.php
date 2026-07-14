<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Record List &mdash; JewishGen</title>
<!-- 1. Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<!-- 2. JewishGen Global Design System -->
<link href="/jg-global.css" rel="stylesheet"/>
<!-- 3. Page-specific styles -->
<style>

        a { text-decoration: none; color: inherit; }

        /* ============================================
           SEARCH HERO OVERRIDES
        ============================================ */
        .sr-hero-inner {
            /* Data tables need the full screen &mdash; wider than the 1100px
               used for prose pages. Caps at 1600px for ultrawide monitors. */
            max-width: min(95vw, 1600px);
            margin: 0 auto;
        }

        /* Dataset subtitle line */
        .sr-dataset-meta {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 6px;
        }
        .sr-dataset-meta-item {
            font-size: 0.8125rem;
	   { color: rgba(255,255,255,0.75); }
            opacity: 0.75;
        }
        .sr-dataset-meta-divider {
            width: 3px; height: 3px; border-radius: 50%;
            background-color: { background-color: rgba(255,255,255,0.4); }; 
	    opacity: 0.35;
        }

        /* ============================================
           CARD INNER
        ============================================ */
        .sr-card-inner { padding: 0; }

        /* ============================================
           CARD HEADER &mdash; query summary + controls
        ============================================ */
        .sr-card-header {
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 12px; padding: 16px 24px;
            border-bottom: 1px solid #e8edf0;
        }
        body.dark-mode .sr-card-header { border-color: #2a2a2a; }

        .sr-query-block {
            display: flex; align-items: center; flex-wrap: wrap; gap: 6px 14px;
        }
        .sr-query-pair { display: flex; align-items: baseline; gap: 5px; }
        .sr-query-label {
            font-size: 11px; font-weight: bold; text-transform: uppercase;
            letter-spacing: 0.6px; color: var(--navy); white-space: nowrap;
        }
        body.dark-mode .sr-query-label { color: #8ab4d4; }
        .sr-query-term {
            font-size: 13px; font-weight: bold; color: var(--charcoal);
            font-family: Georgia, serif;
        }
        body.dark-mode .sr-query-term { color: #e0e0e0; }
        .sr-query-divider {
            width: 1px; height: 14px; background-color: var(--navy);
            opacity: 0.2; align-self: center;
        }

        .sr-controls {
            display: flex; align-items: center; gap: 10px; flex-shrink: 0;
        }
        .sr-count-badge {
            font-size: 12px; font-weight: bold; color: var(--navy);
            background-color: #ffffff; border: 1.5px solid var(--navy);
            border-radius: 20px; padding: 3px 12px; white-space: nowrap;
        }
        body.dark-mode .sr-count-badge {
            background-color: #1a1a1a; color: #8ab4d4; border-color: #8ab4d4;
        }
        .sr-back-btn {
            display: inline-block; font-size: 12px; font-weight: bold;
            color: var(--navy); border: 1.5px solid var(--navy);
            border-radius: 20px; padding: 3px 12px; white-space: nowrap;
            transition: background-color 0.15s, color 0.15s;
        }
        .sr-back-btn:hover {
            background-color: #09497a; border-color: #09497a; color: #ffffff;
        }
        body.dark-mode .sr-back-btn { color: #a8b361; border-color: #a8b361; }
        body.dark-mode .sr-back-btn:hover { background-color: #a8b361; color: #121212; }

        /* ============================================
           TABLE WRAPPER
        ============================================ */
        .sr-table-wrap {
            /* Desktop: no horizontal scroll &mdash; text wraps, wide container
               gives columns room. position: sticky works on thead.
               Mobile: overflow-x: auto added via media query below. */
        }

        /* On tablets/phones, horizontal scroll is unavoidable for
           wide tables. Re-enable it, but keep readable font sizes. */
        @media (max-width: 1024px) {
            .sr-table-wrap {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }

        /* ============================================
           RESULTS TABLE
        ============================================ */
        .sr-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            table-layout: auto;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* Wide tables (9+ columns) &mdash; tighten padding slightly but KEEP
           readable font size. The wider container handles the rest.
           Never shrink below 12px &mdash; many users are elderly. */
        .sr-table.sr-wide-table td { padding: 7px 8px; }
        .sr-table.sr-wide-table thead th { padding: 7px 8px; }

        /* Sticky header */
        .sr-table thead th {
            background-color: #09497a;
            color: #ffffff;
            font-size: 11px; font-weight: bold;
            text-transform: uppercase; letter-spacing: 0.5px;
            padding: 8px 10px; text-align: left;
            border-right: 1px solid rgba(255,255,255,0.25);
            user-select: none;
            position: sticky; top: 0; z-index: 2;
        }
        .sr-table thead th:last-child { border-right: none; }
        body.dark-mode .sr-table thead th { background-color: #0d2a45; }

        /* Stacked header &mdash; multiple lines in one cell */
        .th-stack {
            display: flex; flex-direction: column; gap: 0;
        }
        .th-stack .th-line {
            display: block; line-height: 1.3;
        }
        .th-stack .th-line + .th-line {
            border-top: 1px solid rgba(255,255,255,0.2);
            padding-top: 3px; margin-top: 2px;
        }

        /* Table body background tint */
        .sr-table tbody { background-color: #f4f8fa; }
        body.dark-mode .sr-table tbody { background-color: #161c20; }

        /* Body rows */
        .sr-table tbody tr {
            border-bottom: 1px solid #e8edf0;
            transition: background-color 0.1s;
        }
        body.dark-mode .sr-table tbody tr { border-color: #2a2a2a; }
        .sr-table tbody tr:last-child { border-bottom: none; }
        .sr-table tbody tr:hover { background-color: #f0f5f8; }
        body.dark-mode .sr-table tbody tr:hover { background-color: #1a2533; }

        /* Alternating row tint */
        .sr-table tbody tr:nth-child(even) { background-color: #eaf2f6; }
        body.dark-mode .sr-table tbody tr:nth-child(even) { background-color: #161a1e; }
        .sr-table tbody tr:nth-child(even):hover { background-color: #f0f5f8; }
        body.dark-mode .sr-table tbody tr:nth-child(even):hover { background-color: #1a2533; }

        /* Group divider row */
        .sr-table tbody tr.sr-group-divider-row td {
            background-color: var(--sage);
            padding: 2px 0;
            border: none;
            opacity: 0.35;
        }
        body.dark-mode .sr-table tbody tr.sr-group-divider-row td {
            background-color: #a8b361;
            opacity: 0.2;
        }

        /* Cells */
        .sr-table td {
            padding: 8px 10px;
            vertical-align: top;
            color: var(--charcoal);
            border-right: 1px solid #c8d8e2;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        body.dark-mode .sr-table td { color: #c8c0b0; border-color: #2a2a2a; }
        .sr-table td:last-child { border-right: none; }

        /* Highlighted cell &mdash; rowspan'd family-group lead cells.
           Legacy uses bgcolor=yellow. We use a clear sage/cream tint
           that's unmistakable but still in the JG40 palette. */
        .sr-table td.sr-cell-highlight {
            background-color: #e2e6c4;
            border-right-color: #c8cc9e;
        }
        body.dark-mode .sr-table td.sr-cell-highlight {
            background-color: rgba(168, 179, 97, 0.2);
            border-right-color: rgba(168, 179, 97, 0.3);
        }

        /* Stacked cell &mdash; multiple values in one cell */
        .td-stack {
            display: flex; flex-direction: column; gap: 0;
        }
        .td-stack .td-line { line-height: 1.35; }
        .td-stack .td-line + .td-line {
            border-top: 1px solid #e0e8ed;
            padding-top: 3px; margin-top: 2px;
        }
        body.dark-mode .td-stack .td-line + .td-line { border-color: #2a2a2a; }

        /* Links in cells */
        .sr-table td a {
            color: var(--navy);
            text-decoration: underline;
            text-decoration-style: dotted;
            text-underline-offset: 2px;
        }
        .sr-table td a:hover { text-decoration-style: solid; }
        body.dark-mode .sr-table td a { color: #8ab4d4; }

        /* ============================================
           CARD FOOTER &mdash; pagination + actions
        ============================================ */
        .sr-card-footer {
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 12px; padding: 14px 24px;
            border-top: 1px solid #e8edf0;
            background-color: #fafcfd;
        }
        body.dark-mode .sr-card-footer {
            border-color: #2a2a2a; background-color: #161a1e;
        }
        .sr-footer-note {
            font-size: 12px; color: var(--charcoal); opacity: 0.65;
        }
        .sr-footer-actions {
            display: flex; gap: 10px; flex-wrap: wrap; align-items: center;
        }
        .sr-footer-btn {
            display: inline-block; font-size: 12px; font-weight: bold;
            color: var(--navy); background-color: #ffffff;
            border: 1.5px solid var(--navy); border-radius: 6px;
            padding: 6px 16px; cursor: pointer;
            transition: background-color 0.15s, color 0.15s;
        }
        .sr-footer-btn:hover {
            background-color: #09497a; border-color: #09497a; color: #ffffff;
        }
        .sr-footer-btn:disabled, .sr-footer-btn[disabled] {
            opacity: 0.35; pointer-events: none;
        }
        body.dark-mode .sr-footer-btn {
            background-color: #1a1a1a; border-color: #e0e0e0; color: #e0e0e0;
        }
        body.dark-mode .sr-footer-btn:hover {
            background-color: #e0e0e0; color: #121212;
        }
        .sr-page-info {
            font-size: 12px; color: var(--charcoal); font-weight: bold;
        }
        body.dark-mode .sr-page-info { color: #c8c0b0; }

        /* ============================================
           LOADING / ERROR / EMPTY STATES
        ============================================ */
        .sr-state-msg {
            text-align: center; padding: 60px 24px;
            color: var(--charcoal); font-size: 15px;
        }
        .sr-state-msg .sr-state-title {
            font-size: 1.25rem; font-weight: bold;
            margin-bottom: 8px; color: var(--navy);
        }
        body.dark-mode .sr-state-msg .sr-state-title { color: #8ab4d4; }
        body.dark-mode .sr-state-msg { color: #c8c0b0; }

        .sr-spinner {
            display: inline-block; width: 28px; height: 28px;
            border: 3px solid #e8edf0; border-top-color: var(--navy);
            border-radius: 50%; animation: sr-spin 0.7s linear infinite;
            margin-bottom: 12px;
        }
        @keyframes sr-spin { to { transform: rotate(360deg); } }

        /* ============================================
           RESPONSIVE
        ============================================ */
        @media (max-width: 700px) {
            .sr-card-header { padding: 12px 16px; }
            .sr-table thead th, .sr-table td { padding: 8px 10px; font-size: 12px; }
            .sr-card-footer { padding: 12px 16px; }
        }

    </style>
</head>
<body>
<div id="site-header"></div>

<main id="main-content">

<!-- Heading: populated by JS -->
<div class="page-title-band">
    <span class="tagline">Search Results</span>
    <header id="sr-heading">
        <h1 id="sr-title">Loading&hellip;</h1>
    </header>
</div>

<section aria-label="Dataset search results" class="sr-hero-inner">

<!-- Results card -->
<div class="jg-search-card">
<div class="sr-card-inner">

    <!-- Card header: query + controls &mdash; populated by JS -->
    <div class="sr-card-header" id="sr-card-header" style="display:none;"></div>

    <!-- Table or state message &mdash; populated by JS -->
    <div id="sr-body">
        <div class="sr-state-msg">
            <div class="sr-spinner"></div>
            <div>Searching records&hellip;</div>
        </div>
    </div>

    <!-- Card footer: pagination &mdash; populated by JS -->
    <div class="sr-card-footer" id="sr-card-footer" style="display:none;"></div>

</div><!-- /sr-card-inner -->
</div><!-- /jg-search-card -->
</section>
</main>

<div id="site-footer"></div>

<!-- ============================================
     SCRIPTS
============================================ -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ============================================================
   RECORD LIST &mdash; Tier 2 dynamic page
   ============================================================
   Reads URL params, fetches /databases/recordlistjson.php,
   renders a structured results table using the JG40 design system.
============================================================ */

const RECORDLIST_URL = '/databases/recordlistjson.php';

/* ---- Utility ---- */

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function getParams() {
    return new URLSearchParams(window.location.search);
}

/* Build the fetch URL from current page params */
function buildFetchUrl(overrides) {
    const params = getParams();
    if (overrides) {
        Object.entries(overrides).forEach(([k, v]) => params.set(k, v));
    }
    return RECORDLIST_URL + '?' + params.toString();
}

/* ---- Fetch ---- */

async function fetchRecords(overrides) {
    const url = buildFetchUrl(overrides);
    const resp = await fetch(url);
    if (!resp.ok) {
        throw new Error('Server returned ' + resp.status);
    }
    return resp.json();
}

/* ---- Cell rendering ---- */

/**
 * Clean up encoding artifacts in text/HTML from Solr and MySQL.
 * Handles: diamond placeholders, null bytes, replacement chars,
 * non-breaking spaces, and other common data quality issues.
 * Call on any string before rendering to the DOM.
 */
function cleanData(str) {
    if (!str && str !== 0) return '';
    str = String(str);
    // Null bytes (Solr appends these)
    str = str.replace(/\0/g, '');
    // Unicode replacement character
    str = str.replace(/\uFFFD/g, '');
    // Diamond/bullet placeholders used as field separators or blanks
    str = str.replace(/[\u25C6\u25C7\u2666\u25C8\u2B25\u2B26\u25CF\u25CB\u2022]/g, ' ');
    // Collapse multiple spaces
    str = str.replace(/\s{2,}/g, ' ');
    // Trim &nbsp; at start/end
    str = str.replace(/^(&nbsp;\s*)+/gi, '').replace(/(&nbsp;\s*)+$/gi, '');
    return str.trim();
}

/**
 * Render a cell's HTML content.
 * Splits <hr>-separated values into stacked display.
 */
function renderCellHtml(raw) {
    var str = cleanData(raw);
    if (str === '') return '';

    var parts = str.split(/<hr\s*\/?>/i);
    if (parts.length <= 1) return str;

    return '<div class="td-stack">' +
        parts.map(function(p) {
            var cleaned = cleanData(p);
            return '<span class="td-line">' + (cleaned || '') + '</span>';
        }).join('') +
        '</div>';
}

/* ---- Column header rendering ---- */

function renderColumnHeaders(columns) {
    if (!columns || columns.length === 0) return '';
    var html = '<tr>';
    columns.forEach(function(col) {
        var lines = col.lines || [''];
        if (lines.length <= 1) {
            html += '<th scope="col">' + escapeHtml(cleanData(lines[0] || '')) + '</th>';
        } else {
            html += '<th scope="col"><div class="th-stack">';
            lines.forEach(function(line) {
                html += '<span class="th-line">' + escapeHtml(cleanData(line)) + '</span>';
            });
            html += '</div></th>';
        }
    });
    html += '</tr>';
    return html;
}

/* ---- Data row rendering ---- */

function renderGroups(groups, columnCount) {
    if (!groups || groups.length === 0) return '';
    let html = '';

    groups.forEach((group, gi) => {
        // Add divider between groups (not before first)
        if (gi > 0) {
            html += '<tr aria-hidden="true" class="sr-group-divider-row">' +
                    '<td colspan="' + columnCount + '"></td></tr>';
        }

        group.rows.forEach(row => {
            html += '<tr>';
            row.cells.forEach(cell => {
                let attrs = '';
                let cls = '';
                if (cell.rowspan && cell.rowspan > 1) {
                    attrs += ' rowspan="' + cell.rowspan + '"';
                }
                if (cell.colspan && cell.colspan > 1) {
                    attrs += ' colspan="' + cell.colspan + '"';
                }
                if (cell.highlighted) {
                    cls = ' sr-cell-highlight';
                }
                html += '<td' + attrs + (cls ? ' class="' + cls + '"' : '') + '>' +
                        renderCellHtml(cell.html) + '</td>';
            });
            html += '</tr>';
        });
    });

    return html;
}

/* ---- Pagination rendering ---- */

function renderPagination(pg) {
    const footer = document.getElementById('sr-card-footer');
    if (!pg || pg.total_count === 0) {
        footer.style.display = 'none';
        return;
    }

    footer.style.display = '';

    const note = pg.total_count <= pg.page_size
        ? pg.total_count.toLocaleString() + ' record' + (pg.total_count !== 1 ? 's' : '')
        : 'Records ' + pg.start_record.toLocaleString() + '&ndash;' +
          pg.end_record.toLocaleString() + ' of ' + pg.total_count.toLocaleString();

    let btns = '';

    // Prev
    if (pg.has_prev) {
        const prevStart = (pg.current_page - 2) * pg.page_size;
        btns += '<button class="sr-footer-btn" onclick="goToPage(' + prevStart + ')">&larr; Previous</button>';
    }

    // Page indicator
    if (pg.total_pages > 1) {
        btns += '<span class="sr-page-info">Page ' + pg.current_page + ' of ' + pg.total_pages + '</span>';
    }

    // Next
    if (pg.has_next) {
        const nextStart = pg.current_page * pg.page_size;
        btns += '<button class="sr-footer-btn" onclick="goToPage(' + nextStart + ')">Next &rarr;</button>';
    }

    // Back to all results + new search
    btns += '<a class="sr-footer-btn" href="/search-results.php?' +
            getSearchParamsForBackLink() + '">&larr; All Results</a>';
    btns += '<a class="sr-footer-btn" href="/">New Search</a>';

    footer.innerHTML =
        '<span class="sr-footer-note">' + note + '</span>' +
        '<div class="sr-footer-actions">' + btns + '</div>';
}

function getSearchParamsForBackLink() {
    // Carry the search params back to the Tier 1 page (minus df/recstart)
    const p = getParams();
    p.delete('df');
    p.delete('recstart');
    p.delete('recjump');
    return p.toString();
}

function goToPage(recstart) {
    const p = getParams();
    p.set('recstart', recstart);
    // Update URL without reload, then re-fetch
    const newUrl = window.location.pathname + '?' + p.toString();
    window.history.pushState({}, '', newUrl);
    loadPage();
}

/* ---- Card header rendering ---- */

function renderCardHeader(data) {
    const header = document.getElementById('sr-card-header');
    header.style.display = '';

    // Parse the query summary into individual pairs
    // Format: "Surname (phonetically like) : HOLLANDER AND Town (exact) : KRAKOW"
    const summary = data.query.summary || '';
    const pairs = summary.split(/\s+(?:AND|OR)\s+/i);

    let queryHtml = '';
    pairs.forEach((pair, i) => {
        if (i > 0) {
            queryHtml += '<div aria-hidden="true" class="sr-query-divider"></div>';
        }
        const colonIdx = pair.indexOf(':');
        if (colonIdx > -1) {
            const label = pair.substring(0, colonIdx).trim();
            const term = pair.substring(colonIdx + 1).trim();
            queryHtml += '<div class="sr-query-pair">' +
                '<span class="sr-query-label">' + escapeHtml(label) + '</span>' +
                '<span class="sr-query-term">' + escapeHtml(term) + '</span></div>';
        } else {
            queryHtml += '<div class="sr-query-pair"><span class="sr-query-term">' +
                escapeHtml(pair) + '</span></div>';
        }
    });

    // Region
    const region = data.query.region || '';
    if (region && region !== '0*' && region !== '00ALL') {
        queryHtml += '<div aria-hidden="true" class="sr-query-divider"></div>';
        queryHtml += '<div class="sr-query-pair">' +
            '<span class="sr-query-label">Region</span>' +
            '<span class="sr-query-term">' + escapeHtml(friendlyRegion(region)) + '</span></div>';
    }

    const count = data.pagination.total_count;
    const countText = count.toLocaleString() + ' record' + (count !== 1 ? 's' : '') + ' found';

    header.innerHTML =
        '<div class="sr-query-block" aria-label="Search query" role="region">' + queryHtml + '</div>' +
        '<div class="sr-controls">' +
            '<span class="sr-count-badge">' + countText + '</span>' +
        '</div>';
}

function friendlyRegion(code) {
    // Strip leading digits and zeros from region codes like "01holocaust", "000ukraine"
    var clean = code.replace(/^[0-9]+/, '').replace(/_/g, ' ').trim();
    if (!clean || clean.toLowerCase() === 'all') return 'All Countries';
    return clean.charAt(0).toUpperCase() + clean.slice(1);
}

/* ---- Main page rendering ---- */

function renderPage(data) {
    // Title
    const titleEl = document.getElementById('sr-title');
    const title = data.dataset.title || 'Search Results';
    if (data.dataset.info_url) {
        titleEl.innerHTML = '<a href="' + escapeHtml(data.dataset.info_url) +
            '" style="color:inherit; text-decoration:underline solid; text-underline-offset:4px;">' +
            escapeHtml(title) + '</a>';
    } else {
        titleEl.textContent = title;
    }
    document.title = title + ' &mdash; JewishGen';

    // Card header
    renderCardHeader(data);

    // Body
    const body = document.getElementById('sr-body');
    const pg = data.pagination;
    const columns = data.columns || [];
    const groups = data.groups || [];

    if (pg.total_count === 0) {
        body.innerHTML =
            '<div class="sr-state-msg">' +
                '<div class="sr-state-title">No records found</div>' +
                '<p>Try broadening your search or changing the match type.</p>' +
            '</div>';
        renderPagination(pg);
        return;
    }

    // Build table
    // Wide tables: 9+ columns get slightly tighter padding (but NOT smaller font)
    const wideClass = columns.length >= 9 ? ' sr-wide-table' : '';
    let tableHtml =
        '<div class="sr-table-wrap" aria-label="Search results table" role="region" tabindex="0">' +
        '<table class="sr-table' + wideClass + '" aria-label="' + escapeHtml(title) + ' results">' +
        '<thead>' + renderColumnHeaders(columns) + '</thead>' +
        '<tbody>' + renderGroups(groups, columns.length) + '</tbody>' +
        '</table></div>';

    body.innerHTML = tableHtml;

    // Rewrite legacy glue_s2.php links to our Tier 3 page.
    // Done in JS (not PHP) to avoid file-encoding corruption of the '?' character.
    rewriteLegacyLinks(body);

    // Pagination
    renderPagination(pg);
}

/**
 * Find all legacy glue_s2.php links in the rendered table and rewrite
 * them to our record-list.php Tier 3 page.
 */
function rewriteLegacyLinks(container) {
    // Rewrite glue_s2.php links -> Tier 3 expanded view
    container.querySelectorAll('a[href*="glue_s2.php"]').forEach(function(link) {
        var href = link.getAttribute('href');
        var match = href.match(/rec=([^&\s]+)/);
        if (match) {
            link.setAttribute('href', '/search/record-list.php?rec=' + match[1]);
            link.setAttribute('target', '_blank');
        }
    });
    // Rewrite jowbr.php / jowbr_2.php links -> JOWBR burial record page
    container.querySelectorAll('a[href*="jowbr.php"], a[href*="jowbr_2.php"]').forEach(function(link) {
        var href = link.getAttribute('href');
        var match = href.match(/rec=([^&\s]+)/);
        if (match) {
            link.setAttribute('href', '/search/jowbr-record.php?rec=' + match[1]);
            link.setAttribute('target', '_blank');
        }
    });

    // Rewrite memorialshow.php links -> Synagogue/Society page
    container.querySelectorAll('a[href*="memorialshow.php"]').forEach(function(link) {
        var href = link.getAttribute('href');
        var match = href.match(/id=([^&\s]+)/);
        if (match) {
            link.setAttribute('href', '/search/synagogue.php?id=' + match[1]);
        }
    });
}

function renderError(message) {
    document.getElementById('sr-body').innerHTML =
        '<div class="sr-state-msg">' +
            '<div class="sr-state-title">Search failed</div>' +
            '<p>' + escapeHtml(message) + '</p>' +
        '</div>';
    document.getElementById('sr-card-footer').innerHTML =
        '<span class="sr-footer-note"></span>' +
        '<div class="sr-footer-actions">' +
            '<a class="sr-footer-btn" href="/">&larr; New Search</a>' +
        '</div>';
    document.getElementById('sr-card-footer').style.display = '';
}

/* ---- Page load ---- */

async function loadPage() {
    // Auto-redirect to custom display pages for specific record types
    var params = getParams();
    var rec = params.get('rec');
    if (rec) {
        // USC Shoah Foundation testimony records
        if (rec.indexOf('USCINTERV') === 0) {
            window.location.replace('/search/usc-shoah-record.php?rec=' + encodeURIComponent(rec));
            return;
        }
    }

    try {
        var data = await fetchRecords();
        renderPage(data);
    } catch (err) {
        console.error('Record list load failed:', err);
        renderError(err.message + ' &mdash; try a new search, or contact support if this persists.');
    }
}

// Handle back/forward navigation
window.addEventListener('popstate', loadPage);

/* ---- Component loading + init ---- */

function loadComponent(id, file) {
    return fetch(file)
        .then(function(r) {
            if (!r.ok) throw new Error('Could not load ' + file);
            return r.text();
        })
        .then(function(html) {
            document.getElementById(id).innerHTML = html;
        })
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

// Kick off the data fetch
loadPage();
</script>
</body>
</html>
