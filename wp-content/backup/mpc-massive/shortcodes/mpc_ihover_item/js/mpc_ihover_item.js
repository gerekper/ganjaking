/*----------------------------------------------------------------------------*\
	IHOVER ITEM SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_ihover_item = window.InlineShortcodeView.extend( {
			initialize: function () {
				var _parent = vc.shortcodes.findWhere( { id: this.model.get( 'parent_id' ) } );

				this.listenTo( this.model, 'destroy', this.removeView );
				this.listenTo( this.model, 'change:params', this.update );
				this.listenTo( this.model, 'change:parent_id', this.changeParentId );

				this.listenTo( _parent, 'change:params', this.forceUpdate );

				this.listenTo( this.model, 'change:parent_id', this.update );

				window.InlineShortcodeView_mpc_ihover_item.__super__.initialize.call( this );
			},
			clone: function( e ) {
				_.isObject( e ) && e.preventDefault() && e.stopPropagation();

				this.forceUpdate();

				window.InlineShortcodeView_mpc_ihover_item.__super__.clone.call( this );
			},
			rendered: function() {
				var _params = this.model.get( 'params' );

				delete _params.globals;

				this.model.set( 'params', _params );

				window.InlineShortcodeView_mpc_ihover_item.__super__.rendered.call( this );
			},
			beforeUpdate: function () {
				var _params = this.model.get( 'params' ),
					_parent = vc.shortcodes.findWhere( { id: this.model.get( 'parent_id' ) } ),
					_parent_attr;

				_parent_attr = {
					"title_font_preset": _parent.attributes.params.title_font_preset,
					"content_font_preset": _parent.attributes.params.content_font_preset,
					"shape": _parent.attributes.params.shape,
					"effect": _parent.attributes.params.effect,
					"style": _parent.attributes.params.style
				};
				_params.globals = encodeURI( JSON.stringify( _parent_attr ) );

				this.model.set( 'params', _params );
			},
			forceUpdate: function() {
				this.update( this.model );
			}
		} );
	}
} )( jQuery );
