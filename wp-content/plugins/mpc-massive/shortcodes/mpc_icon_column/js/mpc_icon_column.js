/*----------------------------------------------------------------------------*\
	ICON COLUMN SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $icon_column ) {
		var $icon = $icon_column.find( '.mpc-icon' ),
			_icon_size;

		$icon_column.imagesLoaded().always( function() {
			if ( $icon_column.is( '.mpc-icon-column--style_2' ) ) {
				_icon_size = parseInt( $icon.outerHeight() * .5 );
				$icon.css( 'top', '-' + _icon_size + 'px' );
				$icon_column.find( '.mpc-icon-column__content-wrap' ).css( 'margin-top', '-' + _icon_size + 'px' );
			}

			if ( $icon_column.is( '.mpc-icon-column--style_4' ) ) {
				_icon_size = parseInt( $icon.outerHeight() * .5 );
				$icon.css( 'left', '-' + _icon_size + 'px' );
			}

			if ( $icon_column.is( '.mpc-icon-column--style_6' ) ) {
				_icon_size = parseInt( $icon.outerHeight() * .5 );
				$icon.css( 'right', '-' + _icon_size + 'px' );
			}
		} );

		$icon_column.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_icon_column = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $icon_column = this.$el.find( '.mpc-icon-column' );

				$icon_column.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $icon_column ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $icon_column ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $icon_column ] );

				init_shortcode( $icon_column );

				window.InlineShortcodeView_mpc_icon_column.__super__.rendered.call( this );
			    this.afterRender();
			},
			afterRender: function() {
				var _parent = vc.shortcodes.findWhere( { id: this.model.get( 'parent_id' ) } );

				setTimeout( function() {
					_parent.trigger( 'mpc:forceRender' );
				}, 250 );
			}
		} );
	}

	var $icon_columns = $( '.mpc-icon-column' );

	$icon_columns.each( function() {
		var $icon_column = $( this );

		$icon_column.one( 'mpc.init', function () {
			init_shortcode( $icon_column );
		} );
	} );
} )( jQuery );
