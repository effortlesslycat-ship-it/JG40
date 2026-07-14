<!DOCTYPE html>
<html lang="en">
<head>
    <%
    m=Month(date)
    y=Year(date)
    d=DateAdd("d",-29,date)
    d1=DateAdd("d",1,date)
    title = "JewishGen-erosity Wall of Honor"
    fromDate = d
    toDate = d1
    maxMonth = y&"-"&m
    jgid = request.cookies("jgcure")("jgid")
    adminMode=false

    If jgid=832770 or jgid=326798 or jgid=1477 or jgid=200025 Then
        adminMode=true
    End If
    %>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><%=title%> &mdash; JewishGen</title>

    <!-- 1. Bootstrap 5.3.3 (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- 2. Global Styles (Tokens, Dark Mode, Nav, Band) -->
    <link href="/jg-global.css?v=45" rel="stylesheet">

    <!-- 3. Page-specific CSS -->
    <style>
        a { text-decoration: none; color: inherit; }

        /* ------------------------------------------------------------------
           Band helper not yet in jg-global.css -- kept page-local for now,
           candidate to graduate to global post-launch.
        ------------------------------------------------------------------ */
        .jg-band-content {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* ------------------------------------------------------------------
           Wall of Honor -- masonry of plaques
        ------------------------------------------------------------------ */
        .jg-wall-masonry {
            column-count: 3;
            column-gap: 1.5rem;
            padding: 2rem 0;
        }

        .jg-plaque-card {
            break-inside: avoid;
            margin-bottom: 1.5rem;
            background-color: var(--ecru); /* uniform soft cream interior */
            border: 1.5px solid var(--sage); /* full outline; recolored per type below */
            border-radius: 8px;
            padding: 1.25rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        /* Type is carried by the outline color only; interior stays cream.
           navy/sage/charcoal all flip lighter in dark mode, so the outline
           stays visible on the dark card with no extra overrides. */
        .jg-pc--memory { border-color: var(--navy); }
        .jg-pc--honor  { border-color: var(--sage); }
        .jg-pc--thanks { border-color: var(--charcoal); }

        .jg-pc-intent {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
        }
        .jg-pc--memory .jg-pc-intent { color: var(--navy); }
        .jg-pc--honor  .jg-pc-intent { color: var(--sage); }
        .jg-pc--thanks .jg-pc-intent { color: var(--charcoal); }

        .jg-pc-honoree {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 1.3rem;
            font-weight: normal;
            color: var(--navy);
            line-height: 1.2;
            margin: 0;
        }

        .jg-pc-message {
            font-size: 0.95rem;
            color: var(--charcoal);
            line-height: 1.6;
        }

        .jg-pc-donor {
            font-size: 0.9rem;
            font-style: italic;
            color: var(--sage);
            text-align: right;
            margin-top: auto;
            border-top: 1px solid var(--light-blue);
            padding-top: 0.6rem;
        }

        /* ------------------------------------------------------------------
           MONTH NAV -- quiet strip above the wall: date range on the left,
           Prev/Next on the right. Sits on the white page (not the hero),
           so the nav buttons are navy-outlined.
        ------------------------------------------------------------------ */
        .jg-wall-monthnav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            padding: 0.25rem 0 1.25rem;
            margin-bottom: 0.5rem;
            border-bottom: 1px solid var(--light-blue);
        }
        .jg-wall-daterange {
            margin: 0;
            font-size: 0.9rem;
            font-style: italic;
            color: var(--charcoal);
        }

        /* Month nav (div role=navigation, not <nav>) */
        .jg-wall-pagination {
            display: flex;
            gap: 0.75rem;
        }
        .jg-wall-pagination .btn {
            background-color: transparent;
            color: #09497a;            /* navy hardcoded; var(--navy) flips */
            border-color: #09497a;
        }
        .jg-wall-pagination .btn:hover {
            background-color: #09497a;
            border-color: #09497a;
            color: #ffffff;
        }
        body.dark-mode .jg-wall-pagination .btn {
            color: #e0e0e0;
            border-color: #e0e0e0;
        }
        body.dark-mode .jg-wall-pagination .btn:hover {
            background-color: #e0e0e0;
            border-color: #e0e0e0;
            color: #121212;
        }
        @media (max-width: 600px) {
            .jg-wall-monthnav { justify-content: center; text-align: center; }
        }

        /* ------------------------------------------------------------------
           DEDICATION BANNER -- compact cream donation banner. Borrows the
           Featured Fundraiser DNA (cream card, serif heading, soft shadow)
           but slimmer, centered, no eyebrow, sage top-accent + navy CTA.
        ------------------------------------------------------------------ */
        .jg-dedicate-banner {
            background-color: var(--cream);
            border-top: 4px solid var(--sage);
            border-radius: 8px;
            padding: 2.25rem 2rem 2.5rem;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.07);
            margin: 1.75rem 0 2.25rem;
        }
        body.dark-mode .jg-dedicate-banner {
            background-color: #1e1e1e;
            border-top-color: #a8b361;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.35);
        }
        .jg-dedicate-heading {
            color: var(--navy);
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 1.6rem;
            font-weight: normal;
            line-height: 1.2;
            margin: 0 0 12px 0;
        }
        body.dark-mode .jg-dedicate-heading { color: #ffffff; }
        .jg-dedicate-copy {
            color: var(--charcoal);
            font-size: 0.95rem;
            line-height: 1.6;
            margin: 0 auto 22px;
            max-width: 620px;
        }
        body.dark-mode .jg-dedicate-copy { color: #c8c8c8; }
        /* Navy CTA hardcoded; var(--navy) flips to light gray in dark mode. */
        .jg-dedicate-cta {
            display: inline-block;
            background-color: #09497a;
            color: #ffffff;
            font-size: 0.9375rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 13px 34px;
            border-radius: 8px;
            text-decoration: none;
            transition: filter 0.15s ease, transform 0.15s ease;
        }
        .jg-dedicate-cta:hover {
            filter: brightness(1.15);
            transform: translateY(-1px);
            color: #ffffff;
        }
        .jg-dedicate-cta:focus {
            outline: 3px solid var(--light-green);
            outline-offset: 2px;
            color: #ffffff;
        }
        body.dark-mode .jg-dedicate-cta {
            background-color: #0d2a45;
            color: #ffffff;
        }
        body.dark-mode .jg-dedicate-cta:hover,
        body.dark-mode .jg-dedicate-cta:focus { color: #ffffff; }

        /* Concept credit + back links */
        .jg-wall-credit {
            text-align: center;
            color: var(--charcoal);
            font-size: 0.9rem;
            padding: 1.5rem 0 0.5rem;
        }
        .jg-wall-backlinks {
            display: flex;
            justify-content: center;
            gap: 2rem;
            padding: 0.5rem 0 2.5rem;
            font-size: 0.9rem;
        }
        .jg-wall-backlinks a { color: var(--navy); }
        .jg-wall-backlinks a:hover { color: var(--sage); }
        body.dark-mode .jg-wall-backlinks a:hover { color: #a8b361; }

        .jg-wall-empty {
            text-align: center;
            color: var(--charcoal);
            padding: 3rem 0;
            font-style: italic;
        }

        /* Responsive degradation */
        @media (max-width: 992px) { .jg-wall-masonry { column-count: 2; } }
        @media (max-width: 768px) { .jg-wall-masonry { column-count: 1; } }

<%If adminMode Then %>
        /* ------------------------------------------------------------------
           ADMIN-ONLY UI (edit modal, per-card actions, CSV)
        ------------------------------------------------------------------ */
        .jg-pc-admin {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
            border-top: 1px dashed var(--light-blue);
            padding-top: 0.6rem;
        }
        .jg-pc-admin button {
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.3rem 0.8rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .greenBtn { background: var(--sage); color: #ffffff; }
        .greenBtn:hover { background: var(--navy); }
        .salBtn { background: #b03a2e; color: #ffffff; }
        .salBtn:hover { background: #922c22; }
        .blueBtn { background: var(--navy); color: #ffffff; }
        .blueBtn:hover { background: var(--sage); }

        .jg-admin-bar {
            display: flex;
            justify-content: flex-end;
            padding: 0.5rem 0 1.5rem;
        }
        .jg-admin-bar button {
            font-size: 0.85rem;
            font-weight: 600;
            padding: 0.45rem 1.1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0; top: 0;
            width: 100%; height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.45);
        }
        .modal-content {
            background-color: var(--white);
            color: var(--charcoal);
            margin: 5% auto;
            padding: 1.5rem;
            border: 1px solid var(--light-blue);
            border-radius: 4px;
            width: 520px;
            max-width: 92%;
        }
        body.dark-mode .modal-content { background-color: #1e1e1e; }
        .modal-content label {
            min-width: 150px;
            display: inline-block;
            font-size: 0.95rem;
            font-weight: 600;
        }
        .modal-content input[type=text],
        .modal-content select,
        .modal-content textarea {
            display: inline-block;
            font-size: 0.95rem;
            min-width: 300px;
            max-width: 100%;
            margin: 5px 0;
            border: 1px solid var(--light-blue);
            border-radius: 4px;
            padding: 0.4rem;
        }
        .close { color: var(--charcoal); float: right; font-size: 1.6rem; font-weight: bold; }
        .close:hover, .close:focus { color: var(--navy); text-decoration: none; cursor: pointer; }
<%End If%>
    </style>
</head>
<body>

    <!-- Shared Header Injected Here -->
    <div id="site-header"></div>

    <!-- Standard Canonical Page Title Band -->
      <div class="page-title-band">
    <div class="page-title-band__inner"> <a href="/jewishgen-erosity/" class="jg-back-link-light" aria-label="Back to JewishGen-erosity">
            <span class="arrow" aria-hidden="true">&larr;</span> Back to JewishGen-erosity
        </a><br>
        <span class="tagline">JewishGen-erosity</span>
        <h1>Wall of Honor</h1>
        <p class="hero-subtitle">&quot;To honor, to thank, to cherish the memory&quot;</p>
    </div>
</div>

    <div class="container jg-band-content">

        <!-- Dedication banner -->
        <section class="jg-dedicate-banner" aria-labelledby="dedicate-title">
            <h2 id="dedicate-title" class="jg-dedicate-heading">Honor Someone with a Contribution</h2>
            <p class="jg-dedicate-copy">Dedicate a gift in memory, in honor, or in thanks &mdash; and add a name to the Wall.</p>
            <a class="jg-dedicate-cta" href="/JewishGen-erosity/honors.asp">Donate Now</a>
        </section>

        <!-- Month nav + what-you're-viewing, sits just above the wall -->
        <div class="jg-wall-monthnav">
            <p class="jg-wall-daterange" id="stat-line"></p>
            <div role="navigation" class="jg-wall-pagination">
                <a href="#" id="prev-btn" class="btn btn-sm" onclick="_30minus();return false;">&larr; Previous Month</a>
                <a href="#" id="next-btn" class="btn btn-sm" onclick="_30plus();return false;">Next Month &rarr;</a>
            </div>
        </div>

        <!-- Plaques injected by loadWoH() -->
        <div id="WallOfHonor"></div>

<%If adminMode Then %>
        <div class="jg-admin-bar">
            <button class="blueBtn" type="button" id="csv_btn" onclick="exportCSV();">Download CSV</button>
        </div>
<%End If%>

        <div class="jg-wall-credit">
            JewishGen Wall of Honor original concept by Susana Leistner Bloch
        </div>

    </div>

<%If adminMode Then %>
    <div id="myModal" class="modal">
      <div class="modal-content">
        <span class="close" onclick="document.getElementById('myModal').style.display = 'none'">&times;</span>
        <form id="honor">
            <input class="formVal" type="hidden" id="id" name="id"/>
            <input class="formVal" type="hidden" id="action" name="action"/>
            <label for="type">Type:</label>
            <select class="formVal" name="type" id="type">
              <option value="Honor">In Honor Of</option>
              <option value="Memory">In Memory Of</option>
              <option value="Thanks">Special Thanks To</option>
            </select><br>
            <label for="honoree">Honoree:</label>
            <input class="formVal" type="text" id="honoree" name="honoree" value=""/><br>
            <label for="reason">Reason or Occasion:</label><br>
            <textarea class="formVal" id="reason" name="reason" cols="37" rows="5" style="resize: none;"></textarea><br>
            <label for="fromName">From:</label>
            <input class="formVal" type="text" id="fromName" name="fromName" value=""/><br>
            <hr>
            <div style="text-align:center;">
                <button class="greenBtn" type="button" onclick="doApply();">Apply</button>
                <button class="salBtn" type="button" onclick="document.getElementById('myModal').style.display = 'none'">Cancel</button>
            </div>
        </form>
      </div>
    </div>
<%End If%>

    <!-- Shared Footer Injected Here -->
    <div id="site-footer"></div>

    <!-- Component Loading Script -->
    <script>
        function loadComponent(id, file) {
            return fetch(file)
                .then(response => response.text())
                .then(html => { document.getElementById(id).innerHTML = html; })
                .catch(err => console.warn('Something went wrong loading ' + file, err));
        }
        Promise.all([
            loadComponent('site-header', '/Header_NavBar.html'),
            loadComponent('site-footer', '/Footer.html')
        ]).then(function () {
            // header/footer init (if any) runs here, after innerHTML is injected
        });
    </script>

    <!-- Wall of Honor logic -->
    <script>
        var data;
        const today = new Date('<%=toDate%>');
        var _fromDate = new Date('<%=fromDate%>');
        var _toDate   = new Date('<%=toDate%>');

        function formatDate(d) {
            const month = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
            return d.getDate() + "-" + month[d.getMonth()] + "-" + d.getFullYear();
        }

        function setStatLine() {
            var el = document.getElementById("stat-line");
            if (el) el.textContent = "Showing dedications from " + formatDate(_fromDate) + " to " + formatDate(_toDate);
        }

        function _30plus() {
            _fromDate.setDate(_fromDate.getDate() + 30);
            _toDate.setDate(_toDate.getDate() + 30);
            if (_toDate >= today) {
                document.getElementById("next-btn").style.display = "none";
            }
            loadWoH(_fromDate, _toDate);
        }

        function _30minus() {
            _toDate.setDate(_toDate.getDate() - 30);
            _fromDate.setDate(_fromDate.getDate() - 30);
            document.getElementById("next-btn").style.display = "inline-block";
            loadWoH(_fromDate, _toDate);
        }

<%If adminMode Then %>
        function doEdit(idx) {
            document.getElementById("id").value      = data.data[idx].id;
            document.getElementById("action").value  = "edit";
            document.getElementById("honoree").value = data.data[idx].Honoree;
            document.getElementById("type").value    = data.data[idx].Type;
            document.getElementById("reason").value  = data.data[idx].ReasonOccasion;
            document.getElementById("fromName").value = data.data[idx]._from;
            document.getElementById("myModal").style.display = "block";
        }

        function doApply() {
            var elements = document.getElementsByClassName("formVal");
            var formData = new FormData();
            for (var i = 0; i < elements.length; i++) {
                formData.append(elements[i].name, elements[i].value);
            }
            var xmlHttp = new XMLHttpRequest();
            xmlHttp.onreadystatechange = function () {
                if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
                    loadWoH(_fromDate, _toDate);
                    document.getElementById('myModal').style.display = 'none';
                }
            };
            xmlHttp.open("post", "./edit.php");
            xmlHttp.send(formData);
        }

        function doRemove(id) {
            var r = confirm("Pls confirm you want to hide the record for " + data.data[id].Honoree + "?");
            if (r == true) {
                var formData = new FormData();
                formData.append("id", data.data[id].id);
                formData.append("action", "delete");
                var xmlHttp = new XMLHttpRequest();
                xmlHttp.onreadystatechange = function () {
                    if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
                        loadWoH(_fromDate, _toDate);
                    }
                };
                xmlHttp.open("post", "./edit.php");
                xmlHttp.send(formData);
            }
        }

        function exportCSV() {
            var csvData = 'Type,Honoree,ReasonOccasion,From,ID\n';
            csvData += data.data.map(function (d) {
                return JSON.stringify(Object.values(d));
            }).join('\n').replace(/(^\[)|(\]$)/mg, '');
            var a = document.createElement('a');
            a.setAttribute('style', 'display:none;');
            document.body.appendChild(a);
            var blob = new Blob(["\ufeff", csvData], { type: 'text/csv' });
            a.href = window.URL.createObjectURL(blob);
            a.download = 'Honors.csv';
            a.click();
        }
<%End If%>

        function loadWoH(from, to) {
            setStatLine();
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    try {
                        data = JSON.parse(xmlhttp.responseText);
                        var wall = document.getElementById("WallOfHonor");
                        wall.innerHTML = "";

                        if (!data.data || data.data.length === 0) {
                            wall.innerHTML = '<div class="jg-wall-empty">No honors recorded for this period.</div>';
                            return;
                        }

                        var masonry = document.createElement("div");
                        masonry.className = "jg-wall-masonry";

                        data.data.forEach(function (item, index) {
                            var typeClass = "jg-pc--thanks";
                            var intentText = "Special Thanks To";
                            if (item.Type == "Memory") {
                                typeClass = "jg-pc--memory";
                                intentText = "In Memory Of";
                            } else if (item.Type == "Honor") {
                                typeClass = "jg-pc--honor";
                                intentText = "In Honor Of";
                            }

                            var card = document.createElement("div");
                            card.className = "jg-plaque-card " + typeClass;

                            var intent = document.createElement("div");
                            intent.className = "jg-pc-intent";
                            intent.textContent = intentText;
                            card.appendChild(intent);

                            var honoree = document.createElement("h2");
                            honoree.className = "jg-pc-honoree";
                            honoree.innerHTML = item.Honoree;
                            card.appendChild(honoree);

                            if (item.ReasonOccasion && item.ReasonOccasion.trim() !== "") {
                                var msg = document.createElement("div");
                                msg.className = "jg-pc-message";
                                msg.innerHTML = item.ReasonOccasion;
                                card.appendChild(msg);
                            }

                            var donor = document.createElement("div");
                            donor.className = "jg-pc-donor";
                            donor.innerHTML = "From " + item._from;
                            card.appendChild(donor);

<%If adminMode Then %>
                            var admin = document.createElement("div");
                            admin.className = "jg-pc-admin";
                            var editBtn = document.createElement("button");
                            editBtn.className = "greenBtn";
                            editBtn.textContent = "Edit";
                            editBtn.addEventListener("click", function () { doEdit(index); });
                            admin.appendChild(editBtn);
                            var hideBtn = document.createElement("button");
                            hideBtn.className = "salBtn";
                            hideBtn.textContent = "Hide";
                            hideBtn.addEventListener("click", function () { doRemove(index); });
                            admin.appendChild(hideBtn);
                            card.appendChild(admin);
<%End If%>

                            masonry.appendChild(card);
                        });

                        wall.appendChild(masonry);
                    } catch (err) {
                        console.log(err.message + " in " + xmlhttp.responseText);
                        return;
                    }
                }
            };
            data = {};
            xmlhttp.open("GET", "../gethonors.asp?from=" + from.toLocaleDateString('en-US') + "&to=" + to.toLocaleDateString('en-US'), true);
            xmlhttp.send();
        }

        (function () { loadWoH(_fromDate, _toDate); })();
    </script>
</body>
</html>
