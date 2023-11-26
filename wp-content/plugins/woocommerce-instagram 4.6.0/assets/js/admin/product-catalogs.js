/**
 * Handles the product catalogs table.
 *
 * @package WC_Instagram/Assets/JS/Admin
 * @since   4.0.0
 */

/* global ajaxurl, wcSetClipboard, wcClearClipboard, wc_instagram_product_catalogs_params */
(function( $, wp, Backbone, ajaxurl, params ) {

	'use strict';

	if ( typeof params === 'undefined' ) {
		return false;
	}

	var
		CopyURL = {
			copyURL: function( event ) {
				var $target = $( event.target );

				event.preventDefault();

				wcClearClipboard();
				wcSetClipboard( $target.attr( 'href' ), $target );
			},

			copyURLTip: function( event ) {
				$( event.target ).tipTip({
					'attribute':  'data-tip',
					'activation': 'focus',
					'fadeIn':     50,
					'fadeOut':    50,
					'delay':      0
				}).trigger( 'focus' );
			}
		},

		ModelAjaxSync = Backbone.Model.extend({
			url: ajaxurl,

			getSyncData: function() {
				return {};
			},

			sync: function( method, object, options ) {
				var action, data;

				options = ( options || {} );
				action  = ( options.action || method );
				data    = ( options.data || {} );

				options.data        = this.getSyncData( data, action );
				options.emulateJSON = true;

				// Use 'create' to always submit a POST request.
				return Backbone.sync.call( this, 'create', object, options );
			}
		}),

		Catalog = ModelAjaxSync.extend({
			defaults: function() {
				return {
					id: 0,
					name: '',
					url: '',
					xmlFile: {},
					csvFile: {}
				};
			},

			getSyncData: function( data, action ) {
				return _.defaults({
					action: 'wc_instagram_' + action,
					catalog_id: this.get( 'id' )
				}, data );
			},

			delete: function( options ) {
				var defaults = {
					action: 'delete_product_catalog',
					data: {
						_wpnonce: params.nonce.delete
					}
				};

				this.destroy( _.extend( {}, defaults, options ) );
			},

			getFile: function( format ) {
				return this.get( format + 'File' );
			},

			setFile: function( format, value ) {
				return this.set( format + 'File', _.extend( {}, this.getFile( format ), value ) );
			},

			fileAction: function( action, format ) {
				var that = this;

				this.fetch({
					action: action + '_product_catalog_file',
					data: {
						format: format,
						_wpnonce: params.nonce.fileAction
					},
					wait: true,
					success: function( model, response ) {
						if ( response.success && response.data.file ) {
							that.setFile( format, response.data.file );
						}
					}
				});
			}
		}),

		Catalogs = Backbone.Collection.extend({
			model: Catalog
		}),

		CatalogView = Backbone.View.extend({
			events: {
				'click .wc-instagram-product-catalog-copy': 'copyURL',
				'aftercopy .wc-instagram-product-catalog-copy': 'copyURLTip',
				'click .wc-instagram-product-catalog-delete': 'delete',
				'click .wc-instagram-product-catalog-feed-xml': 'feedModal',
				'click .wc-instagram-product-catalog-feed-csv': 'feedModal'
			},

			initialize: function() {
				this.listenTo( this.model, 'destroy', this.remove );
			},

			delete: function( event ) {
				event.preventDefault();

				if ( ! window.confirm( params.confirmDelete ) ) {
					return false;
				}

				this.model.delete( { wait: true } );
			},

			feedModal: function( event ) {
				event.preventDefault();

				new CatalogFeedModalView({
					model: this.model,
					format: $( event.target ).text().toLowerCase()
				});
			}
		}),

		CatalogFeedModalView = $.WCBackboneModal.View.extend({
			refreshTimeout: false,

			events: function() {
				return _.extend( {}, $.WCBackboneModal.View.prototype.events, {
					'click .request-update': 'requestUpdate',
					'click .cancel-update': 'cancelUpdate',
					'click .copy-url': 'copyURL',
					'aftercopy .copy-url': 'copyURLTip',
				});
			},

			initialize: function( data ) {
				var that = this, file;

				this.format = data.format;

				file = this.getFile();

				$.WCBackboneModal.View.prototype.initialize.apply( this, [{
					target: 'wc-instagram-product-catalog-feed',
					string : {
						format: this.format,
						action: this.getActionForFile( file ),
						catalog: this.model.toJSON(),
						file: file
					}
				}]);

				this.listenTo( this.model, 'change:' + this.format + 'File', function() {
					that._string.file = that.model.getFile( that.format );

					// Refresh every 5 seconds when processing the file or canceling the process.
					if ( that._string.file.status ) {
						that.refreshTimeout = setTimeout( function() {
							that.fetchFile()
						}, 5000 );
					}

					that.setAction( that.getActionForFile( that._string.file ) );
					that.render();
				});
			},

			render: function() {
				this.$el.empty();

				$.WCBackboneModal.View.prototype.render.apply( this );
			},

			getFile: function() {
				var file = this.model.getFile( this.format );

				if ( _.isEmpty( file ) ) {
					this.fetchFile();
				}

				return file;
			},

			fetchFile: function() {
				this.model.fileAction( 'fetch', this.format );
			},

			getActionForFile: function( file ) {
				if ( _.isEmpty( file ) ) {
					return 'loading';
				}

				switch ( file.status ) {
					case '':
						return ( file.lastModified ? 'viewing' : 'creating' );
					case 'queued':
					case 'processing':
						return 'updating';
					default:
						return file.status;
				}
			},

			setAction: function( action ) {
				this._string.action = action;
			},

			doAction: function( action ) {
				clearTimeout( this.refreshTimeout );

				this.setAction( action );
				this.render();
			},

			requestUpdate: function( event ) {
				event.preventDefault();

				this.doAction( 'updating' );
				this.model.fileAction( 'generate', this.format );
			},

			cancelUpdate: function( event ) {
				event.preventDefault();

				this.doAction( 'canceling' );
				this.model.fileAction( 'cancel', this.format );
			}
		}),

		CatalogsView = Backbone.View.extend({
			initialize: function() {
				var that = this;

				this.collection = new Catalogs( [], { comparator: false } );

				this.$el.find( 'tbody tr' ).each( function( index, row ) {
					var model = that.extractCatalogModel( row );

					that.collection.add( model );

					new CatalogView({
						el: row,
						model: model
					});
				});

				$( document.body ).trigger( 'init_tooltips' );
			},

			extractCatalogModel: function( html ) {
				var $title = $( html ).find( 'td.title > a' ),
					urlParams = new URLSearchParams( $title.prop( 'href' ) );

				return new Catalog({
					id: urlParams.get( 'catalog_id' ),
					name: $title.text(),
					url: $( html ).find( '.wc-instagram-product-catalog-copy' ).prop( 'href' )
				});
			}
		});

	_.defaults( CatalogView.prototype, CopyURL );
	_.defaults( CatalogFeedModalView.prototype, CopyURL );

	$( function() {
		new CatalogsView( {
			el: '.product_catalogs'
		});
	});

})( jQuery, wp, Backbone, ajaxurl, wc_instagram_product_catalogs_params );
