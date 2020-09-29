/* global wc_pb_min_max_items_params */

;( function ( $, window, document ) {

	function init_script( bundle ) {

		var min = bundle.$bundle_form.find( '.min_max_items' ).data( 'min' );
		var max = bundle.$bundle_form.find( '.min_max_items' ).data( 'max' );

		if ( typeof( min ) !== 'undefined' && typeof( max ) !== 'undefined' ) {

			bundle.min_max_validation = {

				min: min,
				max: max,

				bind_validation_handler: function() {

					var min_max_validation = this;

					bundle.$bundle_data.on( 'woocommerce-product-bundle-validate', function( event, bundle ) {

						var total_qty         = 0;
						var qty_error_status  = '';
						var qty_error_prompt  = '';
						var passed_validation = true;

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

							bundle.add_validation_message( qty_error_prompt.replace( '%s', wc_pb_min_max_items_params.i18n_qty_error_status_format.replace( '%s', qty_error_status ) ) );
						}

					} );
				}

			};

			bundle.min_max_validation.bind_validation_handler();
		}
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
