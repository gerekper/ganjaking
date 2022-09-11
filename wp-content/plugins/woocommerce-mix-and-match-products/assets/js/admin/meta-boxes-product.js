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
		$( '#shipping_product_data .dimensions_field' ).closest( '.options_group' ).addClass( 'show_if_packed_together show_if_has_physical_container hide_if_packed_separately hide_if_virtual' );
		$( '#shipping_product_data .shipping_class_field' ).closest( '.options_group' ).addClass( 'show_if_packed_together show_if_has_physical_container hide_if_packed_separately hide_if_virtual' );

		// Hide/Show contents fields.
		$( '.wc_mnm_content_source' ).on(
			'change',
			function() {

				if ( 'mix-and-match' === $( '#product-type' ).val() ) {

					var source = $( '#mnm_product_data .wc_mnm_content_source:checked' ).val();

					$( '#mnm_product_data .show_if_source_categories' ).hide();
					$( '#mnm_product_data .show_if_source_products' ).hide();

					if ( 'categories' === source ) {
						$( '#mnm_product_data .show_if_source_categories' ).show();
					} else {
						$( '#mnm_product_data .show_if_source_products' ).show();
					}

				}

			}
		);

		// Hide/Show Layout fields.
		$( '#wc_mnm_layout_override' ).on(
			'change',
			function() {

				if ( 'mix-and-match' === $( '#product-type' ).val() ) {
					if ( this.checked ) {
						$( '#mnm_product_data .show_if_layout_override' ).show();
					} else {
						$( '#mnm_product_data .show_if_layout_override' ).hide();
					}
				}

			}
		);

		// Hide/Show Per-Item pricing related fields.
		$( '.wc_mnm_per_product_pricing' ).on(
			'change',
			function() {

				var $nyp = $( '#_nyp' ).closest( 'label' ).hide();

				if ( 'yes' === this.value ) {
					$nyp.hide();
					$( '#mnm_product_data .show_if_per_item_pricing' ).show();
				} else {
					$nyp.show();
					$( '#mnm_product_data .show_if_per_item_pricing' ).hide();
				}

			}
		);

		// Hide/Show shipping related fields.
		$( '.mnm_packing_options .packing_mode' ).on(
			'change',
			function() {

				if ( 'mix-and-match' === $( '#product-type' ).val() ) {

					var mode = $( '.mnm_packing_options .packing_mode:checked' ).val();

					if ( 'together' === mode ) {
						$( '#shipping_product_data .show_if_packed_together' ).show();
						$( '#shipping_product_data .hide_if_packed_together' ).hide();
					} else if ( 'separate' === mode ) {
						$( '#shipping_product_data .show_if_packed_separately' ).show();
						$( '#shipping_product_data .hide_if_packed_separately' ).hide();

						// Trigger when packed separately is selected.
						$( '.mnm_packing_options .wc_mnm_has_physical_container' ).trigger( 'change' );
					} else {
						$( '#shipping_product_data .show_if_virtual' ).show();
						$( '#shipping_product_data .hide_if_virtual' ).hide();
					}

				}

			}
		);

		// Hide/Show shipping fields when packed separately fields.
		$( '.mnm_packing_options .wc_mnm_has_physical_container' ).on(
			'change',
			function() {

				if ( 'mix-and-match' === $( '#product-type' ).val() ) {

					if ( this.checked ) {
						$( '#shipping_product_data .show_if_has_physical_container' ).show();
					} else {
						$( '#shipping_product_data .show_if_has_physical_container' ).hide();
					}

				}

			}
		);

		// Mix and Match type specific options.
		$( document.body ).on(
			'woocommerce-product-type-change',
			function( event, select_val ) {

				if ( select_val === 'mix-and-match' ) {

					// Handle hide/show of toggles inside MNM panel.
					$( '#wc_mnm_layout_override' ).trigger( 'change' );
					$( '.wc_mnm_content_source:checked' ).trigger( 'change' );
					$( '.wc_mnm_per_product_pricing:checked' ).trigger( 'change' );
					$( 'input#_manage_stock' ).trigger( 'change' );

					// Handle hide/show of toggles inside shipping panel.
					$( '.mnm_packing_options .packing_mode:checked' ).trigger( 'change' );

					// Blunt-force the shipping tab to show. Necessary if not updated and user still has _virtual meta = 'yes'.
					$( '.product_data_tabs .shipping_options ' ).show();

				}

			}
		);

		if ( typeof $.fn.selectWoo === 'function' ) {

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

			$( '#mnm_allowed_categories' ).selectWoo( select2_args ).addClass( 'enhanced' );

			// Make sortable.
			if ( $( '#mnm_allowed_categories' ).data( 'sortable' ) ) {

				var $select = $( '#mnm_allowed_categories' );
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
		}

		// Trigger product type change.
		$( '#product-type' ).trigger( 'change' );

	}
);
