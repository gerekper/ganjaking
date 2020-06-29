jQuery( document ).ready( function( $ ) {
	'use strict';

	// create namespace to avoid any possible conflicts
	$.wc_store_catalog_pdf_download_admin = {

		init: function() {

			$( '.form-table' ).on( 'click', '.wc-store-catalog-pdf-download-upload-custom-pdf', function( e ) {
				e.preventDefault();

				// create the media frame
				var i18n = wc_store_catalog_pdf_download_admin_local,
					inputField = $( this ).parents( '.forminp' ).find( 'input#wc_store_catalog_pdf_download_custom_pdf' ),
					previewField = $( this ).parents( '.forminp' ).find( '.custom-pdf-preview' ),
					mediaFrame = wp.media.frames.mediaFrame = wp.media({

					title: i18n.modalPDFTitle,

					button: {
						text: i18n.buttonPDFText
					},

					// document only
					library: {
						type: 'application'
					},

					multiple: false
				});

				// after a file has been selected
				mediaFrame.on( 'select', function() {
					var selection = mediaFrame.state().get( 'selection' );

					selection.map( function( attachment ) {
	
						attachment = attachment.toJSON();

						if ( attachment.id ) {

							// add id to input field
							inputField.val( attachment.id );

							// show preview pdf link
							previewField.prop( 'href', attachment.url ).html( i18n.previewLinkText ).show();

							// show remove image icon
							$( inputField ).parents( '.forminp' ).find( '.remove-pdf' ).show();
						}
					});
				});

				// open the modal frame
				mediaFrame.open();
			});

			$( '.form-table' ).on( 'click', 'a.remove-pdf', function( e ) {
				e.preventDefault();

				$( this ).parents( '.forminp' ).find( '.remove-pdf' ).hide();
				$( this ).parents( '.forminp' ).find( '.custom-pdf-preview' ).hide();
				$( '#wc_store_catalog_pdf_download_custom_pdf' ).val( '' );
			});

			$( '.form-table' ).on( 'click', '.wc-store-catalog-pdf-download-upload-logo', function( e ) {
				e.preventDefault();

				// create the media frame
				var i18n = wc_store_catalog_pdf_download_admin_local,
					inputField = $( this ).parents( '.forminp' ).find( 'input#wc_store_catalog_pdf_download_logo' ),
					previewField = $( this ).parents( '.forminp' ).find( '.logo-preview-image' ),
					mediaFrame = wp.media.frames.mediaFrame = wp.media({

					title: i18n.modalLogoTitle,

					button: {
						text: i18n.buttonLogoText
					},

					// only images
					library: {
						type: 'image'
					},

					multiple: false
				});

				// after a file has been selected
				mediaFrame.on( 'select', function() {
					var selection = mediaFrame.state().get( 'selection' );

					selection.map( function( attachment ) {
	
						attachment = attachment.toJSON();

						if ( attachment.id ) {

							// add attachment id to input field
							inputField.val( attachment.id );

							// show preview image
							previewField.prop( 'src', attachment.url ).removeClass( 'hide' );

							// show remove image icon
							$( inputField ).parents( '.forminp' ).find( '.remove-image' ).show();
						}
					});
				});

				// open the modal frame
				mediaFrame.open();
			});
		
			$( '.form-table' ).on( 'click', 'a.remove-image', function( e ) {
				e.preventDefault();

				$( this ).parents( '.forminp' ).find( '.remove-image' ).hide();
				$( this ).parents( '.forminp' ).find( '.logo-preview-image' ).prop( 'src', '' ).addClass( 'hide' );
				$( '#wc_store_catalog_pdf_download_logo' ).val( '' );
			});

			// toggles header text
			$( '.form-table' ).on( 'change', '#show-header', function() {
				if ( $( this ).is( ':checked' ) ) {
					$( '.header-text-row' ).stop( true, true ).fadeIn( 'fast' );
				} else {
					$( '.header-text-row' ).stop( true, true ).fadeOut( 'fast' );
				}
			});

			// toggles footer text
			$( '.form-table' ).on( 'change', '#show-footer', function() {
				if ( $( this ).is( ':checked' ) ) {
					$( '.footer-text-row' ).stop( true, true ).fadeIn( 'fast' );
				} else {
					$( '.footer-text-row' ).stop( true, true ).fadeOut( 'fast' );
				}
			});
		}
	}; // close namespace

	$.wc_store_catalog_pdf_download_admin.init();
// end document ready
});	