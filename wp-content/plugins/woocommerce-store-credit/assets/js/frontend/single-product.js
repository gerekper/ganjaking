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
				this.initPresetAmountsButtons();
				this.initSendToDifferentCustomer();
			},

			initPresetAmountsButtons: function() {
				var that = this,
				    $presetAmounts = $( '.store-credit-preset-amount' );

				// Show the custom amount fields if there are no preset amounts.
				this.toggleCustomAmountFields( ! $presetAmounts.length );

				$presetAmounts.on( 'click', function() {
					var value = $( this ).data( 'value' ),
						isCustom = ( value === 'custom' );
					$( '.store-credit-preset-amount' ).removeClass( 'selected' );
					$( this ).addClass( 'selected' );

					that.toggleCustomAmountFields( isCustom );

					if ( ! isCustom ) {
						that.updateCustomAmountField( value );
					}
				});
			},

			updateCustomAmountField: function( amount ) {
				$( '#store_credit_custom_amount' ).val( amount );
			},

			toggleCustomAmountFields: function( show ) {
				if ( show ) {
					this.updateCustomAmountField( '' );
					$( 'div.store-credit-custom-amount-fields' ).slideDown();
				} else {
					$( 'div.store-credit-custom-amount-fields' ).hide();
				}
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
