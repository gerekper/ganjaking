/**
 * Single Product scripts.
 *
 * @package WC_Store_Credit/Assets/Js/Frontend
 * @since   3.2.0
 */

(function( $ ) {

	'use strict';

	$(function() {
		var wc_store_credit_single_product = {

			init: function() {
				this.initSendToDifferentCustomer();
			},

			initSendToDifferentCustomer: function() {
				var $checkbox = $( '#send-to-different-customer' );

				$( 'div.store-credit-receiver-fields' ).toggle( $checkbox.prop( 'checked' ) );

				$checkbox.on( 'change', this.toggleReceiverFields );
			},

			toggleReceiverFields: function() {
				if ( $( this ).is( ':checked' ) ) {
					$( 'div.store-credit-receiver-fields' ).slideDown();
				} else {
					$( 'div.store-credit-receiver-fields' ).slideUp();
				}
			}
		};

		wc_store_credit_single_product.init();
	});
})( jQuery );
