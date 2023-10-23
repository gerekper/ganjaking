/**
 * Admin scripts
 *
 * @package YITH\MinimumMaximumQuantity
 */

jQuery(
	function ( $ ) {

		$( '.ywmmq-rules #doaction, .ywmmq-rules #doaction2, .ywmmq-rules #search-submit, .ywmmq-rules .pagination-links, .ywmmq-rules .yith-add-button, .ywmmq-rules .yith-update-button, .ywmmq-rules .yith-save-button' ).on(
			'click',
			function () {
				window.onbeforeunload = null;
			}
		);

		var product_ids_row  = $( 'tr.ajax-products.product_ids' ),
			category_ids_row = $( 'tr.ajax-terms.category_ids' ),
			tag_ids_row      = $( 'tr.ajax-terms.tag_ids' ),
			value_limit_row  = $( 'tr._value_limit_override' ),
			description      = $( 'tr._exclusion span.description' );

		$( '#item_type' ).on(
			'change',
			function () {
				switch ( $( this ).val() ) {
					case 'category':
						product_ids_row.hide();
						category_ids_row.show( 500 );
						tag_ids_row.hide();
						value_limit_row.show( 500 );
						description.find( 'span' ).hide();
						description.find( 'span.category' ).show();
						break;
					case 'tag':
						product_ids_row.hide();
						category_ids_row.hide();
						tag_ids_row.show( 500 );
						value_limit_row.show( 500 );
						description.find( 'span' ).hide();
						description.find( 'span.tag' ).show();
						break;
					default:
						product_ids_row.show( 500 );
						category_ids_row.hide();
						tag_ids_row.hide();
						value_limit_row.hide();
						description.find( 'span' ).hide();
						description.find( 'span.product' ).show();
						$( '.ywmmq-table #_value_limit_override' )
							.prop( 'checked', false )
							.val( 'no' )
							.removeClass( 'onoffchecked' )
							.trigger( 'change' );
				}
			}
		).trigger( 'change' );

		$( '.ywmmq-table #_exclusion' ).on(
			'change',
			function () {
				if ( $( this ).is( ':checked' ) ) {
					$( 'tr._quantity_limit_override' ).hide();
					$( 'tr._minimum_quantity' ).hide();
					$( 'tr._maximum_quantity' ).hide();
					$( 'tr._step_quantity' ).hide();
					$( 'tr._value_limit_override' ).hide();
					$( 'tr._minimum_value' ).hide();
					$( 'tr._maximum_value' ).hide();
				} else {
					$( 'tr._quantity_limit_override' ).show( 500 );
					if ( 'product' !== $( '#item_type' ).val() ) {
						$( 'tr._value_limit_override' ).show( 500 );
					}
					$( '.ywmmq-table #_quantity_limit_override' ).trigger( 'change' );
					$( '.ywmmq-table #_value_limit_override' ).trigger( 'change' );
				}
			}
		).trigger( 'change' );

		$( '.ywmmq-table #_quantity_limit_override' ).on(
			'change',
			function () {
				if ( $( this ).is( ':checked' ) ) {
					$( 'tr._minimum_quantity' ).show( 500 );
					$( 'tr._maximum_quantity' ).show( 500 );
					$( 'tr._step_quantity' ).show( 500 );
				} else {
					$( 'tr._minimum_quantity' ).hide();
					$( 'tr._maximum_quantity' ).hide();
					$( 'tr._step_quantity' ).hide();
				}
			}
		).trigger( 'change' );

		$( '.ywmmq-table #_value_limit_override' ).on(
			'change',
			function () {
				if ( $( this ).is( ':checked' ) ) {
					$( 'tr._minimum_value' ).show( 500 );
					$( 'tr._maximum_value' ).show( 500 );
				} else {
					$( 'tr._minimum_value' ).hide();
					$( 'tr._maximum_value' ).hide();
				}
			}
		).trigger( 'change' );

		function lock_unlock_product() {
			var variations_override         = $( '#_ywmmq_product_quantity_limit_variations_override' ),
				product_override_enabled    = $( '#_ywmmq_product_quantity_limit_override' ).is( ':checked' ),
				variations_override_enabled = (variations_override.length > 0 ? variations_override.is( ':checked' ) : false);

			if ( product_override_enabled ) {
				variations_override.prop( 'disabled', false );
				if ( ! variations_override_enabled ) {
					$( '#_ywmmq_product_minimum_quantity' ).prop( 'disabled', false );
					$( '#_ywmmq_product_maximum_quantity' ).prop( 'disabled', false );
					$( '#_ywmmq_product_step_quantity' ).prop( 'disabled', false );
				}
			} else {
				variations_override.prop( 'disabled', true );
				$( '#_ywmmq_product_minimum_quantity' ).prop( 'disabled', true );
				$( '#_ywmmq_product_maximum_quantity' ).prop( 'disabled', true );
				$( '#_ywmmq_product_step_quantity' ).prop( 'disabled', true );
			}
		}

		$( '#_ywmmq_product_exclusion' ).on(
			'change',
			function () {
				if ( $( this ).is( ':checked' ) ) {
					$( '#_ywmmq_product_quantity_limit_override' ).prop( 'disabled', true );
					$( '#_ywmmq_product_minimum_quantity' ).prop( 'disabled', true );
					$( '#_ywmmq_product_maximum_quantity' ).prop( 'disabled', true );
					$( '#_ywmmq_product_step_quantity' ).prop( 'disabled', true );
					$( '#_ywmmq_product_quantity_limit_variations_override' ).prop( 'disabled', true );
				} else {
					$( '#_ywmmq_product_quantity_limit_override' ).prop( 'disabled', false );
					lock_unlock_product();
				}
			}
		).trigger( 'change' );

		$( '#_ywmmq_product_quantity_limit_override' ).on(
			'change',
			function () {
				lock_unlock_product();
			}
		).trigger( 'change' );

		$( '#_ywmmq_product_quantity_limit_variations_override' ).on(
			'change',
			function () {
				if ( $( '#_ywmmq_product_quantity_limit_override' ).is( ':checked' ) ) {
					if ( $( this ).is( ':checked' ) ) {
						$( '#_ywmmq_product_minimum_quantity' ).prop( 'disabled', true );
						$( '#_ywmmq_product_maximum_quantity' ).prop( 'disabled', true );
						$( '#_ywmmq_product_step_quantity' ).prop( 'disabled', true );
						$( '.ywmmq-variation-field' ).each(
							function () {
								$( this ).prop( 'disabled', false );
							}
						);
					} else {
						$( '#_ywmmq_product_minimum_quantity' ).prop( 'disabled', false );
						$( '#_ywmmq_product_maximum_quantity' ).prop( 'disabled', false );
						$( '#_ywmmq_product_step_quantity' ).prop( 'disabled', false );
						$( '.ywmmq-variation-field' ).each(
							function () {
								$( this ).prop( 'disabled', true );
							}
						);
					}
				}
			}
		).trigger( 'change' );

		$( '#woocommerce-product-data' ).on(
			'woocommerce_variations_loaded',
			function () {
				$( '.ywmmq-variation-field' ).each(
					function () {
						if ( $( '#_ywmmq_product_quantity_limit_override' ).is( ':checked' ) && $( '#_ywmmq_product_quantity_limit_variations_override' ).is( ':checked' ) ) {
							$( this ).prop( 'disabled', false );
						} else {
							$( this ).prop( 'disabled', true );
						}
					}
				);
			}
		)

	}
);
