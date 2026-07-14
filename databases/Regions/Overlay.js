// Overlay.js
// ~~~~~~~~~~
// JavaScript functions for 'Donors Only' access,
//    to enable/disable advanced search database features.
// Warren Blatt, June 2005.
// Updated April 2012, June 2015, August 2015.


var objOverlay    = null;   // Overlay pop-up window (a <DIV>)
var OverlayInited = false;

var ovstyle = null;
var message = null;

function OverlayInit(loginState)
{
  objOverlay = document.createElement("div");
  document.body.appendChild(objOverlay);

  ovstyle = objOverlay.style;

  ovstyle.display         = "none"
  ovstyle.visibility      = "visible"
  ovstyle.position        = "absolute"
  ovstyle.backgroundColor = "#F5F5F5"
  ovstyle.opacity         = "0.85"
  ovstyle.zIndex          = "2147483647"  // max

  //objOverlay.onmouseout = OverlayKill;

  // Create the message to be displayed in the overlay.
  // The "loginState" variable (set by Access.asp) can be:
  //   - "NoLog"  - - User is NOT logged in.
  //   - "NotDonor" - User is logged in, and is NOT a VAS donor.

  message = "The advanced database search features are available only to<BR>" +
	"contributors of $100 to the JewishGen General Fund.<BR>" +
	"To become a contributor, " +
	"<A HREF='/JewishGen/ValueAdded.asp'>click here</A>.";

  if ( loginState == "NoLog" )
    {
      message += "<BR>If you are already a contributor, " +
	             "<A HREF='/CURE/'>click here to login</A>.";
     }

  OverlayInited = true;
}


function OverlayKill()
{
  //ovstyle.transition = "opacity 3s 2s, display 0s 5s";
  //ovstyle.opacity = "0.00";
  ovstyle.display = "none";
  //Alex kotovsky 02.04.20 
  if(document.getElementById('allcountry')){ 
	var op = document.getElementById("allcountry").getElementsByTagName("option");
	for (var i = 0; i < op.length; i++) {
	  // lowercase comparison for case-insensitivity
	  if(op[i].value.toLowerCase() == "cemetery"){ 
	     op[i].disabled = false; 
	     break;
	  }
	}  
  }

}


function OverlayPopup(objTrig, loginState)
{
  if ( ! OverlayInited )
    { OverlayInit(loginState); }

  var offsetTrail = objTrig;
  var offsetTop  = 0;
  var offsetLeft = 0;
  while (offsetTrail)
    {
      offsetTop  += offsetTrail.offsetTop;
      offsetLeft += offsetTrail.offsetLeft;
      offsetTrail = offsetTrail.offsetParent;
    }

  ovstyle.top  = offsetTop;
  ovstyle.left = offsetLeft;

  var content = "<TABLE BORDER='1' " +
  	" WIDTH=" + parseInt(objTrig.offsetWidth) +
  	" HEIGHT="+ parseInt(objTrig.offsetHeight) + ">" +
  	"<TR><TD ALIGN='center'>" + message + "</TR></TD></TABLE>";

  objOverlay.innerHTML = content;

  //ovstyle.opacity = "0.85";
  ovstyle.display = '';
  //Alex kotovsky 02.04.20 
  if(document.getElementById('allcountry')){ 
	var op = document.getElementById("allcountry").getElementsByTagName("option");
	for (var i = 0; i < op.length; i++) {
	  // lowercase comparison for case-insensitivity
	  if(op[i].value.toLowerCase() == "cemetery"){ 
	     op[i].disabled = true; 
	     break;
	  }
	}  
  }
}
