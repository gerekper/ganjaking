function ar_get_arg(name)
{
  name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
  var regexS = "[\\?&]" + name + "=([^&#]*)";
  var regex = new RegExp(regexS);
  var results = regex.exec(window.location.search);
  if(results == null)
    return "";
  else
    return decodeURIComponent(results[1].replace(/\+/g, " "));
}

function ar_set_cookie(c_name, value, exdays)
{
  var exdate = new Date();
  exdate.setDate( exdate.getDate() + exdays );
  var c_value = escape( value ) + ( ( exdays == null ) ? "" : "; expires=" + exdate.toUTCString() );
  document.cookie = c_name + "=" + c_value;
}

function ar_get_cookie(c_name)
{
  var i, x, y, ar_cookies=document.cookie.split(";");
  for ( i = 0; i < ar_cookies.length; i++ )
  {
    x = ar_cookies[i].substr( 0, ar_cookies[i].indexOf("=") );
    y = ar_cookies[i].substr( ar_cookies[i].indexOf("=") + 1 );
    x = x.replace(/^\s+|\s+$/g,"");
    if ( x == c_name ) {
      return unescape(y);
    }
  }
}