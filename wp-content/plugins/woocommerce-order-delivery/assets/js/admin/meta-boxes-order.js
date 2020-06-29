/**
 * Shop Order Meta Boxes
 *
 * @package WC_OD
 * @since   1.5.0
 */

(function( $ ) {

	'use strict';

	$(function() {
		var wc_od_meta_boxes_order_delivery = {
			$body: $( document.body ),

			init: function() {
				this.$body.on( 'wc_od_init_order_delivery_meta_box', this.initDelivery );

				this.$body.trigger( 'wc_od_init_order_delivery_meta_box' );
			},

			initDelivery: function() {
				// Init timepickers.
				$( '#woocommerce-order-delivery .timepicker' ).timepicker({
					timeFormat: 'H:i',
					maxTime: '23:59'
				});
			}
		};

		wc_od_meta_boxes_order_delivery.init();
	});
})( jQuery );