<!--#include virtual="/CURE/db.asp"-->
<!--#include virtual="/CURE/utils.asp"-->
<!--#include virtual="/databases/Regions/Access.asp"-->
<HTML>
<HEAD>
   <TITLE>JewishGen Belarus Database</TITLE>
   <!--#include virtual="/JG/HeadSection.txt"-->
</HEAD>


<BODY onload="initEvents(); InitSearchForm();"
      onunload="EnableForm(document.f);">

<!--#include virtual="/JG/header.txt"-->

<!--#include virtual="/databases/Belarus/$Header.txt"-->

<!--#include virtual="/databases/Regions/JavaScript.txt"-->

<%
' Set parameters for SearchForm:
region   = "BELARUS"
button   = " Search the JewishGen Belarus Database "
reglabel = "<A HREF='$GeoRegions.htm'>Region</A>:"
%>
      
<!--#include virtual="/databases/Regions/SearchForm.txt"-->

<SCRIPT>
   setRegions("BELARUS");
</SCRIPT>


<HR>


<H2>Component Databases:</H2>

<!--#include virtual="/databases/Belarus/Test/$Components.txt"-->


<HR>

<!--#include virtual="/databases/footer.txt"-->

<P ALIGN="RIGHT"><CITE>
Last Update: 4 Jul 2005 &nbsp; WSB 
</CITE></P>

<!--#include virtual="/JG/footer.txt"-->
