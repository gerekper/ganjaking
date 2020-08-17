/**
 * Cart
 *
 * @package WC_Account_Funds/Assets/JS/Frontend
 * @since   2.3.0
 */
( function( $ ) {

	'use strict';

	var wcAccountFundsCart = {
		init: function() {
			$( document ).on( 'change', '#apply_account_funds', function( event ) {
				if ( event.target.checked ) {
					$( '.woocommerce-cart-form' ).append( '<input type="hidden" name="wc_account_funds_apply" value="Use Account Funds" />' );
					$( '.woocommerce-cart-form :input[name="update_cart"]' )
						.prop( 'disabled', false )
						.attr( 'aria-disabled', false )
						.trigger( 'click' )
					;
				}
			} );
		}
	};

	wcAccountFundsCart.init();
})( jQuery );
