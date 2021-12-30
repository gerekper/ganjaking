/**
 * Product Meta Boxes.
 *
 * @package WC_Store_Credit/Assets/Js/Admin
 * @since   3.2.0
 */

(function( $ ) {

	'use strict';

	$(function() {
		var wc_store_credit_meta_boxes_product = {

			/**
			 * Initialize actions.
			 */
			init: function() {
				this.addShowHideClasses();

				// Trigger the initial change event.
				$( 'select#product-type' ).trigger( 'change' );

				$( '#_store_credit_allow_different_receiver' )
					.on( 'change', this.toggleReceiverFields )
					.trigger( 'change' );
			},

			/**
			 * Add the show/hide classes to the store credit fields.
			 */
			addShowHideClasses: function() {
				$( '#general_product_data .pricing.show_if_simple' ).addClass( 'show_if_store_credit' );
				$( '#general_product_data #_tax_status' ).closest( '.show_if_simple' ).addClass( 'show_if_store_credit' );

				$( '#inventory_product_data ._manage_stock_field' ).addClass( 'show_if_store_credit' );
				$( '#inventory_product_data ._sold_individually_field' )
					.addClass( 'show_if_store_credit' )
					.closest( '.options_group' )
					.addClass( 'show_if_store_credit' );
			},

			/**
			 * Toggle between showing or hiding display receiver fields options.
			 */
			toggleReceiverFields: function () {
				$( '#_store_credit_display_receiver_fields, #_store_credit_receiver_fields_title' )
					.closest( '.form-field' )
					.toggle( $( this ).prop( 'checked' ) );
			}
		};

		wc_store_credit_meta_boxes_product.init();
	});
})( jQuery );
