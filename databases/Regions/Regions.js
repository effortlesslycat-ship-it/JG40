// Regions.js
// ~~~~~~~~~~
// JavaScript functions for 'Regions' SQL database.
// Called by the "All Country" and "All Topic" systems' index.asp.
// Works in conjunction with data in "RegionsData.js", setup by Regions.asp.
// Warren Blatt & Michael Tobias, Apr 2005, Mar 2015, Jun 2015


// setRegions()
// Populates the "Regions" <SELECT> pulldown for the given database.

function setRegions(database)
{
  // Special case for the JOWBR database:
  if (database == "JOWBR") { database = "DEFAULT"; }

  // Get the Regions <SELECT> pulldown:
  var regionChooser = document.getElementById("GeoRegion");
  if ( ! regionChooser )
    {
      // Error: This should never happen
      return;
    }

  // Save any previously-selected item:
  var save_selectedIndex = regionChooser.selectedIndex;

  // Clear all previous settings of the Regions pulldown:
  //   *** Why?  We really should be able to cache this data,
  //       and prevent unnecessary work. ***
  regionChooser.options.length = 0;

if(database!="CEMETERY"){
  var label = "All Regions";
  var value = "ALL";
  if(document.getElementById("CemCountryListDiv"))document.getElementById("CemCountryListDiv").style.display = "none"; 	
  if(document.getElementById("CemRegionListDiv"))document.getElementById("CemRegionListDiv").style.display = "none"; 	

  // Populate options in the Regions pulldown:
  var index = 0;
  for (var i = 0; i < reg_data.length; i++)
    {
      if ( database == reg_data[i].sys )
        {
          // Get the region's label (the displayed name):
          label = reg_data[i].lab;

          // Save the top-level value:
          if ( reg_data[i].ind == "1" )
            { value = reg_data[i].val; }

          // For sub-regions, indent the label appropriately:
          switch ( reg_data[i].ind )
            {
            case "2":
              label = " - - - " + label;
              break;
            case "3":
              label = " - - - - - - " + label;
              break;
            case "4":
              label = " - - - - - - - - - " + label;
              break;
            case "5":
              label = " - - - - - - - - - - - - " + label;
              break;
            }

          // Create an <OPTION> in the <SELECT> pulldown:
          var y = document.createElement('span');
          y.innerHTML = label;

          regionChooser.options[index++] = new Option (y.innerHTML, reg_data[i].val);
        }
    }
  // *** Should we issue a warning if "database" not found in reg_data? (index==0) ***
  // Provide a default option if this system has no regions:
  if ( ! index )
    { regionChooser.options[0] = new Option (label, value, true, false); }
  // Restore any previously-selected option:
  // *** Doesn't work...  value not saved.
  // regionChooser.selectedIndex = save_selectedIndex;

  // Set the sensitivity of the Regions pulldown:
  regionChooser.disabled = ( ! index );

} else {
  if(document.getElementById("CemRegionListDiv"))document.getElementById("CemCountryListDiv").style.display = "block"; 	
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4){
	const data = JSON.parse(xhr.responseText);
	var option;
	var dropdown = document.getElementById("CemCountryList");
	for (i = dropdown.options.length-1; i >= 0; i--) {
	  dropdown.options[i] = null;
	}
	for (var i = 0; i < data.country.length; i++) {
      		option = document.createElement('option');
      		option.text = data.country[i];
      		option.value = data.country[i];;
      		dropdown.add(option);
    	}
	setCemRegion(dropdown.value)
        
    }
  };
  xhr.open('GET', '/wp_php/cemlist.php');
  xhr.send();
}

}



function setButtonText(ButtonID, Text)
{
  var button = document.getElementById(ButtonID);
  if ( ! button )
    { ; }
  else
    { button.value = Text; }
}


function setSpanText(SpanID, Text)
{
  var span = document.getElementById(SpanID);
  if ( ! span )
    { ; }
  else
    { span.innerHTML = Text; }
}

function setCemRegion(country)
{
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4){
	const data = JSON.parse(xhr.responseText);
	var option;
	var dropdown = document.getElementById("CemRegionList");
	for (i = dropdown.options.length-1; i >= 0; i--) {
	  dropdown.options[i] = null;
	}
	for (var i = 0; i < data.region.length; i++) {
      		option = document.createElement('option');
      		option.text = data.region[i];
      		option.value = data.region[i];;
      		dropdown.add(option);
    	}
	setCemCity(dropdown.value);        
	if(dropdown.options.length>1)
		if(document.getElementById("CemRegionListDiv"))document.getElementById("CemRegionListDiv").style.display = "block"; 	
	else
		if(document.getElementById("CemRegionListDiv"))document.getElementById("CemRegionListDiv").style.display = "none"; 	
    }
  };
  xhr.open('GET', '/wp_php/cemlist.php?country='+country);
  xhr.send();
}
function setCemCity(region)
{

  document.getElementById("SubRegionsDiv").style.display = "block"; 	
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4){
	const data = JSON.parse(xhr.responseText);
	var option;
	var dropdown = document.getElementById("GeoRegion");
	for (i = dropdown.options.length-1; i >= 0; i--) {
	  dropdown.options[i] = null;
	}
	for (var i = 0; i < data.cem_list.length; i++) {
      		option = document.createElement('option');
      		option.text = data.cem_list[i].city+' / '+data.cem_list[i].cem_name;
      		option.value = '01jowbr_99'+data.cem_list[i].cemeteryid;
      		dropdown.add(option);
    	}
	document.getElementById("GeoRegion").disabled = false;;
    }
  };
  xhr.open('GET', '/wp_php/cemlist.php?country='+document.getElementById("CemCountryList").value+'&region='+region);
  xhr.send();
}
