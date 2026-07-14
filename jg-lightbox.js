/* =====================================================================
   jg-lightbox.js  --  Dataset description page image lightbox (JG40)
   ---------------------------------------------------------------------
   Auto-wires any <a> that wraps an <img> on dataset pages so the image
   enlarges in an overlay instead of opening a new tab.

   USAGE:  <script src="/jg-lightbox.js"></script>
           (after jg-dataset.css is loaded)

   Captions pulled from the nearest <figcaption>, or the img alt text.

   Close: click backdrop, click X button, or press Escape.
   CHW + JG40
   ===================================================================== */
(function () {
    /* -- Inject styles once ----------------------------------------- */
    var css = [
        '.jg-lb-overlay {',
        '  position: fixed; inset: 0; z-index: 99999;',
        '  background: rgba(0,0,0,0.85);',
        '  display: flex; align-items: center; justify-content: center;',
        '  flex-direction: column; gap: 12px;',
        '  opacity: 0; transition: opacity 0.2s ease;',
        '  cursor: zoom-out;',
        '}',
        '.jg-lb-overlay.is-visible { opacity: 1; }',
        '.jg-lb-close {',
        '  position: absolute; top: 16px; right: 20px;',
        '  background: none; border: none; color: #fff;',
        '  font-size: 32px; line-height: 1; cursor: pointer;',
        '  padding: 4px 10px; opacity: 0.8;',
        '}',
        '.jg-lb-close:hover { opacity: 1; }',
        '.jg-lb-img {',
        '  max-width: 90vw; max-height: 80vh;',
        '  object-fit: contain;',
        '  border-radius: 4px;',
        '  box-shadow: 0 4px 40px rgba(0,0,0,0.5);',
        '  cursor: default;',
        '}',
        '.jg-lb-caption {',
        '  color: rgba(255,255,255,0.85); font-size: 13px;',
        '  max-width: 700px; text-align: center; line-height: 1.5;',
        '  padding: 0 20px;',
        '}'
    ].join('\n');
    var style = document.createElement('style');
    style.textContent = css;
    document.head.appendChild(style);

    /* -- Overlay element (created once, reused) ---------------------- */
    var overlay = document.createElement('div');
    overlay.className = 'jg-lb-overlay';
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-label', 'Image preview');
    overlay.innerHTML = '<button class="jg-lb-close" aria-label="Close">&times;</button>'
        + '<img class="jg-lb-img" src="" alt="">'
        + '<div class="jg-lb-caption"></div>';
    document.body.appendChild(overlay);

    var lbImg = overlay.querySelector('.jg-lb-img');
    var lbCap = overlay.querySelector('.jg-lb-caption');
    var lbClose = overlay.querySelector('.jg-lb-close');

    function open(src, caption) {
        lbImg.src = src;
        lbImg.alt = caption || '';
        lbCap.textContent = caption || '';
        lbCap.style.display = caption ? '' : 'none';
        overlay.style.display = 'flex';
        /* Force reflow so opacity transition fires */
        overlay.offsetHeight; /* jshint ignore:line */
        overlay.classList.add('is-visible');
        document.body.style.overflow = 'hidden';
    }

    function close() {
        overlay.classList.remove('is-visible');
        document.body.style.overflow = '';
        setTimeout(function () {
            overlay.style.display = 'none';
            lbImg.src = '';
        }, 200);
    }

    /* -- Close triggers ---------------------------------------------- */
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) close();
    });
    lbClose.addEventListener('click', close);
    lbImg.addEventListener('click', function (e) { e.stopPropagation(); });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay.classList.contains('is-visible')) close();
    });

    /* -- Auto-wire image links --------------------------------------- */
    function getCaption(anchor) {
        var figure = anchor.closest('figure');
        if (figure) {
            var cap = figure.querySelector('figcaption');
            if (cap) return cap.textContent.trim();
        }
        var img = anchor.querySelector('img');
        if (img && img.alt) return img.alt;
        return '';
    }

    function wire() {
        var links = document.querySelectorAll('.main-column a, .dataset-main a, main a');
        Array.prototype.forEach.call(links, function (a) {
            var img = a.querySelector('img');
            if (!img) return;
            var href = a.getAttribute('href') || '';
            /* Only wire links that point to an image */
            if (!/\.(jpe?g|png|gif|webp|bmp|svg)(\?.*)?$/i.test(href)) return;
            a.addEventListener('click', function (e) {
                e.preventDefault();
                open(href, getCaption(a));
            });
            a.removeAttribute('target');
            a.style.cursor = 'zoom-in';
            var links = document.querySelectorAll('.main-column a, .dataset-main a, main a, a.jg-lightbox');
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', wire);
    } else {
        wire();
    }
})();
