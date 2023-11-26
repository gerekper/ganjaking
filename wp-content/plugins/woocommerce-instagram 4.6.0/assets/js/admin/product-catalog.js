/**
 * Product catalog
 *
 * @package WC_Instagram/Assets/JS/Admin
 * @since   3.0.0
 */

/* global wc_instagram_product_catalog_params */
( function( $ ) {

	'use strict';

	if ( typeof wc_instagram_product_catalog_params === 'undefined' ) {
		return false;
	}

	var wcInstagramProductCatalog = {
		$body: $( 'body' ),
		$deleteLink: null,
		formFields: {},
		autoUpdateSlug: true,

		init: function() {
			var catalogSlug = this.getFormField( 'slug' ).val();

			this.autoUpdateSlug = ( '' === catalogSlug );
			this.toggleFormFields( 'slug', '' !== catalogSlug );

			$( '.wc-instagram-field-editable-url' ).wc_instagram_editable_url();

			if ( wc_instagram_product_catalog_params.delete_link ) {
				this.$deleteLink = $( wc_instagram_product_catalog_params.delete_link );

				$( '#mainform .woocommerce-save-button' ).after( this.$deleteLink );
			}

			this.bindEvents();
		},

		bindEvents: function() {
			var that = this;

			// Use the catalog title to generate the slug.
			this.getFormField( 'title' ).on( 'keyup', debounce(
			function() {
					if ( that.autoUpdateSlug ) {
						that.refreshCatalogSlug( $( this ).val() );
					}
				},
				1000,
				false
				)
			);

			// Slug edited manually.
			this.getFormField( 'slug' ).on( 'wc_instagram_editable_url_change', function( event, value ) {
				that.refreshCatalogSlug( value );
			});

			this.$body.on( 'wc_instagram_product_catalog_slug_updated', function ( event, slug ) {
				that.autoUpdateSlug = ( that.autoUpdateSlug && '' === slug );
				that.toggleFormFields( 'slug', true );
				that.getFormField( 'slug' ).val( slug ).trigger( 'change' );
			});

			this.$body.on( 'wc_instagram_subset_values_updated', function ( event, subset, data ) {
				var includeVariations = false;

				if ( 'product_types' === subset ) {
					includeVariations = that.inSubset( 'variable', data );

					that.toggleFormFields( ['virtual_products', 'downloadable_products'], that.inSubset( 'simple', data ) );
					that.toggleFormFields( 'include_variations', includeVariations );
					that.toggleFormFields( ['product_group_id', 'variation_description_field'], includeVariations && that.getFormField( 'include_variations' ).prop( 'checked' ) );
				}
			});

			this.$body.on( 'wc_instagram_subset_fields_loaded', function() {
				that.filterByToggle( $('input[name=filter_by]:checked' ).val() );
			});

			// Handle the visibility of the fields related to product variations.
			this.getFormField( 'include_variations' ).on( 'change', function () {
				var checked = $( this ).prop( 'checked' );

				that.toggleFormFields( ['product_group_id', 'variation_description_field'], checked );
			}).trigger( 'change' );

			// Handle the visibility of the 'custom_mpn' field.
			this.getFormField( 'product_mpn' ).on( 'change', function() {
				that.toggleFormFields( 'custom_mpn', ( 'custom' === $( this ).val() ) );
			}).trigger( 'change' );

			// Handle the visibility of the 'tax_country' field.
			this.getFormField( 'include_tax' ).on( 'change', function () {
				that.toggleFormFields('tax_country', 'base' !== wc_instagram_product_catalog_params.tax_based_on && $( this ).prop( 'checked' ) );
			}).trigger( 'change' );

			// Handle the visibility of the fields related to product stock info.
			this.getFormField( 'include_stock' ).on( 'change', function () {
				var checked = $( this ).prop( 'checked' );

				that.toggleFormFields( ['stock_quantity', 'backorder_stock_quantity'], checked );
			}).trigger( 'change' );

			if ( this.$deleteLink ) {
				this.$deleteLink.on( 'click', function( event ) {
					if ( ! window.confirm( $( this ).data( 'confirm' ) ) ) {
						event.preventDefault();
						return false;
					}
				});
			}

			this.getFormField( 'products_option' ).on( 'change', function() {
				that.productsOptionToggle( $( this ).val() );
			});

			$( '.form-table' )
				.on( 'change', 'input[name=filter_by]', function() {
					that.filterByToggle( $( this ).val() );
				});
		},

		/**
		 * Gets the jQuery object which represents the form field.
		 */
		getFormField: function( id ) {
			// Load field on demand.
			if ( ! this.formFields[ id ] ) {
				this.formFields[ id ] = $( '#' + id );
			}

			return this.formFields[ id ];
		},

		/**
		 * Handles the visibility of the form fields.
		 */
		toggleFormFields: function( ids, visible ) {
			var that = this;

			if ( ! Array.isArray( ids ) ) {
				ids = [ ids ];
			}

			$.each( ids, function( index, id ) {
				that.getFormField( id ).closest( 'tr' ).toggle( visible );
			});
		},

		/**
		 * Refresh the product catalog slug.
		 */
		refreshCatalogSlug: function( value ) {
			var that = this;

			if ( ! value ) {
				return;
			}

			$.post({
				url: wc_instagram_product_catalog_params.ajax_url,
				data: {
					'action': 'wc_instagram_generate_product_catalog_slug',
					'catalog_id': wc_instagram_product_catalog_params.catalog_id,
					'catalog_title': value
				},
				dataType: 'json',
				success: function( result ) {
					if ( result.success ) {
						that.$body.trigger( 'wc_instagram_product_catalog_slug_updated', result.data.slug );
					}
				}
			});
		},

		/**
		 * Gets if the element is included in the subset.
		 */
		inSubset: function( needle, subset ) {
			return (
				'' === subset.option ||
				( 'specific' === subset.option && -1 !== subset.values.indexOf( needle ) ) ||
				( 'all_except' === subset.option && -1 === subset.values.indexOf( needle ) )
			);
		},

		filterByToggle: function( option ) {
			var selector = option.replace( '_', '-' );

			$( '.show-if-' + selector ).closest( 'tr' ).show();
			$( '.hide-if-' + selector ).closest( 'tr' ).hide();

			// Refresh visible subset fields.
			$( '.wc-instagram-field-subset-option.show-if-' + selector ).each( function() {
				$( this ).trigger( 'change' );
			});

			// Hide subset fields.
			$( '.wc-instagram-field-subset-option.hide-if-' + selector ).each( function() {
				$( this ).closest( 'tr' ).nextAll( 'tr' ).slice( 0, 2 ).hide();
			});

			if ( 'products' === option ) {
				this.productsOptionToggle();
			}
		},

		productsOptionToggle: function( value ) {
			var option = ( value || this.getFormField( 'products_option' ).val() );

			this.toggleFormFields( 'include_product_ids', ( 'specific' === option ) );
			this.toggleFormFields( 'exclude_product_ids', ( 'all_except' === option ) );
		}
	};

	wcInstagramProductCatalog.init();

	/**
	 * Debounce.
	 *
	 * @param {Function} func
	 * @param {number} wait
	 * @param {boolean} immediate
	 */
	function debounce( func, wait, immediate ) {
		var timeout;

		return function() {
			var context = this,
				args = arguments,
				callNow = ( immediate && ! timeout ),
				later = function() {
					timeout = null;

					if ( ! immediate ) {
						func.apply( context, args );
					}
			};

			clearTimeout( timeout );

			timeout = setTimeout( later, wait );

			if ( callNow ) {
				func.apply( context, args );
			}
		};
	}
})( jQuery );
