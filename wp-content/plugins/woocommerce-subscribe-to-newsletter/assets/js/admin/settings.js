/**
 * Settings
 *
 * @package WC_Newsletter_Subscription/Assets/JS/Admin
 * @since   2.8.0
 */

(function( $ ) {

	'use strict';

	var wcExtensionSettings = {
		init: function() {
			$( '#woocommerce_newsletter_service' ).change(function() {
				$( '.form-table' )
					.find( '[id^=woocommerce_mailchimp_], [id^=woocommerce_cmonitor_], [id^=woocommerce_mailpoet_]' )
					.closest( 'tr' )
					.hide();

				$( '#mainform [id^=woocommerce_' + $( this ).val() + '_]' ).closest( 'tr' ).show();
			}).change();
		}
	};

	wcExtensionSettings.init();
})( jQuery );
