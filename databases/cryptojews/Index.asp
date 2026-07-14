<!--#include virtual="/databases/Regions/asp.inc"-->

<HTML>
<HEAD>
   <TITLE>JewishGen CryptoJews Collection</TITLE>
   <!--#include virtual="/JG/HeadSection.txt"-->
</HEAD>


<BODY onload="initEvents(); InitSearchForm();"
      onunload="EnableForm(document.f);">

<!--#include virtual="/JG/header.txt"-->

<!--#include virtual="/databases/cryptojews/$Header.txt"-->

<!--#include virtual="/databases/Regions/JavaScript_solr.txt"-->


<%
' Set parameters for SearchForm:
region   = "00crypto"
button   = " Search the JewishGen CryptoJews Database "
' Unfortunately, NS and IE handle this differently.
'   IE 6 doesn't respect the visibility:collapse setting.
'regvis   = "STYLE='visibility:hidden'"
regvis   = "STYLE='visibility:collapse'"
pardis   = "STYLE='display:none'"
%>

<!--#include virtual="/databases/Regions/SearchForm_solr.txt"-->

<H2>Component Databases:</H2>

<!--#include virtual="/databases/cryptojews/$Components.txt"-->

<HR>

<!--#include virtual="/databases/footer.new.txt"-->


<!--#include virtual="/JG/footer.txt"-->
