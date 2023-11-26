/**
 * Subset fields
 *
 * @package WC_Instagram/Assets/JS/Admin
 * @since   3.0.0
 */

jQuery( function( $ ) {

	'use strict';

	var wcInstagramSubsetFields = {
		subsets: {},
		$body: $( 'body' ),

		init: function() {
			this.initSubsets();
			this.bindEvents();
		},

		initSubsets: function() {
			var that = this;

			$( 'select.wc-instagram-field-subset-option' ).each( function() {
				var key = $( this ).attr( 'id' ).replace( '_option', '' ),
					option = $( this ).val();

				that.subsets[ key ] = {
					'option' : option,
					'values' : that.getSubsetValuesForOption( key, option )
				};
			});
		},

		bindEvents: function() {
			var that = this;

			$.each( this.subsets, function( key ) {
				$( '#' + key + '_option' ).on( 'change', function() {
					that.subsetToggle( $( this ) );
					that.setSubsetOption( key, $( this ).val() );
				}).trigger( 'change' );

				$( '#specific_' + key + ', #all_except_' + key ).on( 'change', function() {
					var option = $( this ).attr( 'id' ).replace( '_' + key, '' );

					if ( option === that.subsets[ key ].option ) {
						that.setSubsetValues( key, $( this ).val() );
					}
				});
			});

			$( 'body' ).trigger( 'wc_instagram_subset_fields_loaded' );
		},

		/**
		 * Handles the visibility of the 'subset' fields.
		 */
		subsetToggle: function( $option ) {
			var $optionTr = $option.closest( 'tr' );

			if ( 'specific' === $option.val() ) {
				$optionTr.next( 'tr' ).hide();
				$optionTr.nextAll( 'tr' ).slice( 1, 2 ).show();
			} else if ( 'all_except' === $option.val() ) {
				$optionTr.next( 'tr' ).show();
				$optionTr.next().next( 'tr' ).hide();
			} else {
				$optionTr.nextAll( 'tr' ).slice( 0, 2 ).hide();
			}
		},

		/**
		 * Gets the subset values for the specified option.
		 */
		getSubsetValuesForOption: function( key, option ) {
			var values = [];

			if ( option ) {
				values = $( '#' + option + '_' + key ).val();
			}

			return ( values ? values : [] );
		},

		/**
		 * Updates the parameter 'option' of a subset.
		 */
		setSubsetOption: function( key, option ) {
			if ( option === this.subsets[ key ].option ) {
				return;
			}

			this.subsets[ key ].option = option;

			this.setSubsetValues( key, this.getSubsetValuesForOption( key, option ) );

			$( 'body' ).trigger( 'wc_instagram_subset_option_updated', [ key, this.subsets[ key ] ] );
		},

		/**
		 * Updates the parameter 'values' of a subset.
		 */
		setSubsetValues: function( key, values ) {
			if ( ! values ) {
				values = [];
			}

			if ( values === this.subsets[ key ].values ) {
				return;
			}

			this.subsets[ key ].values = values;

			$( 'body' ).trigger( 'wc_instagram_subset_values_updated', [ key, this.subsets[ key ] ] );
		}
	};

	wcInstagramSubsetFields.init();
});