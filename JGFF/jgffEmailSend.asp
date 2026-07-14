<%
' /JGFF/jgffEmailSend.asp -- CHW JG40 redesign
' Response handler for TownQ.asp.
' Backend logic preserved from original unchanged.

l_fromEmail = Trim(Request.Form("email"))
l_fromName  = Trim(Request.Form("from"))
l_jgid      = Trim(Request.Form("jgid"))
l_town      = Trim(Request.Form("town"))
l_maps      = Trim(Request.Form("maps"))
l_country   = Trim(Request.Form("country"))
l_surname   = Trim(Request.Form("surname"))
l_source    = Trim(Request.Form("source"))
l_comments  = Trim(Request.Form("comments"))

If l_jgid="" Or IsNull(l_jgid) Or Not IsNumeric(l_jgid) Then
    Response.write ("<H2>Invalid access.</H2>")
    Response.End
End If

If l_fromemail = "" Or IsNull(l_fromemail) Then blank_response("Your email address")
If l_town = ""     Or IsNull(l_town)       Then blank_response("Ancestral Town Name")
If l_country = ""  Or IsNull(l_country)    Then blank_response("Ancestral Country Name")
If l_surname = ""  Or IsNull(l_surname)    Then blank_response("Ancestral Surname")
If l_source = ""   Or IsNull(l_source)     Then blank_response("Personal Source")

l_sendtoEmail    = "jgffhelp@jewishgen.org"
l_sendtoFullName = "JGFF Help"

subj = "JGFF Town Query - " & l_town & ", " & l_country

l_backlink = Request.Form("Ref")
If l_backlink = "" Or IsNull(l_backlink) Then l_backlink = "https://www.jewishgen.org/jgff/"

l_referrer = Request.ServerVariables("HTTP_REFERER")

unspec_var l_source
unspec_var l_maps
unspec_var l_comments
unspec_var l_backlink
unspec_var l_referrer

sBody = ""
sBody = sBody & "- From:               " & l_fromName & VbCrLf
sBody = sBody & "- Email:              " & l_fromEmail & VbCrLf
sBody = sBody & "- JewishGen ID#:      " & l_jgid & VbCrLf
sBody = sBody & "" & VbCrLf
sBody = sBody & "- Ancestral Town:     " & l_town & VbCrLf
sBody = sBody & "- Ancestral Country:  " & l_country & VbCrLf
sBody = sBody & "- Ancestral Surname:  " & l_surname & VbCrLf
sBody = sBody & "" & VbCrLf
sBody = sBody & "- Personal Source: " & l_source & VbCrLf
sBody = sBody & "" & VbCrLf
sBody = sBody & "- Geographic Info: " & l_comments & VbCrLf
sBody = sBody & "" & VbCrLf
sBody = sBody & "- Maps/Gazetteers: " & l_maps & VbCrLf
sBody = sBody & "" & VbCrLf
sBody = sBody & "------------------------------------------------------------" & VbCrLf
sBody = sBody & "  HTTP_USER_AGENT: " & Request.ServerVariables("HTTP_USER_AGENT") & VbCrLf
sBody = sBody & "  REMOTE_ADDR:     " & Request.ServerVariables("REMOTE_ADDR") & VbCrLf
sBody = sBody & "  HTTP_REFERER:    " & Request.ServerVariables("HTTP_REFERER") & VbCrLf
sBody = sBody & "Previous URL:      " & l_backlink & VbCrLf

jgffe = " &nbsp; [<A HREF='https://www.jewishgen.org/jgff/jgfflist.php?code=" & l_jgid & "'>JGFF Entries</A>]"

HTML_footer = "" _
  & "<TABLE align='center' border='0' cellpadding='0' cellspacing='0' width='100%'>" _
  & "<TR><TD align='center' bgcolor='#FFFFFF'>" _
  & "<span style='color: #134B7D; font-size: 10px; font-family: Verdana'>" _
  & "<B>JewishGen</B> | 36 Battery Place | New York, NY 10280 | " _
  & "<a href='https://www.JewishGen.org'>www.JewishGen.org</a>" _
  & "</span></TD></TR></TABLE>"

hB = "<HTML><HEAD></HEAD><BODY>" & VbCrLf
hB = hB & "<TABLE>" & VbCrLf
hB = hB & "<TR><TD>From: </TD><TD>" & l_fromName & ", " & l_fromEmail & "</TD></TR>" & VbCrLf
hB = hB & "<TR><TD>JewishGen ID#: </TD><TD>" & l_jgid & jgffe & "</TD></TR>" & VbCrLf
hB = hB & "<TR><TD COLSPAN='2'><HR></TD></TR>" & VbCrLf
hB = hB & "<TR><TD>Ancestral Town:    </TD><TD>" & l_town    & "</TD></TR>" & VbCrLf
hB = hB & "<TR><TD>Ancestral Country: </TD><TD>" & l_country & "</TD></TR>" & VbCrLf
hB = hB & "<TR><TD>Surname:           </TD><TD>" & l_surname & "</TD></TR>" & VbCrLf
hB = hB & "<TR><TD COLSPAN='2'><HR></TD></TR>" & VbCrLf
hB = hB & "<TR><TD>Personal Source:   </TD><TD>" & l_source   & "</TD></TR>" & VbCrLf
hB = hB & "<TR><TD COLSPAN='2'><HR></TD></TR>" & VbCrLf
hB = hB & "<TR><TD>Geographic Info:   </TD><TD>" & l_comments & "</TD></TR>" & VbCrLf
hB = hB & "<TR><TD COLSPAN='2'><HR></TD></TR>" & VbCrLf
hB = hB & "<TR><TD>Maps/Gazetteers:   </TD><TD>" & l_maps     & "</TD></TR>" & VbCrLf
hB = hB & "</TABLE>" & VbCrLf
hB = hB & "<HR>" & VbCrLf & HTML_footer & VbCrLf & "<HR><BR>" & VbCrLf
hB = hB & "<B>Server Variables:</B><UL>" & VbCrLf
hB = hB & "<LI>HTTP_USER_AGENT: " & Request.ServerVariables("HTTP_USER_AGENT") & VbCrLf
hB = hB & "<LI>REMOTE_ADDR:     " & Request.ServerVariables("REMOTE_ADDR") & VbCrLf
hB = hB & "<LI>HTTP_REFERER:    " & Request.ServerVariables("HTTP_REFERER") & VbCrLf
hB = hB & "</UL>Previous URL: " & l_backlink & VbCrLf
hB = hB & "</BODY></HTML>" & VbCrLf

Set objJMail = Server.CreateObject("JMail.Message")
With objJMail
    .Logging = True
    .Silent  = True
    .From     = l_fromEmail
    .FromName = l_fromName
    .AddRecipient l_sendtoemail, l_sendtofullname
    .AddRecipient "support@jewishgen.org", "JewishGen Support"
    .AddRecipient "gsandler@jewishgen.org", "Gary Sandler"
    .Subject  = subj
    .HTMLBody = hB
    .ContentTransferEncoding = "7bit"
    If Not .Send("mail.jewishgen.int") Then
        Response.Write ("Mail send error: <pre>" & .Log & "</pre>")
    End If
End With
%>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Query Submitted &ndash; JewishGen Family Finder</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="/jg-global.css">
<style>
/* == JGFF TownQ confirmation -- CHW ======================== */
.page-title-band {
    background-color: #09497a;
    padding: 44px 50px;
    text-align: center;
}
body.dark-mode .page-title-band { background-color: #0d2a45; }
.page-title-band h1 {
    margin: 0;
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 2rem;
    font-weight: normal;
    color: #ffffff;
}
.jgff-ecru-wrap { background-color: var(--ecru); padding: 3rem 2rem; min-height: 50vh; }
.jgff-ecru-inner { max-width: 740px; margin: 0 auto; }
.jgff-card {
    background-color: var(--white);
    border: 1px solid #d1caba;
    border-radius: 8px;
    padding: 36px 40px;
    text-align: center;
}
body.dark-mode .jgff-card { background-color: #1e1e1e; border-color: #333; }
.jgff-success-icon {
    width: 52px;
    height: 52px;
    background-color: #09497a;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 18px;
    color: #ffffff;
    font-size: 24px;
}
body.dark-mode .jgff-success-icon { background-color: #0d2a45; }
.jgff-card h2 {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 1.375rem;
    font-weight: normal;
    color: var(--navy);
    margin: 0 0 14px 0;
}
body.dark-mode .jgff-card h2 { color: #e0e0e0; }
.jgff-card p {
    font-size: 0.9375rem;
    color: var(--charcoal);
    line-height: 1.7;
    margin: 0 0 14px 0;
}
body.dark-mode .jgff-card p { color: #a0a0a0; }
.jgff-card p:last-child { margin-bottom: 0; }
.jgff-back-btn {
    display: inline-block;
    background-color: #09497a;
    color: #ffffff;
    border-radius: 5px;
    padding: 9px 24px;
    font-size: 0.9375rem;
    font-weight: bold;
    text-decoration: none;
    margin-top: 6px;
    transition: filter 0.2s;
}
.jgff-back-btn:hover { filter: brightness(1.1); color: #ffffff; }
body.dark-mode .jgff-back-btn { background-color: #0d2a45; }
</style>
</head>
<body>

<div id="site-header"></div>

<div class="page-title-band" role="banner">

    <span class="tagline">JewishGen Family Finder</span>
    <h1>Questionable Town Query</h1>
</div>

<div class="jgff-ecru-wrap">
    <div class="jgff-ecru-inner">
        <div class="jgff-card">
            <div class="jgff-success-icon" aria-hidden="true">
                <i class="ti ti-check"></i>
            </div>
            <h2>Thank you, <%=l_fromName%></h2>
            <p>Your query has been submitted to the JGFF Help Desk.<br>
               We will investigate and email a response back to you.</p>
            <a class="jgff-back-btn" href="<%=l_backlink%>">&larr; Return to Family Finder</a>
        </div>
    </div>
</div>

<div id="site-footer"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function loadComponent(id, file) {
    return fetch(file)
        .then(function(r){ if(!r.ok) throw new Error('Cannot load '+file); return r.text(); })
        .then(function(h){ document.getElementById(id).innerHTML=h; })
        .catch(function(e){ console.warn(e); });
}
Promise.all([
    loadComponent('site-header', '/Header_NavBar.html'),
    loadComponent('site-footer', '/Footer.html')
]);
</script>

</body>
</html>
<%
Sub unspec_var(var)
    If var = "" Or IsNull(var) Then var = "(unspecified)"
End Sub

Sub blank_response(fname)
%>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Incomplete Form &ndash; JewishGen Family Finder</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="/jg-global.css">
<style>
.page-title-band { background-color: #09497a; padding: 36px 50px; text-align: center; }
body.dark-mode .page-title-band { background-color: #0d2a45; }
.page-title-band h1 { margin: 0; font-family: Georgia, serif; font-size: 1.75rem; font-weight: normal; color: #ffffff; }
.jgff-ecru-wrap { background-color: var(--ecru); padding: 3rem 2rem; }
.jgff-ecru-inner { max-width: 740px; margin: 0 auto; }
.jgff-error-card { background-color: var(--white); border: 1px solid #d1caba; border-left: 4px solid #c0392b; border-radius: 0 8px 8px 0; padding: 24px 28px; }
body.dark-mode .jgff-error-card { background-color: #1e1e1e; border-color: #333; }
.jgff-error-card p { font-size: 0.9375rem; color: var(--charcoal); margin: 0 0 14px 0; line-height: 1.6; }
.jgff-error-card p:last-child { margin-bottom: 0; }
</style>
</head>
<body>
<div id="site-header"></div>
<div class="page-title-band"><h1>Incomplete Form</h1></div>
<div class="jgff-ecru-wrap">
    <div class="jgff-ecru-inner">
        <div class="jgff-error-card">
            <p>The field <strong><%=fname%></strong> appears to be missing or invalid. Please press your browser&rsquo;s Back button and complete the form.</p>
        </div>
    </div>
</div>
<div id="site-footer"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function loadComponent(id,file){return fetch(file).then(function(r){if(!r.ok)throw new Error('Cannot load '+file);return r.text();}).then(function(h){document.getElementById(id).innerHTML=h;}).catch(function(e){console.warn(e);});}
Promise.all([loadComponent('site-header','/Header_NavBar.html'),loadComponent('site-footer','/Footer.html')]);
</script>
</body>
</html>
<%
    Response.End
End Sub
%>
