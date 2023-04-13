/**
 * Order Refund.
 *
 * @package WC_Account_Funds/Assets/Js/Admin
 * @since   2.9.0
 */

/* global wc_account_funds_order_refund_params */
(function( $, params ) {

	'use strict';

	var OrderRefund = {

		init: function() {
			this.bindEvents();
		},

		bindEvents: function() {
			$( '#woocommerce-order-items' )
				.on( 'click', 'button.refund-items', this.initRefundForm )
				.on( 'click', '.do-account-funds-refund', function( event ) {
					event.preventDefault();

					// Set the account funds refund flag to true.
					$( '#account_funds_refund' ).val( 1 );

					// Trigger manual refund.
					$( '.do-manual-refund' ).trigger( 'click' );
				})
				.on( 'woocommerce_order_meta_box_do_refund_ajax_data', function( event, data ) {
					var $field = $( '#account_funds_refund' );

					// Include the account funds info in the AJAX request.
					data.account_funds_refund = $field.val();

					// Set the account funds refund flag to false.
					$field.val( 0 );

					return data;
				});
		},

		initRefundForm: function() {
			// WC doesn't trigger any event after reloading the items, so we must add the content on the fly.
			if ( $( '.refund-actions .do-account-funds-refund' ).length ) {
				return;
			}

			$( '.refund-actions .do-manual-refund' ).before( '<button type="button" class="button button-primary do-account-funds-refund">' + params.button_text + '</button>' );
			$( '#refunded_amount' ).after( '<input type="hidden" id="account_funds_refund" name="account_funds_refund" value="0" />' );
		}
	};

	$(function() {
		OrderRefund.init();
	});
})( jQuery, wc_account_funds_order_refund_params );
