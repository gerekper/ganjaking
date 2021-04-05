/* global _ */
( function( wp, $ ) {
	'use strict';

	if ( ! wp || ! wp.customize ) { return; }

	// Set up our namespace.
	var api = wp.customize;

	api.SPHeader = api.SPHeader || {};

	/**
	 * wp.customize.SPHeader.WidgetsCollection
	 *
	 * Collection for widget models.
	 *
	 * @constructor
	 * @augments Backbone.Model
	 */
	api.SPHeader.WidgetsCollection = Backbone.Collection.extend();

	// Colection of Widgets.
	api.SPHeader.Widgets = new api.SPHeader.WidgetsCollection();

	/**
	 * wp.customize.SPHeader.WidgetModel
	 *
	 * A single widget model.
	 *
	 * @constructor
	 * @augments Backbone.Model
	 */
	api.SPHeader.WidgetModel = Backbone.Model.extend({
		defaults: {
			'id': null,
			'x': null,
			'y': null,
			'w': null,
			'h': null
		},

		initialize: function() {
			this.on( 'change', this.updateSetting );
		},

		removeFromSetting: function() {
			var settingValue;

			settingValue = api.SPHeader.Setting.getValue();

			settingValue = _.omit( settingValue, this.get( 'id' ) );

			api.SPHeader.Setting.setValue( settingValue, true );
		},

		updateSetting: function() {
			var settingValue;

			settingValue = api.SPHeader.Setting.getValue();

			settingValue[ this.get( 'id' ) ] = {
				'x': this.get( 'x' ),
				'y': this.get( 'y' ),
				'w': this.get( 'w' ),
				'h': this.get( 'h' )
			};

			api.SPHeader.Setting.setValue( settingValue, true );
		}
	});

	/**
	 * wp.customize.SPHeader.WidgetView
	 *
	 * View class for an individual widget.
	 *
	 * @constructor
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	api.SPHeader.WidgetView = wp.Backbone.View.extend({
		events: {
			'click .sp-header-widget-delete': 'removeWidget'
		},

		parent: null,
		title: '',

		initialize: function() {
			this.parent = this.options.parent;
			this.title = this.options.title;

			this.render();
		},

		render: function() {
			// Set min width for the widgets
			this.$el.attr( 'data-gs-min-width', 2 );

			// Set data
			this.$el.attr( 'data-widget-id', this.model.get( 'id' ) );

			// Build widget
			this.$el.append( $( '<div/>' )
				.addClass( 'grid-stack-item-content' )
				.append( $( '<h3/>' ).text( this.title ) )
				.append( $( '<span/>' ).addClass( 'sp-header-widget-delete' ) )
			);

			return this;
		},

		removeWidget: function() {
			var removedID = this.model.get( 'id' );

			// Remove from setting
			this.model.removeFromSetting();

			// Remove model
			this.model.collection.remove( this.model );

			// Remove widget from grid
			this.parent.grid.removeWidget( this.$el );

			// Add the widget back to the shelf
			this.parent.shelf.find( '[data-component-id="' + removedID + '"]' ).show();
		}
	});

	/**
	 * wp.customize.SPHeader.ConfiguratorView
	 *
	 * View class for the menu item configurator panel.
	 *
	 * @constructor
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	api.SPHeader.HeaderCustomizerView = wp.Backbone.View.extend({
		el: '#sp-header-configurator',
		events: {
			'click .sp-header-components-shelf a': 'addWidgetFromShelf'
		},
		grid: null,
		shelf: null,

		initialize: function() {
			// Initialize gridstack.js
			try {
				this.initGridstack();
			} catch ( error ) {}
		},

		didInitialize: function() {
			if ( _.isNull( this.grid ) ) {
				return false;
			}

			return true;
		},

		initGridstack: function() {
			var self = this;

			this.grid = this.$el.find( '.sp-header-gridstack' ).gridstack({
				itemClass: 'grid-stack-item',
				width: 12,
				height: 10,
				cellHeight: 40,
				cell_width: 40,
				acceptWidgets: '.grid-stack-item',
				'float': true,
				resizable: {
					handles: 'e, w'
				}
			}).data( 'gridstack' );

			// Look for changes in the grid
			this.$el.find( '.sp-header-gridstack' ).bind( 'change', _.bind( self.updateWidgets, self ) );

			// Remove item from shelf
			this.shelf = this.$el.find( '.sp-header-components-shelf' );
		},

		open: function() {
			$( 'body' ).addClass( 'sp-header-panel-visible' );
		},

		close: function() {
			$( 'body' ).removeClass( 'sp-header-panel-visible' );
		},

		updateWidgets: function() {
			// Maybe toggle empty class on the grid
			this.maybeToggleEmptyClass();

			// Loop through changes and update model data
			_.each( this._getGridData(), this.updateSingleWidget, this );
		},

		updateSingleWidget: function( widget ) {
			var model = api.SPHeader.Widgets.get( widget.id );

			if ( parseInt( model.get( 'x' ), 10 ) !== parseInt( widget.x, 10 ) ) {
				model.set( 'x', parseInt( widget.x, 10 ) );
			}

			if ( parseInt( model.get( 'y' ), 10 ) !== parseInt( widget.y, 10 ) ) {
				model.set( 'y', parseInt( widget.y, 10 ) );
			}

			if ( parseInt( model.get( 'w' ), 10 ) !== parseInt( widget.w, 10 ) ) {
				model.set( 'w', parseInt( widget.w, 10 ) );
			}

			if ( parseInt( model.get( 'h' ), 10 ) !== parseInt( widget.h, 10 ) ) {
				model.set( 'h', parseInt( widget.h, 10 ) );
			}
		},

		maybeToggleEmptyClass: function() {
			var isEmpty = true,
				items = this._getGridData();

			if ( items && 0 < items.length ) {
				isEmpty = false;
			}

			this.$el.find( '.sp-header-gridstack-wrapper' ).toggleClass( 'sp-header-grid-empty', isEmpty );
		},

		_getGridData: function() {
			var node, res = _.map( this.$el.find( '.sp-header-gridstack' ).find( '.grid-stack-item:visible' ), function( el ) {
				el = $( el );
				node = el.data( '_gridstack_node' );
				return {
					id: _.escape( el.attr( 'data-widget-id' ) ),
					x: parseInt( node.x, 10 ),
					y: parseInt( node.y, 10 ),
					w: parseInt( node.width, 10 ),
					h: parseInt( node.height, 10 )
				};
			});

			return res;
		},

		addWidgetFromShelf: function( event ) {
			this.addWidget( $( event.target ).attr( 'data-component-id' ), false );
		},

		addWidget: function( component, widget ) {
			var widgetView;

			if ( ! component ) {
				return;
			}

			// Create model
			if ( ! widget ) {
				widget = new api.SPHeader.WidgetModel({
					'id': component
				});
			}

			// Add to collection
			api.SPHeader.Widgets.add( widget );

			// Create view
			widgetView = new api.SPHeader.WidgetView({
				id: 'sp-header-widget-' + widget.get( 'id' ),
				className: 'grid-stack-widget',
				model: widget,
				parent: this,
				'title': this.shelf.find( '[data-component-id="' + widget.get( 'id' ) + '"]' ).text()
			});

			// Add to gridstack
			if ( ( ! widget.get( 'x' ) ) && ( ! widget.get( 'y' ) ) && ( ! widget.get( 'w' ) ) && ( ! widget.get( 'h' ) ) ) {
				this.grid.addWidget( widgetView.$el, 0, 0, 2, 1, true );
			} else {
				this.grid.addWidget( widgetView.$el, widget.get( 'x' ), widget.get( 'y' ), widget.get( 'w' ), widget.get( 'h' ) );
			}

			// Hide widget from shelf
			this.shelf.find( '[data-component-id="' + widget.get( 'id' ) + '"]' ).hide();
		}
	});

	/**
	 * wp.customize.SPHeader.Setting
	 */
	api.SPHeader.Setting = {
		setting: null,
		$settingField: null,

		/**
		 * Initialize
		 */
		init: function() {
			var wasSaved = api.state( 'saved' ).get();

			this.setting = api( 'sp_header_setting' );

			if ( _.isEmpty( this.setting.get() ) ) {
				this.setValue( {}, false );
			} else {
				this.setValue( this.setting.get(), false );
			}

			// Don't change the setting to dirty, we're just initializing.
			this.setting._dirty = false;
			api.state( 'saved' ).set( wasSaved );

			this.$settingField = $( '[data-customize-setting-link="' + 'sp_header_setting' + '"]' );
		},

		/**
		 * Get the current value of the setting
		 *
		 * @return Object
		 */
		getValue: function() {
			// The setting is saved in JSON
			return JSON.parse( decodeURI( this.setting.get() ) );
		},

		/**
		 * Set a new value for the setting
		 *
		 * @param newValue Object
		 * @param refresh If we want to refresh the previewer or not
		 */
		setValue: function( newValue, refresh ) {
        	this.setting.set( encodeURI( JSON.stringify( newValue ) ) );

			if ( refresh ) {
				// Trigger the change event on the hidden field so
				// previewer refresh the website on Customizer
				this.$settingField.trigger( 'change' );
			}
		}
	};

	api.bind( 'ready', function() {
		// Init configurator view
		api.SPHeader.HeaderCustomizer = new api.SPHeader.HeaderCustomizerView();

		if ( api.SPHeader.HeaderCustomizer.didInitialize() ) {
			// Init setting
			api.SPHeader.Setting.init();

			// Add existing widgets
			_.each( api.SPHeader.Setting.getValue(), function( val, key ) {
				var existingWidget = new api.SPHeader.WidgetModel({
					id: _.escape( key ),
					x: parseInt( val.x, 10 ),
					y: parseInt( val.y, 10 ),
					w: parseInt( val.w, 10 ),
					h: parseInt( val.h, 10 )
				});

				api.SPHeader.HeaderCustomizer.addWidget( key, existingWidget );
			});

			// Open & Close side panel
			$( '.sp-header-open' ).on( 'click', function( event ) {
				event.preventDefault();

				if ( $( this ).hasClass( 'sp-header-active' ) ) {
					api.SPHeader.HeaderCustomizer.close();
					$( this ).removeClass( 'sp-header-active' );
				} else {
					api.SPHeader.HeaderCustomizer.open();
					$( this ).addClass( 'sp-header-active' );
				}
			});

			// Track clicks on the back button
			$( '.customize-section-back' ).on( 'click', function() {
				api.SPHeader.HeaderCustomizer.close();
				$( '.sp-header-open' ).removeClass( 'sp-header-active' );
			});
		}
	});

} )( window.wp, jQuery );