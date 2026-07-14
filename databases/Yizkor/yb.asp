<%
' File: yb.asp  
'
' Used as short URL for referencing Yizkor Book Database pages.
' Directory Path: /databases/Yizkor/
' Called like:  
' "http://www.jewishgen.org/databases/Yizkor/yb.asp?id=123", 
'   or just "yb.asp?id=123" for web pages within the 
'   "/databases/Yizkor/" directory itself.


' Tell browser to not cache this page; force re-load.
Response.Expires = 0


' Get the USBGN Feature Code Number, from the URL parameter "id".
ybid = Request.Querystring("id")

If (ybid = "") OR (Not isNumeric(ybid)) Then
   Response.Write "Invalid Yizkor Book ID Number"
   Response.End
end if


' Send the user to the Yizkor Book database page for this YBID:
response.redirect _
  "https://data.jewishgen.org/wconnect/wc.dll?jg~jgsys~yizkor~lookup_pb~" & ybid

%>