/* ==========================================================================
   jg-footer-init.js - runs the JavaScript the footer needs.

   Why this file exists:
   Footer.html is injected with element.innerHTML, and the browser will NOT
   execute <script> tags inserted that way. Inline handler attributes (onload,
   onerror) DO still fire - so the footer uses a tiny hidden <img onload> to
   append THIS file as a real <script> element, which does run. Everything the
   footer must execute lives here.
   CHW
   ========================================================================== */
(function () {
    'use strict';

    /* Run once, even if the footer is injected more than once. */
    if (window.__jgFooterInit) { return; }
    window.__jgFooterInit = true;

    /* ---- Google Analytics (GA4) --------------------------------------------
       Standard gtag bootstrap. allow_google_signals stays false per policy.
       dataLayer is a plain array, so pushing config before gtag.js finishes
       loading is fine - the queue is processed once the library arrives. */
    var GA_ID = 'G-NP9XWXG64C';

    var ga = document.createElement('script');
    ga.async = true;
    ga.src = 'https://www.googletagmanager.com/gtag/js?id=' + GA_ID;
    document.head.appendChild(ga);

    window.dataLayer = window.dataLayer || [];
    function gtag() { window.dataLayer.push(arguments); }
    gtag('js', new Date());
    gtag('config', GA_ID, { 'allow_google_signals': false });

    /* ---- Return-to-top button ----------------------------------------------
       Loaded as its own file so it stays reusable on its own. It self-injects
       its button and wires up scroll/click. Bump ?v=N when you edit that file. */
    var totop = document.createElement('script');
    totop.src = '/jg-totop.js?v=1';
    document.head.appendChild(totop);
})();
