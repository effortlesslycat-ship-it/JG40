<%
' File: g.asp.  
'
' Used as short URL for referring to JOWBR Table SHOW entries.
' Directory Path: /databases/Cemetery/Tree/
' Called like:  
' "http://www.jewishgen.org/databases/Cemetery/Tree/t.asp?ID=USA-01111"


' Tell browser to not cache this page; force re-load.
Response.Expires = 0


' Get the JOWBR Cemetery ID, from the URL parameter "ID".
cemid = Request.Querystring("ID")

if cemid = "" then
   Response.Write "Invalid Cemetery ID"
   Response.End
end if


' Send the user to the JOWBR Table SHOW page for this cemetery:
response.redirect _
  "https://data.jewishgen.org/wconnect/wc.dll?jg~jgsys~admin~&S=JOWBR&Action=SHOWUSER&ID=" _
  & cemid

%>