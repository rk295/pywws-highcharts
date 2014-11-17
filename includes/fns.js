// Javascript to enable link to tab
var url = document.location.toString();
if (url.match('#')) {
    // bit of a hack because on some pages I use tabs, 
    // but on others I use pills
    $('.nav-tabs a[href=#'+url.split('#')[1]+']').tab('show') ;
    $('.nav-pills a[href=#'+url.split('#')[1]+']').tab('show') ;
} 
