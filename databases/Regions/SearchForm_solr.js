// SearchForm_solr.js
// ~~~~~~~~~~~~~~~~~~
// JavaScript functions for the "All Country" and "All Topic" Database search form.
// Warren Blatt, May 2005.
// Updated July 2007, May 2010, Oct 2011, Jul 2013, Jun 2015

// Globals:
var num_opts   = 0;      // Number of <OPTION>s in the "Search Type" <SELECT>
var OptsInited = false;

function InitOpts(select_obj)
{
  num_opts = select_obj.length;
  OptsInited = true;
}


// OnChange event handler for "Data Type" <SELECT> drop-down.
//   Also called by InitSearchForm().
function SelProc(choice, target1, target2, initialize)
{
  var target_opt = document.getElementById(target1);  // "Search Type" SELECT pulldown
  var target_txt = document.getElementById(target2);  // Search text field

  // First time: Find out the number of "Search Type" options, save it.
  if ( ! OptsInited )
    { InitOpts(target_opt); }


  // Get the selected value of the "Data Type" <SELECT> drop-down.
  var datatype;
  if ( choice == null )
    { datatype = "S"; }
  else
    { datatype = choice.value; }

  if (datatype == "X")
    {
      // If DataType is "Any Field", then "Search Type" MUST be "contains".
      target_opt.disabled = 1;   // Don't allow user to change the "Search Type"
      // Add a new "contains" <OPTION> at the bottom:
      target_opt.options[num_opts] = new Option ("contains", "", false, true);
      target_txt.disabled = 0;   // Enable text field
    }
  else if (datatype == "")
    {
      // If DataType is Unselected, then disable both "Search Type" and text field.
      target_opt.disabled = 1;
      target_opt.selectedIndex = 0;
      target_txt.disabled = 1;
    }
  else
    {
      // For DataTypes "Surname", "GivenName" and "Town":
      // If initializing (and on Page Back), leave "Search Type" pulldown alone.
      // If interactive, then set the "Search Type" pulldown to
      //    a good default for that DataType.
      if ( initialize == 0 )
        {
          if (datatype == "S")                 // Surname -
            { target_opt.selectedIndex = 1; }  //   "Phonetically Like" (BMPM)
          else if (datatype == "G")            // Given Name -
            { target_opt.selectedIndex = 2; }  //   "Sounds Like" (D-M Soundex)
          else if (datatype == "T")            // Town -
            { target_opt.selectedIndex = 1; }  //   "Phonetically Like" (BMPM)
		}

      // Enable both the "Search Type" and search text field:
      target_opt.disabled = 0;
      // If a "contains" <OPTION> was added, remove it:
      if ( target_opt.options.length == num_opts+1 )
         { target_opt.options.length = num_opts; }
      target_txt.disabled = 0;
    }
  return true;
}



function SensitizeDateOpts(val)
{
  document.f.Months.disabled = val;
  document.f.Years.disabled  = val;
}



// Used as a BODY OnLoad() event handler:
function InitSearchForm()
{
  //document.f.dates[0].checked = 1;  // Select "All Entries"
  //SensitizeDateOpts(1);             // Disable Month & Year pulldowns
  var sel1 = document.getElementById('SEL1'); SelProc(sel1, 'OPT1', 'SRCH1', 1);
  var sel2 = document.getElementById('SEL2'); SelProc(sel2, 'OPT2', 'SRCH2', 1);
  var sel3 = document.getElementById('SEL3'); SelProc(sel3, 'OPT3', 'SRCH3', 1);
  var sel4 = document.getElementById('SEL4'); SelProc(sel4, 'OPT4', 'SRCH4', 1);
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