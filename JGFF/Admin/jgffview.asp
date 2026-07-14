<%
' /JGFF/Admin/JGFFview.asp
%>

<!--#include virtual="/JG/utils/security.asp"-->
<!--#include virtual="/JG/utils/sec_utils.inc"-->

<%
' To use this page, the user must be logged in to CURE (via security.asp), 
'    and have admin access the "JGFF" system in JewishGen Security.
' access = GetSecurityAccessLevel ("JGFF-A")
cooks = getSessionCookie()
jgid = cooks(0)
access = check_permission("JGFF", jgid)
If ( IsNull(access) Or access <> "A" ) Then
    Response.Write ("Invalid Access.")
    Response.End
End If
%>
<HTML>
<HEAD>
   <TITLE>JGFF Admin: Data Viewer/Editor</TITLE>
   <!--#include virtual="/JG/HeadSection.txt"-->
</HEAD>

<BODY onload="initEvents(); document.forms[0].elements[0].focus();">

<!--#include virtual="/JG/header.txt"-->

<SCRIPT SRC="/JG/Scripts/HighlightForm.js"></SCRIPT>

<CENTER>
<H1>JGFF Admin: Modify JGFF Entries</H1>
</CENTER>


<HR>


<BLOCKQUOTE>
<P>This page allows you to modify JGFF data &mdash;
the surnames and towns for a researcher.
</P>
</BLOCKQUOTE>


<FORM Method="GET"
      Action="/JGFF/jgffviewadd.php">

<TABLE BORDER="1" CELLPADDING="10" rules="rows" ALIGN="CENTER"
       BORDERCOLOR="#DDD6E4" BGCOLOR="#F5F5F5">
<TR>
<TD ALIGN="CENTER">


<P>
<B>JewishGen ID:</B> <input name="code" type="text" size="6">
</P>

<P ALIGN="LEFT">
<LABEL for="rad_1">
<input type="radio" name="add" value="N" ID="rad_1" CHECKED>
   <B>Modify</B> Surname/Town Information</LABEL> <BR>
<LABEL for="rad_2">
<input type="radio" name="add" value="Y" ID="rad_2">
   <B>Add</B> Surname/Town Information</LABEL>
</P>

<P ALIGN="CENTER">
<input type="submit" value=" Send Request ">
</P>

</TD></TR>
</TABLE>

</FORM>


<HR>

<!--#include file="footer.txt"-->

<DIV CLASS="lastupdate">
Last Update: 3 July 2017 &nbsp; WSB
</DIV>

<!--#include virtual="/JG/footer.txt"-->

