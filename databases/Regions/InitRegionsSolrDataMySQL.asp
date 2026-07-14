<HTML>
<HEAD>
   <TITLE>Initialize Regions Data</TITLE>
   <!--#include virtual="/JG/HeadSection.txt"-->
</HEAD>

<BODY>

<!--#include virtual="/JG/header.txt"-->

<CENTER>
<H1>Regions Data Initialized</H1>
</CENTER>

<!--#include virtual="/databases/Regions/RegionsSolrMySQL.asp"-->

<%
' Call functions in RegionsSolr.asp to get RegionsSolr data from SQLSERVER,
'   and write it to the RegionsData_solr.js file.
GetRegions()
recs = InitRegionsFile()
%>

<P><%=recs %> records exported to /databases/Regions/RegionsData_solr.js.</P>


<HR>


<!--#include virtual="/databases/footer.new.txt"-->


<DIV CLASS="lastupdate">
Last Update: 12 May 2005 &nbsp; WSB
</DIV>

<!--#include virtual="/JG/footer.txt"-->


