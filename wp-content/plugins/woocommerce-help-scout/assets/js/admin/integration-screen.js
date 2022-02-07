/** Global woocommerce_admin_meta_boxes, woocommerce_help_scout_shop_order_params
 *
 * @package integration screen
 */

jQuery( document ).ready(
	function(){
		var app_key = jQuery( '#woocommerce_help-scout_app_key' ).val();
		var app_secret = jQuery( '#woocommerce_help-scout_app_secret' ).val();
		// var helpscout_app_invalide = jQuery('#woocommerce_help-scout_helpscout_app_invalide').val();.

		if (app_key != '' && app_secret != '') {
			var url = "https://secure.helpscout.net/authentication/authorizeClientApplication?client_id=" + app_key + "&secret=" + app_secret;
			jQuery( '#allow_access_url' ).attr( 'href',url ).show();
		}
	}
);
