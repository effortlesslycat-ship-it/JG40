<%
'****************************************************************************
'****************************************************************************
' Regions.asp
' Warren Blatt, April 2005
' Last Updated: May 12 2005, Oct 3 2006 WSB


' Global scope data:
Dim arrDBData
Dim iRecFirst, iRecLast, iFieldFirst, iFieldLast


'****************************************************************************


Function GetRegions()

' This function reads the entire 'Regions' table from
'    the SQL server, into the ASP array "arrDBData".

' Open connection to the 'Regions' SQL database:
Set conn = Server.CreateObject("ADODB.Connection")
'xDb_Conn_Str = "dsn=Regions;uid=goldmine;pwd=p1mpl3"

xDb_Conn_Str = "Driver={SQL Server};" & _
	"Server=sqlserver.jewishgen.org;" & _
	"Address=sqlserver.jewishgen.org;" & _
	"Network=DBMSSOCN;" & _
	"Database=Regions;" & _
	"Uid=goldmine;" & _
	"Pwd=p1mpl3;"

conn.Open xDb_Conn_Str

' Query the Regions database:
Set rstGetRows = conn.Execute("SELECT * FROM regions ORDER by [order]")

' Populate ASP array "arrDBData":
arrDBData = rstGetRows.GetRows()

' Close SQL database connections:
rstGetRows.Close
Set rstGetRows = Nothing
conn.Close
Set conn = Nothing

' Initialize some ASP variables:
iRecFirst   = LBound(arrDBData, 2)
iRecLast    = UBound(arrDBData, 2)
iFieldFirst = LBound(arrDBData, 1)
iFieldLast  = UBound(arrDBData, 1)

End Function 


'***************************************************************************


Function InitRegions()

' ASP-generated JavaScript code, which initializes the
'   JavaScript array "reg_data" from the ASP array "arrDBData".

Response.Write "<SCRIPT>" & vbCrLf
Response.Write "var reg_data = new Array();" & vbCrLf


' Loop through the database records (second dimension of the array):
For Rec = iRecFirst To iRecLast
	' An array row for each record
	Response.Write "reg_data[" & Rec & "] = {" & _ 
	   "sys:" & Chr(34) & UCase(RTrim(arrDBData(0, Rec))) & Chr(34) & ", " & _
	   "lab:" & Chr(34) & RTrim(arrDBData(1, Rec)) & Chr(34) & ", " & _
	   "val:" & Chr(34) & RTrim(arrDBData(2, Rec)) & Chr(34) & ", " & _
	   "ord:" & Chr(34) & arrDBData(3, Rec) & Chr(34) & ", " & _
	   "ind:" & Chr(34) & RTrim(arrDBData(4, Rec)) & Chr(34) & "};" & vbCrLf
Next ' Rec

Response.Write "</SCRIPT>" & vbCrLf

End Function 


'***************************************************************************


Function InitRegionsFile()

' ASP-generated JavaScript code, which initializes the
'   JavaScript array "reg_data" from the ASP array "arrDBData",
'   and writes it to a file named "/databases/RegionsData.js".

' Create a FileSystemObject, to create the textfile.
Dim oFS
Set oFS = CreateObject ("Scripting.FileSystemObject")

' Modified 11-01-2008 GSandler -- mapped path is to current directory
strPath = Server.MapPath ("/databases/Regions/RegionsData.js")
'strPath = Server.MapPath ("RegionsData.js")

' FileSystemObject.CreateTextFile() returns a TextStream object.
Set oTS = oFS.CreateTextFile (strPath, 1)


oTS.Write "var reg_data = new Array();" & vbCrLf


' Loop through the database records (second dimension of the array):
For Rec = iRecFirst To iRecLast
	' An array row for each record
	oTS.Write "reg_data[" & Rec & "] = {" & _ 
	   "sys:" & Chr(34) & UCase(RTrim(arrDBData(0, Rec))) & Chr(34) & ", " & _
	   "lab:" & Chr(34) & RTrim(arrDBData(1, Rec)) & Chr(34) & ", " & _
	   "val:" & Chr(34) & RTrim(arrDBData(2, Rec)) & Chr(34) & ", " & _
	   "ord:" & Chr(34) & arrDBData(3, Rec) & Chr(34) & ", " & _
	   "ind:" & Chr(34) & RTrim(arrDBData(4, Rec)) & Chr(34) & "};" & vbCrLf
Next ' Rec

oTS.Close

' Return the number of records.
InitRegionsFile = Rec

End Function 


'***************************************************************************


Function PrintRegions()

' Prints the entire contents of arrDBData to the screen.

Response.Write "<P>iRecFirst = " & iRecFirst & "  iRecLast = " & iRecLast & "<BR>"
Response.Write "iFieldFirst = " & iFieldFirst & "  iRecLast = " & iFieldLast & "</P>"

Response.Write "<table border=1>" & vbCrLf

' Loop through the records (second dimension of the array):
For I = iRecFirst To iRecLast
	' Write a table row for each Region record:
	Response.Write "<tr>" & vbCrLf
	
	' Loop through the fields (first dimension of the array):
	For J = iFieldFirst To iFieldLast
		' Write a table cell for each field:
		Response.Write vbTab & "<td>" & arrDBData(J, I) & "</td>" & vbCrLf
	Next ' J
	
	Response.Write "</tr>" & vbCrLf
Next ' I

Response.Write "</table>" & vbCrLf

End Function 

'***************************************************************************
%>