jQuery(document).ready(function(){
	if( typeof user_action == 'undefined' && ( jQuery('[class^="userpro"]').length > 0 || jQuery('[class^="popup_userpro-"]').length ) ){
        var up_link  = document.createElement('link');
    	up_link.id   = 'userpro_min';
    	up_link.rel  = 'stylesheet';
    	up_link.type = 'text/css';
    	up_link.href = up_values.up_url+'css/userpro.min.css';
    	up_link.media = 'all';
    	document.getElementsByTagName("head")[0].appendChild(up_link);
        jQuery.getScript(up_values.up_url+"scripts/scripts.min.js");
	}
});
