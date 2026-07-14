// SearchForm.js
// JavaScript functions for the All Country DBs search form.
// Warren Blatt, May 2005.  
// Updated July 2007, May 2010, Oct 2011, Jul 2013.


// OnChange event handler for DataType SELECT box:
function SelProc(choice, target1, target2)
{
  var datatype;
  if ( choice == null )
    { datatype = "S"; }
  else
    { datatype = choice.value; }

  var target_opt = document.getElementById(target1);
  var target_txt = document.getElementById(target2);

  if (datatype == "X")
    { 
      target_opt.disabled=1; 
      target_opt.options[5] = new Option ("contains", "", false, true);
      target_txt.disabled=0;
    }
  else if (datatype == "")
    { 
      target_opt.disabled=1;
      target_opt.selectedIndex = 0;
      target_txt.disabled=1;
    }
  else
    { 
      if (datatype == "S")
        { target_opt.selectedIndex = 1; }
      else if (datatype == "G")
        { target_opt.selectedIndex = 1; }
      else if (datatype == "T")
        { target_opt.selectedIndex = 1; }
        
      target_opt.disabled=0; 
      if ( target_opt.options.length == 6 )
         { target_opt.options.length = 5; }
      target_txt.disabled=0; 
    }
  return true;
}



function Sensitize(val)
{
  document.f.Months.disabled=val;
  document.f.Years.disabled=val;
}



// To be used as a BODY onload() event handler:
function InitSearchForm()
{
  document.f.dates[0].checked=1;
  Sensitize(1);
  var sel1 = document.getElementById('SEL1'); SelProc(sel1, 'OPT1', 'SRCH1');
  var sel2 = document.getElementById('SEL2'); SelProc(sel2, 'OPT2', 'SRCH2');
  var sel3 = document.getElementById('SEL3'); SelProc(sel3, 'OPT3', 'SRCH3');
  var sel4 = document.getElementById('SEL4'); SelProc(sel4, 'OPT4', 'SRCH4');
  document.f.srch1.focus();
}



// Sets the value of a SELECT pulldown:
function setOption(SelectID, Text)
{
  if ( ! Text ) return;

  var select = document.getElementById(SelectID);
  for (i=0; i < select.length; i++)
    {
      iVal = select.options[i].text.indexOf(Text);
      if (iVal != -1)
        {
          // alert('Matched Text: ' + select.options[i].text);
          select.selectedIndex = i;
          return;
        }
    }  
}
    


// Gets the value of a URL Query variable:
function getQueryVariable(variable) 
{
  var query = window.location.search.substring(1);
  if ( ! query ) return;
  
  var vars = query.split("&");
  for (var i = 0; i < vars.length; i++) 
    {
      var pair = vars[i].split("=");
      if (pair[0] == variable) 
        { 
          // alert('Query Variable ' + variable + ' is ' + pair[1]);
          return unescape(pair[1]); 
        }
    }
}