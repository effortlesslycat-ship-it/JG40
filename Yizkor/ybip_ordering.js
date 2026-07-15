/*
ybip_ordering.js 
    Utilities to create the link to the Google form with parameters and set up the PayPal cart item for payment
Revs:
  30-Dec-2023 GSandler Initial, and added base64 encoding for cookie values
*/

function setCookie(name, value, hours) {
    var expires = "";
    if (hours) {
        var date = new Date();
        date.setTime(date.getTime() + (hours*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + btoa((value) || "")  + expires + "; path=/; secure; domain=jewishgen.org;";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return atob(c.substring(nameEQ.length,c.length));
    }
    return null;
}

function eraseCookie(name) {   
    document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT; domain=jewishgen.org;';
}


function openTitleLink(title, price) {
     var targetForm = 'https://docs.google.com/forms/d/e/1FAIpQLSdCEgAKRXIgEaP-a5dcsPva7scEceD-dsRuVWMFxgnz7ZtUdw/viewform' +
    '?entry.2085436649=' + title +
    '&entry.1035189444=' + price + '&entry.771863574=1';
	 var targetForm = '/auth0/YBIPcartForm.php';
     setBookCookies(title.replace(/'/g,"\\'") ,price);
     window.open(targetForm, "_blank");
     return true;
}

function makeTitleLink(title, price) {
	var targetForm = 'https://docs.google.com/forms/d/e/1FAIpQLSdCEgAKRXIgEaP-a5dcsPva7scEceD-dsRuVWMFxgnz7ZtUdw/viewform' +
    '?entry.2085436649=' + title +
    '&entry.1035189444=' + price + '&entry.771863574=1';
	var targetForm = '/auth0/YBIPcartForm.php';
	document.write ('<a target="_blank" href="' + targetForm + 
				'" onClick="setBookCookies(\'' + title.replace(/'/g,"\\'")  + '\',\'' + price + '\');">JewishGen Press</a>');
	return true;
}

function setBookCookies(title, price) {
	setCookie('jgp_title', title, 1);
	setCookie('jgp_price', price, 1);
	return true;
}

function clearBookCookies() {
	eraseCookie('jgp_title');
	eraseCookie('jgp_price');
	return true;
}

