/**
 * Subscription delivery script
 *
 * @package WC_OD
 * @since   1.5.0
 */

/* global ajaxurl */
(function( $ ) {

	'use strict';

	$(function() {
		var wc_od_meta_boxes_subscription_delivery = {
			$body: $( document.body ),

			init: function() {
				this.$body.on( 'wc_od_init_subscription_delivery_meta_box', this.initDelivery );
				this.$body.on( 'wc_od_update_subscription_delivery_meta_box', this.updateDelivery );
				this.$body.on( 'wc_od_updated_subscription_delivery_meta_box', this.initDelivery );

				this.$body.trigger( 'wc_od_init_subscription_delivery_meta_box' );

				$( '#woocommerce-subscription-delivery' ).on( 'click', '.wc-od-subscription-delivery__preferences-toggle', function() {
					$( '#woocommerce-subscription-delivery' ).find( '.wc-od-subscription-delivery__preferences' ).toggleClass( 'open' );
				});
			},

			initDelivery: function() {
				$( '#woocommerce-subscription-delivery [name="_delivery_date"]' ).datepicker({
					dateFormat: 'yy-mm-dd',
					numberOfMonths: 1,
					showButtonPanel: true
				}).on( 'change', function() {
					wc_od_meta_boxes_subscription_delivery.$body.trigger( 'wc_od_update_subscription_delivery_meta_box' );
				});
			},

			updateDelivery: function() {
				var data = {
					action : 'wc_od_refresh_subscription_delivery_meta_box',
					post_id: $( '#post_ID' ).val(),
					delivery_date: $( '#woocommerce-subscription-delivery [name="_delivery_date"]' ).val(),
					delivery_time_frame: $( '#woocommerce-subscription-delivery [name="_delivery_time_frame"]' ).val()
				};

				$.post( ajaxurl, data, function( response ) {
					if ( response && response.content ) {
						$( '#woocommerce-subscription-delivery .inside' ).html( response.content );
					}

					wc_od_meta_boxes_subscription_delivery.$body.trigger( 'wc_od_updated_subscription_delivery_meta_box' );
				});
			}
		};

		wc_od_meta_boxes_subscription_delivery.init();
	});
})( jQuery );