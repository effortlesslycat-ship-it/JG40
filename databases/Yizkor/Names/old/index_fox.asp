<!--#include virtual="/databases/Regions/asp.inc"-->

<HTML>
<HEAD>
   <TITLE>The JewishGen Yizkor Book Master Name Index</TITLE>
   <!--#include virtual="/JG/HeadSection.txt"-->
</HEAD>

<BODY onload="initEvents(); InitSearchForm();"
      onunload="EnableForm(document.f);">

<!--#include virtual="/JG/header.txt"-->

<!--#include virtual="/databases/Yizkor/Names/$Header.txt"-->

<!--#include virtual="/databases/Regions/JavaScript.txt"-->

<%
' Set parameters for SearchForm:
region   = "YBMNI"
button   = " Search the JewishGen Yizkor Book Master Name Index "
reglabel = "Region:"
%>

<!--#include virtual="/databases/Regions/SearchForm.txt"-->

<SCRIPT>
   setRegions("NECROLOGY");
</SCRIPT>


<HR>


<!--#include virtual="/databases/Yizkor/Names/$Components.txt"-->

<HR>


<!--#include virtual="/databases/footer.new.txt"-->

<DIV CLASS="lastupdate">Last Update: 3 Feb 2011 &nbsp; WSB</DIV>

<!--#include virtual="/JG/footer.txt"-->

