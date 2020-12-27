/*----------------------------------------------------------------------------*\
 VISUAL COMPOSER - CUSTOM VIEWS
 \*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";
	var Shortcodes = vc.shortcodes;

	window.mpcTabsView = vc.shortcode_view.extend( {
		new_tab_adding:     false,
		events:             {
			'click .add_tab':                              'addTab',
			'click > .vc_controls .vc_control-btn-delete': 'deleteShortcode',
			'click > .vc_controls .vc_control-btn-edit':   'editElement',
			'click > .vc_controls .vc_control-btn-clone':  'clone'
		},
		initialize:         function( params ) {
			window.mpcTabsView.__super__.initialize.call( this, params );
			_.bindAll( this, 'stopSorting' );
		},
		render:             function() {
			window.mpcTabsView.__super__.render.call( this );
			this.$tabs = this.$el.find( '.wpb_tabs_holder' );
			this.createAddTabButton();
			return this;
		},
		ready:              function( e ) {
			window.mpcTabsView.__super__.ready.call( this, e );
		},
		clearTabs:          function() {
			this.$tabs.find( 'ul.tabs_controls li:not(.add_tab_block)' ).remove();
		},
		prependTabs:        function() {
			this.$tabs.prepend( this.$tabs.find( 'ul.tabs_controls' ) );
		},
		createAddTabButton: function() {
			var new_tab_button_id = (Date.now() + '-' + Math.floor( Math.random() * 11 )),
				shortcode         = this.$tabs.find( '[data-element_type]' ).attr( 'data-element_type' );
			this.$tabs.append( '<div id="new-tab-' + new_tab_button_id + '" class="new_element_button"></div>' );
			this.$add_button = $( '<li class="add_tab_block"><a href="#new-tab-' + new_tab_button_id + '" class="add_tab" title="' + window.i18nLocale.add_tab + '"></a></li>' ).appendTo( this.$tabs.find( ".tabs_controls" ) );
			if ( ( typeof vc_user_access != 'undefined' && !vc_user_access().shortcodeAll( shortcode ) ) || ( typeof this.hasUserAccess != 'undefined' && !this.hasUserAccess() ) ) {
				this.$add_button.hide();
			}
		},
		addTab:             function( e ) {
			e.preventDefault();
			var shortcode = this.$tabs.find( '[data-element_type]' ).attr( 'data-element_type' );
			// check user role to add controls
			if ( ( typeof vc_user_access != 'undefined' && !vc_user_access().shortcodeAll( shortcode ) ) || ( typeof this.hasUserAccess != 'undefined' && !this.hasUserAccess() ) ) {
				return false;
			}
			this.new_tab_adding = true;
			var tab_title  = window.i18nLocale.tab,
				tabs_count = this.$tabs.find( '[data-element_type=' + shortcode + ']' ).length,
				tab_id     = (Date.now() + '-' + tabs_count + '-' + Math.floor( Math.random() * 11 ));
			vc.shortcodes.create( {
				shortcode: shortcode,
				params:    { title: tab_title, tab_id: tab_id },
				parent_id: this.model.id
			} );
			return false;
		},
		stopSorting:        function( event, ui ) {
			var shortcode;
			this.$tabs.find( 'ul.tabs_controls li:not(.add_tab_block)' ).each( function( index ) {
				var href = $( this ).find( 'a' ).attr( 'href' ).replace( "#", "" );
				shortcode = vc.shortcodes.get( $( '[id="' + $( this ).attr( 'aria-controls' ) + '"]' ).data( 'model-id' ) );
				vc.storage.lock();
				shortcode.save( { 'order': $( this ).index() } ); // Optimize
			} );
			shortcode && shortcode.save();
		},
		changedContent:     function( view ) {
			var params = view.model.get( 'params' );

			if ( !this.$tabs.hasClass( 'ui-tabs' ) ) {
				this.$tabs.tabs( {
					select: function( event, ui ) {
						return !$( ui.tab ).hasClass( 'add_tab' );
					}
				} );
				this.$tabs.find( ".ui-tabs-nav" ).prependTo( this.$tabs );
				// check user role to add controls
				var shortcode = 'mpc_tab';
				if ( ( typeof vc_user_access != 'undefined' && vc_user_access().shortcodeAll( shortcode ) ) || ( typeof this.hasUserAccess != 'undefined' && this.hasUserAccess() ) ) {
					this.$tabs.find( ".ui-tabs-nav" ).sortable( {
						axis:   'x',
						update: this.stopSorting,
						items:  "> li:not(.add_tab_block)"
					} );
				}
			}
			if ( true === view.model.get( 'cloned' ) ) {
				var cloned_from   = view.model.get( 'cloned_from' ),
					$tab_controls = $( '.tabs_controls > .add_tab_block', this.$content ),
					$new_tab      = $( "<li><a href='#tab-" + params.tab_id + "'>" + params.title + "</a></li>" ).insertBefore( $tab_controls );
				this.$tabs.tabs( 'refresh' );
				this.$tabs.tabs( "option", 'active', $new_tab.index() );
			} else {
				$( "<li><a href='#tab-" + params.tab_id + "'>" + params.title + "</a></li>" )
					.insertBefore( this.$add_button );
				this.$tabs.tabs( 'refresh' );
				this.$tabs.tabs( "option", "active", this.new_tab_adding ? $( '.ui-tabs-nav li', this.$content ).length - 2 : 0 );
			}
			this.new_tab_adding = false;

			this.prependTabs();
		},
		cloneModel:         function( model, parent_id, save_order ) {
			var new_order, model_clone, params, tag;

			new_order = _.isBoolean( save_order ) && true === save_order ? model.get( 'order' ) : parseFloat( model.get( 'order' ) ) + vc.clone_index;
			params = _.extend( {}, model.get( 'params' ) );
			tag = model.get( 'shortcode' );

			if ( 'mpc_tab' === tag ) {
				_.extend( params,
					{ tab_id: Date.now() + '-' + this.$tabs.find( '[data-element-type=mpc_tab]' ).length + '-' + Math.floor( Math.random() * 11 ) } );
			}

			model_clone = Shortcodes.create( {
				shortcode:   tag,
				id:          vc_guid(),
				parent_id:   parent_id,
				order:       new_order,
				cloned:      (tag !== 'mpc_tab'),
				cloned_from: model.toJSON(),
				params:      params
			} );

			_.each( Shortcodes.where( { parent_id: model.id } ), function( shortcode ) {
				this.cloneModel( shortcode, model_clone.get( 'id' ), true );
			}, this );
			return model_clone;
		}
	} );

	window.mpcTabView = window.VcColumnView.extend( {
		events:                {
			'click > .vc_controls .vc_control-btn-delete':        'deleteShortcode',
			'click > .vc_controls .vc_control-btn-prepend':       'addElement',
			'click > .vc_controls .vc_control-btn-edit':          'editElement',
			'click > .vc_controls .vc_control-btn-clone':         'clone',
			'click > .wpb_element_wrapper > .vc_empty-container': 'addToEmpty'
		},
		render:                function() {
			var params = this.model.get( 'params' );
			window.mpcTabView.__super__.render.call( this );

			if ( this.model.get( 'cloned' ) === true ) {
				params = this.model.get( 'params' );

				params.tab_id = (+new Date() + '-' + Math.floor( Math.random() * 11 ));
				this.model.save( 'params', params );
			}
			/**
			 * @deprecated 4.4.3
			 * @see composer-atts.js vc.atts.tab_id.addShortcode
			 */
			if ( !params.tab_id/* || params.tab_id.indexOf('def') != -1*/ ) {
				params.tab_id = (Date.now() + '-' + Math.floor( Math.random() * 11 ));
				this.model.save( 'params', params );
			}
			this.id = 'tab-' + params.tab_id;
			this.$el.attr( 'id', this.id );
			return this;
		},
		ready:                 function( e ) {
			window.mpcTabView.__super__.ready.call( this, e );
			this.$tabs = this.$el.closest( '.wpb_tabs_holder' );
			var params = this.model.get( 'params' );
			return this;
		},
		changeShortcodeParams: function( model ) {
			var params;

			window.mpcTabView.__super__.changeShortcodeParams.call( this, model );
			params = model.get( 'params' );
			if ( _.isObject( params ) && _.isString( params.title ) && _.isString( params.tab_id ) ) {
				$( '.ui-tabs-nav [href="#tab-' + params.tab_id + '"]' ).text( params.title );
			}
		},
		deleteShortcode:       function( e ) {
			_.isObject( e ) && e.preventDefault();
			var answer    = confirm( window.i18nLocale.press_ok_to_delete_section ),
				parent_id = this.model.get( 'parent_id' );
			if ( true !== answer ) {
				return false;
			}
			this.model.destroy();
			if ( !vc.shortcodes.where( { parent_id: parent_id } ).length ) {
				var parent = vc.shortcodes.get( parent_id );
				parent.destroy();
				return false;
			}
			var params            = this.model.get( 'params' ),
				current_tab_index = $( '[href="#tab-' + params.tab_id + '"]', this.$tabs ).parent().index();
			$( '[href="#tab-' + params.tab_id + '"]' ).parent().remove();
			var tab_length = this.$tabs.find( '.ui-tabs-nav li:not(.add_tab_block)' ).length;
			if ( 0 < tab_length ) {
				this.$tabs.tabs( 'refresh' );
			}
			if ( current_tab_index < tab_length ) {
				this.$tabs.tabs( "option", "active", current_tab_index );
			} else if ( 0 < tab_length ) {
				this.$tabs.tabs( "option", "active", tab_length - 1 );
			}

		},
		cloneModel:            function( model, parent_id, save_order ) {
			var new_order,
				model_clone,
				params,
				tag;

			new_order = _.isBoolean( save_order ) && true === save_order ? model.get( 'order' ) : parseFloat( model.get( 'order' ) ) + vc.clone_index;
			params = _.extend( {}, model.get( 'params' ) );
			tag = model.get( 'shortcode' );

			if ( tag === 'mpc_tab' ) {
				_.extend( params,
					{ tab_id: Date.now() + '-' + this.$tabs.find( '[data-element_type=mpc_tab]' ).length + '-' + Math.floor( Math.random() * 11 ) } );
			}

			model_clone = Shortcodes.create( {
				shortcode:   tag,
				parent_id:   parent_id,
				order:       new_order,
				cloned:      true,
				cloned_from: model.toJSON(),
				params:      params
			} );

			_.each( Shortcodes.where( { parent_id: model.id } ), function( shortcode ) {
				this.cloneModel( shortcode, model_clone.get( 'id' ), true );
			}, this );
			return model_clone;
		}
	} );

	window.mpcAccordionView = vc.shortcode_view.extend( {
		adding_new_tab:        false,
		events:                {
			'click .add_tab':                                                             'addTab',
			'click > .vc_controls .column_delete, > .vc_controls .vc_control-btn-delete': 'deleteShortcode',
			'click > .vc_controls .column_edit, > .vc_controls .vc_control-btn-edit':     'editElement',
			'click > .vc_controls .column_clone,> .vc_controls .vc_control-btn-clone':    'clone'
		},
		render:                function() {
			window.mpcAccordionView.__super__.render.call( this );
			// check user role to add controls
			if ( ( typeof vc_user_access != 'undefined' && !vc_user_access().shortcodeAll( 'mpc_accordion_tab' ) ) || ( typeof this.hasUserAccess != 'undefined' && !this.hasUserAccess() ) ) {
				this.$el.find( '.tab_controls' ).hide();
				return this;
			}
			this.$content.sortable( {
				axis:   "y",
				handle: "h3",
				stop:   function( event, ui ) {
					// IE doesn't register the blur when sorting
					// so trigger focusout handlers to remove .ui-state-focus
					ui.item.prev().triggerHandler( "focusout" );
					$( this ).find( '> .wpb_sortable' ).each( function() {
						var shortcode = $( this ).data( 'model' );
						shortcode.save( { 'order': $( this ).index() } ); // Optimize
					} );
				}
			} );
			return this;
		},
		changeShortcodeParams: function( model ) {
			var params, collapsible;

			window.mpcAccordionView.__super__.changeShortcodeParams.call( this, model );
			params = model.get( 'params' );
			collapsible = _.isString( params.collapsible ) && 'yes' === params.collapsible ? true : false;
			if ( this.$content.hasClass( 'ui-accordion' ) ) {
				this.$content.accordion( "option", "collapsible", collapsible );
			}
		},
		changedContent:        function( view ) {
			if ( this.$content.hasClass( 'ui-accordion' ) ) {
				this.$content.accordion( 'destroy' );
			}
			var collapsible = _.isString( this.model.get( 'params' ).collapsible ) && 'yes' === this.model.get( 'params' ).collapsible ? true : false;
			this.$content.accordion( {
				header:      "h3",
				navigation:  false,
				autoHeight:  true,
				heightStyle: "content",
				collapsible: collapsible,
				active:      false === this.adding_new_tab && true !== view.model.get( 'cloned' ) ? 0 : view.$el.index()
			} );
			this.adding_new_tab = false;
		},
		addTab:                function( e ) {
			e.preventDefault();
			// check user role to add controls
			if ( ( typeof vc_user_access != 'undefined' && !vc_user_access().shortcodeAll( 'mpc_accordion_tab' ) ) || ( typeof this.hasUserAccess != 'undefined' && !this.hasUserAccess() ) ) {
				return false;
			}
			this.adding_new_tab = true;
			vc.shortcodes.create( {
				shortcode: 'mpc_accordion_tab',
				params:    { title: window.i18nLocale.section },
				parent_id: this.model.id
			} );
		},
		_loadDefaults:         function() {
			window.mpcAccordionView.__super__._loadDefaults.call( this );
		}
	} );

	window.mpcAccordionTabView = window.VcColumnView.extend( {
		events:                {
			'click > [data-element_type] > .vc_controls .vc_control-btn-delete':        'deleteShortcode',
			'click > [data-element_type] > .vc_controls .vc_control-btn-prepend':       'addElement',
			'click > [data-element_type] > .vc_controls .vc_control-btn-edit':          'editElement',
			'click > [data-element_type] > .vc_controls .vc_control-btn-clone':         'clone',
			'click > [data-element_type] > .wpb_element_wrapper > .vc_empty-container': 'addToEmpty'
		},
		setContent:            function() {
			this.$content = this.$el.find( '> [data-element_type] > .wpb_element_wrapper > .vc_container_for_children' );
		},
		changeShortcodeParams: function( model ) {
			var params;

			window.mpcAccordionTabView.__super__.changeShortcodeParams.call( this, model );
			params = model.get( 'params' );
			if ( _.isObject( params ) && _.isString( params.title ) ) {
				this.$el.find( '> h3 .tab-label' ).text( params.title );
			}
		},
		setEmpty:              function() {
			$( '> [data-element_type]', this.$el ).addClass( 'vc_empty-column' );
			if ( typeof vc_user_access != 'undefined' ) {
				if ( 'edit' !== vc_user_access().getState( 'shortcodes' ) ) {
					this.$content.addClass( 'vc_empty-container' );
				}
			} else {
				this.$content.addClass( 'vc_empty-container' );
			}
		},
		unsetEmpty:            function() {
			$( '> [data-element_type]', this.$el ).removeClass( 'vc_empty-column' );
			this.$content.removeClass( 'vc_empty-container' );
		}
	} );

})( jQuery );
