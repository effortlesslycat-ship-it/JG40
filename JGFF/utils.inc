<%
'****************************************************************************
' /JGFF/utils.inc
' ~~~~~~~~~~~~~~~
' Utility functions for the JGFF ASP code.
' Warren Blatt, Aug 2015, Jun 2017
'
'****************************************************************************

%>
<!--#include virtual="/JG/utils/CURE_db.inc"-->
<%

' JGFF_Get_Num_Entries() -- 
'   Returns the number of JGFF entries for this user, or -1 if error. 
Function JGFF_Get_Num_Entries (jgid)

    On Error Resume Next
    
    ' The connection string for the "jewishgen" MySQL database on jewishgen13:
    '   (Should be put into a common included module ***)
    xDb_Conn_Str_JG = "DRIVER={MySQL ODBC 8.0 Unicode Driver};" & _
        "Server=jewishgen13;Database=jewishgen;Uid=mtobias;Pwd=nt732b#$;"

    ' Open connection to the "jewishgen" MySQL database on jewishgen13:
    Set jgff_conn = Server.CreateObject("ADODB.Connection")
    jgff_conn.Open xDb_Conn_Str_JG

    If Err.Number <> 0 Then
        JGFF_Log_Error ("Opening database.")
        JGFF_Get_Num_Entries = -1
        Exit Function
    End If

    ' Build SQL query to get the # of JGFF entries for this user:
    jgff_strsql = "SELECT count(*) AS Total FROM jgffdata where code='" & jgid & "'"

    ' Response.write jgff_strsql 

    Set jgff_rs = Server.CreateObject("ADODB.Recordset")
    jgff_rs.cursorlocation = 3
    jgff_rs.Open jgff_strsql, jgff_conn, 1, 2

    If Err.Number <> 0 Then
        JGFF_Log_Error (jgff_strsql)
        JGFF_Get_Num_Entries = -1
        Exit Function
    End If


    ' Return the # of JGFF entries: 
    If ( ( jgff_rs.EOF ) OR ( jgff_rs.BOF ) ) Then 
        ' Response.write "EOF/BOF" 
        JGFF_Get_Num_Entries = -1
    Else
        retval = jgff_rs("Total")

	' Need to convert the returned SQL count value from 
	'    a Decimal to a String to an Int.  Don't know why.
        retval = CStr(retval) 
        retval = CInt(retval) 
        
        JGFF_Get_Num_Entries = retval
    End If

    ' Close recordset and connection:
    jgff_rs.Close
    jgff_Set rs = Nothing
    jgff_conn.Close
    jgff_Set conn = Nothing
    Err.Clear

End Function


'****************************************************************************


Function isUserReadOnly(jgid)


	' Open connection to the GoldMine database.
	Set conn = Server.CreateObject("ADODB.Connection")
	conn.Open xDb_Conn_Str1
	If Err.Number <> 0 Then
		LogSQLError("Connect")
		SendBack 1005, Null, Null
	End If


	' Get information from GoldMine.
	SQL = "Select UREADONLY From CONTACT2 inner join contact1 on contact1.accountno = contact2.accountno where key3 = '" & jgid & "'"
	Set rs = Server.CreateObject("ADODB.Recordset")
	rs.Open sql, conn, 1, 2
	If Err.Number <> 0 Then
        JGFF_Log_Error (SQL)
	End If

	If rs.bof and rs.eof Then
		' Goldmine record for this JGID does not exist.
		' This should never happen -- report error.
		logmsg = "JGFF:utils:isUserReadOnly: JGID not in GoldMine.  jgcure: " & request.cookies("jgcure")
        JGFF_Log_Error (logmsg)
	End If

	isUserReadOnly = false
	ro = rs("UREADONLY")
	If Not IsNull(ro) Then 
		If ro = "Y" OR instr(ro, "F", 1) > 0 Then 
			isUserReadOnly = True
		End If 
	End If 

End Function


'****************************************************************************


Sub JGFF_Log_Error(message)
	
    If Err.Number <> 0 Then
	errText = "'" & Err.Number & _ 
		"'  :  '" & Err.Description & _
		"'  :  '" & Err.Source & "'."

	' Write the error to the browser screen:
	Response.write "Error: " & errText & "<BR>"
	Response.write "Query: " & message & "<BR>"

	Err.Clear
    End If

End Sub


'****************************************************************************
'****************************************************************************
%>