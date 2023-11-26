/**
 * Google Product Category.
 *
 * @package WC_Instagram/Assets/JS/Admin
 * @since   3.6.0
 */

/* global ajaxurl, wc_instagram_google_product_category_params */
( function( $ ) {

	'use strict';

	if ( typeof wc_instagram_google_product_category_params === 'undefined' ) {
		return false;
	}

	var GoogleProductCategory = function( input, options ) {
		var defaults = {};

		this.options = $.extend( true, {}, defaults, options );

		this.$input     = $( input );
		this.$container = $( '<div class="wc-instagram-gpc-selects-container"></div>' );

		this.bindEvents();
		this.render();
	};

	GoogleProductCategory.prototype = {

		bindEvents: function() {
			var that = this;

			this.$container.on( 'change', '.wc-instagram-gpc-select-wrapper select', function() {
				var value    = $( this ).val(),
					$wrapper = $( this ).closest( '.wc-instagram-gpc-select-wrapper' ),
					$previous;

				if ( value ) {
					that.fetchSelectors({
						selects: 'child',
						category_id: value
					}, function( data ) {
						// Remove the following select fields.
						$wrapper.nextAll( '.wc-instagram-gpc-select-wrapper' ).remove();

						// Append the child select.
						if ( data.output ) {
							that.$container.append( data.output ).trigger( 'selects:updated' );
						}
					} );
				} else {
					// Remove the following select fields.
					$wrapper.nextAll( '.wc-instagram-gpc-select-wrapper' ).remove();

					// Empty option chosen, use the value of the previous select field.
					$previous = $wrapper.prev( '.wc-instagram-gpc-select-wrapper' );

					if ( $previous.length ) {
						value = $previous.find( 'select' ).val();
					}
				}

				that.setValue( value );
			});

			that.$container.on( 'selects:updated', function() {
				$( this ).find( 'select:not(.select2-hidden-accessible)' ).selectWoo();
			} );
		},

		render: function () {
			var that = this;

			this.fetchSelectors( {}, function( data ) {
				if ( data.output ) {
					that.$container.html( data.output ).trigger( 'selects:updated' );
				}
			} );

			// Add the container to the DOM.
			this.$input.after( this.$container );
		},

		fetchSelectors: function( params, success ) {
			var defaults = {
				action: this.options.action,
				_wpnonce: this.options.nonce,
				category_id: this.getValue(),
				selects: 'all'
			};

			$.ajax( {
				type: 'POST',
				url: ajaxurl,
				dataType: 'json',
				data: $.extend( true, {}, defaults, params ),
				success: function( response ) {
					if ( response.success && typeof success === 'function' ) {
						success( response.data );
					}
				}
			});
		},

		getValue: function() {
			return this.$input.val();
		},

		setValue: function( value ) {
			this.$input.val( value );
		}
	};

	$.fn.GoogleProductCategory = function( options ) {
		options = $.extend( true, {}, wc_instagram_google_product_category_params, options );

		this.each( function() {
			new GoogleProductCategory( this, options );
		} );

		return this;
	};

	$( function() {
		$( '.wc-instagram-gpc-field' ).GoogleProductCategory();
	} );
})( jQuery );
