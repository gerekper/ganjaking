jQuery(document).ready(function(){
	var app_key = jQuery('#woocommerce_help-scout_app_key').val();
	var app_secret = jQuery('#woocommerce_help-scout_app_secret').val();
	if(app_key!='' && app_secret!=''){
		var url = "https://secure.helpscout.net/authentication/authorizeClientApplication?client_id="+app_key+"&secret="+app_secret;
		jQuery('#allow_access_url').attr('href',url).show();
	}	
	
});