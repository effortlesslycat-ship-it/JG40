<HTML>

<HEAD>
   <TITLE>JewishGen Family Finder - Search</TITLE>
   <!--#include virtual="/JG/HeadSection.txt"-->
</HEAD>

<BODY OnLoad="initEvents();" 
      OnPageShow="var opt2 = document.getElementById('TownSearchType'); TownSel(opt2);
         document.f.Surname.focus();"
         document.f.dates[0].checked=1; Sensitize(1);"
      OnUnload="EnableForm(document.f);">

<!--#include virtual="/JG/header.txt"-->

<CENTER>
<H1><I>JewishGen Family Finder</I> Search</H1>
</CENTER>
<%
' Response.cookies("login") = "https://www.jewishgen.org/jgff/jgffweb_iajgs.asp"
%>
<!--#include virtual="/JG/utils/utils.asp"-->
<!--#include virtual="/JG/utils/security.asp"-->

<SCRIPT SRC="/JG/Scripts/HighlightForm.js"></SCRIPT>


<P>The JGFF search form provides you with several options:
search-by-surname, search-by-town, search-by-surname-and-town;
and variations of those basic searches using exact spelling,
starts-with, contains, and sounds-like matching.
</P>


<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
<TR VALIGN="TOP">
<TD WIDTH="55%">


<A NAME="form"></A>
<FORM Action="/jgff/jgffform.php" name="f" Method="POST">

<CENTER>

<P>
<TABLE BORDER="1" CELLPADDING="6" rules="rows"
       BORDERCOLOR="#DDD6E4" BGCOLOR="#F5F5F5">

<%
If ( IsDatabaseDisabled (DB_JGFF_Search) ) Then
%>
<TR>
   <TD COLSPAN="3">
   <P ALIGN="CENTER"><FONT COLOR="red"><B>The JGFF is
   currently unavailable.</B></FONT></P>
   </TD>
</TR>
<%
End If
%>

  <TR>
    <TD>
      <select ID="SurnameSearchType" name="stype">
	<option value="bmpm" SELECTED>Surname (phonetic)</option>
	<option value="dm">Surname (DM soundex) </option>
	<option value="exact">Surname (exact) </option>
	<option value="starts">Surname (starts with) </option>
	<option value="contains">Surname (contains) </option>
	<option value="fuzzy">Surname (fuzzy) </option>
	<option value="fuzzier">Surname (fuzzier) </option>
	<option value="fuzziest">Surname (fuzziest) </option>
      </select> :
    </TD>
    <TD>
      <input ID="Surname" name="surname" size="18" maxlength="20">
    </TD>
  </TR>
  <TR>
    <TD>
      <select ID="TownSearchType" name="ttype" OnChange="TownSel(this)">
	<option value="bmpm">Town (phonetic)</option>
	<option value="dm">Town (DM soundex) </option>
	<option value="exact" SELECTED>Town (exact) </option>
	<option value="starts">Town (starts with) </option>
	<option value="contains">Town (contains) </option>	
	<option value="fuzzy">Town (fuzzy) </option>
	<option value="fuzzier">Town (fuzzier) </option>
	<option value="fuzziest">Town (fuzziest) </option>
      </select> :
    </TD>
    <TD>
      <input name="town" size="18" maxlength="40">
    </TD>
  </TR>
<TR>
  <td>COUNTRY</td>
  <td><SELECT name="Coun1" size="1">
      <option value="Any" SELECTED>Any Country
      <!--#include virtual="/jgff/country_list.txt"-->
      </SELECT>
      </td>
</TR>
</table>
</P>


<P ID="TownSyn">
<LABEL for="synonym">
   Use Town Synonyms?
   <input type="checkbox" name="synonym" ID="synonym" checked>
</LABEL>
</P>


<P>
<CENTER>
<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0">
<tr><td>
<label for="rall"><input type="radio" name="dates" id="rall" value="all"
       onClick="Sensitize(1)" checked>Search all entries</label>
<BR>
<label for="rsome"><input type="radio" name="dates" id="rsome" value="some"
       onClick="Sensitize(0)">Search only entries added/changed since
<NOBR>
<select name="Months" size="1" disabled>
   <option value="01" selected>Jan</option>
   <option value="02">Feb</option>
   <option value="03">Mar</option>
   <option value="04">Apr</option>
   <option value="05">May</option>
   <option value="06">Jun</option>
   <option value="07">Jul</option>
   <option value="08">Aug</option>
   <option value="09">Sep</option>
   <option value="10">Oct</option>
   <option value="11">Nov</option>
   <option value="12">Dec</option>
</select>
<select name="Years" size="1" disabled>
   <%
	cyear = Year(Now())
	For yr = 2014 To cyear
	%>
		<option value="<% =yr %>"
		<% If yr = cyear Then 
			%> SELECTED
		<% End If %>
		><% =yr %></option>
	<% Next
   %>
</select></NOBR>
</label>
</td></tr>
</TABLE>
</CENTER>
</P>


<SCRIPT>

// OnChange event handler for Town SearchType SELECT box:
function TownSel (choice)
{
  var searchtype = choice.value;
  var syn_check = document.getElementById("synonym");
  var syn_quest = document.getElementById("TownSyn");

  // If "Is Exactly" is selected, then
  //   enable the "Use Town Synonyms" features:
  if (searchtype == "exact")
    {
      syn_check.disabled = 0;
      syn_check.checked  = true;
      syn_quest.style.opacity = "1.00";
    }
  else
  // If any other search-type is selected, then
  //   dis-able the "Use Town Synonyms" features:
    {
      syn_check.disabled = 1;
      syn_check.checked  = false;
      syn_quest.style.opacity = "0.33";
    }

  return true;
}


// OnClick event handler for date filter radio buttons:
function Sensitize(val)
{
   document.f.Months.disabled = val;
   document.f.Years.disabled  = val;
}

</SCRIPT>


<input type="HIDDEN" name="dummy" value="on">

<P><font SIZE="+1">
<INPUT TYPE="submit" VALUE=" Search " ID="SearchButton">
</font></P>

</CENTER>

</FORM>

</TD>


<TD>

<P>To search for other researchers who are seeking ancestors of the same
surname or town of origin, type a surname and/or town in the appropriate
space in the form.

<UL>
   <li>To search for people researching a particular surname,
       type that surname in the Surname box.</li>
   <li>To search for people researching a particular ancestral town,
       type the name of that town in the Town box.</li>
   <li>If you wish to combine searches to find surname
       <b>X</b> in town <b>Y</b>, then fill in <B>both</B>
       Surname and Town spaces.</li>
</UL>
</P>

<P>You can also filter your searches by country.&nbsp;
Please note that the country filter works only on <b>modern</b>
country borders, which are often irrelevant for Jewish
genealogical research.
</P>

</TD>
</TR>
</TABLE>


<HR>


<A NAME="SearchTypes"></A>
<H2>Search types:</H2>

<p>For each search, you can choose one of four different types of matching,
for Surnames and Town names:
<ul>
  <li><b>Is Exactly</b> (Standard) &#151; match on <i>exact</i> spelling only</li>
  <li><b>Starts With</b> (Wildcard) &#151; match based upon a <i>prefix</i></li>
  <li><b>Sounds Like</b> (D-M Soundex) &#151; match based upon a name's <i>sound</i></li>
  <li><b>Contains </b> (Partial Text) &#151; match based upon text matching
      <i>anywhere</i> within a name</li>
</ul>
</p>


<DL>
<DT><H3>Is Exactly:</H3></DT>
<DD>
<p>Selecting the <b><i>Is Exactly</i></b> option will search for all
instances of your surname or town based upon exact spelling only.&nbsp;
<NOBR>For Surnames,</NOBR> this option is not very useful, since surnames
were rarely spelled consistently in historical records.&nbsp;
<NOBR>For Towns,</NOBR> you can also opt to use the Town Synonym database,
if a specific country is chosen.
</p>
</DD>
</DL>


<DL>
<DT><H3>Starts With:</H3></DT>
<DD>
<p>Selecting the <b><i>Starts With</i></b> option will let you
search for all combinations of your surname or town based upon your
input prefix.&nbsp;
<NOBR>For example,</NOBR> selecting the Starts With option for the name
&quot;<b>Stein</b>&quot; will match all names that start with the letters
&quot;<b>Stein</b>&quot;, such as:
&quot;<b>Stein</b>&quot;, &quot;<b>Stein</b>berg&quot;,
&quot;<b>Stein</b>er&quot;, &quot;<b>Stein</b>man&quot;, etc.
</p>
</DD>
</DL>


<DL>
<DT><H3>Sounds Like:</H3></DT>
<DD>
<p>Selecting the <b><i>Sounds Like</i></b> option will let
you search for all similar-sounding names, according to the
<A HREF="/InfoFiles/soundex.html">Daitch-Mokotoff Soundex system</A>.&nbsp;
<NOBR>For example,</NOBR> &quot;Rosenstein&quot;,
&quot;Rozenstine&quot; and &quot;Rojzensztejn&quot; will all be searched
if you request a Sounds Like search of &quot;Rosensteen&quot;.</p>
</DD>
</DL>


<DL>
<DT><H3>Contains:</H3></DT>
<DD>
<P>Selecting the <b><i>Contains</i></b> option will
let you search for all data records which contain that text
<i>anywhere</i> within the surname or town field.&nbsp;
<NOBR>For example,</NOBR> selecting the Contains option
for the name &quot;<b>Blatt</b>&quot; will match
&quot;<b>Blatt</b>&quot;, &quot;<b>Blatt</b>man&quot;,
&quot;<b>Blatt</b>enberg&quot;, &quot;Fein<b>blatt</b>&quot;,
&quot;Green<b>blatt</b>&quot;, &quot;Stein<b>blatt</b>er&quot;,
etc.</P>
</DD>
</DL>


<HR>


<% If ( IsDatabaseDisabled (DB_JGFF_Search) ) Then %>
<SCRIPT>
  DisableForm(document.f);
</SCRIPT>
<% End If %>


<!--#include file="footer.txt"-->

<DIV CLASS="lastupdate">
Last Update: 25 May 2023 &nbsp;

</DIV>

<!--#include virtual="/JG/footer.txt"-->
