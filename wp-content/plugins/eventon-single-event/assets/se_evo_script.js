/*
	Javascript code all eventon calendars usng single event addon
	version: 0.1
*/
jQuery(document).ready(function($){
	
	if(is_mobile()){
		$('body').find('.fb.evo_ss').each(function(){
			obj = $(this);
			obj.attr({'href':'http://m.facebook.com/sharer.php?u='+obj.attr('data-url')});
		});
	}

	// if mobile check
		function is_mobile(){
			return ( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) )? true: false;
		}
});