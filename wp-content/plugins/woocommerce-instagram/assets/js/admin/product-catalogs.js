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
				'click .wc-instagram-product-catalog-copy': 'copyLink',
				'aftercopy .wc-instagram-product-catalog-copy': 'copyLinkTip',
				'click .wc-instagram-product-catalog-delete': 'delete',
				'click .wc-instagram-product-catalog-download-xml': 'exportXML',
				'click .wc-instagram-product-catalog-download-csv': 'exportCSV'
			},

			initialize: function() {
				this.listenTo( this.model, 'destroy', this.remove );
			},

			copyLink: function( event ) {
				var $target = $( event.target );

				event.preventDefault();

				wcClearClipboard();
				wcSetClipboard( $target.attr( 'href' ), $target );
			},

			copyLinkTip: function( event ) {
				$( event.target ).tipTip({
					'attribute':  'data-tip',
					'activation': 'focus',
					'fadeIn':     50,
					'fadeOut':    50,
					'delay':      0
				}).trigger( 'focus' );
			},

			delete: function( event ) {
				event.preventDefault();

				if ( ! window.confirm( params.confirmDelete ) ) {
					return false;
				}

				this.model.delete( { wait: true } );
			},

			exportXML: function( event ) {
				event.preventDefault();

				new CatalogExportModalView({
					model: this.model,
					format: 'xml'
				});
			},

			exportCSV: function( event ) {
				event.preventDefault();

				new CatalogExportModalView({
					model: this.model,
					format: 'csv'
				});
			}
		}),

		CatalogExportModalView = $.WCBackboneModal.View.extend({
			events: function() {
				return _.extend( {}, $.WCBackboneModal.View.prototype.events, {
					'click .request-update': 'requestUpdate'
				});
			},

			initialize: function( data ) {
				var that = this;

				this.format = data.format;

				$.WCBackboneModal.View.prototype.initialize.apply( this, [{
					target: 'wc-instagram-product-catalog-download',
					string : {
						format: this.format.toUpperCase(),
						catalog: this.model.toJSON(),
						file: this.getFile()
					}
				}]);

				this.listenTo( this.model, 'change:' + this.format + 'File', function() {
					that._string.file = that.model.getFile( that.format );
					that.render();
				});
			},

			render: function() {
				var that = this;

				this.$el.empty();

				$.WCBackboneModal.View.prototype.render.apply( this );

				// Processing file, refresh every 5 seconds.
				if ( this._string.file.status ) {
					setTimeout( function() {
						that.fetchFile()
					}, 5000 );
				}
			},

			getFile: function() {
				var file = this.model.getFile( this.format );

				if ( _.isEmpty( file ) ) {
					this.model.fileAction( 'fetch', this.format );
				}

				return file;
			},

			fetchFile: function() {
				this.model.fileAction( 'fetch', this.format );
			},

			requestUpdate: function( event ) {
				event.preventDefault();

				this.model.setFile( this.format, { status: 'requested' } );
				this.model.fileAction( 'generate', this.format );
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
					name: $title.text()
				});
			}
		});

	$( function() {
		new CatalogsView( {
			el: '.product_catalogs'
		});
	});

})( jQuery, wp, Backbone, ajaxurl, wc_instagram_product_catalogs_params );
