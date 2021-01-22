/* global wc_pb_min_max_items_params */

;( function ( $, window, document ) {

	function init_script( bundle ) {

		if ( typeof( bundle.price_data.size_min ) === 'undefined' || typeof( bundle.price_data.size_max ) === 'undefined' ) {
			return;
		}

		bundle.min_max_validation = {

			min: bundle.price_data.size_min,
			max: bundle.price_data.size_max,

			bind_validation_handler: function() {

				var min_max_validation = this;

				bundle.$bundle_data.on( 'woocommerce-product-bundle-validate', function( event, bundle ) {

					var total_qty         = 0,
					    qty_error_status  = '',
					    qty_error_prompt  = '',
					    passed_validation = true;

					// Count items.
					$.each( bundle.bundled_items, function( index, bundled_item ) {
						if ( bundled_item.is_selected() ) {
							total_qty += bundled_item.get_quantity();
						}
					} );

					// Validate.
					if ( min_max_validation.min !== '' && total_qty < parseInt( min_max_validation.min ) ) {

						passed_validation = false;

						if ( min_max_validation.min === 1 ) {

							if ( min_max_validation.min === min_max_validation.max ) {
								qty_error_prompt = wc_pb_min_max_items_params.i18n_min_zero_max_qty_error_singular;
							} else {
								qty_error_prompt = wc_pb_min_max_items_params.i18n_min_qty_error_singular;
							}

						} else {

							if ( min_max_validation.min === min_max_validation.max ) {
								qty_error_prompt = wc_pb_min_max_items_params.i18n_min_max_qty_error_plural;
							} else {
								qty_error_prompt = wc_pb_min_max_items_params.i18n_min_qty_error_plural;
							}

							qty_error_prompt = qty_error_prompt.replace( '%q', parseInt( min_max_validation.min ) );
						}

					} else if ( min_max_validation.max !== '' && total_qty > parseInt( min_max_validation.max ) ) {

						passed_validation = false;

						if ( min_max_validation.max === 1 ) {

							if ( min_max_validation.min === min_max_validation.max ) {
								qty_error_prompt = wc_pb_min_max_items_params.i18n_min_max_qty_error_singular;
							} else {
								qty_error_prompt = wc_pb_min_max_items_params.i18n_max_qty_error_singular;
							}

						} else {

							if ( min_max_validation.min === min_max_validation.max ) {
								qty_error_prompt = wc_pb_min_max_items_params.i18n_min_max_qty_error_plural;
							} else {
								qty_error_prompt = wc_pb_min_max_items_params.i18n_max_qty_error_plural;
							}

							qty_error_prompt = qty_error_prompt.replace( '%q', parseInt( min_max_validation.max ) );
						}
					}

					// Add notice.
					if ( ! passed_validation ) {

						if ( total_qty === 0 ) {

							qty_error_status = '';

							if ( 'no' === bundle.price_data.zero_items_allowed ) {

								var validation_messages         = bundle.get_validation_messages(),
									cleaned_validation_messages = [];

								for ( var i = 0; i <= validation_messages.length - 1; i++ ) {
									if ( validation_messages[ i ] !== wc_bundle_params.i18n_zero_qty_error ) {
										cleaned_validation_messages.push( validation_messages[ i ] );
									}
								}

								bundle.validation_messages = cleaned_validation_messages;
							}

						} else if ( total_qty === 1 ) {
							qty_error_status = wc_pb_min_max_items_params.i18n_qty_error_singular;
						} else {
							qty_error_status = wc_pb_min_max_items_params.i18n_qty_error_plural;
						}

						qty_error_status = qty_error_status.replace( '%s', total_qty );

						if ( bundle.validation_messages.length > 0 || '' === qty_error_status ) {
							bundle.add_validation_message( qty_error_prompt );
						} else {
							bundle.add_validation_message( '<span class="status_msg">' + '<span class="bundled_items_selection_msg">' + qty_error_prompt + '</span>' + '<span class="bundled_items_selection_status">' + qty_error_status + '</span>' + '</span>' );
						}
					}

				} );
			}

		};

		bundle.min_max_validation.bind_validation_handler();
	}

	$( 'body .component' ).on( 'wc-composite-component-loaded', function( event, component ) {
		if ( component.get_selected_product_type() === 'bundle' ) {
			var bundle = component.get_bundle_script();
			if ( bundle ) {
				init_script( bundle );
				bundle.update_bundle_task();
			}
		}
	} );

	$( '.bundle_form .bundle_data' ).each( function() {

		$( this ).on( 'woocommerce-product-bundle-initializing', function( event, bundle ) {
			if ( ! bundle.is_composited() ) {
				init_script( bundle );
			}
		} );
	} );

} ) ( jQuery, window, document );
