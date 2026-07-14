<!--#include virtual="/JG/utils/utils.asp"-->

<HTML>

<HEAD>
   <TITLE>JewishGen Family Finder - Bulk Email Contact</TITLE>
   <!--#include virtual="/JG/HeadSection.txt"-->
</HEAD>

<BODY onload="initEvents();
      var opt2 = document.getElementById('option2'); TownSel(opt2);
      document.f.dates[0].checked=1; Sensitize(1);
      document.f.Name1.focus();"
      onunload="EnableForm(document.f);">

<!--#include virtual="/JG/header.txt"-->

<A NAME="form"></A>
<CENTER>
<H1><I>JewishGen Family Finder</I> Bulk Email Contact</H1>
</CENTER>

<SCRIPT SRC="/JG/Scripts/HighlightForm.js"></SCRIPT>


<TABLE BORDER="0" WIDTH="100%">
<TR VALIGN="TOP">
<TD WIDTH="55%">


<FORM Action="https://data.jewishgen.org/wconnect/wc.dll?jg~jgsys~jgffbulkemail~C"
      name="f" Method="POST">
<input type="hidden" name="Name1" value="">
<input type="hidden" name="option1" value="ST">
<input type="hidden" name="option2" value="ST">

<CENTER>

<P>
<TABLE BORDER="1" CELLPADDING="6" rules="rows"
       BORDERCOLOR="#DDD6E4" BGCOLOR="#F5F5F5">

<%
If DisableDatabaseForm = True THEN
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
  <td>TOWN</td>
  <td> &nbsp; </td>
  <td><input type="text" size="18" maxlength="30" name="Town1"></td>
</TR>
<TR>
  <td>COUNTRY</td>
  <td> &nbsp; </td>
  <td><!--#include virtual="/jgff/country_list.txt"--></td>
</TR>
</table>
</P>

<P>
<LABEL for="synonym">
<SPAN ID="TownSyn">Use Town Synonyms?</SPAN>
   <input type="checkbox" name="synonym" ID="synonym">
</LABEL>
</P>

<P>
<table>
<tr><td>
<label for="rall"><input type="radio" name="dates" id="rall" value="all"
       onClick="Sensitize(1)" checked>Search all entries</label> <br>
<label for="rsome"><input type="radio" name="dates" id="rsome" value="some"
       onClick="Sensitize(0)">Search only entries added/changed since
<select name="Months" size="1" disabled>
   <option selected value="01">Jan</option>
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
   <option value="2004">2004</option>
   <option value="2005">2005</option>
   <option value="2006">2006</option>
   <option value="2007">2007</option>
   <option value="2008">2008</option>
   <option value="2009">2009</option>
   <option value="2010">2010</option>
   <option value="2011" selected>2011</option>
</select>
</label>
</td></tr>
</table>
</P>


<script>
// OnChange event handler for Town SearchType SELECT box:
function TownSel (choice)
{
  var searchtype = choice.value;
  var syn_check = document.getElementById("synonym");
  var syn_label = document.getElementById("TownSyn");

  if (searchtype == "ST")
    {
      syn_check.disabled = 0;
      syn_check.checked = true;
      syn_label.style['color'] = "black";
    }
  else
    {
      syn_check.disabled = 1;
      syn_check.checked = false;
      syn_label.style['color'] = "gray";
    }
  return true;
}

// OnClick event handler for date filter radio buttons:
function Sensitize(val)
{
  if (document.getElementById)
    {
      document.f.Months.disabled=val;
      document.f.Years.disabled=val;
    }
}
</script>


<input type="HIDDEN" name="dummy" value="on">

<P><font SIZE="+1">
<INPUT TYPE="submit" VALUE=" Search " ID="SearchButton">
</font></P>

</CENTER>

</FORM>

</TD>


</TR>
</TABLE>



<HR>


<% If DisableDatabaseForm = True Then %>
<SCRIPT>
  DisableForm(document.f);
</SCRIPT>
<% End If %>


<!--#include file="footer.txt"-->

<DIV CLASS="lastupdate">
Last Update: 12 Jul 2011 &nbsp; MT
</DIV>

<!--#include virtual="/JG/footer.txt"-->
