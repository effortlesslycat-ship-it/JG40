<?php
/**
 * /RD/Holocaust/index.php  -  Holocaust Research Division landing page
  */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>JewishGen Holocaust Database</title>
<meta name="description" content="The JewishGen Holocaust Database is a unique and critically valuable collection of databases containing information about Holocaust victims and survivors.">

<!-- 1. Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<!-- 2. JewishGen Global Design System -->
<link rel="stylesheet" href="/jg-global.css">
<!-- 3. JewishGen Research Division stylesheet -->
<link rel="stylesheet" href="/RD/jg-rd.css?v=13">
<!-- 4. Page-specific styles -->
<style>
a { text-decoration: none; color: inherit; }

</style>
</head>
<body>

<!-- HEADER -->
<div id="site-header">
<?php
$headerPath = $_SERVER['DOCUMENT_ROOT'] . '/Header_NavBar.html';
if (file_exists($headerPath)) { include $headerPath; }
?>
</div>

<!-- TITLE BAND -->
<div class="page-title-band" role="banner">
    <span class="tagline">Holocaust Research Division</span>
    <h1>Holocaust Database Project</h1>
    <p class="hero-subtitle">A unique and critically valuable collection of databases containing information about Holocaust victims and survivors.</p>
</div>

<!-- RD TOOLBAR (Holocaust-specific; replaces the sidebar on this search-led page) -->
<div id="rd-toolbar">
<?php
$toolbarPath = $_SERVER['DOCUMENT_ROOT'] . '/RD/Holocaust/RD_Toolbar.html';
if (file_exists($toolbarPath)) { include $toolbarPath; }
?>
</div>


<!-- PAGE BODY -->
<div class="rd-body-wrap">
    <div class="rd-body-inner--full">
      
        <!-- Main content -->
        <main>

            <!-- About -->
            <section>
                <span class="section-heading">About the Division</span>
                <div class="green-accent-line"></div>
                <div class="rd-intro">
                    <p>JewishGen's Holocaust Database is a unique and critically valuable collection of databases which contain information about Holocaust victims and survivors.  As of May 2025, more than 6 million records are available in this continually updated collection, which includes Camp Records, Transport Lists, Name Lists, and a multitude of other record types.</p>
                    <p>If you would be interested in making a major contribution to the Holocaust Division's efforts, please email <a href="mailto:development@mjhnyc.org">development@mjhnyc.org</a>.</p>
                </div>

                <!-- Currently working on
                <div class="rd-current-card">
                    <span class="rd-current-label">Currently Working On</span>
                    <p class="rd-current-text">The Division is in the middle of indexing birth, marriage, and death records from the Jewish communities of Bucharest and Iasi County, as well as surveying, photographing, and documenting the cemeteries of Romania</p>
                </div>-->

                <!-- Volunteer strip -->
                <div class="volunteer-strip rd-section">
                    For more information or to get involved, please visit our <a href="/RD/Holocaust/volunteer.html">detailed volunteer resource</a>.
                </div>

            </section>

            <!-- Our Team (populated from people.json) -->
            <section class="rd-section">
                <span class="section-heading">Our Team</span>
                <div class="green-accent-line"></div>
                <div id="rd-team-container"></div>
            </section>
            <br>

            <!-- Centered donation block - TODO: NEED LINK
            <section class="rd-section">
                <span class="section-heading">Support the Holocaust Research Division</span>
                <div class="green-accent-line"></div>
                <div class="rd-donation-block">
                    <p>Donor support enables the Division to acquire new materials, index rare records, and make them widely accessible for future generations.</p>
                    <a href="#" class="rd-btn-donate-solid">Support the Holocaust Research Division</a>
                </div>
            </section>-->
        </main>

    </div>
</div>

<!-- FOOTER -->
<div id="site-footer">
<?php
$footerPath = $_SERVER['DOCUMENT_ROOT'] . '/Footer.html';
if (file_exists($footerPath)) { include $footerPath; }
?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/jg-rd-sidebar.js"></script>
<script>


/* -- Toolbar active link (runs immediately - DOM is server-included) -- */
(function() {
    var path = window.location.pathname;
    document.querySelectorAll('.rd-toolbar-links a').forEach(function(a) {
        if (a.getAttribute('href') === path) {
            a.classList.add('is-active');
            a.setAttribute('aria-current', 'page');
        }
    });
})();

/* -- Sidebar active link ----------------------------------
   Sidebar is server-included so DOM is ready immediately. */
if (window.JG && JG.setActiveSidebarLink) {
    JG.setActiveSidebarLink();
}

/* -- Nav dropdown keyboard handlers ----------------------- */
document.querySelectorAll('.jg-nav .dropbtn, .main-nav .dropbtn').forEach(function(btn) {
    btn.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            var menu = this.nextElementSibling;
            if (menu) menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        }
        if (e.key === 'Escape') {
            var menu = this.nextElementSibling;
            if (menu) menu.style.display = 'none';
        }
    });
});
</script>

<script src="/people/jg-people-loader.js"></script>
<script>
JG.loadTeam({
    container: 'rd-team-container',
    roster: [
        { id: 'nolan-altman', role: 'Director of the Holocaust Database' },
    ]
});
</script>

</body>
</html>
