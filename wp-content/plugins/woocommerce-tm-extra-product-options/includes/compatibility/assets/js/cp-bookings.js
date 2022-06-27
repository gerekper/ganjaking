( function( document, $ ) {
	'use strict';

	var TMEPOBOOKINGSJS;

	/**
	 * Calculate the product price
	 *
	 * @since  1.0
	 * @return String
	 */
	function calculateProductPrice( price, totalsHolder ) {
		var bookingPrice;
		var form = totalsHolder.data( 'tm_for_cart' );
		var cost;
		if ( form ) {
			cost = form.find( '.wc-bookings-booking-cost' );
			if ( cost.length ) {
				bookingPrice = parseFloat( cost.attr( 'data-raw-price' ) );
				if ( ! isNaN( bookingPrice ) && ! bookingPrice.isNaN ) {
					price = bookingPrice;
				} else {
					price = false;
				}
			}
		}

		return price;
	}

	// document ready
	$( function() {
		if ( ! $.epoAPI ) {
			return;
		}

		TMEPOBOOKINGSJS = window.TMEPOBOOKINGSJS || null;

		if ( ! TMEPOBOOKINGSJS ) {
			return;
		}

		if ( TMEPOBOOKINGSJS.wc_bookings_add_options_display_cost === 'yes' ) {
			$( '.tm-epo-field' )
				.on( 'change.tcbookings', function() {
					$( '.wc-bookings-booking-form' ).find( 'input, select:not("#wc-bookings-form-start-time, #wc-bookings-form-end-time")' ).first().trigger( 'change' );
				} );
		}
		$( document ).ajaxSuccess( function( event, request, settings ) {
			var parsedUrl = $.epoAPI.util.parseParams( settings.data );
			var n;
			if ( parsedUrl.action === 'wc_bookings_calculate_costs' ) {
				n = $.epoAPI.util.parseJSON( request.responseText );
				$( 'form.cart' ).find( '.wc-bookings-booking-cost' ).attr( 'data-raw-price', n.raw_price );
				$( 'form.cart' ).trigger( {
					type: 'tm-epo-update',
					norules: 1
				} );
			}
		} );
		$( document.body ).on( 'wc_booking_form_changed', function() {
			$( 'form.cart' ).trigger( {
				type: 'tm-epo-update',
				norules: 1
			} );
		} );
		$.epoAPI.addFilter( 'tc_calculate_product_price', calculateProductPrice, 10, 2 );
		$.epoAPI.addFilter( 'tc_calculate_product_regular_price', calculateProductPrice, 10, 2 );
	} );
}( document, window.jQuery ) );
