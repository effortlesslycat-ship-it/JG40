/* ============================================================================
   JG-PEOPLE-LOADER.JS
   Shared team/bio loader for JewishGen pages.
   Fetches /people/people.json and renders team members in one of
   two modes:
       - 'cards'  : side-by-side photo + bio (RD pages, 1-3 people)
       - 'tiles'  : clickable grid tiles with expand panel (4+ people)

   Usage:
       <div id="rd-team-container"></div>
       <script src="/people/jg-people-loader.js"></script>
       <script>
       JG.loadTeam({
           container: 'rd-team-container',
           roster: [
               { id: 'lara-diamond', role: 'Director' }
           ]
       });
       </script>

   Mode is auto-detected from roster count:
       - 3 or fewer = 'cards' (side-by-side photo + bio)
       - 4 or more  = 'tiles' (clickable grid, expand panel)
   To override: add  mode: 'cards'  or  mode: 'tiles'  to config.

   People with empty bios render without expand behavior (tiles mode)
   or without a bio paragraph (cards mode).

   CHW
============================================================================ */

var JG = window.JG || {};

JG.loadTeam = function(config) {
    var container = document.getElementById(config.container);
    if (!container) return;

    var roster = config.roster || [];

    /* Auto-detect mode from roster count unless explicitly set.
       3 or fewer = cards (inline bio), 4+ = tiles (expand grid). */
    var mode = config.mode || (roster.length > 3 ? 'tiles' : 'cards');

    fetch('/people/people.json')
        .then(function(r) {
            if (!r.ok) throw new Error('Could not load people.json');
            return r.json();
        })
        .then(function(people) {
            var byId = {};
            people.forEach(function(p) { byId[p.id] = p; });

            if (mode === 'tiles') {
                buildTiles(container, roster, byId);
            } else {
                buildCards(container, roster, byId);
            }
        })
        .catch(function(err) {
            console.warn('People loader error:', err);
            container.innerHTML = '<p style="color:var(--charcoal);font-size:0.9rem;">Unable to load team information. Please try again later.</p>';
        });
};


/* -- Cards mode (rd-team-card, stacked vertically) ------------------------ */

function buildCards(container, roster, byId) {
    roster.forEach(function(entry) {
        var person = byId[entry.id];
        if (!person) { console.warn('people.json: no entry for', entry.id); return; }

        var card = document.createElement('article');
        card.className = 'rd-team-card';

        var bioHtml = person.bio
            ? '<p class="rd-team-bio">' + person.bio + '</p>'
            : '';

/* Contact button: opt-in per page via  contact: true  in the roster.
           Renders only if the person also has an email on file. Deliberately
           NOT driven by the presence of an email alone, so RD pages are
           unaffected. CHW */

        var contactHtml = (entry.contact && person.email)
            ? '<a class="jg-contact-btn" href="mailto:' + person.email + '">' +
                  'Contact <span class="visually-hidden">' + person.name + '</span>' +
              '</a>'
            : '';

        card.innerHTML =
            '<div class="rd-photo-wrap">' +
                '<img class="rd-photo" src="' + person.photo + '" alt="' + person.name + '">' +
                contactHtml +
            '</div>' +
            '<div>' +
                '<h2 class="rd-team-name">' + person.name + '</h2>' +
                '<span class="rd-team-role">' + entry.role + '</span>' +
                bioHtml +
            '</div>';
        container.appendChild(card);
    });
}


/* -- Tiles mode (bio-grid with expand/collapse) --------------------------- */

function buildTiles(container, roster, byId) {
    var grid = document.createElement('div');
    grid.className = 'bio-grid';
    grid.id = 'bioGrid';

    /* Adjust grid class if only 1 person */
    if (roster.length === 1) {
        grid.classList.add('bio-grid--single');
    }

    roster.forEach(function(entry) {
        var person = byId[entry.id];
        if (!person) { console.warn('people.json: no entry for', entry.id); return; }

        var hasBio = person.bio && person.bio.trim() !== '';
        var safeId = 'bio-' + entry.id;

        /* -- Tile -- */
        var tile = document.createElement('button');
        tile.type = 'button';
        tile.className = 'bio-tile';

        if (hasBio) {
            tile.setAttribute('aria-expanded', 'false');
            tile.setAttribute('aria-controls', safeId);
        } else {
            /* No bio: tile is not expandable */
            tile.style.cursor = 'default';
            tile.setAttribute('aria-disabled', 'true');
        }

        tile.innerHTML =
            '<img class="bio-photo-tile" src="' + person.photo + '" alt="">' +
            '<h2 class="bio-name">' + person.name + '</h2>' +
            '<span class="bio-role">' + entry.role + '</span>' +
            (hasBio ? '<span class="chevron" aria-hidden="true">&#9662;</span>' : '');

        grid.appendChild(tile);

        /* -- Panel (only if bio exists) -- */
        if (hasBio) {
            var panel = document.createElement('div');
            panel.className = 'bio-panel';
            panel.id = safeId;
            panel.setAttribute('role', 'region');
            panel.setAttribute('aria-label', person.name + ' biography');
            panel.innerHTML =
                '<h3 class="bio-panel-name">' + person.name + '</h3>' +
                '<span class="bio-panel-role">' + entry.role + '</span>' +
                '<p>' + person.bio + '</p>';
            grid.appendChild(panel);
        }
    });

    container.appendChild(grid);

    /* -- Init expand/collapse -- */
    initTileGrid(grid);
}


/* -- Expand/collapse logic for tiles -------------------------------------- */

function initTileGrid(grid) {
    var tiles  = grid.querySelectorAll('.bio-tile[aria-controls]');
    var panels = grid.querySelectorAll('.bio-panel');

    function closeAll(exceptId) {
        tiles.forEach(function(t) {
            if (t.getAttribute('aria-controls') !== exceptId) {
                t.setAttribute('aria-expanded', 'false');
            }
        });
        panels.forEach(function(p) {
            if (p.id !== exceptId) p.classList.remove('is-open');
        });
    }

    function placePanelAfterRow(tile, panel) {
        /* Find the last tile in the same visual row so the full-width
           panel lands cleanly at the next grid row break. */
        var allTiles = Array.from(grid.querySelectorAll('.bio-tile'));
        var tileTop  = tile.getBoundingClientRect().top;
        var lastInRow = tile;
        allTiles.forEach(function(t) {
            if (Math.abs(t.getBoundingClientRect().top - tileTop) < 5) {
                lastInRow = t;
            }
        });
        if (lastInRow.nextSibling !== panel) {
            lastInRow.parentNode.insertBefore(panel, lastInRow.nextSibling);
        }
    }

    tiles.forEach(function(tile) {
        tile.addEventListener('click', function() {
            var panelId = tile.getAttribute('aria-controls');
            var panel   = document.getElementById(panelId);
            var isOpen  = tile.getAttribute('aria-expanded') === 'true';
            if (isOpen) {
                tile.setAttribute('aria-expanded', 'false');
                panel.classList.remove('is-open');
            } else {
                closeAll(panelId);
                placePanelAfterRow(tile, panel);
                tile.setAttribute('aria-expanded', 'true');
                panel.classList.add('is-open');
            }
        });
    });
}