( function( $ ) {
	"use strict";

	function get_icons_modal_font_icons( font ) {
		$.post( ajaxurl, {
			action: 'mpc_icon_get_icons_modal_font_icons',
			font:   font
		}, function( response ) {
			$icons_modal_grid.find( '.mpc-' + font ).remove();

			$icons_modal_grid
				.children()
					.css( 'display', 'none' )
					.end()
				.append( response )
					.trigger( 'mpc.updated' )
					.trigger( 'mpc.selected' );

			_icons_fonts_loaded[ font ] = true;

			$icons_modal.trigger( 'mpc.search' );
		} );
	}

	function get_icons_modal_font_link( font ) {
		$.post( ajaxurl, {
			action: 'mpc_icon_get_icons_modal_font_link',
			font:   font
		}, function( response ) {
			$( 'head' ).prepend( response );

			get_icons_modal_font_icons( font );
		} );
	}

/* Icon modal */
	var $icons_modal = $( '#mpc_icon_select_grid_modal' ),
		$icons_modal_grid = $( '#mpc_icon_select_grid' ),
		_icons_fonts_loaded = {};

	$icons_modal_grid.children().each( function() {
		_icons_fonts_loaded[ this.className.replace( 'mpc-', '' ) ] = true;
	} );

	if ( $icons_modal.is( '.mpc-modal-init' ) ) {
		var $icons_search = $( '#mpc_icon_select_search' ),
			$icons_family = $( '#mpc_icon_select_family' ),
			$icons = $icons_modal.find( 'i' );

		$icons_modal.removeClass( 'mpc-modal-init' );

		$icons_modal.dialog( {
			title: $icons_modal.attr( 'data-modal-title' ),
			dialogClass: 'mpc-icons-modal',
			target: null,
			active: null,
			show: true,
			hide: true,
			modal: true,
			resizable: false,
			width: 640,
			height: 600,
			autoOpen: false,
			closeOnEscape: true,
			close: function() {
				$icons_search.val( '' );
				$icons.show();
				$icons.filter( '[data-active="1"]' ).attr( 'data-active', 0 );

				_mpc_vars.$body.css( 'overflow', '' );
			},
			open: function() {
				var _active = $icons_modal.dialog( 'option', 'active' );

				if ( _active ) {
					_active = _active.split( ' ' );

					if ( _active.length == 2 ) {
						$icons_family.val( _active[ 0 ] );
					}
				}

				$icons_family.trigger( 'change' );

				_mpc_vars.$body.css( 'overflow', 'hidden' );
			}
		} );

		$icons_modal.on( 'click', 'i', function() {
			var icon_class = $( this ).attr( 'class' ),
				$target = $icons_modal.dialog( 'option', 'target' );

			if ( $target != null ) {
				$target.trigger( 'mpc.update', [ icon_class ] );

				$icons_modal.dialog( 'option', 'target', null );
			}

			$icons_modal.dialog( 'close' );
		} ).on( 'mpc.search', function() {
			if ( $icons_search.val() != '' ) {
				$icons_search.trigger( 'keyup' );
			}
		} );

		$icons_search.on( 'keyup', function() {
			if ( $icons_search.val() != '' ) {
				$icons.hide();
				$icons.filter( '[class*="' + $icons_search.val() + '"]' ).show();
			} else {
				$icons.show();
			}
		} );

		$icons_family.on( 'change', function() {
			if ( typeof _icons_fonts_loaded[ $icons_family.val() ] == 'undefined' ) {
				get_icons_modal_font_link( $icons_family.val() );
			} else {
				$icons_modal_grid
					.children()
					.css( 'display', 'none' )
					.filter( 'div.mpc-' + $icons_family.val() )
					.css( 'display', 'block' );

				$icons_modal_grid.trigger( 'mpc.selected' );

				$icons_modal.trigger( 'mpc.search' );
			}
		} );

		$icons_modal_grid.on( 'mpc.updated', function() {
			$icons = $icons_modal.find( 'i' );
		} ).on( 'mpc.selected', function() {
			$icons.filter( '[class="' + $icons_modal.dialog( 'option', 'active' ) + '"]' ).attr( 'data-active', 1 );
		} );
	}

/* Icon fields */
	var $icons_fields = $( '.vc_wrapper-param-type-mpc_icon' ),
		$icons_fields_values = $icons_fields.find( '.mpc-icon-value' ),
		$current_icon;

	$icons_fields.on( 'click', '.mpc-icon-select', function( event ) {
		$current_icon = $( this );

		if ( $icons_modal.length ) {
			$icons_modal.dialog( 'option', 'target', $icons_fields );
			$icons_modal.dialog( 'option', 'active', $current_icon.siblings( '.mpc-icon-value' ).val() );
			$icons_modal.dialog( 'open' );
		}

		event.preventDefault();
	} );

	// Update icon
	$icons_fields.on( 'mpc.update', function( event, icon_class ) {
		if ( $current_icon != null ) {
			$current_icon.siblings( '.mpc-icon-value' ).val( icon_class ).trigger( 'change' );
			$current_icon.children( 'i' ).attr( 'class', icon_class );
			$current_icon.removeClass( 'mpc-icon-empty' );
		}
	} );
	$icons_fields_values.on( 'mpc.change', function() {
		var $icon = $( this ),
			_icon_class = $icon.val();

		if ( _icon_class != '' ) {
			$icon
				.siblings( '.mpc-icon-select' )
					.removeClass( 'mpc-icon-empty' )
					.children( 'i' )
						.attr( 'class', _icon_class );

			get_icons_modal_font_link( _icon_class.split( ' ' )[ 0 ] );
		} else {
			$icon.siblings( '.mpc-icon-clear' ).trigger( 'click' );
		}
	} );

	// Clear icon
	$icons_fields.on( 'click', '.mpc-icon-clear', function( event ) {
		var $icon_clear = $( this );

		$icon_clear.siblings( '.mpc-icon-value' ).val( '' );
		$icon_clear.siblings( '.mpc-icon-select' )
			.addClass( 'mpc-icon-empty' )
			.children( 'i' ).attr( 'class', '' );

		event.preventDefault();
	} );

	// Get used icon fonts
	var _used_icons_fonts = {};
	$icons_fields.find( '.mpc-icon-value' ).filter( ':not([value=""])' ).each( function() {
		var _font = $( this ).val();

		if ( _font ) {
			_font = _font.split( ' ' );

			if ( _font.length == 2 && typeof _icons_fonts_loaded[ _font[ 0 ] ] == 'undefined' ) {
				_used_icons_fonts[ _font[ 0 ] ] = true;
			}
		}
	} );

	for ( var _font in _used_icons_fonts ) {
		get_icons_modal_font_link( _font );
	}
})( jQuery );
