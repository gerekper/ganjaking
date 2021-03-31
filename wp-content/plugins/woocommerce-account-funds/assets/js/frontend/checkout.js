/**
 * Checkout
 *
 * @package WC_Account_Funds/Assets/JS/Frontend
 * @since   2.2.0
 */
( function( $ ) {

	'use strict';

	var wcAccountFundsCheckout = {
		init: function() {
			$( document )
				.on( 'change', 'input[name=payment_method]', function() {
					if ( $( '#payment_method_accountfunds' ).length ) {
						$( 'body' ).trigger( 'update_checkout' );
					}
				})
				.on( 'change', '#apply_account_funds', function( event ) {
					if ( event.target.checked ) {
						$( 'body' ).trigger( 'update_checkout' );
					}
				})
			;
		}
	};

	wcAccountFundsCheckout.init();
})( jQuery );
