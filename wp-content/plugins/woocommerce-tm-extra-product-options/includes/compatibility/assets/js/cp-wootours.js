( function( $ ) {
	'use strict';

	function tc_adjust_product_total_price( product_total_price, product_total_price_without_options, total_plus_fee, extraFee ) {
		var adult;
		var child;
		var infant;
		var ct1;
		var ct2;
		var price_adult;
		var price_child;
		var price_infant;
		var price_ct1;
		var price_ct2;
		var woo_product_total_price;

		if ( $( '[name=wt_number_adult]' ).length ) {
			adult = parseFloat( $( '[name=wt_number_adult]' ).val() );
			child = parseFloat( $( '[name=wt_number_child]' ).length ? $( '[name=wt_number_child]' ).val() : 0 );
			infant = parseFloat( $( '[name=wt_number_infant]' ).length ? $( '[name=wt_number_infant]' ).val() : 0 );
			ct1 = parseFloat( $( '[name=wt_number_ct1]' ).length ? $( '[name=wt_number_ct1]' ).val() : 0 );
			ct2 = parseFloat( $( '[name=wt_number_ct1]' ).length ? $( '[name=wt_number_ct1]' ).val() : 0 );
			if ( $( '.product-type-variable' ).length ) {
				price_adult = $( '.woocommerce---price .p-price .amount' ).text().replace( /^\D+/g, '' );
				price_child = $( '.woocommerce-variation-wt-child-price .amount' ).text().replace( /^\D+/g, '' );
				price_infant = $( '.woocommerce-variation-wt-infant-price .amount' ).text().replace( /^\D+/g, '' );
				price_ct1 = $( '.woocommerce-variation-wt-ct1-price .amount' ).text().replace( /^\D+/g, '' );
				price_ct2 = $( '.woocommerce-variation-wt-ct2-price .amount' ).text().replace( /^\D+/g, '' );
			} else {
				price_adult = $( '._adult_select .p-price .amount' ).text().replace( /^\D+/g, '' );
				price_child = $( '._child_select .p-price .amount' ).text().replace( /^\D+/g, '' );
				price_infant = $( '._infant_select .p-price .amount' ).text().replace( /^\D+/g, '' );
				price_ct1 = $( '._ct1_select .p-price .amount' ).text().replace( /^\D+/g, '' );
				price_ct2 = $( '._ct2_select .p-price .amount' ).text().replace( /^\D+/g, '' );
			}
			if ( isNaN( parseFloat( price_adult ) ) ) {
				price_adult = 0;
			}
			if ( isNaN( parseFloat( price_child ) ) ) {
				price_child = 0;
			}
			if ( isNaN( parseFloat( price_infant ) ) ) {
				price_infant = 0;
			}
			if ( isNaN( parseFloat( price_ct1 ) ) ) {
				price_ct1 = 0;
			}
			if ( isNaN( parseFloat( price_ct2 ) ) ) {
				price_ct2 = 0;
			}
			price_adult = $.epoAPI.util.unformat( price_adult );
			price_child = $.epoAPI.util.unformat( price_child );
			price_infant = $.epoAPI.util.unformat( price_infant );
			price_ct1 = $.epoAPI.util.unformat( price_ct1 );
			price_ct2 = $.epoAPI.util.unformat( price_ct2 );
			woo_product_total_price = parseFloat( parseFloat( price_adult ) * adult ) + parseFloat( total_plus_fee + extraFee ) + parseFloat( parseFloat( price_child ) * child ) + parseFloat( parseFloat( price_infant ) * infant ) + parseFloat( parseFloat( price_ct1 ) * ct1 ) + parseFloat( parseFloat( price_ct2 ) * ct2 );

			if ( ! isNaN( woo_product_total_price ) ) {
				product_total_price = woo_product_total_price;
			}
		}

		return product_total_price;
	}

	// document ready
	$( function() {
		$.epoAPI.addFilter( 'tc_adjust_product_total_price', tc_adjust_product_total_price, 10, 4 );
		$.epoAPI.addFilter( 'tc_adjust_product_total_original_price', tc_adjust_product_total_price, 10, 4 );

		$( document ).on( 'change.tc', '.wt-qf, [name="wt_number_adult"], [name="wt_number_child"], [name="wt_number_infant"], [name="wt_number_ct1"], [name="wt_number_adult"], [name="wt_number_ct2"]', function() {
			$( this ).closest( 'form' ).trigger( {
				type: 'tm-epo-update'
			} );
		} );
		$( document ).on( 'click.tc', '.wt-quantity .minus, .wt-quantity .plus', function() {
			$( this ).closest( '.wt-quantity' ).find( '.wt-qf' ).trigger( 'change' );
		} );
	} );
}( window.jQuery ) );
