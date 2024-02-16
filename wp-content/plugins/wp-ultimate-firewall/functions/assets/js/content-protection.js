//WP Ultimate Firewall Content Protection
var isNS = (navigator.appName == "Netscape") ? 1 : 0;
  if(navigator.appName == "Netscape") document.captureEvents(Event.MOUSEDOWN||Event.MOUSEUP);
  function mischandler(){
   return false;
 }
  function mousehandler(e){
	 var myevent = (isNS) ? e : event;
	 var eventbutton = (isNS) ? myevent.which : myevent.button;
	if((eventbutton==2)||(eventbutton==3)) return false;
 }
 document.oncontextmenu = mischandler;
 document.onmousedown = mousehandler;
 document.onmouseup = mousehandler;
 document.onkeydown = function(e) {
		if (e.ctrlKey && (e.keyCode === 85)) {
			return false;
		} else {
			return true;
		}
};
jQuery(document).bind("keyup keydown", function(e){
	if( e.ctrlKey && e.keyCode == 80 || e.ctrlKey && e.keyCode ==  67 || e.ctrlKey && e.keyCode ==  83 ){
		return false;
	}
});		