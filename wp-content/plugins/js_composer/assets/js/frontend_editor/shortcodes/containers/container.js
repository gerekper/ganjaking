(function ( $ ) {
	'use strict';

	window.vc.events.on( 'shortcodeView:updated', function ( model ) {
		var modelId, settings;
		settings = vc.map[ model.get( 'shortcode' ) ] || false;
		if ( true === settings.is_container ) {
			modelId = model.get( 'id' );
			window.vc.frame_window.vc_iframe.updateChildGrids( modelId );
		}
	} );
	window.InlineShortcodeViewContainer = window.InlineShortcodeView.extend( {
		controls_selector: '#vc_controls-template-container',
		events: {
			'click > .vc_controls .vc_element .vc_control-btn-delete': 'destroy',
			'click > .vc_controls .vc_element .vc_control-btn-edit': 'edit',
			'click > .vc_controls .vc_element .vc_control-btn-clone': 'clone',
			'click > .vc_controls .vc_element .vc_control-btn-prepend': 'prependElement',
			'click > .vc_controls .vc_control-btn-append': 'appendElement',
			'click > .vc_empty-element': 'appendElement',
			'mouseenter': 'resetActive',
			'mouseleave': 'holdActive'
		},
		hold_active: false,
		parent_view: false,
		initialize: function ( params ) {
			_.bindAll( this, 'holdActive' );
			window.InlineShortcodeViewContainer.__super__.initialize.call( this, params );
			if ( this.model.get( 'parent_id' ) ) {
				this.parent_view = vc.shortcodes.get( this.model.get( 'parent_id' ) ).view;
			}
		},
		resetActive: function ( e ) {
			if ( this.hold_active ) {
				window.clearTimeout( this.hold_active );
			}
		},
		holdActive: function ( e ) {
			this.resetActive();
			this.$el.addClass( 'vc_hold-active' );
			var view = this;
			this.hold_active = window.setTimeout( function () {
				if ( view.hold_active ) {
					window.clearTimeout( view.hold_active );
				}
				view.hold_active = false;
				view.$el.removeClass( 'vc_hold-active' );
			}, 700 );
		},
		content: function () {
			if ( false === this.$content ) {
				this.$content = this.$el.find( '.vc_container-anchor:first' ).parent();
				this.$el.find( '.vc_container-anchor:first' ).remove();
			}
			return this.$content;
		},
		render: function () {
			window.InlineShortcodeViewContainer.__super__.render.call( this );
			this.content().addClass( 'vc_element-container' );
			this.$el.addClass( 'vc_container-block' );
			return this;
		},
		changed: function () {
			if ( this.allowAddControlOnEmpty() ) {
				if ( 0 === this.$el.find( '.vc_element[data-tag]' ).length ) {
					this.$el.addClass( 'vc_empty' ).find( '> :first' ).addClass( 'vc_empty-element' );
				} else {
					this.$el.removeClass( 'vc_empty' ).find( '> .vc_empty-element' ).removeClass( 'vc_empty-element' );
				}
			}
		},
		prependElement: function ( e ) {
			if ( e && e.preventDefault ) {
				e.preventDefault();
			}
			this.prepend = true;
			window.vc.add_element_block_view.render( this.model, true );
		},
		appendElement: function ( e ) {
			if ( e && e.preventDefault ) {
				e.preventDefault();
			}
			window.vc.add_element_block_view.render( this.model );
		},
		addControls: function () {
			var shortcodeTag, parentShortcodeTag, allAccess, moveAccess, editAccess, parentAllAccess, parentEditAccess, template, parent, data;
			shortcodeTag = this.model.get( 'shortcode' );
			template = $( this.controls_selector ).html();
			var parentName;
			parent = vc.shortcodes.get( this.model.get( 'parent_id' ) );
			if ( parent ) {
				parentName = vc.getMapped( parent.get( 'shortcode' ) ).name;
				parentShortcodeTag = parent.get( 'shortcode' );
			}

			allAccess = vc_user_access().shortcodeAll( shortcodeTag );
			editAccess = vc_user_access().shortcodeEdit( shortcodeTag );
			parentAllAccess = vc_user_access().shortcodeAll( parentShortcodeTag );
			parentEditAccess = vc_user_access().shortcodeEdit( parentShortcodeTag );
			moveAccess = vc_user_access().partAccess( 'dragndrop' );

			data = {
				name: vc.getMapped( shortcodeTag ).name,
				tag: shortcodeTag,
				parent_name: parentName,
				parent_tag: parentShortcodeTag,
				can_edit: editAccess,
				can_all: allAccess,
				moveAccess: moveAccess,
				parent_can_edit: parentEditAccess,
				parent_can_all: parentAllAccess,
				state: vc_user_access().getState( 'shortcodes' ),
				allowAdd: this.allowAddControl(),
				switcherPrefix: !parentAllAccess || !allAccess ? '-disable-switcher' : ''
			};
			var compiledTemplate = vc.template( _.unescape( template ),
				_.extend( {}, vc.templateOptions.custom, { evaluate: /\{#([\s\S]+?)#}/g } ) );
			this.$controls = $( compiledTemplate( data ).trim() ).addClass( 'vc_controls' );

			this.$controls.appendTo( this.$el );
		},
		allowAddControl: function () {
			return 'edit' !== vc_user_access().getState( 'shortcodes' );
		},
		multi_edit: function ( e ) {
			var models = [], parent, children;
			if ( e && e.preventDefault ) {
				e.preventDefault();
			}
			if ( this.model.get( 'parent_id' ) ) {
				parent = vc.shortcodes.get( this.model.get( 'parent_id' ) );
			}
			if ( parent ) {
				models.push( parent );
				children = vc.shortcodes.where( { parent_id: parent.get( 'id' ) } );
				window.vc.multi_edit_element_block_view.render( models.concat( children ), this.model.get( 'id' ) );
			} else {
				window.vc.edit_element_block_view.render( this.model );
			}
		},
		allowAddControlOnEmpty: function () {
			return 'edit' !== vc_user_access().getState( 'shortcodes' );
		}
	} );
})( window.jQuery );
