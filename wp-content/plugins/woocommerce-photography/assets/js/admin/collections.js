/*global WCPhotographyAdminCollectionsParams */
( function( $ ) {

	$( function() {
		// Only show the "remove image" button when needed
		if ( ! $( '#collection-thumbnail-id' ).val() ) {
			$( '.remove-image-button' ).hide();
		}

		// Uploading files
		var file_frame;

		$( document ).on( 'click', '.upload-image-button', function( e ) {
			e.preventDefault();

			// If the media frame already exists, reopen it.
			if ( file_frame ) {
				file_frame.open();
				return;
			}

			// Create the media frame.
			file_frame = wp.media.frames.downloadable_file = wp.media({
				title: WCPhotographyAdminCollectionsParams.upload_title,
				button: {
					text: WCPhotographyAdminCollectionsParams.upload_use
				},
				multiple: false
			});

			// When an image is selected, run a callback.
			file_frame.on( 'select', function() {
				var attachment = file_frame.state().get( 'selection' ).first().toJSON();

				$( '#collection-thumbnail-id' ).val( attachment.id );
				$( '#collection-thumbnail img' ).attr( 'src', attachment.url );
				$( '.remove-image-button' ).show();
			});

			// Finally, open the modal.
			file_frame.open();
		});

		$( document ).on( 'click', '.remove-image-button', function() {
			$( '#collection-thumbnail img' ).attr( 'src', WCPhotographyAdminCollectionsParams.placeholder );
			$( '#collection-thumbnail-id' ).val( '' );
			$( '.remove-image-button' ).hide();

			return false;
		});
	});

}( jQuery ) );
