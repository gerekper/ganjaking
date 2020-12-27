/*----------------------------------------------------------------------------*\
	INTERACTIVE IMAGE SHORTCODE - Panel
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $popup = $( '#vc_ui-panel-edit-element' );

	$popup.on( 'mpc.render', function() {
		if ( $popup.attr( 'data-vc-shortcode' ) != 'mpc_interactive_image' ) {
			return;
		}

		var $divider = $( '.vc_shortcode-param[data-vc-shortcode-param-name="preview_divider"] .edit_form_line' ),
			$load_preview = $( '<button class="mpc-vc-button button mpc-default mpc-preview">' + _mpc_lang.mpc_interactive_image.preview + '</button>' ),
			$preview = $( '<div class="mpc-coords__preview" />' ),
			_hotspots = [];

		$divider.append( $load_preview );
		$load_preview.after( $preview ).after( '<br>' );

		_hotspots = vc.shortcodes.where( { parent_id: vc.active_panel.model.attributes.id } );

		$load_preview.on( 'click', function() {
			var _background_id = $popup.find( '.wpb_vc_param_value.background_image' ).val();

			$preview.html( '' );

			if ( _background_id == '' ) {
				$preview.append( '<p class="mpc-error">' + _mpc_lang.mpc_interactive_image.no_background + '</p>' );
			} else if ( _hotspots.length == 0 ) {
				$preview.append( '<p class="mpc-error">' + _mpc_lang.mpc_interactive_image.no_hotspots + '</p>' );
			} else {
				$.post( ajaxurl, {
					action: 'mpc_interactive_image_get_image',
					image_id: _background_id
				}, function( response ) {
					if ( response == 'error' ) {
						$preview.append( '<p class="mpc-error">' + _mpc_lang.mpc_interactive_image.no_background + '</p>' );
						return;
					}

					$preview
						.append( response )
						.addClass( 'mpc-loaded' );

					var _image_width = $preview.find( '.mpc-coords__image' )[ 0 ].width;

					$preview.css( 'max-width', _image_width );

					for ( var _index = 0; _index < _hotspots.length; _index++ ) {
						var $point = $( '<div class="mpc-coords__point" />' ),
							_position = _hotspots[ _index ].attributes.params.position.split( '||' );

						if ( _position.length == 2 ) {
							_position[ 0 ] = isNaN( parseFloat( _position[ 0 ] ) ) ? 50 : parseFloat( _position[ 0 ] );
							_position[ 1 ] = isNaN( parseFloat( _position[ 1 ] ) ) ? 50 : parseFloat( _position[ 1 ] );

							$point.css( {
								left: _position[ 0 ] + '%',
								top: _position[ 1 ] + '%'
							} );

							$preview.append( $point );
						}
					}
				} );
			}
		} );
	} );
} )( jQuery );
