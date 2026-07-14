/* =====================================================
   jg-jos.js -- inline results for JOS tool pages
   Submits a tool form in the background (format=fragment)
   and injects the returned results into a panel on the
   same page, instead of navigating to a results page.
   Progressive enhancement: if JS is off, the form's
   normal action still loads the full results page.
   CHW
   ===================================================== */
function josInlineResults(opts) {
    var form    = document.querySelector(opts.form);
    var results = document.querySelector(opts.results);
    var endpoint = opts.endpoint;
    var match    = opts.match;   /* substring identifying same-tool links */
    if (!form || !results) { return; }

    function show(html) {
        results.innerHTML = '<div class="jos-results-card">' + html + '</div>';
        results.hidden = false;
        results.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    function state(cls, msg) {
        results.innerHTML = '<div class="jos-results__state ' + cls + '">' + msg + '</div>';
        results.hidden = false;
    }
    function loading() {
        state('', '<div class="jos-results__spinner" aria-hidden="true"></div>' +
            '<div>Calculating&hellip;</div>');
    }
    function run(fd) {
        loading();
        fd.append('format', 'fragment');
        fetch(endpoint, { method: 'POST', body: fd })
            .then(function(r) {
                if (!r.ok) { throw new Error('Server returned ' + r.status); }
                return r.text();
            })
            .then(function(html) { show(html); })
            .catch(function(e) {
                state('jos-results__error',
                    'Something went wrong running the calculation (' +
                    e.message + '). Please try again.');
            });
    }

    /* Submit the form inline */
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        run(new FormData(form));
    });

    /* Re-run inline when a results link points back to this tool
       (e.g. the Festival Dates previous/next-year arrows) */
    results.addEventListener('click', function(e) {
        var a = e.target.closest ? e.target.closest('a') : null;
        if (!a) { return; }
        var href = a.getAttribute('href') || '';
        if (!match || href.indexOf(match) === -1) { return; }
        var qi = href.indexOf('?');
        if (qi === -1) { return; }
        e.preventDefault();
        var params = new URLSearchParams(href.substring(qi + 1));
        var fd = new FormData();
        params.forEach(function(v, k) { fd.append(k, v); });
        run(fd);
    });
}
