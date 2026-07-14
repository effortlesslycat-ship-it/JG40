function validateMT_JGFF() {

    document.forms["f"].submit();
    var count = document.forms["f"].elements.length;
    for (i=0; i<count; i++)
    {
      document.forms["f"].elements[i].disabled = true;
    }

    document.getElementById("SearchButton").value = "Request has been submitted";


    return true;

}
function reloadMT() {
    var count = document.forms["f"].elements.length;
    for (i=0; i<count; i++)
    {
      document.forms["f"].elements[i].disabled = false;
    }

}