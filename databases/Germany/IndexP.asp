<!--#include virtual="/databases/Regions/asp.inc"-->

<HTML>
<HEAD>
   <TITLE>JewishGen Poland Database</TITLE>
   <META NAME="keywords" CONTENT="Poland, Polish">
   <!--#include virtual="/JG/HeadSection.txt"-->
</HEAD>


<BODY onload="initEvents(); InitSearchForm();"
      onunload="EnableForm(document.f);">

<!--#include virtual="/JG/header.txt"-->

<!--#include virtual="/databases/Poland/$Header.txt"-->

<!--#include virtual="/databases/Regions/JavaScript_solr.txt"-->

<%
' Set parameters for SearchForm:
region   = "ALLPOLAND"
button   = " Search the JewishGen Poland Database "
reglabel = "<A HREF='GeoRegions.htm'>Geographical Region</A>:"
%>

<!--#include virtual="/databases/Regions/SearchForm_solr.txt"-->

<SCRIPT>
   setRegions("ALLPOLAND");
   regval = getQueryVariable("region")
   if ( regval ) 
     { setOption ("GeoRegion", regval) }
</SCRIPT>


<HR>


<H2>Component Databases:</H2>

<!--#include virtual="/databases/Poland/$Components.txt"-->


<HR>

<!--#include virtual="/databases/footer.new.txt"-->

<!--#include virtual="/JG/footer.txt"-->


