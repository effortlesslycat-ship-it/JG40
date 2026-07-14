<HTML>

<HEAD>
   <TITLE>JewishGen Family Finder - IAJGS Conference Search</TITLE>
   <!--#include virtual="/JG/HeadSection.txt"-->
</HEAD>

<BODY OnLoad="initEvents();" 
      OnPageShow="var opt2 = document.getElementById('TownSearchType'); TownSel(opt2);
         document.f.Surname.focus();"
         document.f.dates[0].checked=1; Sensitize(1);">

<!--#include virtual="/JG/header.txt"-->

<H1 ALIGN="CENTER"><I>JewishGen Family Finder</I> Search
<BR>
for IAJGS Conference Attendees</H1>

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
      <select name="stype">
	<option value="bmpm" selected>Surname (phonetic)</option>
       	<option value="dm">Surname (DM soundex) </option>
       	<option value="exact">Surname (exact) </option>
       	<option value="fuzzy">Surname (fuzzy) </option>
       	<option value="fuzzier">Surname (fuzzier) </option>
       	<option value="fuzziest">Surname (fuzziest) </option>
      </select> :
    </TD>
    <TD>
      <input ID="Surname" name="surname" size="18" maxlength="20" 
             pattern="[A-Za-zacelnószzACELNÓSZZ*?]{3,}" >
    </TD>
  </TR>
  <TR>
    <TD>
      <select ID="TownSearchType" name="ttype" OnChange="TownSel(this)">
	<option value="bmpm">Town (phonetic)</option>
	<option value="dm">Town (DM soundex) </option>
	<option value="exact" selected>Town (exact) </option>
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
      <!--#include virtual="/JGFF/country_list.txt"-->
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

<input name="IAJGS" size="1" maxlength="1" value="Y" hidden="true">

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


<P> &nbsp; </P>


<% If ( IsDatabaseDisabled (DB_JGFF_Search) ) Then %>
<SCRIPT>
  DisableForm(document.f);
</SCRIPT>
<% End If %>


<IMG WIDTH="100%" BORDER="0"
     SRC="https://www.iajgs.org/wp21/wp-content/uploads/2024/03/IAJGS-conference-logo.png">

<HR>


<!--#include file="footer.txt"-->

<!--#include virtual="/JG/footer.txt"-->
