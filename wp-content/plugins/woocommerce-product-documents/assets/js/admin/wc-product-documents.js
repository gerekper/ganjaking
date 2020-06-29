jQuery( document ).ready( function( $ ) {

	'use strict';

	/* global confirm, wc_product_documents_admin_params */

	// Open/close
	$( '#wc-product-documents-data .wc-metaboxes-wrapper' )
	.on( 'click', '.expand_all', function() {
		$( this ).closest('.wc-metaboxes-wrapper').find( '.wc-metabox > .wc-metabox-content' ).show();
		return false;
	} )
	.on( 'click', '.close_all', function(){
		$( this ).closest( '.wc-metaboxes-wrapper' ).find( '.wc-metabox > .wc-metabox-content' ).hide();
		return false;
	} );


	$( '#wc-product-documents-data' )
	// add a new document section
	.on( 'click', '.add-new-product-documents-section', function() {

		var index = -1;

		// find the largest current section index (if any)
		$( '.wc-product-documents-sections .wc-product-documents-section .product-documents-section-index' ).each( function() {
			var value = parseInt( $( this ).val(), 10 );
			index = ( value > index ) ? value : index;
		} );

		index++;

		var html = wc_product_documents_admin_params.new_section.replace( /{index}/g, index );

		$( '.wc-product-documents-sections' ).append( html );
		updateDocumentSectionRowIndexes();
		setDefaultDocumentSection();

		return false;
	} )
	// remove a document section
	.on( 'click', '.remove-wc-product-documents-section', function() {

		var answer = confirm( wc_product_documents_admin_params.confirm_remove_section_text );

		if ( answer ) {
			var section = $( this ).closest( '.wc-product-documents-section' );
			$( section ).remove();

			updateDocumentSectionRowIndexes();
			setDefaultDocumentSection();
		}

		return false;
	} );


	// Make the document sections sortable
	$( '.wc-product-documents-sections' ).sortable( {
		items:  '.wc-product-documents-section',
		cursor: 'move',
		axis:   'y',
		handle: 'h3',
		scrollSensitivity: 40,
		helper: function( e, ui ) {
			return ui;
		},
		start: function( event, ui ) {
			ui.item.css( 'border-style', 'dashed' );
		},
		stop: function( event, ui ) {
			ui.item.removeAttr( 'style' );
			updateDocumentSectionRowIndexes();
		}
	} );


	// update the product tab indexes, based on the current section ordering
	function updateDocumentSectionRowIndexes() {
		$( '.wc-product-documents-sections .wc-product-documents-section' ).each(
			function( index, el ) {
				$( '.product-documents-section-position', el ).val( parseInt( $( el ).index( '.wc-product-documents-sections .wc-product-documents-section' ), 10 ) );
			}
		);
	}


	// ensure a default is set
	function setDefaultDocumentSection() {
		var $radios = $( 'input:radio[name=product_documents_default_section]' );

		if ( $radios.is( ':checked' ) === false ) {
			$radios.filter( '[value=0]' ).prop( 'checked', true );
		}
	}


	/* Documents Handling */


	$( '#wc-product-documents-data' )
	// add a new document row
	.on( 'click', '.wc-product-documents-add-document', function() {

		var $parentSection = $( this ).closest( '.wc-product-documents-section' );
		var index = $( '.product-documents-section-index', $parentSection ).val();

		var subIndex = -1;

		// find the largest current section index (if any)
		$( '.wc-product-document .wc-product-document-sub-index', $parentSection ).each( function() {
			var value = parseInt( $( this ).val(), 10 );
			subIndex = ( value > subIndex ) ? value : subIndex;
		} );

		subIndex++;

		var html = wc_product_documents_admin_params.new_document.replace( /{index}/g, index ).replace( /{sub_index}/g, subIndex );

		$( this ).closest( '.wc-product-documents' ).append( html );
		updateDocumentRowIndexes( $parentSection );
		setAttachFileHandler();

		return false;
	} )
	// remove a document
	.on( 'click', '.wc-product-documents-remove-document', function() {

		var answer = confirm( wc_product_documents_admin_params.confirm_remove_document_text );

		if ( answer ) {
			var $parentSection = $( this ).closest( '.wc-product-documents-section' );
			var $documentRow   = $( this ).closest( '.wc-product-document' );
			$documentRow.remove();

			updateDocumentRowIndexes( $parentSection );
		}

		return false;
	} );


	// product document ordering
	$( 'table.wc-product-documents tbody' ).sortable( {
		items  : 'tr',
		cursor : 'move',
		axis   : 'y',
		handle : 'td',
		scrollSensitivity : 40,
		helper: function( e, ui ) {
			return ui;
		},
		start: function( event, ui ) {
			ui.item.css( 'background-color','#f6f6f6' );
		},
		stop: function( event, ui ) {
			ui.item.removeAttr( 'style' );
			updateDocumentRowIndexes();
		}
	} );


	// update the product document table row positions, based on the current ordering
	function updateDocumentRowIndexes( $section ) {
		$( '.wc-product-documents .wc-product-document', $section ).each(
			function( index, el ) {
				$( '.wc-product-document-position', el ).val( parseInt( $( el ).index( '.wc-product-documents .wc-product-document' ), 10 ) );
			}
		);
	}


	/* File Upload */

	var file_frame;
	var $currentDocumentRow;

	/**
	 * Add handlers to the "Select a File" inputs, to launch the media browser
	 */
	function setAttachFileHandler() {

		$( '#wc-product-documents-data .wc-product-documents-set-file' ).on( 'click', function( event ) {

			event.preventDefault();

			// save the element that was clicked on so we can set the image
			$currentDocumentRow = $( event.target ).closest( 'tr' );

			// If the media frame already exists, reopen it.
			if ( file_frame ) {
				file_frame.open();
				return;
			}

			// Create the media frame.
			file_frame = wp.media.frames.file_frame = wp.media( {
				title: wc_product_documents_admin_params.select_file_text,
				button: {
					text: wc_product_documents_admin_params.set_file_text
				},
				multiple: false
			} );

			// When an image is selected, run a callback.
			file_frame.on( 'select', function() {
				// We set multiple to false so only get one image from the uploader
				var attachment = file_frame.state().get( 'selection' ).first().toJSON();

				// Set the file id/display the file url
				$( 'input.wc-product-document-file-location', $currentDocumentRow ).val( attachment.url );
			});

			// Finally, open the modal
			file_frame.open();
		});
	}

	// Initialize
	setAttachFileHandler();

});
