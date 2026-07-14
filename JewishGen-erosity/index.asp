<%
' JewishGen-erosity -- Main Page:
' ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Response.expires = 0
Response.expiresabsolute = Now() - 1
Response.addHeader "pragma", "no-cache"
Response.addHeader "cache-control", "private"
Response.CacheControl = "no-cache"
' Response.Buffer = True

On Error Resume Next
%>

<!--#include file="db.asp"-->

<%
' Open connection to the "Generosity" MS-SQL database:
Set conn = Server.CreateObject("ADODB.Connection")
conn.Open xDb_Conn_Str

' Build SQL query: 
strsql = "SELECT * FROM [v_category] ORDER BY Category ASC"
Set rs = Server.CreateObject("ADODB.Recordset")
rs.cursorlocation = 3
rs.Open strsql, conn, 1, 2

' *** Error check? ***
' *** See "Error Handler" at bottom of page...  does it work?
' *** Response.Write strsql
%>


<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Support JewishGen - JewishGen</title>

<!-- 1. Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<!-- 2. JewishGen Global Design System -->
<link rel="stylesheet" href="/jg-global.css">
<!-- 3. Page-specific styles -->
<style>
a { text-decoration: none; color: inherit; }

/* == Dark mode page-specific overrides == */
body.dark-mode .page-title-band { background-color: #0d2a45 !important; }
body.dark-mode .page-title-band p.hero-subtitle { color: rgba(255,255,255,0.85) !important; }
body.dark-mode .ops-card,
body.dark-mode .funds-box,
body.dark-mode .ow-card { background-color: #1e1e1e !important; border-color: #333 !important; }
body.dark-mode .ops-card:hover,
body.dark-mode .ow-card:hover { border-color: var(--sage) !important; }
body.dark-mode .funds-list li { border-bottom-color: #333 !important; }
body.dark-mode .funds-list a { color: #e0e0e0 !important; }
body.dark-mode .ops-card h3,
body.dark-mode .ow-card h3,
body.dark-mode .funds-box h2,
body.dark-mode .ops-section h2,
body.dark-mode .other-ways-section h2 { color: #e0e0e0 !important; }
body.dark-mode .ops-card p,
body.dark-mode .ow-card p { color: #c0c0c0 !important; }
body.dark-mode .disclaimer { color: #a0a0a0 !important; }
body.dark-mode .other-ways-section { background-color: #141414 !important; border-top-color: #333 !important; border-bottom-color: #333 !important; }

/* == Page title band == */
.page-title-band {
    background-color: var(--navy);
    padding: 44px 50px;
    text-align: center;
}
/* SAGE BLOCK TAG  -  candidate for jg-global.css */
.page-title-band .tagline {
    display: inline-block;
    background-color: var(--sage);
    color: #ffffff;
    font-size: 0.6875rem;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    padding: 4px 12px;
    border-radius: 3px;
    margin-bottom: 14px;
}
.page-title-band h1 {
    margin: 0 0 10px 0;
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 2.25rem;
    font-weight: normal;
    line-height: 1.15;
    color: #ffffff;
}
.page-title-band p.hero-subtitle {
    margin: 0 auto;
    font-size: 0.9375rem;
    color: rgba(255,255,255,0.85);
    max-width: 720px;
    line-height: 1.6;
}

body.dark-mode 
.page-title-band .tagline { 
	background-color: #a8b361 !important;
	 color: #121212 !important; 
}

/* == Ecru content wrap == */
.support-ecru-wrap {
    background-color: var(--ecru);
    padding: 3rem 2rem;
}
.support-ecru-inner {
    max-width: 1100px;
    margin: 0 auto;
}

/* == Main 70 / 30 grid == */
.support-grid {
    display: grid;
    grid-template-columns: minmax(0, 7fr) minmax(0, 3fr);
    gap: 28px;
    align-items: start;
}

/* == Section headings with 40px sage underline bar == */
.section-heading {
    margin: 0 0 18px 0;
    font-size: 20px;
    color: var(--navy);
    position: relative;
    padding-bottom: 8px;
    font-weight: bold;
}
.section-heading::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 40px;
    height: 3px;
    background-color: var(--sage);
    border-radius: 2px;
}

/* == Left column stacking == */
.left-col > * + * { margin-top: 28px; }

/* == Ops grid: 2 columns, General Fund spans full width when present == */
.ops-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
}
.ops-card {
    background-color: var(--white);
    border: 1px solid #d1caba;
    border-radius: 8px;
    padding: 16px 18px;
    transition: border-color 0.2s, box-shadow 0.2s, transform 0.15s;
    display: flex;
    flex-direction: column;
}
.ops-card:hover {
    border-color: var(--sage);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}
.ops-card--wide { grid-column: 1 / -1; }
.ops-card h3 {
    color: var(--navy);
    margin: 0 0 6px 0;
    font-size: 0.9375rem;
    font-weight: bold;
    line-height: 1.3;
}
.ops-card p {
    margin: 0 0 10px 0;
    font-size: 0.8125rem;
    line-height: 1.5;
    color: var(--charcoal);
    flex: 1;
}
.ops-card a.ops-link {
    font-size: 0.75rem;
    font-weight: bold;
    color: var(--sage);
    letter-spacing: 0.5px;
    text-transform: uppercase;
    margin-top: auto;
}
.ops-card a.ops-link:hover { color: var(--navy); }

/* == Right column: Regional & Topical Funds == */
.funds-box {
    background-color: var(--white);
    border: 1px solid #d1caba;
    border-radius: 8px;
    padding: 18px 20px;
    position: sticky;
    top: 20px;
}
.funds-box h2 { margin: 0 0 14px 0; font-size: 16px; }
.funds-box h2::after { width: 34px; }
.funds-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.funds-list li { border-bottom: 1px dotted #d1caba; }
.funds-list li:last-child { border-bottom: none; }
.funds-list a {
    display: block;
    padding: 7px 4px;
    color: var(--navy);
    font-size: 0.8125rem;
    line-height: 1.4;
    transition: background-color 0.15s, padding-left 0.15s, color 0.15s;
}
.funds-list a:hover,
.funds-list a:focus {
    background-color: rgba(147,155,81,0.10);
    padding-left: 10px;
    color: var(--navy);
    outline: none;
}
.funds-list a::after {
    content: " \203A";
    color: var(--sage);
    font-weight: bold;
    opacity: 0;
    margin-left: 4px;
    transition: opacity 0.15s;
}
.funds-list a:hover::after,
.funds-list a:focus::after { opacity: 1; }

/* == Other Ways to Give: full-width cream band == */
.other-ways-section {
    background-color: var(--cream);
    border-top: 1px solid #d1caba;
    border-bottom: 1px solid #d1caba;
    padding: 48px 2rem 44px 2rem;
}
.other-ways-inner {
    max-width: 1100px;
    margin: 0 auto;
}
.other-ways-section h2 {
    margin: 0 0 24px 0;
    font-size: 20px;
    color: var(--navy);
    position: relative;
    padding-bottom: 8px;
    font-weight: bold;
    text-align: left;
}
.other-ways-section h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 40px;
    height: 3px;
    background-color: var(--sage);
    border-radius: 2px;
}

.ow-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
.ow-card {
    background-color: var(--white);
    border: 1px solid #d1caba;
    border-radius: 8px;
    padding: 18px 20px;
    transition: border-color 0.2s, box-shadow 0.2s, transform 0.15s;
}
.ow-card:hover {
    border-color: var(--sage);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}
.ow-card h3 {
    color: var(--navy);
    font-size: 0.75rem;
    margin: 0 0 10px 0;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.ow-card p {
    font-size: 0.8125rem;
    line-height: 1.5;
    color: var(--charcoal);
    margin: 0 0 6px 0;
}
.ow-card p:last-child { margin-bottom: 0; }
.ow-card a {
    color: var(--navy);
    font-weight: bold;
    text-decoration: underline dotted;
}
.ow-card a:hover { color: var(--sage); }
body.dark-mode .ow-card a { color: #e0e0e0; }
body.dark-mode .ow-card a:hover { color: var(--sage); }

.disclaimer {
    max-width: 900px;
    margin: 0 auto;
    text-align: center;
    font-size: 0.75rem;
    font-style: italic;
    color: var(--charcoal);
    line-height: 1.6;
    opacity: 0.8;
}

/* == Responsive == */
@media (max-width: 960px) {
    .support-grid { grid-template-columns: 1fr; gap: 24px; }
    .funds-box { position: static; }
    .ow-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 640px) {
    .ops-grid { grid-template-columns: 1fr; }
    .ops-card--wide { grid-column: auto; }
}
@media (max-width: 768px) {
    .page-title-band { padding: 30px 20px; }
    .page-title-band h1 { font-size: 1.75rem; }
    .support-ecru-wrap { padding: 2rem 1.25rem; }
    .other-ways-section { padding: 36px 1.25rem 32px 1.25rem; }
}
@media (max-width: 520px) {
    .ow-grid { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<!-- HEADER -->
<div id="site-header"></div>

<!-- PAGE TITLE BAND -->
<div class="page-title-band">
    <span class="tagline">JewishGen-erosity</span>
    <h1>Support JewishGen</h1>
    <p class="hero-subtitle">JewishGen, the Jewish Genealogy Research Division of the Museum of Jewish Heritage - A Living Memorial to the Holocaust, is the premier online resource for Jewish genealogy. Your gifts, which are greatly appreciated, help to ensure our continued ability to serve the ever-growing worldwide JewishGen community. Please contribute whatever you are able at this time &ndash; it will make an immediate difference.</p>
</div>

<!-- MAIN CONTENT  -  ecru wrap -->
<main class="support-ecru-wrap">
<div class="support-ecru-inner">

    <div class="support-grid">

        <!-- LEFT COLUMN: featured fundraiser + general operations -->
        <div class="left-col">

            <!-- FEATURED FUNDRAISER (fetched from separate file) -->
            <div id="featured-fundraiser" aria-live="polite"></div>

            <!-- GENERAL OPERATIONS SUPPORT -->
            <section class="ops-section" aria-labelledby="ops-heading">
                <h2 id="ops-heading" class="section-heading">General Operations Support</h2>

                <div class="ops-grid">

<!-- HIDE IF THIS IS FEATURED
                    General Fund card spans full width (grid-column: 1/-1) when shown.
                    When hidden, remaining 4 cards form a clean 2x2.
                    <article class="ops-card ops-card--wide">
                        <h3>The JewishGen General Fund</h3>
                        <p>Your gift fuels JewishGen's mission to preserve the precious history of the Jewish people, and enables us to increase the information we provide to all who have an interest in researching their Jewish ancestry.</p>
                        <a class="ops-link" href="https://jewishgen.net/donation/jewishgen-general-fund">Give Now &rsaquo;</a>
                    </article>
END HIDE -->

                    <article class="ops-card">
                        <h3>Monthly Giving &amp; Chai Societies</h3>
                        <p>Join our &ldquo;Monthly Giving Society&rdquo; with recurring automatic donations that give JewishGen predictable, stable funding. Gifts of $8.34/month qualify for Value Added Services; $18/month or more makes you a member of our &ldquo;Chai Society.&rdquo;</p>
                        <a class="ops-link" href="https://jewishgen.net/project/redirect/546">Join Now &rsaquo;</a>
                    </article>

                    <article class="ops-card">
                        <h3>Planned Giving Opportunities</h3>
                        <p>Contribute to the future of JewishGen by planning for a special kind of gift or bequest.</p>
                        <a class="ops-link" href="/jewishgen-erosity/PlannedGiving.html">Learn More &rsaquo;</a>
                    </article>

                    <article class="ops-card">
                        <h3>JewishGen Wall of Honor</h3>
                        <p>Honor someone on a special occasion, or choose a unique way to express your appreciation, and have a message displayed on our Wall of Honor (honorees will be notified).</p>
                        <a class="ops-link" href="/jewishgen-erosity/Honors/">Dedicate a Tribute &rsaquo;</a>
                    </article>

                    <article class="ops-card">
                        <h3>Dedicated Day of Research</h3>
                        <p>An amplification of the Wall of Honor: choose a specific day (anniversary, Yahrzeit, or other meaningful occasion) and have your dedication displayed on the website. A weekly email announces upcoming dedications.</p>
                        <a class="ops-link" href="/JewishGen-erosity/Honors/">Choose a Day &rsaquo;</a>
                    </article>

                </div>
            </section>
        </div>

        <!-- RIGHT COLUMN: regional & topical funds -->
        <aside class="funds-box" aria-labelledby="funds-heading">
            <h2 id="funds-heading" class="section-heading">Regional &amp; Topical Funds</h2>
            <ul class="funds-list">
                <li><a href="/RD/AustriaCzech/donate.html">Austria-Czech</a></li>
                <li><a href="/RD/Belarus/donate.html">Belarus</a></li>
                <li><a href="/RD/Bessarabia/donate.html">Bessarabia</a></li>
                <li><a href="/RD/JOWBR/donate.html">Burial Registry (JOWBR)</a></li>
                <li><a href="https://www.jewishgen.org/jewishgen-erosity/v_projectslist.asp?project_cat=52">Education Projects</a></li>
                <li><a href="/RD/France/donate.html">France</a></li>
               <li><a href="/JewishGen-erosity/valueadded.html">JewishGen General Fund</a></li>
                <li><a href="/RD/Germany/donate.html">Germany</a></li>
                <li><a href="/RD/Hungary/donate.html">Hungary</a></li>
                <li><a href="/RD/Holocaust/donate.html">Holocaust</a></li>
                <li><a href="/RD/LatAm/donate.html">Latin America Research Division</a></li>
                <li><a href="/RD/Latvia/donate.html">Latvia &amp; Estonia</a></li>
                <li><a href="https://www.jewishgen.org/jewishgen-erosity/v_projectslist.asp?project_cat=62">Peter &amp; Mary Kalikow Center</a></li>
                <li><a href="https://www.jewishgen.org/jewishgen-erosity/v_projectslist.asp?project_cat=70">Poland</a></li>
                <li><a href="/RD/Romania/donate.html">Romania</a></li>
                <li><a href="/RD/Sephardic/donate.html">Sephardic</a></li>
                <li><a href="/RD/SubCarpathia/donate.html">Subcarpathia</a></li>
                <li><a href="/RD/Ukraine/donate.html">Ukraine</a></li>
                <li><a href="/RD/USA/donate.html">USA Research Division</a></li>
                <li><a href="/jewishgen-erosity/YizkorTr/">Yizkor Book Translations</a></li>
            </ul>
        </aside>

    </div>

</div>
</main>

<!-- OTHER WAYS TO GIVE  -  full-width cream band -->
<section class="other-ways-section" aria-labelledby="other-ways-heading">
    <div class="other-ways-inner">
        <h2 id="other-ways-heading">Other Ways to Give</h2>

        <div class="ow-grid">

            <div class="ow-card">
                <h3>By Mail</h3>
                <p>Prefer to mail your gift? Send your check, payable to &ldquo;Museum of Jewish Heritage,&rdquo; to: Museum of Jewish Heritage / JewishGen, 36 Battery Place, New York, NY 10280.</p>
                <p><em>Please include &ldquo;JewishGen&rdquo; in the reference line.</em></p>
            </div>

            <div class="ow-card">
                <h3>By DAF</h3>
                <p>We accept DAF donations. <a href="https://www.jewishgen.org/daf/">Click here to start your DAF donation</a>.</p>
                <br>
                <h3>Via PayPal</h3>
                <p>If you prefer to donate via PayPal, please <a href="https://www.jewishgen.org/JewishGen-erosity/PayPal.asp">click here</a>.</p>
            </div>

            <div class="ow-card">
                <h3>Major Gifts & Development</h3>
                <p>Interested in making a significant gift or discussing naming opportunities and legacy giving?<br><br>
                    Email us at <a href="mailto:Development@mjhnyc.org" aria-label="Email development@mjhnyc.org for major gifts">development@mjhnyc.org</a></p>
            </div>

            <div class="ow-card">
                <h3>By Phone</h3>
                <p>Contact the JewishGen office at <a href="tel:+16464942972">646-494-2972</a> or email <a href="mailto:info@JewishGen.org">info@JewishGen.org</a> for a call back.</p>
            </div>

        </div>

        <p class="disclaimer">JewishGen is the Jewish Genealogy Research Division of the Museum of Jewish Heritage - A Living Memorial to the Holocaust, which is a qualified 501(c)(3) tax-exempt organization. All donations are tax-deductible to the full extent of the law. Our Tax ID# is 13-3376265.</p>
    </div>
</section>

<!-- FOOTER -->
<div id="site-footer"></div>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function loadComponent(id, file) {
    return fetch(file)
        .then(r => { if (!r.ok) throw new Error('Could not load ' + file); return r.text(); })
        .then(html => { document.getElementById(id).innerHTML = html; })
        .catch(err => console.warn(err));
}

Promise.all([
    loadComponent('site-header',         '/Header_NavBar.html'),
    loadComponent('site-footer',         '/Footer.html'),
    loadComponent('featured-fundraiser', '/JewishGen-erosity/Featured_Fundraiser.html')
]).then(() => {
    document.querySelectorAll('.jg-nav .dropbtn').forEach(btn => {
        btn.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const expanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', String(!expanded));
                const menu = document.getElementById(this.getAttribute('aria-controls'));
                if (menu) menu.style.display = expanded ? 'none' : 'block';
            }
            if (e.key === 'Escape') {
                this.setAttribute('aria-expanded', 'false');
                const menu = document.getElementById(this.getAttribute('aria-controls'));
                if (menu) menu.style.display = 'none';
            }
        });
    });
});
</script>

</body>
</html>


<% ' Error Handler:
If Err.Number <> 0 Then
	Response.Clear
%>

        <P>An error occurred in the execution of this ASP page.<BR>
 	Please report the following information to the 
 	JewishGen Support Desk:
 	</P>
 	<P>
 	<B>Page Error Object</B>
 	<UL>
 	  <LI>Page URL: 
 	      'https://www.jewishgen.org/JewishGen-erosity/index.asp' </LI>
 	  <LI>Error Number: '<%= Err.Number %>' </LI>
 	  <LI>Description:  '<%= Err.Description %>' </LI>
 	  <LI>Source:       '<%= Err.Source %>' </LI>
 	  <LI>Line Number:  '<%= Err.Line %>' </LI>
 	</UL>
 	</P>
<% 
	Err.Clear
End If
%>


<!--#include virtual="/JG/footer.txt"-->

