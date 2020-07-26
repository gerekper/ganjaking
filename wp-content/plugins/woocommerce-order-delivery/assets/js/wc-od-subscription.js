/**
 * Subscription delivery script
 *
 * @package WC_OD
 * @since   1.5.0
 */

/* global woocommerce_params */
;(function( $, wc ) {

	'use strict';

	$(function() {
		var wc_od_subscription = {
			$body: $( document.body ),

			init: function() {
				this.$body.on( 'wc_od_init_subscription_delivery', this.initDelivery );
				this.$body.on( 'wc_od_update_subscription_delivery', this.updateDelivery );
				this.$body.on( 'wc_od_updated_subscription_delivery', this.initDelivery );

				this.$body.trigger( 'wc_od_init_subscription_delivery' );
			},

			initDelivery: function() {
				$( '#delivery_date' ).wc_od_datepicker( window.wc_od_subscription_l10n ).on( 'changeDate', function() {
					wc_od_subscription.$body.trigger( 'wc_od_update_subscription_delivery' );
				});
			},

			updateDelivery: function() {
				var $form = $( '.woocommerce-MyAccount-content' ).find( 'form' ),
					data = {
					action       : 'wc_od_refresh_subscription_delivery_content',
					delivery_days: {},
					post_data    : $form.serialize()
				};

				// Backward compatibility with older versions of the 'myaccount/edit-delivery.php' template.
				$form.find( '[name]' ).not( '[type="submit"], [name^="delivery_days"], [name="action"]' ).each( function() {
					var $field = $( this );

					data[ $field.attr( 'name' ) ] = $field.val();
				});

				$form.find( '.wc-od-subscription-delivery-days__row' ).each( function() {
					var $enabledField = $( this ).find( '.wc-od-subscription-delivery-days__cell-enabled [name^="delivery_days"]' ),
						$timeFrameField = $( this ).find( '.wc-od-subscription-delivery-days__cell-time-frame [name^="delivery_days"]' ),
						index = $enabledField.attr( 'name' ).replace( 'delivery_days[', '' ).replace( '][enabled]', '' );

					data.delivery_days[ index ] = {
						enabled: ( $enabledField.prop( 'checked' ) ? 1 : 0 ),
						time_frame: $timeFrameField.val()
					};
				});

				$.post( wc.ajax_url, data, function( response ) {
					if ( response && response.content ) {
						$( '.woocommerce-MyAccount-content' ).html( response.content );
					}

					wc_od_subscription.$body.trigger( 'wc_od_updated_subscription_delivery' );
				});
			}
		};

		wc_od_subscription.init();
	});
})( jQuery, woocommerce_params );