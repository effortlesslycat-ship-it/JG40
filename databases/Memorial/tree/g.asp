<%
' File: g.asp.
'
' Used as short URL for referring to JewishGen Memorial Plaque Database Table SHOW entries.
' Directory Path: /databases/memorial/Tree/
' Called like:
' "http://www.jewishgen.org/databases/memorial/Tree/t.asp?ID=USA-01111"


' Tell browser to not cache this page; force re-load.
Response.Expires = 0


' Get the JewishGen Memorial Plaque Database ID, from the URL parameter "ID".
cemid = Request.Querystring("ID")

if cemid = "" then
   Response.Write "Invalid Synagogue/Spociety ID"
   Response.End
end if


' Send the user to the JewishGen Memorial Plaque Database Table SHOW page for this cemetery:
response.redirect _
  "https://data.jewishgen.org/wconnect/wc.dll?jg~jgsys~admin~&S=memorial&Action=SHOWUSER&ID=" _
  & cemid

%>