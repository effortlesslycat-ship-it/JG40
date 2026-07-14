<!--#include virtual="/databases/Regions/asp.inc"-->

<HTML>
<HEAD>
   <TITLE>JewishGen Germany Database</TITLE>
   <!--#include virtual="/JG/HeadSection.txt"-->
</HEAD>

<BODY onload="initEvents(); InitSearchForm();"
      onunload="EnableForm(document.f);">

<!--#include virtual="/JG/header.txt"-->

<!--#include virtual="/databases/Germany/$Header.txt"-->

<!--#include virtual="/databases/Regions/JavaScript.txt"-->


<%
' Set parameters for SearchForm:
region   = "ALLGERMANY"
button   = " Search the JewishGen Germany Database "
' Unfortunately, NS and IE handle this differently.
'   IE 6 doesn't respect the visibility:collapse setting.
'regvis   = "STYLE='visibility:hidden'"
regvis   = "STYLE='visibility:collapse'"
pardis   = "STYLE='display:none'"
%>

<!--#include virtual="/databases/Regions/SearchForm.txt"-->


<HR>


<H2>Component Databases:</H2>

<!--#include virtual="/databases/Germany/$Components.txt"-->


<HR>

<!--#include virtual="/databases/footer.new.txt"-->

<DIV CLASS="lastupdate">
Last Update: 29 Jul 2011 &nbsp; WSB
</DIV>

<!--#include virtual="/JG/footer.txt"-->
