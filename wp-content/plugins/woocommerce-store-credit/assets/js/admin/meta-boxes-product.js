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
				$( 'select#product-type' ).change();
			},

			/**
			 * Add the show/hide classes to the store credit fields.
			 */
			addShowHideClasses: function() {
				$( '#general_product_data .pricing.show_if_simple' ).addClass( 'show_if_store_credit' );
				$( '#general_product_data #_tax_status' ).closest( '.show_if_simple' ).addClass( 'show_if_store_credit' );
			}
		};

		wc_store_credit_meta_boxes_product.init();
	});
})( jQuery );
