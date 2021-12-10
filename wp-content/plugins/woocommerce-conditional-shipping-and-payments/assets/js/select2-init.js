/*global wc_enhanced_select_params */
/*global wc_composite_admin_params */
jQuery( function( $ ) {

	var select2_utils         = $.fn.selectSW.amd.require( 'selectSW/utils' ),
	    select2_format_string = {

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

	// Prevent select2 opening on delete.
	function select2_close_on_delete( $el ) {

		$el
			.on( 'select2:unselecting', function() {
				$el.data( 'unselecting', true );
			} )

			.on( 'select2:opening', function( e ) {
				if ( $el.data( 'unselecting' ) ) {
					$el.removeData( 'unselecting' );
					e.preventDefault();
				}
			} );
	}

	// Make select2's sortable.
	function select2_sortable( $el, args ) {

		if ( $el.data( 'sortable' ) && $el.prop( 'multiple' ) ) {

			var $list = $el.next( '.select2-container' ).find( 'ul.select2-selection__rendered' );

			args = args || {};

			$list.sortable( {

				placeholder:          'ui-state-highlight select2-selection__choice',
				forcePlaceholderSize: true,
				items:                'li:not(.select2-search__field)',
				tolerance:            'pointer',
				stop:                 function() {

					$( $list.find( '.select2-selection__choice' ).get().reverse() ).each( function() {

						var id     = select2_utils.GetData( this, 'data' ).id,
							option = $el.find( 'option[value="' + id + '"]' )[0];

						$el.prepend( option );

						if ( typeof args.stop === 'function' ) {

							args.stop( $el );
						}

					} );
				}

			} );

		}
	}

	// Keep results sorted.
	function select2_sorted( $el ) {

		if ( ! $el.data( 'sortable' ) && $el.prop( 'multiple' ) ) {

			$el.on( 'change', function(){

				var $children = $el.children();

				$children.sort(function(a, b) {

					var atext = a.text.toLowerCase();
					var btext = b.text.toLowerCase();

					if ( atext > btext ) {
						return 1;
					}

					if ( atext < btext ) {
						return -1;
					}

					return 0;

				} );

				$el.html( $children );

			} );
		}
	}

	$.fn.sw_select2 = function( args ) {

		// Regular (multi)select boxes.
		$( ':input.sw-select2', this ).filter( ':not(.sw-select2--initialized)' ).each( function() {

			var $el          = $( this ),
				wrap         = $el.data( 'wrap' ),
				select2_args = {

					minimumResultsForSearch: 10,
					allowClear:              $el.data( 'allow_clear' ) ? true : false,
					placeholder:             $el.data( 'placeholder' ),
					closeOnSelect:           true
				};

			if ( wrap ) {
				wrap = 'yes' !== wrap ? 'sw-select2-wrap-' + wrap.toString() : 'sw-select2-wrap';
				$el.wrap( '<div class="' + wrap + '"></div>' );
			}

			select2_args = $.extend( select2_args, select2_format_string );

			$el.selectSW( select2_args ).addClass( 'sw-select2--initialized' );

			// Prevent opening on delete.
			select2_close_on_delete( $el );

			// Allow sortable multi-selects.
			select2_sortable( $el );

		} );

		// Ajax product search box.
		$( ':input.sw-select2-search--products', this ).filter( ':not(.sw-select2--initialized)' ).each( function() {

			var $el          = $( this ),
				wrap         = $el.data( 'wrap' ),
				select2_args = {

					cache:              true,
					allowClear:         $el.data( 'allow_clear' ) ? true : false,
					placeholder:        $el.data( 'placeholder' ),
					minimumInputLength: $el.data( 'minimum_input_length' ) ? $el.data( 'minimum_input_length' ) : '3',
					escapeMarkup:       function( m ) {
						return m;
					},
					ajax:               {

						url:      wc_enhanced_select_params.ajax_url,
						dataType: 'json',
						delay:    250,
						data:     function( params ) {

							return {
								term:     params.term,
								action:   $el.data( 'action' ) || 'woocommerce_json_search_products_and_variations',
								security: wc_enhanced_select_params.search_products_nonce,
								exclude:  $el.data( 'exclude' ),
								exclude_type:  $el.data( 'exclude_type' ),
								include:  $el.data( 'include' ),
								limit:    $el.data( 'limit' )
							};
						},
						processResults: function( data ) {

							var terms = [];

							if ( 'woocommerce_json_search_products_and_variations_in_component' === $el.data( 'action' ) ) {

								if ( 'yes' === $el.data( 'component_optional' ) || '8.0' === $el.data( 'action_version' ) ) {
									terms.push( { id: '-1', text: wc_composite_admin_params.i18n_none } );
								}
								if ( '8.0' !== $el.data( 'action_version' ) ) {
									terms.push( { id: '0', text: wc_composite_admin_params.i18n_all } );
								}
							}

							if ( data ) {
								$.each( data, function( id, text ) {
									terms.push( { id: id, text: text } );
								} );
							}

							return {
								results: terms
							};
						}
					}
				};

			if ( wrap ) {
				wrap = 'yes' !== wrap ? 'sw-select2-wrap-' + wrap.toString() : 'sw-select2-wrap';
				$el.wrap( '<div class="' + wrap + '"></div>' );
			}

			select2_args = $.extend( select2_args, select2_format_string );

			$el.selectSW( select2_args ).addClass( 'sw-select2--initialized' );

			// Prevent opening on delete.
			select2_close_on_delete( $el );

			// Allow sortable multi-selects.
			select2_sortable( $el, args && args.sortable ? args.sortable : {} );

			// Keep non-sortable multi-select results in alphabetical order.
			select2_sorted( $el );

		} );

		// Ajax category search box.
		$( ':input.sw-select2-search--categories', this ).filter( ':not(.sw-select2--initialized)' ).each( function() {

			var $el          = $( this ),
				wrap         = $el.data( 'wrap' ),
				select2_args = {

					cache:              true,
					allowClear:         $el.data( 'allow_clear' ) ? true : false,
					placeholder:        $el.data( 'placeholder' ),
					minimumInputLength: $el.data( 'minimum_input_length' ) ? $el.data( 'minimum_input_length' ) : 3,
					escapeMarkup:       function( m ) {
						return m;
					},
					ajax: {
						url:      wc_enhanced_select_params.ajax_url,
						dataType: 'json',
						delay:    250,
						data:     function( params ) {
							return {
								term:     params.term,
								action:   'woocommerce_json_search_categories',
								security: wc_enhanced_select_params.search_categories_nonce
							};
						},
						processResults: function( data ) {

							var terms = [];

							if ( data ) {

								$.each( data, function( id, term ) {

									terms.push( {
										id:   term.slug,
										text: term.formatted_name
									} );

								} );
							}

							return {
								results: terms
							};
						}
					}
				};

			if ( wrap ) {
				wrap = 'yes' !== wrap ? 'sw-select2-wrap-' + wrap.toString() : 'sw-select2-wrap';
				$el.wrap( '<div class="' + wrap + '"></div>' );
			}

			select2_args = $.extend( select2_args, select2_format_string );

			$el.selectSW( select2_args ).addClass( 'sw-select2--initialized' );

		} );

		$( '.sw-select2-search--customers', this ).filter( ':not(.sw-select2--initialized)' ).each( function() {

			var $el          = $( this ),
				wrap         = $el.data( 'wrap' ),
				select2_args = {

					cache:              true,
					allowClear:         $el.data( 'allow_clear' ) ? true : false,
					placeholder:        $el.data( 'placeholder' ),
					minimumInputLength: $el.data( 'minimum_input_length' ) ? $el.data( 'minimum_input_length' ) : 3,
					escapeMarkup:       function( m ) {
						return m;
					},
					ajax: {
						url:         wc_enhanced_select_params.ajax_url,
						dataType:    'json',
						delay:       1000,
						data:        function( params ) {
							return {
								term:     params.term,
								action:   'woocommerce_json_search_customers',
								security: wc_enhanced_select_params.search_customers_nonce,
								exclude:  $el.data( 'exclude' )
							};
						},
						processResults: function( data ) {

							var terms = [];

							if ( data ) {
								$.each( data, function( id, text ) {
									terms.push({
										id: id,
										text: text
									});
								});
							}

							return {
								results: terms
							};
						},
						cache: true
					}
				};

				if ( wrap ) {
					wrap = 'yes' !== wrap ? 'sw-select2-wrap-' + wrap.toString() : 'sw-select2-wrap';
					$el.wrap( '<div class="' + wrap + '"></div>' );
				}

				select2_args = $.extend( select2_args, select2_format_string );

				$el.selectSW( select2_args ).addClass( 'sw-select2--initialized' );

		} );

		$( document.body ).trigger( 'sw-select2-init' );
	};

	// Close.
	$( 'html' )

		.on( 'wc_backbone_modal_before_remove', function() {
			$( '.sw-select2, :input.sw-select2-search--products' ).filter( '.select2-hidden-accessible' ).selectSW( 'close' );
		} )

		.on( 'click', function( event ) {
			if ( this === event.target ) {
				$( '.sw-select2, :input.sw-select2-search--products' ).filter( '.select2-hidden-accessible' ).selectSW( 'close' );
			}
		} );

	// Autoinitialize.
	$( document.body ).find( '.sw-select2-autoinit' ).each( function() {
		$( this ).sw_select2();
	} );

} );
