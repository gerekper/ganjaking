jQuery( function ($) {
	$( document ).on( 'found_variation', function (e, variation) {
		$( '.woocommerce-product-gallery .yith-wcbm-badge-advanced,.woocommerce-product-gallery .yith-wcbm-badge-show-if-variation' ).removeClass( 'yith-wcbm-badge-show-if-variation--visible' ).hide();
		$( '.yith-wcbm-badge-show-if-variation--' + variation.variation_id ).addClass( 'yith-wcbm-badge-show-if-variation--visible' ).show();
	} );
	$( document ).on( 'reset_data', function () {
		$( '.woocommerce-product-gallery .yith-wcbm-badge-advanced' ).show();
		$( '.yith-wcbm-badge-show-if-variation' ).removeClass( 'yith-wcbm-badge-show-if-variation--visible' ).hide();
	} );
} );
