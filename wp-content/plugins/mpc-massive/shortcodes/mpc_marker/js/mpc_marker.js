/*----------------------------------------------------------------------------*\
	MARKER SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_marker = window.InlineShortcodeView.extend( {
			initialize: function () {
				this.listenTo( this.model, 'update change', this.mpcUpdate );

				this.$el.find( '.vc_element-move' ).remove();

				window.InlineShortcodeView_mpc_marker.__super__.initialize.call( this );
			},
			mpcUpdate: function() {
				var _parent = vc.shortcodes.findWhere( { id: this.model.get( 'parent_id' ) } );
				_parent.trigger( 'mpc:forceRender' );
			}
		} );
	}
} )( jQuery );