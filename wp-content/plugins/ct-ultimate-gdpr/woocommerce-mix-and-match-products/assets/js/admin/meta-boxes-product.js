/**
 * Product data metabox.
 *
 * @package WooCommerce Mix and Match Products/Scripts
 */

jQuery(
	function( $ ) {

		// Hide the "Grouping" field.
		$( '#linked_product_data .grouping.show_if_simple, #linked_product_data .form-field.show_if_grouped' ).addClass( 'hide_if_mix-and-match' );

		// Simple type options are valid for mnm.
		$( '#woocommerce-product-data .show_if_simple:not(.hide_if_mix-and-match)' ).addClass( 'show_if_mix-and-match' );

		// Relocate shipping method.
		$( '#shipping_product_data .mnm_packing_options' ).prependTo( '#shipping_product_data' );

		// Toggle dimensions by packing method.
		$( '#shipping_product_data .dimensions_field' ).closest( '.options_group' ).addClass( 'show_if_wc_mnm_packing_mode_together show_if_wc_mnm_has_physical_container_yes hide_if_wc_mnm_has_physical_container_no hide_if_wc_mnm_packing_mode_separate hide_if_wc_mnm_packing_mode_virtual' );
		$( '#shipping_product_data .shipping_class_field' ).closest( '.options_group' ).addClass( 'show_if_wc_mnm_packing_mode_together show_if_wc_mnm_has_physical_container_yes hide_if_wc_mnm_has_physical_container_no hide_if_wc_mnm_packing_mode_separate hide_if_wc_mnm_packing_mode_virtual' );

		// Hide/show related fields on input change.
		$( '#woocommerce-product-data' ).on( 'change', '.wc_mnm_display_toggle :input', function() {

			var $panels = $( this ).closest( '.product_data' );

			var target = $( this ).attr( 'name' );

			var regex = /\[.+\]/g;
			
			if ( target.match(regex) ) {
				target  = target.replace( regex, '' ); // Eliminate any inputname[99] numbers for compatibility with variation fields.
				$panels = $( this ).closest( '.woocommerce_variation' );
			}

			var value =  $( this ).is( ':checked' ) ? $( this ).val() : 'no'; // Unchecked checkboxes default to "no".

			// Hide/Show all with rules.
			var hide_classes = '.hide_if_' + target + '_' + value;
			var show_classes = '.show_if_' + target + '_' + value;
			
			$panels.find( $( hide_classes ) ).hide();
			$panels.find( $( show_classes ) ).show();

			$( '#woocommerce-product-data' ).trigger( target + '_changed', value );

		} );

		// Hide/Show physical container field as function of packing mode.
		$( '#woocommerce-product-data' ).on( 'wc_mnm_packing_mode_changed', function( e, value ) {

			if ( value === 'separate' ) {
				// Re-Trigger when packed separately is selected.
				$( '.mnm_packing_options .wc_mnm_has_physical_container_field :input' ).trigger( 'change' );
			}

		} );

		// Hide/Show NYP field as function of per-item pricing.
		$( '#woocommerce-product-data' ).on( 'wc_mnm_per_product_pricing_changed', function( e, value ) {

			if ( 'mix-and-match' === $( '#product-type' ).val() ) {

				var $nyp = $( '#_nyp' ).closest( 'label' ).hide();

				if ( 'yes' === value ) {
					$nyp.hide();
				} else {
					$nyp.show();
				}
			}

		} );


		// Mix and Match type specific options.
		$( document.body ).on(
			'woocommerce-product-type-change',
			function( event, select_val ) {

				if ( select_val === 'mix-and-match' ) {

					// Handle hide/show of toggles inside MNM panel.
					$( '.wc_mnm_display_toggle input[type="checkbox"]' ).trigger( 'change' );
					$( '.wc_mnm_display_toggle :input[type!="checkbox"]:checked' ).trigger( 'change' );
					
					$( 'input#_manage_stock' ).trigger( 'change' );

					// Blunt-force the shipping tab to show. Necessary if not updated and user still has _virtual meta = 'yes'.
					$( '.product_data_tabs .shipping_options' ).show();

				}

			}
		);

		
		// Category enhanced search.
		try {

			// Duplicated Ajax category search box since Woo does not yet support saving cat IDs.
			// @see: https://github.com/woocommerce/woocommerce/pull/32743.
			var getEnhancedSelectFormatString = function() {
				return {
					'language': {
						errorLoading: function() {
							// Workaround for https://github.com/select2/select2/issues/4355 instead of i18n_ajax_error.
							return wc_enhanced_select_params.i18n_searching;
						},
						inputTooLong: function( args ) {
							var overChars = args.input.length - args.maximum;

							if ( 1 === overChars ) {
								return wc_enhanced_select_params.i18n_input_too_long_1;
							}

							return wc_enhanced_select_params.i18n_input_too_long_n.replace( '%qty%', overChars );
						},
						inputTooShort: function( args ) {
							var remainingChars = args.minimum - args.input.length;

							if ( 1 === remainingChars ) {
								return wc_enhanced_select_params.i18n_input_too_short_1;
							}

							return wc_enhanced_select_params.i18n_input_too_short_n.replace( '%qty%', remainingChars );
						},
						loadingMore: function() {
							return wc_enhanced_select_params.i18n_load_more;
						},
						maximumSelected: function( args ) {
							if ( args.maximum === 1 ) {
								return wc_enhanced_select_params.i18n_selection_too_long_1;
							}

							return wc_enhanced_select_params.i18n_selection_too_long_n.replace( '%qty%', args.maximum );
						},
						noResults: function() {
							return wc_enhanced_select_params.i18n_no_matches;
						},
						searching: function() {
							return wc_enhanced_select_params.i18n_searching;
						}
					}
				};
			};

			var select2_args = $.extend(
				{
					allowClear        : $( this ).data( 'allow_clear' ) ? true : false,
					placeholder       : $( this ).data( 'placeholder' ),
					minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : 3,
					escapeMarkup      : function( m ) {
						return m;
					},
					ajax: {
						url:         wc_enhanced_select_params.ajax_url,
						dataType:    'json',
						delay:       250,
						data:        function( params ) {
							return {
								term:     params.term,
								action:   'woocommerce_json_search_categories',
								security: wc_enhanced_select_params.search_categories_nonce
							};
						},
						processResults: function( data ) {
							var terms = [];
							if ( data ) {
								$.each(
									data,
									function( id, term ) {
										terms.push(
											{
												id:   term.term_id,
												text: term.formatted_name
											}
										);
									}
								);
							}
							return {
								results: terms
							};
						},
						cache: true
					}
				},
				getEnhancedSelectFormatString()
			);
			
			$( document.body ).on( 'wc-mnm-enhanced-category-select-init', function() {

				// Ajax category search box
				$( ':input.wc-mnm-category-search' ).filter( ':not(.enhanced)' ).each( function() {
					
					$( this ).selectWoo( select2_args ).addClass( 'enhanced' );

					// Make sortable.
					if ( $( this ).data( 'sortable' ) ) {

						var $select = $( this );
						var $list   = $select.next( '.select2-container' ).find( 'ul.select2-selection__rendered' );

						$list.sortable(
							{
								placeholder : 'ui-state-highlight select2-selection__choice',
								forcePlaceholderSize: true,
								items       : 'li:not(.select2-search__field)',
								tolerance   : 'pointer',
								stop: function() {
									$( $list.find( '.select2-selection__choice' ).get().reverse() ).each(
										function() {
											var id     = $( this ).data( 'data' ).id;
											var option = $select.find( 'option[value="' + id + '"]' )[0];
											$select.prepend( option );
										}
									);
								}
							}
						);
					}

				});

				
			} ).trigger( 'wc-mnm-enhanced-category-select-init' );

		} catch( err ) {
			// If select2 failed (conflict?) log the error but don't stop other scripts breaking.
			window.console.log( err );
		}

		// Trigger product type change.
		$( '#product-type' ).trigger( 'change' );

	}
);
