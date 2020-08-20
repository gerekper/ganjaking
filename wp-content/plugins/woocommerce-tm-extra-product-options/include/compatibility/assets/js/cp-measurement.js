( function( window, document, $ ) {
	'use strict';

	var TMEPOJS;
	var tcAPI;
	var TMEPOMEASUREMENTJS;

	function AlterElementQty( elementQty, dataObject ) {
		if ( TMEPOMEASUREMENTJS.wc_measurement_divide !== '1' && TMEPOMEASUREMENTJS.wc_measurement_qty_multiplier === '1' && dataObject.currentCart.find( '#_measurement_needed' ).length > 0 ) {
			elementQty = dataObject.currentCart.find( '#_measurement_needed' ).val();
		}

		return elementQty;
	}

	$( document ).ready( function() {
		TMEPOJS = window.TMEPOJS || null;
		TMEPOMEASUREMENTJS = window.TMEPOMEASUREMENTJS || null;
		tcAPI = $.tcAPI();

		if ( ! TMEPOJS || ! tcAPI || ! TMEPOMEASUREMENTJS ) {
			return;
		}

		$.epoAPI.addFilter( 'tcAlterElementQty', AlterElementQty, 10, 2 );

		$( window ).on( 'tm-epo-init-end', function( event, eventData ) {
			if ( event && eventData && eventData.variationForm ) {
				eventData.variationForm.trigger( 'wc-measurement-price-calculator-update' );
			}
		} );

		$( window ).on( 'epoEventHandlers', function( event, dataObject ) {
			var get_price_excluding_tax = dataObject.get_price_excluding_tax;
			var get_price_including_tax = dataObject.get_price_including_tax;
			var cartContainer = dataObject.cartContainer;
			var currentCart = dataObject.currentCart;
			var totalsHolder = dataObject.totalsHolder;
			var totalsHolderContainer = dataObject.totalsHolderContainer;

			if ( event && dataObject && dataObject.epo ) {
				// measurement price calculator compatibility
				cartContainer
					.find( '.total_price' )
					.off( 'wc-measurement-price-calculator-total-price-change.cpf' )
					.on( 'wc-measurement-price-calculator-total-price-change.cpf', function( e, d, v ) {
						var force = totalsHolder.attr( 'data-taxable' ) && totalsHolder.attr( 'data-prices-include-tax' ) !== '1' && totalsHolder.attr( 'data-tax-display-mode' ) === 'incl';
						var force2 = totalsHolder.attr( 'data-taxable' ) && totalsHolder.attr( 'data-prices-include-tax' ) === '1' && totalsHolder.attr( 'data-tax-display-mode' ) !== 'incl';

						if ( force && ! force2 ) {
							v = get_price_excluding_tax( v, totalsHolder, null, force );
						} else if ( ! force && force2 ) {
							v = get_price_including_tax( v, totalsHolder, null, force2 );
						}
						if ( TMEPOMEASUREMENTJS.wc_measurement_divide === '1' && totalsHolder.data( 'tm_for_cart' ).find( '#_measurement_needed' ).length > 0 ) {
							v = v / totalsHolder.data( 'tm_for_cart' ).find( '#_measurement_needed' ).val();
						}
						totalsHolderContainer.find( '.cpf-product-price' ).val( v );
						totalsHolder.data( 'price', v );
						currentCart.trigger( {
							type: 'tm-epo-update',
							norules: 2
						} );
					} );

				cartContainer
					.find( '.product_price' )
					.off( 'wc-measurement-price-calculator-product-price-change.cpf dwc-measurement-price-calculator-update.cpf' )
					.on( 'wc-measurement-price-calculator-product-price-change.cpf dwc-measurement-price-calculator-update.cpf', function( e, d, v ) {
						var force = totalsHolder.attr( 'data-taxable' ) && totalsHolder.attr( 'data-prices-include-tax' ) !== '1' && totalsHolder.attr( 'data-tax-display-mode' ) === 'incl';
						var force2 = totalsHolder.attr( 'data-taxable' ) && totalsHolder.attr( 'data-prices-include-tax' ) === '1' && totalsHolder.attr( 'data-tax-display-mode' ) !== 'incl';

						if ( force && ! force2 ) {
							v = get_price_excluding_tax( v, totalsHolder, null, force );
						} else if ( ! force && force2 ) {
							v = get_price_including_tax( v, totalsHolder, null, force2 );
						} else {
							v = parseFloat( v );
						}
						if ( TMEPOMEASUREMENTJS.wc_measurement_divide === '1' && totalsHolder.data( 'tm_for_cart' ).find( '#_measurement_needed' ).length > 0 ) {
							v = v / totalsHolder.data( 'tm_for_cart' ).find( '#_measurement_needed' ).val();
						}
						totalsHolderContainer.find( '.cpf-product-price' ).val( v );
						totalsHolder.data( 'price', v );
						currentCart.trigger( {
							type: 'tm-epo-update',
							norules: 2
						} );
					} );

				if ( $( '.product_price, .total_price' ).length > 0 ) {
					$( 'form.cart' ).trigger( 'wc-measurement-price-calculator-update' );
				}
			}
		} );
	} );
}( window, document, window.jQuery ) );
