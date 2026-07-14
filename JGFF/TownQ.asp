<%
' /JGFF/TownQ.asp -- CHW JG40 redesign
' Questionable Town Query Form.
' Login required. Backend logic preserved from original.
%>
<!--#include virtual="/JG/utils/security.asp"-->
<%
jgid    = request.cookies("jgcure")("jgid")
em      = request.cookies("jgcure")("email")
contact = request.cookies("jgcure")("contact")

q_town    = Request.Querystring("town")
q_country = Request.Querystring("country")
%>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Questionable Town Query &ndash; JewishGen Family Finder</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="/jg-global.css">
<style>
/* == JGFF TownQ -- CHW ===================================== */
.page-title-band {
    background-color: #09497a;
    padding: 44px 50px;
    text-align: center;
}
body.dark-mode .page-title-band { background-color: #0d2a45; }
.page-title-band h1 {
    margin: 0 0 8px 0;
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 2rem;
    font-weight: normal;
    color: #ffffff;
}
.page-title-band p.hero-subtitle {
    margin: 0 auto;
    font-size: 0.9375rem;
    color: rgba(255,255,255,0.85);
    max-width: 640px;
    line-height: 1.6;
}
.jgff-ecru-wrap { background-color: var(--ecru); padding: 3rem 2rem; }
.jgff-ecru-inner { max-width: 860px; margin: 0 auto; }
.jgff-intro {
    background-color: var(--white);
    border: 1px solid #d1caba;
    border-left: 4px solid var(--sage);
    border-radius: 0 8px 8px 0;
    padding: 18px 22px;
    font-size: 0.9375rem;
    line-height: 1.7;
    color: var(--charcoal);
    margin-bottom: 24px;
}
body.dark-mode .jgff-intro { background-color: #1e1e1e; border-color: #333; border-left-color: #a8b361; color: #a0a0a0; }
.jgff-intro a { color: var(--navy); font-weight: bold; }
.jgff-card {
    background-color: var(--white);
    border: 1px solid #d1caba;
    border-radius: 8px;
    padding: 28px 32px;
}
body.dark-mode .jgff-card { background-color: #1e1e1e; border-color: #333; }
.jgff-section-heading {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 1rem;
    font-weight: bold;
    color: var(--navy);
    margin: 0 0 14px 0;
    padding-bottom: 7px;
    border-bottom: 1px solid #d1caba;
    position: relative;
}
body.dark-mode .jgff-section-heading { color: #e0e0e0; border-bottom-color: #333; }
.jgff-section-heading::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    width: 32px;
    height: 3px;
    background-color: var(--sage);
    border-radius: 2px;
}
.jgff-section { margin-bottom: 28px; }
.jgff-readonly-row {
    display: flex;
    font-size: 0.875rem;
    padding: 7px 0;
    border-bottom: 1px dotted #d1caba;
    color: var(--charcoal);
}
body.dark-mode .jgff-readonly-row { border-bottom-color: #333; color: #a0a0a0; }
.jgff-readonly-row:last-child { border-bottom: none; }
.jgff-readonly-label {
    flex: 0 0 180px;
    font-weight: bold;
    color: var(--navy);
}
body.dark-mode .jgff-readonly-label { color: #e0e0e0; }
.jgff-field-group { margin-bottom: 18px; }
.jgff-field-group label {
    display: block;
    font-size: 0.875rem;
    font-weight: bold;
    color: var(--navy);
    margin-bottom: 5px;
}
body.dark-mode .jgff-field-group label { color: #e0e0e0; }
.jgff-field-group label .hint {
    display: block;
    font-weight: normal;
    font-size: 0.8125rem;
    color: var(--charcoal);
    margin-top: 2px;
    line-height: 1.5;
}
body.dark-mode .jgff-field-group label .hint { color: #888; }
.jgff-field-group label span.req { color: #c0392b; }
.jgff-field-group input[type="text"],
.jgff-field-group textarea {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #c5bca8;
    border-radius: 5px;
    font-size: 0.9375rem;
    font-family: inherit;
    background-color: var(--white);
    color: var(--charcoal);
    transition: border-color 0.2s, box-shadow 0.2s;
}
body.dark-mode .jgff-field-group input[type="text"],
body.dark-mode .jgff-field-group textarea {
    background-color: #2a2a2a;
    color: #e0e0e0;
    border-color: #444;
}
.jgff-field-group input[type="text"]:focus,
.jgff-field-group textarea:focus {
    border-color: var(--sage);
    outline: none;
    box-shadow: 0 0 0 2px rgba(147,155,81,0.2);
}
.jgff-field-group textarea { resize: vertical; }
.jgff-submit-btn {
    background-color: #09497a;
    color: #ffffff;
    border: none;
    border-radius: 5px;
    padding: 10px 32px;
    font-size: 1rem;
    font-weight: bold;
    cursor: pointer;
    font-family: inherit;
    transition: filter 0.2s;
}
.jgff-submit-btn:hover { filter: brightness(1.1); }
.jgff-submit-btn:focus { outline: 3px solid var(--sage); outline-offset: 2px; }
body.dark-mode .jgff-submit-btn { background-color: #0d2a45; }
.jgff-sidebar {
    position: sticky;
    top: 20px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}
.jgff-grid {
    display: grid;
    grid-template-columns: minmax(0, 7fr) minmax(0, 3fr);
    gap: 28px;
    align-items: start;
}
@media (max-width: 768px) {
    .jgff-grid { grid-template-columns: 1fr; }
    .jgff-sidebar { position: static; }
    .page-title-band { padding: 32px 24px; }
    .page-title-band h1 { font-size: 1.6rem; }
    .jgff-ecru-wrap { padding: 2rem 1rem; }
    .jgff-readonly-label { flex: 0 0 140px; }
}
</style>
</head>
<body>

<div id="site-header"></div>

<div class="page-title-band" role="banner">
    <span class="tagline">JewishGen Family Finder</span>
    <h1>Questionable Town Query</h1>
    <p class="hero-subtitle">Use this form if the JGFF has rejected your town entry and you believe it should be accepted.</p>
</div>

<div class="jgff-ecru-wrap">
    <div class="jgff-ecru-inner">
        <div class="jgff-grid">

            <div>
                <div class="jgff-intro">
                    The JGFF uses only <em>modern native town names</em> and rejects any town that has not previously been verified.
                    If you feel your entry is correct, please review
                    <a href="/JGFF/FAQ/#q4.2">JGFF FAQ Question 4.2</a>
                    for naming rules, then complete and submit the form below.
                </div>

                <div class="jgff-card">
                    <form action="jgffemailsend.asp" method="post" onload="initEvents();">

                        <!-- About yourself -->
                        <div class="jgff-section">
                            <h2 class="jgff-section-heading">About yourself</h2>
                            <div class="jgff-readonly-row">
                                <span class="jgff-readonly-label">Your name</span>
                                <span><%=contact%></span>
                            </div>
                            <div class="jgff-readonly-row">
                                <span class="jgff-readonly-label">Your email address</span>
                                <span><%=em%></span>
                            </div>
                            <div class="jgff-readonly-row">
                                <span class="jgff-readonly-label">Your JewishGen ID #</span>
                                <span><%=jgid%></span>
                            </div>
                        </div>

                        <!-- About the town -->
                        <div class="jgff-section">
                            <h2 class="jgff-section-heading">About the town</h2>

                            <div class="jgff-field-group">
                                <label for="tq-town">
                                    Ancestral town name <span class="req" aria-hidden="true">*</span>
                                </label>
                                <input type="text"
                                       id="tq-town"
                                       name="town"
                                       maxlength="30"
                                       required
                                       aria-required="true"
                                       value="<%=q_town%>">
                            </div>

                            <div class="jgff-field-group">
                                <label for="tq-country">
                                    Country <span class="req" aria-hidden="true">*</span>
                                </label>
                                <input type="text"
                                       id="tq-country"
                                       name="country"
                                       maxlength="30"
                                       required
                                       aria-required="true"
                                       value="<%=q_country%>">
                            </div>

                            <div class="jgff-field-group">
                                <label for="tq-source">
                                    Genealogical source <span class="req" aria-hidden="true">*</span>
                                    <span class="hint">Why do you believe your Jewish ancestors came from this town?
                                    What personal family document shows this town name? In what language and year?
                                    Or is this from oral tradition?</span>
                                </label>
                                <textarea id="tq-source"
                                          name="source"
                                          rows="5"
                                          required
                                          aria-required="true"></textarea>
                            </div>

                            <div class="jgff-field-group">
                                <label for="tq-comments">
                                    Geographic information
                                    <span class="hint">What region or province is this town in? What towns or features is it near?</span>
                                </label>
                                <textarea id="tq-comments"
                                          name="comments"
                                          rows="3"></textarea>
                            </div>

                            <div class="jgff-field-group">
                                <label for="tq-maps">
                                    Map or gazetteer citations
                                    <span class="hint">If you have seen this town on a map, please cite the map or gazetteer.</span>
                                </label>
                                <textarea id="tq-maps"
                                          name="maps"
                                          rows="3"></textarea>
                            </div>
                        </div>

                        <!-- About the surname -->
                        <div class="jgff-section">
                            <h2 class="jgff-section-heading">About the surname</h2>

                            <div class="jgff-field-group">
                                <label for="tq-surname">
                                    Surname you wish to add to the JGFF <span class="req" aria-hidden="true">*</span>
                                </label>
                                <input type="text"
                                       id="tq-surname"
                                       name="surname"
                                       maxlength="20"
                                       required
                                       aria-required="true">
                            </div>
                        </div>

                        <!-- Hidden fields -->
                        <input type="hidden" name="From"  value="<%=contact%>">
                        <input type="hidden" name="email" value="<%=em%>">
                        <input type="hidden" name="jgid"  value="<%=jgid%>">
                        <script>
                        document.write('<input type="hidden" name="Ref" value="' + document.referrer + '">');
                        </script>

                        <button type="submit" class="jgff-submit-btn">Send query</button>

                    </form>
                </div>
            </div>

            <aside class="jgff-sidebar" aria-label="JGFF navigation">
                <div id="jgff-subnav" data-jgff-page="home"></div>
            </aside>

        </div>
    </div>
</div>

<div id="site-footer"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/JG/Scripts/HighlightForm.js"></script>
<script src="/jg/scripts/jg-jgff.js"></script>
<script>
function loadComponent(id, file) {
    return fetch(file)
        .then(function(r){ if(!r.ok) throw new Error('Cannot load '+file); return r.text(); })
        .then(function(h){ document.getElementById(id).innerHTML=h; })
        .catch(function(e){ console.warn(e); });
}
Promise.all([
    loadComponent('site-header', '/Header_NavBar.html'),
    loadComponent('site-footer', '/Footer.html'),
    loadComponent('jgff-subnav', '/JGFF/jgff-subnav.html')
]).then(function() {
    initJgffSubnav();
    if (typeof initEvents === 'function') { initEvents(); }
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
</script>

</body>
</html>
