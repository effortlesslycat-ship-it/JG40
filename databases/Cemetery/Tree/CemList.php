<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>JOWBR Cemetery Inventory &mdash; JewishGen</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="/jg-global.css">
<style>
/* ============================================================
   JOWBR Cemetery Inventory - page-specific styles
   Tokens from jg-global.css :root. Navy band handled by the
   global .page-title-band dark-mode fix (#0d2a45 / #fff).
   CHW
============================================================ */

.inv-wrapper {
    background-color: var(--ecru);
    padding: 0 1.5rem 3rem;
}
.inv-inner { max-width: 1000px; margin: 0 auto; }

/* -- Controls bar (filter) -- */
.inv-controls {
    display: flex; align-items: center; gap: 12px;
    padding: 20px 0 8px; flex-wrap: wrap;
}
.inv-search {
    flex: 1; min-width: 240px;
    padding: 11px 16px; font-size: 14px;
    border: 1.5px solid #c9c2b6; border-radius: 6px;
    background: #ffffff; color: var(--charcoal);
}
body.dark-mode .inv-search {
    background: #1a1a1a; border-color: #444; color: #e0e0e0;
}
.inv-search:focus {
    outline: 2px solid var(--sage); outline-offset: 1px; border-color: var(--sage);
}
.inv-clear {
    padding: 9px 16px; font-size: 13px; font-weight: bold;
    border: 1.5px solid var(--navy); border-radius: 6px;
    background: none; color: var(--navy); cursor: pointer;
}
.inv-clear:hover { background: #09497a; color: #ffffff; }
body.dark-mode .inv-clear { color: #8ab4d4; border-color: #8ab4d4; }
body.dark-mode .inv-clear:hover { background: #8ab4d4; color: #121212; }

.inv-status {
    font-size: 12px; color: var(--charcoal); opacity: 0.6;
    padding: 2px 0 14px; min-height: 18px;
}

/* -- Letter-grouped sections (jump-bar targets). The .jump-bar
   component itself lives in jg-global.css. -- */
.inv-alpha { scroll-margin-top: 64px; margin-bottom: 8px; }
.inv-alpha-head {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 1.5rem; font-weight: normal; color: var(--navy);
    margin: 18px 0 4px; padding-bottom: 4px;
    border-bottom: 2px solid var(--sage); width: 34px; text-align: center;
}
body.dark-mode .inv-alpha-head { color: #8ab4d4; border-bottom-color: #a8b361; }

/* -- Tree -- */
.inv-tree, .inv-tree ul {
    list-style: none; margin: 0; padding: 0;
}
.inv-tree ul { padding-left: 22px; }
.inv-tree li { margin: 0; }

/* Folder row = disclosure button */
.inv-folder {
    display: flex; align-items: baseline; gap: 8px; width: 100%;
    text-align: left; background: none; border: none;
    padding: 7px 6px; cursor: pointer; border-radius: 5px;
    color: var(--charcoal); font-family: inherit;
}
.inv-folder:hover { background: rgba(147,155,81,0.12); }
.inv-folder:focus-visible { outline: 2px solid var(--sage); outline-offset: 1px; }

.inv-chevron {
    flex: 0 0 auto; width: 14px; font-size: 11px; line-height: 1.4;
    color: var(--sage); transition: transform 0.15s ease;
    transform: rotate(0deg);
}
body.dark-mode .inv-chevron { color: #a8b361; }
.inv-folder[aria-expanded="true"] .inv-chevron { transform: rotate(90deg); }

.inv-name {
    font-weight: bold; color: var(--navy);
    font-family: Georgia, 'Times New Roman', serif;
}
body.dark-mode .inv-name { color: #8ab4d4; }
.inv-folder[data-level="city"] .inv-name { font-size: 0.95rem; }

.inv-counts {
    font-size: 11.5px; color: var(--charcoal); opacity: 0.6;
    font-family: 'Arial', sans-serif;
}

/* Leaf row = cemetery link */
.inv-leaf {
    display: flex; align-items: baseline; gap: 8px;
    padding: 6px 6px 6px 22px;
}
.inv-leaf::before {
    content: "\2022"; color: var(--sage); flex: 0 0 auto;
    font-size: 12px; line-height: 1.4;
}
body.dark-mode .inv-leaf::before { color: #a8b361; }
.inv-leaf a {
    color: var(--navy); text-decoration: underline dotted;
    text-underline-offset: 3px; font-weight: 600;
}
.inv-leaf a:hover { color: var(--sage); }
body.dark-mode .inv-leaf a { color: #8ab4d4; }
.inv-leaf .inv-counts { margin-left: 2px; }
.inv-leaf .inv-date { font-size: 11px; color: var(--charcoal); opacity: 0.45; }

/* Collapsed city-cemetery: render like a leaf but city-styled name */
.inv-leaf.inv-city-cem a { font-family: Georgia, 'Times New Roman', serif; }

/* Filter highlight */
.inv-tree mark {
    background: #f3e9a8; color: inherit; padding: 0 1px; border-radius: 2px;
}
body.dark-mode .inv-tree mark { background: #6b6326; color: #fff; }

/* States */
.inv-state { text-align: center; padding: 60px 20px; color: var(--charcoal); }
.inv-spinner {
    display: inline-block; width: 26px; height: 26px;
    border: 3px solid #e8edf0; border-top-color: var(--navy);
    border-radius: 50%; animation: inv-spin 0.7s linear infinite; margin-bottom: 10px;
}
@keyframes inv-spin { to { transform: rotate(360deg); } }
.inv-empty { font-size: 14px; color: var(--charcoal); opacity: 0.6; padding: 24px 6px; }

/* Back links */
.inv-back {
    display: flex; gap: 20px; flex-wrap: wrap;
    margin-top: 30px; padding-top: 16px;
    border-top: 1px solid rgba(147,155,81,0.3);
}
.inv-back a {
    font-size: 13px; font-weight: bold; color: var(--navy);
    text-decoration: underline dotted; text-underline-offset: 3px;
}
.inv-back a:hover { color: var(--sage); }
body.dark-mode .inv-back a { color: #8ab4d4; }
body.dark-mode .inv-back { border-color: #333; }

@media (max-width: 600px) {
    .inv-counts { display: block; opacity: 0.55; }
    .inv-folder { flex-wrap: wrap; }
}
</style>
</head>
<body>

<div id="site-header"></div>

<main>
    <div class="page-title-band">
        <span class="tagline">JewishGen Online Worldwide Burial Registry</span>
        <h1>JOWBR</h1>
        <p class="hero-subtitle">Browse the full inventory of cemeteries and burial records by country, region, and city.</p>
    </div>

    <!-- Shared JOWBR sub-nav (stats bar + section nav), injected via fetch -->
    <div id="jowbr-subnav"></div>

    <div class="inv-wrapper">
        <div class="inv-inner">

            <div class="inv-controls">
                <input type="text" id="inv-search" class="inv-search"
                       placeholder="Filter by cemetery, city, region, or country name&hellip;"
                       aria-label="Filter the cemetery inventory by name"
                       autocomplete="off">
                <button type="button" id="inv-clear" class="inv-clear" style="display:none;">Clear</button>
            </div>
            <div class="inv-status" id="inv-status" role="status" aria-live="polite"></div>

            <div id="inv-tree-host">
                <div class="inv-state" id="inv-loading">
                    <div class="inv-spinner"></div>
                    <div>Loading cemetery inventory&hellip;</div>
                </div>
            </div>

            <div class="inv-back">
                <a href="/RD/JOWBR/">&larr; JOWBR Home</a>
                <a href="/">&larr; All Databases</a>
            </div>

        </div>
    </div>
</main>

<div id="site-footer"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ============================================================
   JOWBR Cemetery Inventory renderer  -  CHW
   Reads jowbr-inventory.json (same folder), renders a collapsed
   country list by default. Folders are disclosure buttons
   (aria-expanded); children build lazily on first open. The
   filter prunes to matches and opens the full path to each.
============================================================ */
var INVENTORY_JSON = 'jowbr-inventory.json';

var DATA = null;       // {footer, countries}
var uidCounter = 0;

function fmt(n) { return (n || 0).toLocaleString('en-US'); }

function countsLabel(node) {
    // Folder summary, e.g. "294 cemeteries - 81,153 burials - 14,864 photos"
    var cem = node.cemeteries || 0;
    var parts = [
        fmt(cem) + ' ' + (cem === 1 ? 'cemetery' : 'cemeteries'),
        fmt(node.burials) + ' ' + ((node.burials === 1) ? 'burial' : 'burials'),
        fmt(node.photos) + ' ' + ((node.photos === 1) ? 'photo' : 'photos')
    ];
    return parts.join('  \u00b7  ');
}

function leafCounts(node) {
    var parts = [
        fmt(node.burials) + ' ' + ((node.burials === 1) ? 'burial' : 'burials'),
        fmt(node.photos) + ' ' + ((node.photos === 1) ? 'photo' : 'photos')
    ];
    return parts.join(', ');
}

/* -- Build a folder <li> (country / region / city with children) -- */
function buildFolder(node, level) {
    var li = document.createElement('li');

    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'inv-folder';
    btn.setAttribute('data-level', level);
    btn.setAttribute('aria-expanded', 'false');

    var childId = 'inv-grp-' + (uidCounter++);
    btn.setAttribute('aria-controls', childId);

    var chev = document.createElement('span');
    chev.className = 'inv-chevron';
    chev.setAttribute('aria-hidden', 'true');
    chev.textContent = '\u25B6'; // right-pointing triangle
    btn.appendChild(chev);

    var name = document.createElement('span');
    name.className = 'inv-name';
    name.textContent = node.name;
    btn.appendChild(name);

    var counts = document.createElement('span');
    counts.className = 'inv-counts';
    counts.textContent = countsLabel(node);
    btn.appendChild(counts);

    var group = document.createElement('ul');
    group.id = childId;
    group.setAttribute('role', 'group');
    group.style.display = 'none';
    group._built = false;
    group._node = node;

    btn.addEventListener('click', function () {
        var open = btn.getAttribute('aria-expanded') === 'true';
        if (open) {
            btn.setAttribute('aria-expanded', 'false');
            group.style.display = 'none';
        } else {
            if (!group._built) { buildChildren(group, node, level); group._built = true; }
            btn.setAttribute('aria-expanded', 'true');
            group.style.display = '';
        }
    });

    li.appendChild(btn);
    li.appendChild(group);
    return li;
}

/* -- Build a cemetery leaf <li> -- */
function buildLeaf(node, isCityCem) {
    var li = document.createElement('li');
    li.className = 'inv-leaf' + (isCityCem ? ' inv-city-cem' : '');

    var a = document.createElement('a');
    a.href = node.url || '#';
    a.textContent = node.name;
    li.appendChild(a);

    var counts = document.createElement('span');
    counts.className = 'inv-counts';
    counts.textContent = '(' + leafCounts(node) + ')';
    li.appendChild(counts);

    if (node.date) {
        var d = document.createElement('span');
        d.className = 'inv-date';
        d.textContent = 'data online ' + node.date;
        li.appendChild(d);
    }
    return li;
}

function childLevel(level) {
    if (level === 'country') return 'region';   // may actually be city; checked per-node
    if (level === 'region') return 'city';
    return 'city';
}

/* -- Lazily build the children of a folder -- */
function buildChildren(group, node, level) {
    var kids = node.children || [];
    for (var i = 0; i < kids.length; i++) {
        var k = kids[i];
        if (k.type === 'cemetery') {
            group.appendChild(buildLeaf(k, false));
        } else if (k.type === 'city-cemetery') {
            group.appendChild(buildLeaf(k, true));
        } else {
            // folder: region or city. Use the node's own declared type for level.
            group.appendChild(buildFolder(k, k.type));
        }
    }
}

/* -- First letter for A-Z bucketing (diacritics folded: Cote -> C) -- */
function firstLetter(name) {
    var c = (name || '').trim().charAt(0).toUpperCase();
    if (c.normalize) c = c.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    return /[A-Z]/.test(c) ? c : '#';
}

/* -- Build the A-Z jump bar from the letters actually present.
   The bar is inserted as a direct child of .inv-inner, immediately
   before the tree host, so its containing block is the tall content
   column and it can stick across the whole list. -- */
function buildJumpBar(presentSet) {
    var treeHost = document.getElementById('inv-tree-host');
    var inner = treeHost.parentNode;

    // Remove any existing bar before rebuilding
    var existing = inner.querySelector('.jump-bar');
    if (existing) inner.removeChild(existing);

    var bar = document.createElement('div');
    bar.setAttribute('role', 'navigation');
    bar.className = 'jump-bar';
    bar.setAttribute('aria-label', 'Jump to country by letter');

    var label = document.createElement('span');
    label.className = 'jump-bar__label';
    label.textContent = 'Jump to';
    bar.appendChild(label);

    var letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');
    if (presentSet['#']) letters.push('#');
    letters.forEach(function (L) {
        var a = document.createElement('a');
        a.textContent = (L === '#') ? '0-9' : L;
        if (presentSet[L]) {
            a.href = '#inv-sec-' + (L === '#' ? 'num' : L);
        } else {
            a.className = 'disabled';
            a.setAttribute('aria-disabled', 'true');
            a.href = '#';
        }
        bar.appendChild(a);
    });

    inner.insertBefore(bar, treeHost);
}

function showJumpBar(show) {
    var treeHost = document.getElementById('inv-tree-host');
    var bar = treeHost.parentNode.querySelector('.jump-bar');
    if (bar) bar.style.display = show ? '' : 'none';
}

/* -- Initial render: countries grouped into A-Z sections (collapsed) -- */
function renderCountries() {
    var host = document.getElementById('inv-tree-host');
    host.innerHTML = '';

    // Bucket countries by first letter, preserving source order within each
    var buckets = {};
    var present = {};
    for (var i = 0; i < DATA.countries.length; i++) {
        var L = firstLetter(DATA.countries[i].name);
        if (!buckets[L]) { buckets[L] = []; }
        buckets[L].push(DATA.countries[i]);
        present[L] = true;
    }

    var order = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');
    if (present['#']) order.push('#');

    order.forEach(function (L) {
        if (!buckets[L]) return;
        var sec = document.createElement('div');
        sec.className = 'inv-alpha';
        sec.id = 'inv-sec-' + (L === '#' ? 'num' : L);

        var h = document.createElement('h3');
        h.className = 'inv-alpha-head';
        h.textContent = (L === '#') ? '0-9' : L;
        sec.appendChild(h);

        var ul = document.createElement('ul');
        ul.className = 'inv-tree';
        buckets[L].forEach(function (country) {
            ul.appendChild(buildFolder(country, 'country'));
        });
        sec.appendChild(ul);
        host.appendChild(sec);
    });

    buildJumpBar(present);
    showJumpBar(true);
    setStatus(DATA.countries.length + ' countries. Jump to a letter or expand a country.');
}

/* ============================================================
   FILTER  -  prune to matches, open the full path to each
============================================================ */
function nodeMatches(node, q) {
    return (node.name || '').toLowerCase().indexOf(q) !== -1;
}

/* Returns a pruned copy of `node` if it or any descendant matches, else null.
   If the node's OWN name matches, its entire subtree is kept untouched
   (searching "Philadelphia" shows every cemetery in Philadelphia, not just
   the ones whose names also contain "Philadelphia"). If the node doesn't
   match itself, it's kept only as a path to descendants that do. */
function prune(node, q) {
    if (nodeMatches(node, q)) {
        var keep = {};
        for (var k in node) { if (node.hasOwnProperty(k)) keep[k] = node[k]; }
        keep._match = true;            // children kept as-is = full subtree
        return keep;
    }
    var kids = node.children || [];
    var keptKids = [];
    for (var i = 0; i < kids.length; i++) {
        var pk = prune(kids[i], q);
        if (pk) keptKids.push(pk);
    }
    if (keptKids.length === 0) return null;
    var copy = {};
    for (var key in node) { if (node.hasOwnProperty(key)) copy[key] = node[key]; }
    copy.children = keptKids;
    copy._match = false;
    return copy;
}

function highlightInto(el, name, q) {
    var lower = name.toLowerCase();
    var idx = lower.indexOf(q);
    if (idx === -1 || !q) { el.textContent = name; return; }
    el.textContent = '';
    var last = 0;
    while (idx !== -1) {
        if (idx > last) el.appendChild(document.createTextNode(name.slice(last, idx)));
        var mk = document.createElement('mark');
        mk.textContent = name.slice(idx, idx + q.length);
        el.appendChild(mk);
        last = idx + q.length;
        idx = lower.indexOf(q, last);
    }
    if (last < name.length) el.appendChild(document.createTextNode(name.slice(last)));
}

/* Render a pruned node fully expanded (path to every match is open) */
function renderFiltered(node, level, q) {
    if (node.type === 'cemetery' || node.type === 'city-cemetery') {
        var li = buildLeaf(node, node.type === 'city-cemetery');
        highlightInto(li.querySelector('a'), node.name, q);
        return li;
    }
    var li2 = document.createElement('li');
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'inv-folder';
    btn.setAttribute('data-level', node.type);
    btn.setAttribute('aria-expanded', 'true');
    var childId = 'inv-grp-' + (uidCounter++);
    btn.setAttribute('aria-controls', childId);

    var chev = document.createElement('span');
    chev.className = 'inv-chevron'; chev.setAttribute('aria-hidden', 'true');
    chev.textContent = '\u25B6';
    btn.appendChild(chev);

    var name = document.createElement('span');
    name.className = 'inv-name';
    highlightInto(name, node.name, q);
    btn.appendChild(name);

    var counts = document.createElement('span');
    counts.className = 'inv-counts';
    counts.textContent = countsLabel(node);
    btn.appendChild(counts);

    var group = document.createElement('ul');
    group.id = childId; group.setAttribute('role', 'group');

    var kids = node.children || [];
    for (var i = 0; i < kids.length; i++) {
        group.appendChild(renderFiltered(kids[i], node.type, q));
    }

    // Allow collapsing a filtered branch too
    btn.addEventListener('click', function () {
        var open = btn.getAttribute('aria-expanded') === 'true';
        btn.setAttribute('aria-expanded', String(!open));
        group.style.display = open ? 'none' : '';
    });

    li2.appendChild(btn);
    li2.appendChild(group);
    return li2;
}

function countLeaves(node, acc) {
    if (node.type === 'cemetery' || node.type === 'city-cemetery') { acc.n++; return; }
    var kids = node.children || [];
    for (var i = 0; i < kids.length; i++) countLeaves(kids[i], acc);
}

function applyFilter(q) {
    q = (q || '').trim().toLowerCase();
    var host = document.getElementById('inv-tree-host');
    document.getElementById('inv-clear').style.display = q ? '' : 'none';

    if (q.length < 2) {
        renderCountries();
        if (q.length === 1) setStatus('Type at least 2 characters to filter.');
        return;
    }

    // Browsing aid only; hide while filtering
    showJumpBar(false);

    var kept = [];
    for (var i = 0; i < DATA.countries.length; i++) {
        var pk = prune(DATA.countries[i], q);
        if (pk) kept.push(pk);
    }

    host.innerHTML = '';
    if (kept.length === 0) {
        var empty = document.createElement('div');
        empty.className = 'inv-empty';
        empty.textContent = 'No matches for \u201c' + q + '\u201d.';
        host.appendChild(empty);
        setStatus('No matches.');
        return;
    }

    var ul = document.createElement('ul');
    ul.className = 'inv-tree';
    var acc = { n: 0 };
    for (var j = 0; j < kept.length; j++) {
        ul.appendChild(renderFiltered(kept[j], 'country', q));
        countLeaves(kept[j], acc);
    }
    host.appendChild(ul);
    setStatus(fmt(acc.n) + ' matching cemeter' + (acc.n === 1 ? 'y' : 'ies') +
              ' in ' + kept.length + ' countr' + (kept.length === 1 ? 'y' : 'ies') + '.');
}

function setStatus(msg) {
    document.getElementById('inv-status').textContent = msg || '';
}

/* -- Debounced filter input -- */
function wireControls() {
    var input = document.getElementById('inv-search');
    var clear = document.getElementById('inv-clear');
    var t = null;
    input.addEventListener('input', function () {
        clearTimeout(t);
        var v = input.value;
        t = setTimeout(function () { applyFilter(v); }, 180);
    });
    clear.addEventListener('click', function () {
        input.value = '';
        applyFilter('');
        input.focus();
    });
}

/* -- Load -- */
function loadInventory() {
    fetch(INVENTORY_JSON)
        .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
        .then(function (data) {
            DATA = data;
            renderCountries();
            wireControls();
        })
        .catch(function (err) {
            document.getElementById('inv-tree-host').innerHTML =
                '<div class="inv-empty">Could not load the cemetery inventory (' +
                (err.message || 'error') + ').</div>';
            setStatus('');
        });
}

/* -- Shared header/footer (JG40 pattern) -- */
function loadComponent(id, file) {
    return fetch(file)
        .then(function (r) { if (!r.ok) throw new Error('Could not load ' + file); return r.text(); })
        .then(function (html) { document.getElementById(id).innerHTML = html; })
        .catch(function (err) { console.warn(err); });
}

/* -- Shared JOWBR sub-nav: inject, then light up the active tab.
   This page lives at /databases/Cemetery/tree/CemList.php, which is the
   exact href of the subnav's "Cemetery Inventory" link, so that tab
   becomes active. -- */
loadComponent('jowbr-subnav', '/RD/JOWBR/JOWBR_SubNav.html').then(function () {
    var path = window.location.pathname.replace(/\/index\.html$/, '/');
    document.querySelectorAll('.jowbr-nav a').forEach(function (a) {
        var href = a.getAttribute('href');
        if (!href || href.charAt(0) !== '/') return;
        var hrefNorm = href.replace(/\/index\.html$/, '/');
        if (path === hrefNorm || path === href) { a.classList.add('active'); }
    });
});

Promise.all([
    loadComponent('site-header', '/Header_NavBar.html'),
    loadComponent('site-footer', '/Footer.html')
]).then(function () {
    document.querySelectorAll('.jg-nav .dropbtn').forEach(function (btn) {
        btn.addEventListener('keydown', function (e) {
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

loadInventory();
</script>
</body>
</html>