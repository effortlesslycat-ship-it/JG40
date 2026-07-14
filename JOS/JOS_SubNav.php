<?php
/* =====================================================
   JOS_SubNav.php -- shared JOS tool toolbar
   Server-included on every /JOS/ page. Each page sets
   $josActive to one of: index, josdates, josfest,
   jossound, mjyear  BEFORE including this file, e.g.:
       <?php $josActive = 'jossound';
             include $_SERVER['DOCUMENT_ROOT'].'/JOS/JOS_SubNav.php'; ?>
   Adapted from the HowToFAQ subnav; uses PHP-set active
   state instead of fetch()+JS because JOS pages are PHP.
   div[role=navigation] avoids the global nav-element cascade.
   CHW
   ===================================================== */
if (!isset($josActive)) { $josActive = ''; }
function josActiveClass($key) {
    global $josActive;
    return ($josActive === $key) ? ' class="active"' : '';
}
?>
<style>
/* JOS toolbar -- mirrors .htfaq-nav pattern */
.jos-nav {
    background-color: var(--white);           /* flips to #121212 dark */
    border-bottom: 2px solid var(--cream);
    padding: 0 50px;
    display: flex;
    justify-content: center;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
body.dark-mode .jos-nav {
    background-color: #121212;
    border-bottom-color: #333;
}
.jos-nav a {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 13px 20px;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--charcoal);
    border-bottom: 3px solid transparent;
    white-space: nowrap;
    text-decoration: none;
    transition: color 0.15s, border-color 0.15s;
}
.jos-nav a:hover {
    color: var(--navy);
    border-bottom-color: var(--sage);
}
.jos-nav a.active {
    color: var(--navy);
    border-bottom-color: var(--navy);
}
body.dark-mode .jos-nav a { color: #a0a0a0; }
body.dark-mode .jos-nav a:hover,
body.dark-mode .jos-nav a.active {
    color: #e0e0e0;
    border-bottom-color: #a8b361;
}
@media (max-width: 900px) { .jos-nav { padding: 0 20px; } }
@media (max-width: 480px) {
    .jos-nav a { padding: 12px 14px; font-size: 0.8125rem; gap: 6px; }
}
</style>
<div role="navigation" class="jos-nav" aria-label="JewishGen Online Services tools">
    <a href="/JOS/index.php"<?php echo josActiveClass('index'); ?>>All Tools</a>
    <a href="/JOS/josdates.php"<?php echo josActiveClass('josdates'); ?>>Calendar Converter</a>
    <a href="/JOS/josfest.php"<?php echo josActiveClass('josfest'); ?>>Festival Dates</a>
    <a href="/JOS/jossound.php"<?php echo josActiveClass('jossound'); ?>>Soundex Calculator</a>
    <a href="/JOS/mjyear.php"<?php echo josActiveClass('mjyear'); ?>>Hebrew Year Converter</a>
</div>