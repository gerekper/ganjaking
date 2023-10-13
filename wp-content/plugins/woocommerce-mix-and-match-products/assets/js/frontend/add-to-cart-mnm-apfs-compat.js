/**
 * Integrate with All Products for Subscriptions.
 *
 * @package  WooCommerce Mix and Match Products/Scripts
 */

( function ( $ ) {

	// Ensure wcsatt_single_product_params exists to continue.
	if ( typeof wcsatt_single_product_params === 'undefined' ) {
		return false;
	}

	// Mix and Match integration.
	var MNM_Integration = function ( container ) {

		var self = this,
			satt = container.$mnm_form.data( 'satt_script' );

		// Moves SATT options after the price.
		this.initialize_ui = function () {

			if ( satt.schemes_view.$el_content.length > 0 ) {
				if ( container.$addons_totals !== false ) {
					container.$addons_totals.after( satt.schemes_view.$el_content );
				} else {
					container.$mnm_price.after( satt.schemes_view.$el_content );
				}
			}
		};

		// Scans for SATT schemes attached on the Bundle.
		this.initialize_schemes = function () {

			container.satt_schemes         = [];
			container.satt_scheme_one_time = false;

			// Store data for the one-time option.
			var $scheme_option_one_time = satt.schemes_view.$el_option_items.filter( '.one-time-option' );

			if ( $scheme_option_one_time.length ) {
				container.satt_scheme_one_time = {
					$el:  $scheme_option_one_time,
					data: $scheme_option_one_time.find( 'input' ).data( 'custom_data' )
				};
			}

			// Store scheme data for options that override the default prices.
			var $scheme_options = satt.schemes_view.$el_option_items.filter( '.subscription-option' );

			$scheme_options.each(
				function () {

					var $scheme_option = $( this ),
					scheme_data        = $scheme_option.find( 'input' ).data( 'custom_data' );

					container.satt_schemes.push(
						{
							$el:  $scheme_option,
							data: scheme_data
						}
					);

				}
			);
		};

		// Init.
		this.integrate = function () {

			if ( satt.schemes_view.has_schemes() ) {

				self.initialize_ui();
				self.initialize_schemes();

				if ( container.satt_schemes.length > 0 ) {
					container.$mnm_form.on( 'wc-mnm-form-updated', self.update_subscription_totals );
					if ( 'no' !== container.price_data.hide_total_on_validation_fail ) {
						container.$mnm_data.on( 'wc-mnm-validation-status-changed', self.maybe_hide_subscription_options );
					}
				}
			}
		};

		this.has_single_forced_subscription = function () {
			return container.satt_schemes.length === 1 && false === container.satt_scheme_one_time;
		};

		// Hide subscription options?
		this.maybe_hide_subscription_options = function () {

			if ( container.passes_validation() ) {
				if ( ! self.has_single_forced_subscription() ) {
					satt.schemes_view.$el_content.slideDown( 200 );
				}
			} else {
				satt.schemes_view.$el_content.slideUp( 200 );
			}
		};

		// Update totals displayed in SATT options.
		this.update_subscription_totals = function ( event, container ) {

			if ( ! container.passes_validation() && 'no' !== container.price_data.hide_total_on_validation_fail ) {
				return;
			}

			var container_price_html       = container.get_price_html(),
				container_price_inner_html = $( container_price_html ).html();

			if ( false !== container.satt_scheme_one_time && container.api.is_priced_per_product() ) {

				var $one_time_price       = container.satt_scheme_one_time.$el.find( '.price.one-time-price' ),
					one_time_scheme_price = container.satt_scheme_one_time.data.option_details_html.replace( /%p/g, container_price_inner_html );

				if ( $one_time_price.length ) {
					$one_time_price.html( one_time_scheme_price ).find( 'span.total' ).remove();
				}

				if ( satt.schemes_view.has_prompt( 'radio' ) || satt.schemes_view.has_prompt( 'checkbox' ) ) {

					var $one_time_input = satt.schemes_view.$el_prompt.find( '.wcsatt-options-prompt-action-input[value="no"]' ),
						$one_time       = $one_time_input.closest( '.wcsatt-options-prompt-label' ).find( '.wcsatt-options-prompt-action' );

					// If the one-time prompt doesn't contain anything to update, move on.
					if ( $one_time.find( '.price' ).length > 0 ) {
						$one_time.html( container.satt_scheme_one_time.data.prompt_details_html.replace( /%p/g, container_price_inner_html ) ).find( 'span.total' ).remove();
					}
				}
			}

			// If only a single option is present, then container prices are already overridden on the server side.
			// In this case, simply grab the subscription details from the option and append them to the container price string.
			if ( self.has_single_forced_subscription() ) {

				container.$mnm_price.find( '.price' ).html( container.satt_schemes[0].data.option_details_html.replace( /%p/g, container_price_inner_html ) );

				/*
				* If multiple options are present, then:
				* - Calculate the subscription price for each option that overrides default prices and update its html string.
				* - Update the base price plan displayed in the prompt.
				*/
			} else if ( container.api.is_priced_per_product() ) {

				$.each(
					container.satt_schemes,
					function ( index, scheme ) {

						// Do we need to update any prices?
						if ( scheme.data.option_has_price || satt.schemes_view.has_prompt( 'radio' ) || satt.schemes_view.has_prompt( 'checkbox' ) ) {

							var scheme_price_data   = $.extend( true, {}, container.price_data ),
							scheme_price_html       = container_price_html,
							scheme_price_inner_html = container_price_inner_html;

							// Does the current scheme modify prices in any way? If yes, calculate new totals.
							if ( scheme.data.subscription_scheme.has_price_filter ) {

								if ( scheme.data.subscription_scheme.pricing_mode === 'inherit' && scheme.data.subscription_scheme.discount > 0 ) {

									$.each(
										container.api.get_container_config( 'v2' ),
										function ( index, data ) {

											var { product_id } = data;

											if ( scheme.data.discount_from_regular ) {
												scheme_price_data.prices[ product_id ] = scheme_price_data.regular_prices[ product_id ] * ( 1 - scheme.data.subscription_scheme.discount / 100 );
											} else {
												scheme_price_data.prices[ product_id ] = scheme_price_data.prices[ product_id ] * ( 1 - scheme.data.subscription_scheme.discount / 100 );
											}
											// Mix and Match does not yet support addons at the child level.
											if ( scheme_price_data.hasOwnProperty( 'addons_prices' ) ) {
												scheme_price_data.addons_prices[ product_id ] = scheme_price_data.addons_prices[ product_id ] * ( 1 - scheme.data.subscription_scheme.discount / 100 );
											}
										}
									);

									if ( scheme.data.discount_from_regular ) {
										scheme_price_data.base_price = scheme_price_data.base_regular_price * ( 1 - scheme.data.subscription_scheme.discount / 100 );
									} else {
										scheme_price_data.base_price = scheme_price_data.base_price * ( 1 - scheme.data.subscription_scheme.discount / 100 );
									}

									// var addons_raw_price = container.get_addons_raw_price();.
									var addons_raw_price = 0;

									scheme_price_data.addons_regular_price = container.has_addons() && 'yes' === satt.pao.$discount_addons_data.data( 'discount_addons' ) ? addons_raw_price : 0;
									scheme_price_data.addons_price         = container.has_addons() && 'yes' === satt.pao.$discount_addons_data.data( 'discount_addons' ) ? addons_raw_price * ( 1 - scheme.data.subscription_scheme.discount / 100 ) : 0;

								} else if ( scheme.data.subscription_scheme.pricing_mode === 'override' ) {

									scheme_price_data.base_regular_price = Number( scheme.data.subscription_scheme.regular_price );
									scheme_price_data.base_price         = Number( scheme.data.subscription_scheme.price );
								}

								scheme_price_data = container.calculate_subtotals( false, scheme_price_data );
								scheme_price_data = container.calculate_totals( scheme_price_data );

								scheme_price_html       = container.get_price_html( scheme_price_data );
								scheme_price_inner_html = $( scheme_price_html ).html();
							}

							var $option_price   = scheme.$el.find( '.subscription-price' ),
							option_scheme_price = scheme.data.option_details_html.replace( /%p/g, scheme_price_inner_html );

							// Update prompt.
							if ( scheme.data.subscription_scheme.is_base && ( satt.schemes_view.has_prompt( 'radio' ) || satt.schemes_view.has_prompt( 'checkbox' ) ) ) {

								var $prompt_input = satt.schemes_view.$el_prompt.find( '.wcsatt-options-prompt-action-input[value="yes"]' ),
								$prompt           = $prompt_input.closest( '.wcsatt-options-prompt-label' ).find( '.wcsatt-options-prompt-action' );

								// If the prompt doesn't contain anything to update, move on.
								if ( $prompt.find( '.subscription-price' ).length > 0 ) {
									$prompt.html( scheme.data.prompt_details_html.replace( /%p/g, scheme_price_inner_html ) ).find( 'span.total' ).remove();
								}
							}

							// Update plan.
							if ( scheme.data.option_has_price ) {

								$option_price.html( option_scheme_price ).find( 'span.total' ).remove();

								if ( satt.schemes_view.has_dropdown() ) {

									var dropdown_price = wc_mnm_price_format( scheme_price_data.totals.price, { html: false } ),
									discount           = '';

									dropdown_price = scheme.data.dropdown_format.replace( '%p', dropdown_price );

									if ( scheme.data.subscription_scheme.has_price_filter ) {
										if ( scheme.data.subscription_scheme.pricing_mode === 'inherit' && scheme.data.subscription_scheme.discount > 0 ) {

											discount       = satt.round_number( scheme.data.subscription_scheme.discount, scheme.data.dropdown_discount_decimals );
											dropdown_price = scheme.data.dropdown_discounted_format.replace( '%d', discount ).replace( '%p', dropdown_price );

										} else if ( scheme_price_data.totals.regular_price > scheme_price_data.totals.price ) {

											var dropdown_regular_price = wc_mnm_price_format( scheme_price_data.totals.regular_price, { html: false } );

											dropdown_price = scheme.data.dropdown_sale_format.replace( '%r', dropdown_regular_price ).replace( '%p', dropdown_price );
										}
									}

									satt.schemes_view.$el_dropdown.find( 'option[value=' + scheme.data.subscription_scheme.key + ']' ).text( dropdown_price );
								}
							}
							$option_price.trigger( 'wcsatt-updated-mnm-price', [ scheme_price_html, scheme, container, self ] );
						}

					}
				);
			}

			container.$mnm_form.trigger( 'wcsatt-updated-mnm-subscription-totals', [ container, self ] );

		};

		// Lights on.
		if ( satt ) {
			this.integrate();
		}
	};

	$( 'body' ).on(
		'wc-mnm-initializing',
		function ( e, container ) {
			new MNM_Integration( container );
		}
	);

} )( jQuery );
