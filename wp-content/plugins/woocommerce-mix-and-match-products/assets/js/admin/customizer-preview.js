/**
 * This file adds some LIVE to the Theme Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. This javascript will grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package  WooCommerce Mix and Match Products/Customizer
 */

( function ( $ ) {

	// Switch to most recent MNM product page, where we can live-preview Customizer options.
	wp.customize.bind(
		'preview-ready',
		function () {

			var is_mnm_page = $( 'form.mnm_form' ).length;
			var redirected  = false;

			wp.customize.preview.bind(
				'wc-mnm-open-product',
				function ( data ) {
					// When the section is expanded, open the login designer page specified via localization.
					if ( true === data.expanded && ! is_mnm_page && WC_MNM_CONTROLS.product_page ) {
						redirected = true;
						wp.customize.preview.send( 'url', WC_MNM_CONTROLS.product_page );
					}
				}
			);

		}
	);

	// Number of Columns in grid layout.
	wp.customize(
		'wc_mnm_number_columns',
		function ( value ) {
			value.bind(
				function ( to ) {
					$( '.mnm_child_products.grid' ).removeClass(
						function ( index, css ) {
							return ( css.match( /columns-\S+/g ) || [] ).join( ' ' );
						}
					).addClass( 'columns-' + to );
					$( '.mnm_child_products.grid > li' ).removeClass( 'first last' );
					$( '.mnm_child_products.grid > li:nth-child( ' + to + 'n+1 )' ).addClass( 'first' );
					$( '.mnm_child_products.grid > li:nth-child( ' + to + 'n)' ).addClass( 'last' );
				}
			);
		}
	);

} )( jQuery );
