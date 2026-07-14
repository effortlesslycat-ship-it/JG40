<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Our Team - JewishGen</title>

<!-- 1. Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<!-- 2. JewishGen Global Design System -->
<link rel="stylesheet" href="/jg-global.css">
<!-- 3. Card / tile system (shared with the RD pages) -->
<link rel="stylesheet" href="/RD/jg-rd.css">
<!-- 4. Our Team layout + contact button -->
<link rel="stylesheet" href="/jg-team.css?v=1">
<style>
a { text-decoration: none; color: inherit; }
</style>
</head>
<body>

<!-- HEADER -->
<div id="site-header"></div>

<!-- PAGE TITLE BAND -->
<div class="page-title-band">
    <h1>Our Team</h1>
    <p class="hero-subtitle">[Subtitle goes here.]</p>
</div>


<!-- ====================================================================
     STAFF  (Leadership, Systems)
===================================================================== -->
<main class="team-wrap">
<div class="team-inner">

    <!-- ============================ LEADERSHIP ======================== -->
    <section class="team-section">
        <h2 class="team-section-heading">Leadership</h2>
        <p class="team-section-blurb">[Optional section blurb - delete this line if not needed.]</p>
        <div id="team-leadership"><p class="team-loading">Loading&#8230;</p></div>
    </section>


    <!-- ============================== SYSTEMS ========================= -->
    <section class="team-section">
        <h2 class="team-section-heading">Systems</h2>
        <div id="team-systems"><p class="team-loading">Loading&#8230;</p></div>
    </section>

</div>
</main>


<!-- ====================================================================
     DIVIDER - staff above, volunteers below
===================================================================== -->


<!-- ====================================================================
     VOLUNTEERS  (Communications, Support)
===================================================================== -->
<main class="team-wrap">
<div class="team-inner">

    <!-- ========================== COMMUNICATIONS ====================== -->
    <section class="team-section">
        <h2 class="team-section-heading">Communications</h2>
        <div id="team-communications"><p class="team-loading">Loading&#8230;</p></div>
    </section>


    <!-- ============================== SUPPORT ========================= -->
    <section class="team-section">
        <h2 class="team-section-heading">Support</h2>
        <div id="team-support"><p class="team-loading">Loading&#8230;</p></div>
    </section>

</div>
</main>

<!-- FOOTER -->
<div id="site-footer"></div>

<script src="/people/jg-people-loader.js"></script>
<script>
/* ---------------------------------------------------------------------------
   OUR TEAM ROSTERS
   Bios and photos live in /people/people.json. This page only says who is in
   which section and what their role is here.

   To add someone:  add  { id: 'their-id', role: 'Their Title' }  to a roster.
   The id must match the "id" field in people.json exactly.

   Mode is auto-detected from headcount: 3 or fewer renders as cards (photo +
   bio side by side), 4 or more renders as tiles (clickable, bio expands).
   Force it with  mode: 'cards'  or  mode: 'tiles'  if the auto choice is wrong.

   CHW
--------------------------------------------------------------------------- */

JG.loadTeam({
    container: 'team-leadership',
    roster: [
        { id: 'paul-radensky',          role: 'Director', contact: true },
        { id: 'caitlin-hollander-waas', role: 'Chief Genealogist', contact: true  },
        { id: 'karen-franklin',         role: 'Director of Outreach', contact: true }
    ]
});

JG.loadTeam({
    container: 'team-systems',
    roster: [
        { id: 'gary-sandler', role: 'Systems' }
    ]
});

JG.loadTeam({
    container: 'team-communications',
    roster: [
        /* { id: '', role: '' }, */
    ]
});

JG.loadTeam({
    container: 'team-support',
    roster: [
        /* { id: '', role: '' }, */
    ]
});


/* -- Shared components -- */
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
});
</script>

</body>
</html>