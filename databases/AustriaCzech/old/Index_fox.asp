<!--#include virtual="/databases/Regions/asp.inc"-->
<HTML>
<HEAD>
   <TITLE>JewishGen Austria-Czech Database</TITLE>
   <META NAME="keywords"
         CONTENT="Austria, Austrian, Czech, Bohemia, Bohemian, Moravia, Moravian">
   <!--#include virtual="/JG/HeadSection.txt"-->
</HEAD>

<BODY onload="initEvents(); InitSearchForm();"
      onunload="EnableForm(document.f);">


<!--#include virtual="/JG/header.txt"-->

<!--#include virtual="/databases/AustriaCzech/$Header.txt"-->

<!--#include virtual="/databases/Regions/JavaScript.txt"-->

<%
' Set parameters for SearchForm:
region = "ALLBOHMOR"
button = " Search the JewishGen Austria-Czech Database "

' Unfortunately, NS and IE handle this differently.
'   IE 6 doesnt respect the visibility:collapse setting.
'regvis = "STYLE='visibility:hidden'"
regvis  = "STYLE='visibility:collapse'"
pardis  = "STYLE='display:none'"
%>

<!--#include virtual="/databases/Regions/SearchForm.txt"-->


<HR>


<H2>Component Databases:</H2>

<!--#include virtual="/databases/AustriaCzech/$Components.txt"-->


<HR>


<!--#include virtual="/databases/footer.new.txt"-->

<DIV CLASS="lastupdate">
Last Update: 15 Feb 2013 &nbsp; 
<A HREF="/JewishGen/Contact.asp?to=1173WB">WSB</A>
</DIV>


<!--#include virtual="/JG/footer.txt"-->
