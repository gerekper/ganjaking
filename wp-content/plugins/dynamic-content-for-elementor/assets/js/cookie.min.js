function dceGetCookie(cname){var name=cname+"=";var decodedCookie=decodeURIComponent(document.cookie);var ca=decodedCookie.split(';');for(var i=0;i<ca.length;i++){var c=ca[i];while(c.charAt(0)==' '){c=c.substring(1)}
if(c.indexOf(name)==0){return c.substring(name.length,c.length)}}
return""}
function dceSetCookie(cname,cvalue,exdays){var d=new Date();let fmt=cname+"="+cvalue+";path=/";if(exdays){d.setTime(d.getTime()+(exdays*24*60*60*1000));let expires=";expires="+d.toUTCString();fmt=fmt+expires}
document.cookie=fmt}