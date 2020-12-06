/**
 * Shop Coupon Meta Boxes.
 *
 * @package WC_Store_Credit/Assets/Js/Admin
 * @since   3.0.0
 */

(function( $ ) {

	'use strict';

	$(function() {
		var wc_store_credit_meta_boxes_coupon = {

			/**
			 * Initialize variations actions.
			 */
			init: function() {
				$( 'select#discount_type' )
					.on( 'change', this.typeOptions )
					.trigger( 'change' );

				$( 'input#free_shipping' )
					.on( 'change', this.shippingField )
					.trigger( 'change' );
			},

			/**
			 * Show/hide store credit fields by coupon type options.
			 */
			typeOptions: function() {
				var isStoreCredit = ( 'store_credit' === $( this ).val() );

				$( '#store_credit_general_options' ).toggle( isStoreCredit );
			},

			/**
			 * Show/hide the 'Apply to shipping' field.
			 */
			shippingField: function() {
				var freeShipping = $( this ).prop( 'checked' );

				$( '#store_credit_apply_to_shipping' ).closest( '.form-field' ).toggle( ! freeShipping );
			}
		};

		wc_store_credit_meta_boxes_coupon.init();
	});
})( jQuery );
