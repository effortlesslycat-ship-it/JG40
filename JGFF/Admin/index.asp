<!--#include virtual="/JG/utils/security.asp"-->
<!--#include virtual="/JG/utils/sec_utils.inc"-->
<%
' To view this page, the user must be logged in to CURE (via security.asp), 
'    and have some access the "JGFF" system in JewishGen Security.
' access = GetSecurityAccessLevel ("JGFF")
cooks = getSessionCookie()
jgid = cooks(0)
access = check_permission("JGFF", jgid)
If ( IsNull(access) Or access = "" ) Then
    Response.Write ("Invalid Access.")
    Response.End
End If
%>
<HTML>

<HEAD>
   <TITLE>JGFF Data Maintenance and Administration</TITLE>
   <!--#include virtual="/JG/HeadSection.txt"-->
</HEAD>

<BODY>

<!--#include virtual="/JG/header.txt"-->

<CENTER>
<H1>JGFF Admin and Maintenance</H1>
</CENTER>


<P>This page is for JGFF Maintenance and Administration.&nbsp;
You must have a password to perform any of the functions listed below.
</P>

<P>
<TABLE WIDTH="100%" BORDER="0">
<TR VALIGN="TOP">
<TD WIDTH="50%">

<UL CLASS="jgbulletsm" STYLE="line-height:2.0">
<LI><B>OLD</B> <a href="https://data.jewishgen.org/wconnect/wc.dll?jg~jgsys~jgfflist1">List</a> the JGFF Data of a Researcher.</li>
<LI><a href="jgffview.asp"">Modify</a> the JGFF Data of a Researcher.</li>
<LI><B>OLD</B> <A href="delres.htm">Delete</a> the JGFF Data of a Researcher.</li>
<LI><B>OLD</B> <A href="jgffmove.htm">Move</a> JGFF Data records from one Researcher to another.</li>
<LI><B>OLD</B> <A href="/JGFF/jgffMatches.asp">Match</A> all surnames/towns for one Researcher.</li>
<LI><a href="/databases/admin/jgfftowns/jgfftowns.php">JGFF Towns</A> Admin Panel.</li>
<LI><a href="/databases/admin/jgffsynonyms/jgffsynonyms.php">JGFF Synonyms</A> Admin Panel.</li>
</UL>

</TD>

<TD>

<UL CLASS="jgbulletsm" STYLE="line-height:2.0">
<LI><a href="/CURE/Admin/jgidsearch.htm">Search</a> the Researcher contact database (Goldmine).</li>
<LI><B>OLD</B> <a href="jgffsearches.htm">Search</a> the JGFF search history log.</li>
<LI><B>OLD</B> <a href="jgfftownonly.htm">Search</a> the JGFF search history log - Town-only searches.</li>
<LI><a href="blindemails.htm">Blind Contact Emails</a> statistics.</li>
<LI><B>OLD</B> <a href="jgffstats.htm">Statistics</a> about current JGFF data.</li>
<LI><a href="ToDo.txt">Ideas</a> for future JGFF enhancements.</li>
</UL>

</TD>
</TR>
</TABLE>
</P>


<P>Please report any problems or questions to
<A href="mailto:jgffhelp@jewishgen.org">jgffhelp@jewishgen.org</A>.
</P>


<HR>


<!--#include virtual="/JGFF/footer.txt"-->


<DIV CLASS="lastupdate">
Last update 15 Aug 2017 &nbsp; MT
</DIV>


<!--#include virtual="/JG/footer.txt"-->
