/* ==========================================================================
   jg-totop.js - Return-to-Top floating button (self-injecting)

   Add ONE line per page (in <head> or before </body>):
       <script src="/jg-totop.js" defer></script>

   No markup and no inline script needed on the page. Requires the .db-to-top
   styles already living in jg-global.css.
   CHW
   ========================================================================== */
(function () {
    'use strict';

    var SHOW_AFTER = 400;   /* pixels scrolled before the button appears */

    /* Respect the OS "reduce motion" accessibility setting: users who ask for
       less animation get an instant jump instead of a smooth scroll. */
    function reduceMotion() {
        return window.matchMedia &&
               window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    function scrollToTop() {
        window.scrollTo({ top: 0, behavior: reduceMotion() ? 'auto' : 'smooth' });
    }

    function buildButton() {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.id = 'dbToTop';
        btn.className = 'db-to-top';
        /* Visible text "Return to Top" is the accessible name; SVG is hidden. */
        btn.innerHTML =
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" ' +
            'stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" ' +
            'aria-hidden="true"><path d="M18 15l-6-6-6 6"/></svg>' +
            'Return to Top';
        return btn;
    }

    function init() {
        /* Reuse a button that is already hardcoded on the page; otherwise
           inject one. This makes the file safe to add to pages mid-rollout. */
        var btn = document.getElementById('dbToTop');
        if (!btn) {
            btn = buildButton();
            document.body.appendChild(btn);
        }

        /* Guard against wiring the same button twice. */
        if (btn.getAttribute('data-totop-bound')) { return; }
        btn.setAttribute('data-totop-bound', '1');

        /* requestAnimationFrame throttle: scroll fires many times per second,
           so we only recompute visibility once per frame instead of on every
           raw event. Keeps scrolling smooth on long pages. */
        var ticking = false;
        function update() {
            btn.classList.toggle('is-visible', window.pageYOffset > SHOW_AFTER);
            ticking = false;
        }
        function onScroll() {
            if (!ticking) {
                ticking = true;
                window.requestAnimationFrame(update);
            }
        }

        window.addEventListener('scroll', onScroll, { passive: true });
        btn.addEventListener('click', scrollToTop);
        update();   /* set correct state in case the page loads already scrolled */
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
