jQuery( document ).ready( function( $ ) {
	'use strict';

	// create namespace to avoid any possible conflicts
	$.wc_store_catalog_pdf_download_frontend = {
		
		init: function() {

			// when download button is clicked handles both shortcode and loop buttons
			$( '.wc-store-catalog-pdf-download' ).on( 'click', '.wc-store-catalog-pdf-download-link', function( e ) {
				e.preventDefault();
				
				var thisButton = $( this );

				// apply spinning loader on pdf button
				$( thisButton ).addClass( 'loading' );

				var $data = {
						action: 'wc_store_catalog_pdf_download_frontend_generate_pdf_ajax',
						ajaxPDFDownloadNonce: wc_store_catalog_pdf_download_local.ajaxPDFDownloadNonce,
						layout: $( this ).parent( '.wc-store-catalog-pdf-download' ).find( 'input[name="pdf_layout"]' ).val(),
						is_single: $( this ).parent( '.wc-store-catalog-pdf-download' ).find( 'input[name="is_single"]' ).val(),
						custom_pdf: $( this ).parent( '.wc-store-catalog-pdf-download' ).find( 'input[name="custom_pdf"]' ).val(),
						posts: $( this ).parent( '.wc-store-catalog-pdf-download' ).find( 'input[name="posts"]' ).val()
					};

				$.post( wc_store_catalog_pdf_download_local.ajaxurl, $data, function( response ) {
					// remove the spinning loader on pdf button
					$( thisButton ).removeClass( 'loading' );
					
					if ( response.length && response.length !== 'error' ) {

						document.location = response;
					}
				});	
			});
		}
	}; // close namespace

	$.wc_store_catalog_pdf_download_frontend.init();
// end document ready
});