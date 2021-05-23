( function( $ ) {

	$( document ).ready( function() {

		initSettingsPreviewOptions();
		initPreviewLinks();
		updatePreviewLink();

	} );

	function initSettingsPreviewOptions() {

		$( '.gf_form_toolbar_preview .gform-form-toolbar__submenu' )
			.addClass( 'gplp-submenu' )
			.addClass( 'gplp-mode-settings' )
			.html( $( GPLPData.submenuMarkup ).html() );

	}

	function initPreviewLinks() {

		$( 'input.gplp-option' ).on( 'change', function() {
			updatePreviewLink();
			$.post( ajaxurl, {
				action: 'gplp_save_option',
				key:    $( this ).val(),
				value:  $( this ).is( ':checked' ) ? 1 : 0
			}, function( response ) { } );
		} );

	}

	function updatePreviewLink() {

		var $previewLink = $( '.preview-form, .gf_form_toolbar_preview > a' );
		var query        = [];
		var url;

		if ( $( '#gplp-option-live' ).is( ':checked' ) ) {
			$previewLink.text( GPLPData.strings.livePreview );
			url = GPLPData.livePreviewURL;
		} else {
			$previewLink.text( GPLPData.strings.preview );
			url = GPLPData.previewURL;
		}

		$( 'input.gplp-option:not( #gplp-option-live )' ).each( function() {
			if ( $( this ).is( ':checked' ) ) {
				query.push( $( this ).val() + '=1' ); // i.e. ajax=1
			}
		} );

		if ( query.length ) {
			url += '&' + query.join( '&' );
		}

		$previewLink.attr( 'href', url );

	}

} )( jQuery );
