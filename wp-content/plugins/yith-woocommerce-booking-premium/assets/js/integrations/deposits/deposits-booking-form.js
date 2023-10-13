jQuery( function ( $ ) {
	"use strict";

	$( document ).on( 'yith_wcbk_form_update_response', '.yith-wcbk-booking-form', function ( event, response ) {
		var $car_form      = $( event.target ).closest( '.cart' ),
			$deposits      = $car_form.find( '#yith-wcdp-add-deposit-to-cart' ).first(),
			$full_price    = $deposits.find( '.full-price' ).first(),
			$deposit_price = $deposits.find( '.deposit-price' ).first(),
			$expiration    = $deposits.find( '.expiration-date' );

		if ( response.price && response.price.length > 0 ) {
			$full_price.html( response.price );
		}

		if ( response.deposit_price && response.deposit_price.length > 0 ) {
			$deposit_price.html( response.deposit_price );
		}

		if ( response.deposit_expiration && response.deposit_expiration.length > 0  ) {
			$expiration.html( response.deposit_expiration );
		}

		if ( response.deposit_expired ) {
			$deposits.addClass( 'deposit-disabled' );
			$full_price.closest( 'label' ).find( 'input' ).prop( 'checked', true ).change();
		} else {
			$deposits.removeClass('deposit-disabled');
		}

		$( document ).trigger( 'yith_wcdp_updated_deposit_form' );

	} );

} );
