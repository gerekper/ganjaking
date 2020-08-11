( function( window, document, $ ) {
	'use strict';

	var TMEPOJS;
	var TMEPOBOOKINGSJS;
	var tcAPI;

	function tc_compatibility_bookings( epoObject ) {
		var form = epoObject.form;
		var this_epo_totals_container = epoObject.this_epo_totals_container;
		var this_epo_container = epoObject.this_epo_container;
		var this_totals_container = epoObject.this_totals_container;
		var main_epo_inside_form = epoObject.main_epo_inside_form;
		var main_cart = epoObject.main_cart;
		var product_id = epoObject.product_id;
		var epo_id = epoObject.epo_id;
		var optionsForm;
		var epos;
		var bookings_form = form.find( '.wc-bookings-booking-form' );
		var bookings_trigger = bookings_form.find( 'input, select' ).first();
		var epo_trigger11 = bookings_form.find( '.tm-epo-counter' );
		var epo_trigger = main_cart.find( '.tm-epo-counter' );
		var tm_epo_final_total_box = this_epo_totals_container.attr( 'data-tm-epo-final-total-box' );
		var tm_epo_final_total_box_is_hidden = tm_epo_final_total_box === 'hide' || tm_epo_final_total_box === 'disable' || tm_epo_final_total_box === 'disable_change';

		if ( bookings_trigger.length > 0 && bookings_form.length > 0 && epo_trigger.length > 0 ) {
			form.on( 'submit', function() {
				form.find( tcAPI.addToCartButtonSelector ).first().addClass( 'disabled' );
			} );

			this_epo_totals_container.data( 'tc_is_bookings', 1 );
			this_epo_totals_container.data( 'bookings_form', bookings_form );
			this_epo_totals_container.data( 'bookings_form_init', 0 );
			this_epo_totals_container.data( 'price', 0 );
			this_totals_container.find( '.cpf-product-price' ).val( 0 );

			// Don't include option prices in bookings ajax price
			if ( ! tm_epo_final_total_box_is_hidden ) {
				$.ajaxPrefilter( function( options, originalOptions ) {
					if ( options.type.toLowerCase() !== 'post' ) {
						return;
					}
					if ( originalOptions.data && originalOptions.data.action && originalOptions.data.action === 'wc_bookings_calculate_costs' && originalOptions.data.form ) {
						optionsForm = $.epoAPI.util.parseParams( originalOptions.data.form, true );
						optionsForm = $.extend( optionsForm, { tc_suppress_filter_booking_cost: 1 } );
						originalOptions.data.form = $.param( optionsForm, false );
						options.data = $.param( $.extend( originalOptions.data, {} ), false );
					}
				} );
			}

			// Find if product price is valid for the bookable product
			$( document ).ajaxSuccess( function( event, xhr, settings ) {
				var data;
				var response;
				var pp;

				if ( ! event || ! settings.data ) {
					return;
				}
				if ( xhr && xhr.responseText ) {
					data = $.epoAPI.util.parseParams( settings.data );

					if ( data.action && data.action === 'wc_bookings_calculate_costs' ) {
						response = $.epoAPI.util.parseJSON( xhr.responseText );

						if ( response.html ) {
							if ( response && response.result === 'SUCCESS' ) {
								pp = parseFloat(
									$.epoAPI.util.unformat(
										$( '<div>' + response.html + '</div>' )
											.find( '.amount' )
											.text()
									)
								);

								if ( Number.isFinite( pp ) ) {
									if ( tm_epo_final_total_box_is_hidden ) {
										pp = parseFloat( pp ) - parseFloat( this_epo_totals_container.data( 'tc_totals_ob' ).options_total_price );
									}
									this_epo_totals_container.data( 'price', pp );
									this_totals_container.find( '.cpf-product-price' ).val( pp );
									this_epo_totals_container.data( 'bookings_form_init', 1 );
									main_cart.trigger( {
										type: 'tm-epo-update',
										norules: 2
									} );
								}
							} else if ( response && response.result === 'ERROR' ) {
								this_epo_totals_container.data( 'bookings_form_init', 0 );
							}
						}
					}

					// Pricing not valid yet since no product price is calculated
					if ( data.action && data.action === 'wc_bookings_get_blocks' ) {
						this_epo_totals_container.data( 'price', 0 );
						this_totals_container.find( '.cpf-product-price' ).val( 0 );
						main_cart.trigger( {
							type: 'tm-epo-update',
							norules: 2,
							product_false: true
						} );
					}
				}
			} );

			if ( tm_epo_final_total_box_is_hidden && epo_trigger11.length === 0 ) {
				this_epo_container
					.find( '.tm-epo-field' )
					.not( '.tm-epo-counter' )
					.off( 'change.tcbookings' )
					.on( 'change.tcbookings', function() {
						setTimeout( function() {
							bookings_trigger.trigger( 'change' );
						}, 100 );
					} );
			}
		}

		if ( tm_epo_final_total_box_is_hidden && ! main_epo_inside_form ) {
			if ( bookings_trigger.length > 0 ) {
				// Trigger main bookings cost change when plugin fields change
				this_epo_container
					.find( '.tm-epo-field' )
					.not( '.tm-epo-counter' )
					.off( 'change.tcbookings' )
					.on( 'change.tcbookings', function() {
						setTimeout( function() {
							bookings_trigger.trigger( 'change' );
						}, 100 );
					} );

				// Inject options data to bookings ajax
				$.ajaxPrefilter( function( options, originalOptions ) {
					if ( options.type.toLowerCase() !== 'post' ) {
						return;
					}
					if ( originalOptions.data && originalOptions.data.action && originalOptions.data.action === 'wc_bookings_calculate_costs' && originalOptions.data.form ) {
						epos = $( tcAPI.epoSelector + '.tm-cart-main.tm-product-id-' + product_id + "[data-epo-id='" + epo_id + "']" );
						if ( epos.length === 1 ) {
							optionsForm = $.epoAPI.util.parseParams( originalOptions.data.form, true );
							optionsForm = $.extend( optionsForm, epos.tcSerializeObject() );
							originalOptions.data.form = $.param( optionsForm, false );
							options.data = $.param( $.extend( originalOptions.data, {} ), false );
						}
					}
				} );
			}
		}
	}

	function adjustTotal( total, dataObject ) {
		var totalsHolder = dataObject.totalsHolder;
		var bookingform = totalsHolder.data( 'bookings_form' );
		var options_multiplier = 0;
		var found = false;

		if ( totalsHolder.data( 'tc_is_bookings' ) !== undefined ) {
			if ( bookingform.length ) {
				if ( TMEPOBOOKINGSJS.wc_booking_person_qty_multiplier === '1' ) {
					bookingform.find( "[id^='wc_bookings_field_persons']" ).each( function() {
						options_multiplier = options_multiplier + parseFloat( $( this ).val() );
						found = true;
					} );
				}
				if ( TMEPOBOOKINGSJS.wc_booking_block_qty_multiplier === '1' && bookingform.find( '#wc_bookings_field_duration' ).length ) {
					options_multiplier = options_multiplier + parseFloat( bookingform.find( '#wc_bookings_field_duration' ).val() );
					found = true;
				}
			}

			if ( found ) {
				total = total * options_multiplier;
			}
		}

		return total;
	}

	function adjustFormattedFinalTotal( formattedFinalTotal, dataObject ) {
		var event = dataObject.event;
		var totalsHolder = dataObject.totalsHolder;

		if ( event.product_false === true || ( totalsHolder.data( 'bookings_form_init' ) !== undefined && totalsHolder.data( 'bookings_form_init' ) === 0 ) ) {
			formattedFinalTotal = '-';
		}

		return formattedFinalTotal;
	}

	$( document ).ready( function() {
		TMEPOJS = window.TMEPOJS || null;
		TMEPOBOOKINGSJS = window.TMEPOBOOKINGSJS || null;
		tcAPI = $.tcAPI();

		if ( ! TMEPOJS || ! TMEPOBOOKINGSJS || ! tcAPI ) {
			return;
		}

		$.epoAPI.addFilter( 'tcAdjustFormattedFinalTotal', adjustFormattedFinalTotal, 10, 2 );
		$.epoAPI.addFilter( 'tcAdjustTotal', adjustTotal, 10, 2 );

		$( window ).on( 'tm-epo-compatibility', function( event, epoObject ) {
			if ( event && epoObject && epoObject.epo ) {
				tc_compatibility_bookings( epoObject.epo );
			}
		} );

		if ( TMEPOBOOKINGSJS.wc_booking_person_qty_multiplier === '1' ) {
			$( document ).on( 'change', '#wc_bookings_field_persons', function() {
				var t = $( this );
				var form = t.closest( 'form' );

				form.trigger( {
					type: 'tm-epo-update',
					norules: 1
				} );
			} );
		}
	} );
}( window, document, window.jQuery ) );
