( function( window, document, $ ) {
	'use strict';

	var TMEPOEASYBOOKINGSJS;
	var wceb;

	$( document ).ready( function() {
		TMEPOEASYBOOKINGSJS = window.TMEPOEASYBOOKINGSJS || null;
		wceb = window.wceb || null;

		if ( ! TMEPOEASYBOOKINGSJS || ! wceb ) {
			return;
		}

		$( window ).on( 'tm-epo-init-events', function( evt, tc ) {
			var epo_selector = '.tc-extra-product-options';
			var epo = tc.epo;
			var tm_epo_final_total_box = epo.totals_holder.attr( 'data-tm-epo-final-total-box' );
			var current_duration = 1;
			var formatted_total;

			if ( tm_epo_final_total_box === 'disable' ) {
				epo.epo_holder.find( '.tm-epo-field' ).on( 'change.eb', function() {
					if ( wceb.dateFormat === 'two' && wceb.checkIf.datesAreSet() ) {
						wceb.setPrice();
					} else if ( wceb.dateFormat === 'one' && wceb.checkIf.dateIsSet( 'start' ) ) {
						wceb.picker.set();
					} else {
						formatted_total = wceb.formatPrice( wceb.get.basePrice() );
						$( '.booking_price' ).find( '.price .amount' ).html( formatted_total );
					}
				} );
			}
			$( 'body' ).on( 'update_price', function( e, data, response ) {
				var fragments = response.fragments;
				var errors = response.errors;
				var v;

				if ( fragments && ! errors && fragments.epo_base_price ) {
					v = parseFloat( fragments.epo_base_price );

					if ( fragments.epo_duration > 0 ) {
						current_duration = fragments.epo_duration;
					}

					epo.totals_holder.parent().find( '.cpf-product-price' ).val( v );
					epo.totals_holder.data( 'price', v );
					epo.currentCart.trigger( {
						type: 'tm-epo-update',
						norules: 2
					} );
				}
			} );

			function adjustTotal( total ) {
				var options_multiplier = 0;
				var found = false;

				if ( ( ( wceb.dateFormat === 'two' && wceb.checkIf.datesAreSet() ) || ( wceb.dateFormat === 'one' && wceb.checkIf.dateIsSet( 'start' ) ) ) && parseInt( TMEPOEASYBOOKINGSJS.wc_booking_block_qty_multiplier, 10 ) !== 0 ) {
					options_multiplier = options_multiplier + current_duration;
					found = true;
				}

				if ( found ) {
					total = total * options_multiplier;
				}

				return total;
			}

			$.epoAPI.addFilter( 'tcAdjustTotal', adjustTotal, 10, 1 );

			// inject options data to easy bookings ajax
			$.ajaxPrefilter( function( options, originalOptions ) {
				var epos;
				var epos_hidden;
				var form;
				var isURL = false;

				if ( options.type.toLowerCase() !== 'post' ) {
					return;
				}
				if ( originalOptions.url ) {
					isURL = $.epoAPI.util.parseParams( originalOptions.url );
					if ( isURL[ 'wceb-ajax' ] && isURL[ 'wceb-ajax' ] === 'add_new_price' ) {
						isURL = true;
					} else {
						isURL = false;
					}
				}

				if ( isURL || ( originalOptions.data && originalOptions.data.action && originalOptions.data.action === 'add_new_price' && originalOptions.data.additional_cost ) ) {
					epos = $( epo_selector + '.tm-cart-main.tm-product-id-' + epo.product_id + '[data-epo-id="' + epo.epo_id + '"]' );
					epos_hidden = $( '.tm-totals-form-main[data-product-id="' + epo.product_id + '"]' );
					if ( epos.length === 1 ) {
						form = $.extend( epos.tcSerializeObject(), epos_hidden.tcSerializeObject() );
						originalOptions.data.epo_data = $.param( form, false );
						options.data = $.param( $.extend( originalOptions.data, {} ), false );
					}
				}
			} );
		} );
	} );
}( window, document, window.jQuery ) );
