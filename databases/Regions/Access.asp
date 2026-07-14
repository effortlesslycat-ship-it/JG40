<%
' /databases/Regions/Access.asp
' ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
' Warren Blatt, 8-Jun-2005.  
' Updated 2-Dec-2011, 20-Apr-2012, 11-Feb-2014, 22-Jun-2015, 12-Aug-2015
' Included via "/databases/Regions/asp.inc"

' Tell browser to not cache this page; force re-load.
Response.expires = 0
Response.addHeader "pragma", "no-cache"
' Response.expiresabsolute = Now() - 1
' Response.addHeader "cache-control", "private"
' Response.CacheControl = "no-cache"


' Determine the user's status.
' The "state" variable can have one of three values:
'   * "NoLog"  - - User is NOT logged in.
'   * "Donor"  - - User is logged in, and IS a VAS donor.
'   * "NotDonor" - User is logged in, and is NOT a VAS donor. 


' Get the JGID value from the jgcure cookie:
jgid = Request.Cookies("jgcure")("jgid")


' If there's no JGID in the cookie, then the user is NOT logged in.
If ( (isNull(jgid)) OR (isEmpty(jgid)) OR (jgid = "") ) Then
    state = "NoLog"
    
    ' Set up the "login" cookie to be the URL of the current page, 
    '   so that they return here after login.
    retURL = "http://www.jewishgen.org" & Request.ServerVariables("URL")
    Response.Cookies ("login").Domain = ".jewishgen.org"
    Response.Cookies ("login") = retURL
Else
    ' JGID# validity check (prevent SQL injection attack):
    If Not IsNumeric(jgid) Then
       Response.Write "Bad Format TR16477 Regions/Access.asp"
       Response.End
    End If

    ' Open connection to the GoldMine database, to get the user's record:
    Set conn = Server.CreateObject("ADODB.Connection")
    conn.Open xDb_Conn_Str1
    If Err.Number <> 0 Then
       Response.Write "Error Opening GoldMine"
       Response.End
    End If

    ' The "v_qualified" view includes only those users who are donors.
    SQL = "Select * From v_qualified where JGID = '" & jgid & "'"

    Set rs = Server.CreateObject("ADODB.Recordset")
    rs.Open sql, conn, 1, 2
    If Err.Number <> 0 Then
       Response.Write "Error Querying GoldMine"
       Response.End
    End If
    
    If rs.bof and rs.eof Then
        state = "NotDonor"
    Else
        state = "Donor"
    End If
    
    ' *** Should close connection here?
End If


' Set parameters for SearchForm.txt:
If (state <> "Donor")AND(disVAS<>1) Then
   bstyle = "OnMouseOver=""OverlayPopup(this, '" & state & "')"""
   dis    = " DISABLED"
Else
   bstyle = ""
   dis    = ""
End If


' Set the "Number of visible search parameters":
'   The default value is one.  
'   Some special-cases databases have more 
'   (e.g.: "All UK" has 2, "JOWBR" has 4).
num_params = 1


'****************************************************************************
%>