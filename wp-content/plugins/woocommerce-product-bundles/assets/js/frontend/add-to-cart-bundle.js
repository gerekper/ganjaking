/* jshint -W041 */

/*-----------------------------------------------------------------*/
/*  Global script variable.                                        */
/*-----------------------------------------------------------------*/

var wc_pb_bundle_scripts = {};

/*-----------------------------------------------------------------*/
/*  Global utility variables + functions.                          */
/*-----------------------------------------------------------------*/

/**
 * Converts numbers to formatted price strings. Respects WC price format settings.
 */
function wc_pb_price_format( price, plain ) {

	plain = typeof( plain ) === 'undefined' ? false : plain;

	return wc_pb_woocommerce_number_format( wc_pb_number_format( price ), plain );
}

/**
 * Formats price strings according to WC settings.
 */
function wc_pb_woocommerce_number_format( price, plain ) {

	var remove     = wc_bundle_params.currency_format_decimal_sep,
		position   = wc_bundle_params.currency_position,
		symbol     = wc_bundle_params.currency_symbol,
		trim_zeros = wc_bundle_params.currency_format_trim_zeros,
		decimals   = wc_bundle_params.currency_format_num_decimals;

	plain = typeof( plain ) === 'undefined' ? false : plain;

	if ( trim_zeros == 'yes' && decimals > 0 ) {
		for ( var i = 0; i < decimals; i++ ) { remove = remove + '0'; }
		price = price.replace( remove, '' );
	}

	var formatted_price  = String( price ),
		formatted_symbol = plain ? symbol : '<span class="woocommerce-Price-currencySymbol">' + symbol + '</span>';

	if ( 'left' === position ) {
		formatted_price = formatted_symbol + formatted_price;
	} else if ( 'right' === position ) {
		formatted_price = formatted_price + formatted_symbol;
	} else if ( 'left_space' === position ) {
		formatted_price = formatted_symbol + ' ' + formatted_price;
	} else if ( 'right_space' === position ) {
		formatted_price = formatted_price + ' ' + formatted_symbol;
	}

	formatted_price = plain ? formatted_price : '<span class="woocommerce-Price-amount amount">' + formatted_price + '</span>';

	return formatted_price;
}

/**
 * Formats price values according to WC settings.
 */
function wc_pb_number_format( number ) {

	var decimals      = wc_bundle_params.currency_format_num_decimals,
		decimal_sep   = wc_bundle_params.currency_format_decimal_sep,
		thousands_sep = wc_bundle_params.currency_format_thousand_sep;

	var n = number, c = isNaN( decimals = Math.abs( decimals ) ) ? 2 : decimals;
	var d = decimal_sep == undefined ? ',' : decimal_sep;
	var t = thousands_sep == undefined ? '.' : thousands_sep, s = n < 0 ? '-' : '';
	var i = parseInt( n = Math.abs( +n || 0 ).toFixed( c ), 10 ) + '', j = ( j = i.length ) > 3 ? j % 3 : 0;

	return s + ( j ? i.substr( 0, j ) + t : '' ) + i.substr( j ).replace( /(\d{3})(?=\d)/g, '$1' + t ) + ( c ? d + Math.abs( n - i ).toFixed( c ).slice( 2 ) : '' );
}

/**
 * Rounds price values according to WC settings.
 */
function wc_pb_number_round( number, decimals ) {

	var precision         = typeof( decimals ) === 'undefined' ? wc_bundle_params.currency_format_num_decimals : parseInt( decimals, 10 ),
		factor            = Math.pow( 10, precision ),
		tempNumber        = number * factor,
		roundedTempNumber = Math.round( tempNumber );

	return roundedTempNumber / factor;
}

/**
 * i18n-friendly string joining.
 */
function wc_pb_format_list( arr, args ) {

	var formatted = '',
		count     = arr.length,
		plain     = args && args.plain,
		plain_sep = args && args.plain_sep;

	if ( count > 0 ) {

		var loop = 0,
			item = '';

		for ( var i = 0; i < count; i++ ) {

			loop++;
			item = plain ? arr[ i ] : wc_bundle_params.i18n_string_list_item.replace( '%s', arr[ i ] );

			if ( count == 1 || loop == 1 ) {
				formatted = item;
			} else if ( loop === count && ! plain_sep ) {
				formatted = wc_bundle_params.i18n_string_list_last_sep.replace( '%s', formatted ).replace( '%v', item );
			} else {
				formatted = wc_bundle_params.i18n_string_list_sep.replace( '%s', formatted ).replace( '%v', item );
			}
		}
	}

	return formatted;
}

/**
 * Bundle script object getter.
 */
jQuery.fn.wc_get_bundle_script = function() {

	var $bundle_form = jQuery( this );

	if ( ! $bundle_form.hasClass( 'bundle_form' ) ) {
		return false;
	}

	var script_id = $bundle_form.data( 'script_id' );

	if ( typeof( wc_pb_bundle_scripts[ script_id ] ) !== 'undefined' ) {
		return wc_pb_bundle_scripts[ script_id ];
	}

	return false;
};

/*-----------------------------------------------------------------*/
/*  Encapsulation.                                                 */
/*-----------------------------------------------------------------*/

( function( $ ) {

	/**
	 * Main bundle object.
	 */
	function WC_PB_Bundle( data ) {

		var bundle                    = this;

		this.bundle_id                = data.bundle_id;

		this.$bundle_form             = data.$bundle_form;
		this.$bundle_data             = data.$bundle_data;
		this.$bundle_wrap             = data.$bundle_data.find( '.bundle_wrap' );
		this.$bundled_items           = data.$bundle_form.find( '.bundled_product' );

		this.$bundle_availability     = data.$bundle_data.find( '.bundle_availability' );
		this.$bundle_price            = data.$bundle_data.find( '.bundle_price' );
		this.$bundle_button           = data.$bundle_data.find( '.bundle_button' );
		this.$bundle_error            = data.$bundle_data.find( '.bundle_error' );
		this.$bundle_error_content    = this.$bundle_error.find( 'ul.msg' );
		this.$bundle_quantity         = this.$bundle_button.find( 'input.qty' );

		this.$nyp                     = this.$bundle_data.find( '.nyp' );

		this.$addons_totals           = this.$bundle_data.find( '#product-addons-total' );
		this.show_addons_totals       = false;

		this.bundled_items            = {};

		this.price_data               = data.$bundle_data.data( 'bundle_form_data' );

		this.$initial_stock_status    = false;

		this.update_bundle_timer      = false;
		this.update_price_timer       = false;

		this.validation_messages      = [];

		this.is_initialized           = false;

		this.composite_data           = data.composite_data;

		this.dirty_subtotals          = false;

		this.filters                  = false;

		this.api                      = {

			/**
			 * Get the current bundle totals.
			 *
			 * @return object
			 */
			get_bundle_totals: function() {

				return bundle.price_data.totals;
			},

			/**
			 * Get the current bundled item totals.
			 *
			 * @return object
			 */
			get_bundled_item_totals: function( bundled_item_id ) {

				return bundle.price_data[ 'bundled_item_' + bundled_item_id + '_totals' ];
			},

			/**
			 * Get the current bundled item recurring totals.
			 *
			 * @return object
			 */
			get_bundled_item_recurring_totals: function( bundled_item_id ) {

				return bundle.price_data[ 'bundled_item_' + bundled_item_id + '_recurring_totals' ];
			},

			/**
			 * Get the current validation status of the bundle.
			 *
			 * @return string ('pass' | 'fail')
			 */
			get_bundle_validation_status: function() {

				return bundle.passes_validation() ? 'pass' : 'fail';
			},

			/**
			 * Get the current validation messages for the bundle.
			 *
			 * @return array
			 */
			get_bundle_validation_messages: function() {

				return bundle.get_validation_messages();
			},

			/**
			 * Get the current stock status of the bundle.
			 *
			 * @return string ('in-stock' | 'out-of-stock')
			 */
			get_bundle_stock_status: function() {

				var availability = bundle.$bundle_wrap.find( 'p.out-of-stock' ).not( '.inactive' );

				return availability.length > 0 ? 'out-of-stock' : 'in-stock';
			},

			/**
			 * Get the current availability string of the bundle.
			 *
			 * @return string
			 */
			get_bundle_availability: function() {

				var availability = bundle.$bundle_wrap.find( 'p.stock' );

				if ( availability.hasClass( 'inactive' ) ) {
					if ( false !== bundle.$initial_stock_status ) {
						availability = bundle.$initial_stock_status.clone().wrap( '<div></div>' ).parent().html();
					} else {
						availability = '';
					}
				} else {
					availability = availability.clone().removeAttr( 'style' ).wrap( '<div></div>' ).parent().html();
				}

				return availability;
			},

			/**
			 * Gets bundle configuration details.
			 *
			 * @return object | false
			 */
			get_bundle_configuration: function() {

				var bundle_config = {};

				if ( bundle.bundled_items.length === 0 ) {
					return false;
				}

				$.each( bundle.bundled_items, function( index, bundled_item ) {

					var bundled_item_config = {
						title:         bundled_item.get_title(),
						product_title: bundled_item.get_product_title(),
						product_id:    bundled_item.get_product_id(),
						variation_id:  bundled_item.get_variation_id(),
						quantity:      bundle.price_data.quantities[ bundled_item.bundled_item_id ],
						product_type:  bundled_item.get_product_type(),
					};

					bundle_config[ bundled_item.bundled_item_id ] = bundled_item_config;
				} );

				return bundle_config;
			}
		};

		/**
		 * Object initialization.
		 */
		this.initialize = function() {

			/**
			 * Initial states and loading.
			 */

			// Filters API.
			this.filters = new WC_PB_Filters_Manager();

			// Addons compatibility.
			if ( this.has_addons() ) {

				// Totals visible?
				if ( 1 == this.$addons_totals.data( 'show-sub-total' ) || ( this.is_composited() && this.composite_data.component.show_addons_totals ) ) {
					// Ensure addons ajax is not triggered at all, as we calculate tax on the client side.
					this.$addons_totals.data( 'show-sub-total', 0 );
					this.$bundle_price.after( this.$addons_totals );
					this.show_addons_totals = true;

					/**
					 * Trigger addon totals to be re-rendered after changing the 'show-sub-total' data attribute.
					 */
					bundle.$bundle_data.trigger( 'woocommerce-product-addons-update' );
				}

			} else {
				this.$addons_totals = false;
			}

			// Save initial availability.
			if ( this.$bundle_wrap.find( 'p.stock' ).length > 0 ) {
				this.$initial_stock_status = this.$bundle_wrap.find( 'p.stock' ).clone();
			}

			// Back-compat.
			if ( ! this.price_data ) {
				this.price_data = data.$bundle_data.data( 'bundle_price_data' );
			} else if ( ! this.$bundle_data.data( 'bundle_price_data' ) ) {
				this.$bundle_data.data( 'bundle_price_data', this.price_data );
			}

			// Price suffix data.
			this.price_data.suffix_exists              = wc_bundle_params.price_display_suffix !== '';
			this.price_data.suffix                     = wc_bundle_params.price_display_suffix !== '' ? ' <small class="woocommerce-price-suffix">' + wc_bundle_params.price_display_suffix + '</small>' : '';
			this.price_data.suffix_contains_price_incl = wc_bundle_params.price_display_suffix.indexOf( '{price_including_tax}' ) > -1;
			this.price_data.suffix_contains_price_excl = wc_bundle_params.price_display_suffix.indexOf( '{price_excluding_tax}' ) > -1;

			// Delete redundant form inputs.
			this.$bundle_button.find( 'input[name*="bundle_variation"], input[name*="bundle_attribute"]' ).remove();

			/**
			 * Bind bundle event handlers.
			 */

			this.bind_event_handlers();
			this.viewport_resized();

			/**
			 * Init Bundled Items.
			 */

			this.init_bundled_items();

			/**
			 * Init Composite Products integration.
			 */

			if ( this.is_composited() ) {
				this.init_composite();
			}

			/**
			 * Init Product Add-Ons integration.
			 */
			if ( 'yes' === wc_bundle_params.is_pao_installed && typeof window.WC_PAO !== 'undefined' ) {
				this.match_bundled_items_addons_forms();
			}

			/**
			 * Initialize.
			 */

			this.$bundle_data.trigger( 'woocommerce-product-bundle-initializing', [ this ] );

			$.each( this.bundled_items, function( index, bundled_item ) {
				bundled_item.init_scripts();
			} );

			this.update_bundle_task();

			this.is_initialized = true;

			this.$bundle_form.addClass( 'initialized' );

			this.$bundle_data.trigger( 'woocommerce-product-bundle-initialized', [ this ] );
		};

		/**
		 * Shuts down events, actions and filters managed by this script object.
		 */
		this.shutdown = function() {

			this.$bundle_form.find( '*' ).off();

			if ( false !== this.composite_data ) {
				this.remove_composite_hooks();
			}
		};

		/**
		 * Composite Products app integration.
		 */
		this.init_composite = function() {

			/**
			 * Add/remove hooks on the 'component_scripts_initialized' action.
			 */
			this.composite_data.composite.actions.add_action( 'component_scripts_initialized_' + this.composite_data.component.step_id, this.component_scripts_initialized_action, 10, this );
		};

		/**
		 * Add hooks on the 'component_scripts_initialized' action.
		 */
		this.component_scripts_initialized_action = function() {

			var is_bundle_selected = false;

			// Composite Products < 4.0 compatibility.
			if ( typeof this.composite_data.component.component_selection_model.selected_product !== 'undefined' ) {
				is_bundle_selected = parseInt( this.composite_data.component.component_selection_model.selected_product, 10 ) === parseInt( this.bundle_id, 10 );
			} else {
				is_bundle_selected = parseInt( this.composite_data.component.component_selection_model.get( 'selected_product' ), 10 ) === parseInt( this.bundle_id, 10 );
			}

			if ( is_bundle_selected ) {
				this.add_composite_hooks();
			} else {
				this.remove_composite_hooks();
			}
		};

		/**
		 * Composite Products app integration - add actions and filters.
		 */
		this.add_composite_hooks = function() {

			/**
			 * Filter validation state.
			 */
			this.composite_data.composite.filters.add_filter( 'component_is_valid', this.cp_component_is_valid_filter, 10, this );

			/**
			 * Filter title in summary.
			 */
			this.composite_data.composite.filters.add_filter( 'component_selection_formatted_title', this.cp_component_selection_formatted_title_filter, 10, this );
			this.composite_data.composite.filters.add_filter( 'component_selection_meta', this.cp_component_selection_meta_filter, 10, this );

			/**
			 * Filter totals.
			 */
			this.composite_data.composite.filters.add_filter( 'component_totals', this.cp_component_totals_filter, 10, this );

			/**
			 * Filter component configuration data.
			 */
			this.composite_data.composite.filters.add_filter( 'component_configuration', this.cp_component_configuration_filter, 10, this );

			/**
			 * Add validation messages.
			 */
			this.composite_data.composite.actions.add_action( 'validate_step', this.cp_validation_messages_action, 10, this );
		};

		/**
		 * Composite Products app integration - remove actions and filters.
		 */
		this.remove_composite_hooks = function() {

			this.composite_data.composite.filters.remove_filter( 'component_is_valid', this.cp_component_is_valid_filter );
			this.composite_data.composite.filters.remove_filter( 'component_selection_formatted_title', this.cp_component_selection_formatted_title_filter );
			this.composite_data.composite.filters.remove_filter( 'component_selection_meta', this.cp_component_selection_meta_filter );
			this.composite_data.composite.filters.remove_filter( 'component_totals', this.cp_component_totals_filter );
			this.composite_data.composite.filters.remove_filter( 'component_configuration', this.cp_component_configuration_filter );

			this.composite_data.composite.actions.remove_action( 'component_scripts_initialized_' + this.composite_data.component.step_id, this.component_scripts_initialized_action );
			this.composite_data.composite.actions.remove_action( 'validate_step', this.cp_validation_messages_action );
		};

		/**
		 * Appends bundle configuration data to component config data.
		 */
		this.cp_component_configuration_filter = function( configuration_data, component ) {

			if ( component.step_id === this.composite_data.component.step_id && parseInt( component.get_selected_product(), 10 ) === parseInt( bundle.bundle_id, 10 ) ) {
				configuration_data.bundled_items = bundle.api.get_bundle_configuration();
			}

			return configuration_data;
		};

		/**
		 * Filters the component totals to pass on the calculated bundle totals.
		 */
		this.cp_component_totals_filter = function( totals, component, qty ) {

			if ( component.step_id === this.composite_data.component.step_id && parseInt( component.get_selected_product(), 10 ) === parseInt( bundle.bundle_id, 10 ) ) {

				var price_data       = $.extend( true, {}, bundle.price_data ),
					addons_raw_price = bundle.has_addons() ? bundle.get_addons_raw_price() : 0;

				qty = typeof( qty ) === 'undefined' ? component.get_selected_quantity() : qty;

				if ( addons_raw_price > 0 ) {
					// Recalculate price html with add-ons price and qty embedded.
					price_data.base_price = Number( price_data.base_price ) + Number( addons_raw_price );
				}

				price_data = bundle.calculate_subtotals( false, price_data, qty );
				price_data = bundle.calculate_totals( price_data );

				return price_data.totals;
			}

			return totals;
		};

		/**
		 * Filters the summary view title to include bundled product details.
		 */
		this.cp_component_selection_formatted_title_filter = function( formatted_title, raw_title, qty, formatted_meta, component ) {

			if ( component.step_id === this.composite_data.component.step_id && parseInt( component.get_selected_product(), 10 ) === parseInt( this.bundle_id, 10 ) ) {

				var bundled_products_count = 0;

				$.each( this.bundled_items, function( index, bundled_item ) {
					if ( bundled_item.$bundled_item_cart.data( 'quantity' ) > 0 ) {
						bundled_products_count++;
					}
				} );

				if ( this.group_mode_supports( 'component_multiselect' ) ) {
					if ( bundled_products_count === 0 ) {
						formatted_title = wc_composite_params.i18n_no_selection;
					} else {

						var contents = this.cp_get_formatted_contents( component );

						if ( contents ) {
							formatted_title = contents;
						}
					}
				}
			}

			return formatted_title;
		};

		/**
		 * Filters the summary view title to include bundled product details.
		 */
		this.cp_component_selection_meta_filter = function( meta, component ) {

			if ( component.step_id === this.composite_data.component.step_id && parseInt( component.get_selected_product(), 10 ) === parseInt( this.bundle_id, 10 ) ) {

				var bundled_products_count = 0;

				$.each( this.bundled_items, function( index, bundled_item ) {
					if ( bundled_item.$bundled_item_cart.data( 'quantity' ) > 0 ) {
						bundled_products_count++;
					}
				} );

				if ( bundled_products_count !== 0 && false === this.group_mode_supports( 'component_multiselect' ) ) {

					var selected_bundled_products = this.cp_get_formatted_contents( component );

					if ( selected_bundled_products !== '' ) {
						meta.push( { meta_key: wc_bundle_params.i18n_contents, meta_value: selected_bundled_products } );
					}
				}
			}

			return meta;
		};

		/**
		 * Formatted bundle contents for display in Composite Products summary views.
		 */
		this.cp_get_formatted_contents = function( component ) {

			var formatted_contents   = '',
				bundled_item_details = [],
				bundle_qty           = component.get_selected_quantity();

			$.each( this.bundled_items, function( index, bundled_item ) {

				if ( bundled_item.$self.hasClass( 'bundled_item_hidden' ) ) {
					return true;
				}

				if ( bundled_item.$bundled_item_cart.data( 'quantity' ) > 0 ) {

					var $item_image             = bundled_item.$bundled_item_image.find( 'img' ).first(),
						item_image              = $item_image.length > 0 ? $item_image.get( 0 ).outerHTML : false,
						item_quantity           = parseInt( bundled_item.$bundled_item_cart.data( 'quantity' ) * bundle_qty, 10 ),
						item_meta               = wc_cp_get_variation_data( bundled_item.$bundled_item_cart.find( '.variations' ) ),
						formatted_item_title    = bundled_item.$bundled_item_cart.data( 'title' ),
						formatted_item_quantity = item_quantity > 1 ? '<strong>' + wc_composite_params.i18n_qty_string.replace( '%s', item_quantity ) + '</strong>' : '',
						formatted_item_meta     = '';

					if ( item_meta.length > 0 ) {

						$.each( item_meta, function( index, meta ) {
							formatted_item_meta = formatted_item_meta + '<span class="bundled_meta_element"><span class="bundled_meta_key">' + meta.meta_key + ':</span> <span class="bundled_meta_value">' + meta.meta_value + '</span>';
							if ( index !== item_meta.length - 1 ) {
								formatted_item_meta = formatted_item_meta + '<span class="bundled_meta_value_sep">, </span>';
							}
							formatted_item_meta = formatted_item_meta + '</span>';
						} );

						formatted_item_title = wc_bundle_params.i18n_title_meta_string.replace( '%t', formatted_item_title ).replace( '%m', '<span class="content_bundled_product_meta">' + formatted_item_meta + '</span>' );
					}

					formatted_item_title = wc_composite_params.i18n_title_string.replace( '%t', formatted_item_title ).replace( '%q', formatted_item_quantity ).replace( '%p', '' );

					bundled_item_details.push( { title: formatted_item_title, image: item_image } );
				}
			} );

			if ( bundled_item_details.length > 0 ) {

				formatted_contents = formatted_contents + '<span class="content_bundled_product_details_wrapper">';

				$.each( bundled_item_details, function( index, details ) {
					formatted_contents = formatted_contents + '<span class="content_bundled_product_details">' + ( details.image ? '<span class="content_bundled_product_image">' + details.image + '</span>' : '' ) + '<span class="content_bundled_product_title">' + details.title + '</span></span>';
				} );

				formatted_contents = formatted_contents + '</span>';
			}

			return formatted_contents;
		};

		/**
		 * Filters the validation state of the component containing this bundle.
		 */
		this.cp_component_is_valid_filter = function( is_valid, check_scenarios, component ) {

			if ( component.step_id === this.composite_data.component.step_id ) {
				if ( parseInt( component.get_selected_product( check_scenarios ), 10 ) === parseInt( this.bundle_id, 10 ) && component.get_selected_quantity() > 0 && component.is_visible() ) {
					is_valid = this.passes_validation();
				}
			}

			return is_valid;
		};

		/**
		 * Adds validation messages to the component containing this bundle.
		 */
		this.cp_validation_messages_action = function( step, is_valid ) {

			if ( step.step_id === this.composite_data.component.step_id && false === is_valid && parseInt( step.get_selected_product(), 10 ) === parseInt( this.bundle_id, 10 ) ) {

				var validation_messages = this.get_validation_messages();

				$.each( validation_messages, function( index, message ) {
					step.add_validation_message( message );
					step.add_validation_message( message, 'composite' );
				} );
			}
		};

		/**
		 * WC front-end ajax URL.
		 */
		this.get_ajax_url = function( action ) {

			return woocommerce_params.wc_ajax_url.toString().replace( '%%endpoint%%', action );
		};

		/**
		 * Handler for viewport resizing.
		 */
		this.viewport_resized = function() {

			if ( this.is_composited() ) {
				return;
			}

			var form_width = this.$bundle_form.width();

			if ( form_width <= wc_bundle_params.responsive_breakpoint ) {
				this.$bundle_form.addClass( 'small_width' );
			} else {
				this.$bundle_form.removeClass( 'small_width' );
			}
		};

		/**
		 * Attach bundle-level event handlers.
		 */
		this.bind_event_handlers = function() {

			// Add responsive class to bundle form.
			$( window ).on( 'resize', function() {

				clearTimeout( bundle.viewport_resize_timer );

				bundle.viewport_resize_timer = setTimeout( function() {
					bundle.viewport_resized();
				}, 50 );
			} );

			// PAO compatibility.
			if ( bundle.has_addons() ) {
				bundle.$bundle_data.on( 'updated_addons', bundle.updated_addons_handler );
			}

			// CP compatibility.
			if ( bundle.is_composited() ) {
				bundle.$bundle_quantity.on( 'input change', function() {
					bundle.$bundle_data.trigger( 'woocommerce-product-bundle-update' );
				} );
			}

			this.$bundle_data

				// NYP compatibility.
				.on( 'woocommerce-nyp-updated-item', function( event ) {

					if ( bundle.$nyp.is( ':visible' ) ) {

						bundle.price_data.base_regular_price = bundle.$nyp.data( 'price' );
						bundle.price_data.base_price         = bundle.price_data.base_regular_price;

						if ( bundle.is_initialized ) {
							bundle.dirty_subtotals = true;
							bundle.update_totals();
						}
					}

					event.stopPropagation();
				} )

				.on( 'woocommerce-product-bundle-validation-status-changed', function( event, bundle ) {
					bundle.updated_totals();
				} )

				.on( 'click', '.bundle_add_to_cart_button', function( event ) {

					if ( $( this ).hasClass( 'disabled' ) ) {

						event.preventDefault();
						window.alert( wc_bundle_params.i18n_validation_alert );

					}
				} )

				.on( 'woocommerce-product-bundle-update-totals', function( event, force, _bundle ) {

					var target_bundle = typeof( _bundle ) === 'undefined' ? bundle : _bundle;

					force = typeof( force ) === 'undefined' ? false : force;

					if ( force ) {
						target_bundle.dirty_subtotals = true;
					}

					target_bundle.update_totals();
				} )

				.on( 'woocommerce-bundled-item-totals-changed', function( event, bundled_item ) {

					if ( bundled_item.has_addons() ) {
						bundled_item.render_addons_totals();
					}
				} )

				.on( 'woocommerce-product-bundle-update', function( event, triggered_by ) {

					var target_bundle = typeof( triggered_by ) === 'undefined' ? bundle : triggered_by.get_bundle();

					if ( triggered_by ) {
						target_bundle.update_bundle( triggered_by );
					} else {
						target_bundle.update_bundle();
					}
				} );
		};

		/**
		 * Initialize bundled item objects.
		 */
		this.init_bundled_items = function() {

			bundle.$bundled_items.each( function( index ) {

				bundle.bundled_items[ index ] = new WC_PB_Bundled_Item( bundle, $( this ), index );

				bundle.bind_bundled_item_event_handlers( bundle.bundled_items[ index ] );
			} );
		};

		/**
		 * Match addon forms created by the Product Add-On scripts with bundled item forms.
		 */
		this.match_bundled_items_addons_forms = function() {

			var initialized_addon_forms = window.WC_PAO.initialized_forms;

			bundle.$bundled_items.each( function( index ) {

				var bundled_item_cart_form = bundle.bundled_items[ index ].$bundled_item_cart

				$.each( initialized_addon_forms, function() {
					if ( this.$el[0] === bundled_item_cart_form[0] ) {
						bundle.bundled_items[ index ].addons_form = this;
						return false;
					}
				} );

			} );
		};

		/**
		 * Attach bundled-item-level event handlers.
		 */
		this.bind_bundled_item_event_handlers = function( bundled_item ) {

			bundled_item.$self

				/**
				 * Update totals upon changing quantities.
				 */
				.on( 'input change', 'input.bundled_qty', function( event ) {

					var $input = $( this ),
						qty    = parseFloat( $input.val() ),
						min    = parseFloat( $input.attr( 'min' ) ),
						max    = parseFloat( $input.attr( 'max' ) );

					if ( wc_bundle_params.force_min_max_qty_input === 'yes' && 'change' === event.type ) {

						if ( min >= 0 && ( qty < min || isNaN( qty ) ) ) {
							qty = min;
						}

						if ( max > 0 && qty > max ) {
							qty = max;
						}

						$input.val( qty );
					}

					// A zero quantity item is considered optional by NYP.
					if( bundled_item.is_nyp() && ! bundled_item.is_optional() && min === 0 ) {
						bundled_item.$nyp.data( 'optional_status', qty > 0 ? true : false );
					}

					bundled_item.update_selection_title();

					bundle.$bundle_data.trigger( 'woocommerce-product-bundle-update', [ bundled_item ] );
				} )

				.on( 'change', '.bundled_product_optional_checkbox input', function( event ) {

					if ( $( this ).is( ':checked' ) ) {

						bundled_item.$bundled_item_content.css( {
							height:   '',
							display: 'block',
							position: 'absolute',
						} );

						var height = bundled_item.$bundled_item_content.get( 0 ).getBoundingClientRect().height;

						if ( typeof height === 'undefined' ) {
							height = bundled_item.$bundled_item_content.outerHeight();
						}

						bundled_item.$bundled_item_content.css( {
							height:   '',
							position: '',
							display:  'none'
						} );

						if ( height ) {
							bundled_item.$bundled_item_content.addClass( 'bundled_item_cart_content--populated' );
							bundled_item.$bundled_item_content.slideDown( 200 );
						}

						bundled_item.set_selected( true );

						// Tabular mini-extension compat.
						bundled_item.$self.find( '.bundled_item_qty_col .quantity' ).removeClass( 'quantity_hidden' );

						if( bundled_item.is_nyp() ) {
							bundled_item.$nyp.trigger( 'wc-nyp-update', [ { 'force': true } ] );
						}

						// Allow variations script to flip images in bundled_product_images div.
						bundled_item.$bundled_item_cart.find( '.variations select:eq(0)' ).trigger( 'change' );

					} else {

						bundled_item.$bundled_item_content.slideUp( 200 );
						bundled_item.set_selected( false );

						// Tabular mini-extension compat.
						bundled_item.$self.find( '.bundled_item_qty_col .quantity' ).addClass( 'quantity_hidden' );

						// Reset image in bundled_product_images div.
						if ( bundled_item.reset_variation_image() ) {
							bundled_item.maybe_add_wc_core_gallery_class();
							bundled_item.$bundled_item_cart.trigger( 'reset_image' );
							bundled_item.maybe_remove_wc_core_gallery_class();
						}
					}

					bundled_item.update_selection_title();

					bundle.$bundle_data.trigger( 'woocommerce-product-bundle-update', [ bundled_item ] );

					event.stopPropagation();
				} )

				.on( 'found_variation', function( event, variation ) {

					bundled_item.set_variation_id( variation.variation_id );

					var variation_price         = variation.price,
					    variation_regular_price = variation.regular_price;

					if ( bundled_item.is_nyp() && variation.is_nyp && bundled_item.$nyp.is( ':visible' ) ) {
						variation_price = variation_regular_price = bundled_item.$nyp.data( 'price' );
					}

					// Put variation price data in price table.
					bundle.price_data.prices[ bundled_item.bundled_item_id ]                   = Number( variation_price );
					bundle.price_data.regular_prices[ bundled_item.bundled_item_id ]           = Number( variation_regular_price );

					bundle.price_data.prices_tax[ bundled_item.bundled_item_id ]               = variation.price_tax;

					// Put variation recurring component data in price table.
					bundle.price_data.recurring_prices[ bundled_item.bundled_item_id ]         = Number( variation.recurring_price );
					bundle.price_data.regular_recurring_prices[ bundled_item.bundled_item_id ] = Number( variation.regular_recurring_price );

					bundle.price_data.recurring_html[ bundled_item.bundled_item_id ]           = variation.recurring_html;
					bundle.price_data.recurring_keys[ bundled_item.bundled_item_id ]           = variation.recurring_key;

					// Update availability data.
					bundle.price_data.quantities_available[ bundled_item.bundled_item_id ]            = variation.avail_qty;
					bundle.price_data.is_in_stock[ bundled_item.bundled_item_id ]                     = variation.is_in_stock ? 'yes' : 'no'; // Boolean value coming from WC.
					bundle.price_data.backorders_allowed[ bundled_item.bundled_item_id ]              = variation.backorders_allowed ? 'yes' : 'no'; // Boolean value coming from WC.
					bundle.price_data.backorders_require_notification[ bundled_item.bundled_item_id ] = variation.backorders_require_notification;

					// Remove .images class from bundled_product_images div in order to avoid styling issues.
					bundled_item.maybe_remove_wc_core_gallery_class();

					// If the bundled item is optional and not selected, reset the variable product image.
					if ( bundled_item.reset_variation_image() ) {
						bundled_item.maybe_add_wc_core_gallery_class();
						bundled_item.$bundled_item_cart.trigger( 'reset_image' );
						bundled_item.maybe_remove_wc_core_gallery_class();
					}

					bundled_item.update_selection_title();

					bundle.$bundle_data.trigger( 'woocommerce-product-bundle-update', [ bundled_item ] );

					event.stopPropagation();
				} )

				.on( 'reset_image', function() {
					// Remove .images class from bundled_product_images div in order to avoid styling issues.
					bundled_item.maybe_remove_wc_core_gallery_class();

				} )

				.on( 'woocommerce-product-addons-update', function( event ) {
					event.stopPropagation();
				} )

				.on( 'woocommerce_variation_select_focusin', function( event ) {
					event.stopPropagation();
				} )

				.on( 'woocommerce_variation_has_changed', function( event ) {

					if ( bundled_item.$reset_bundled_variations ) {
						if ( bundled_item.variation_id ) {
							bundled_item.$reset_bundled_variations.slideDown( 200 );
						} else {
							bundled_item.$reset_bundled_variations.slideUp( 200 );
						}
					}

					event.stopPropagation();
				} )

				.on( 'woocommerce_variation_select_change', function( event ) {

					bundled_item.set_variation_id( '' );

					bundle.price_data.quantities_available[ bundled_item.bundled_item_id ]            = '';
					bundle.price_data.is_in_stock[ bundled_item.bundled_item_id ]                     = '';
					bundle.price_data.backorders_allowed[ bundled_item.bundled_item_id ]              = '';
					bundle.price_data.backorders_require_notification[ bundled_item.bundled_item_id ] = '';

					// Add .images class to bundled_product_images div (required by the variations script to flip images).
					if ( bundled_item.is_selected() ) {
						bundled_item.maybe_add_wc_core_gallery_class();
					}

					if ( bundled_item.$attribute_select ) {
						bundled_item.$attribute_select.each( function() {

							if ( $( this ).val() === '' ) {

								// Prevent from appearing as out of stock.
								bundled_item.$bundled_item_cart.find( '.bundled_item_wrap .stock' ).addClass( 'disabled' );
								// Trigger bundle update.
								bundle.$bundle_data.trigger( 'woocommerce-product-bundle-update', [ bundled_item ] );
								return false;
							}
						} );
					}

					event.stopPropagation();

				} );


			if ( bundled_item.has_addons() ) {

				bundled_item.$bundled_item_cart

					/**
					 * Calculate taxes and render addons totals on the client side.
					 * We already prevented Add-ons from firing an ajax request in 'WC_PB_Bundled_Item'.
					 */
					.on( 'updated_addons', function( event ) {

						// Always restore totals state because PAO empties it before the 'updated_addons' event.
						bundled_item.$addons_totals.html( bundled_item.addons_totals_html );

						bundle.$bundle_data.trigger( 'woocommerce-product-bundle-update', [ bundled_item ] );

						event.stopPropagation();
					} );
			}

			if ( bundled_item.is_nyp() ) {

				bundled_item.$bundled_item_cart

					.on( 'woocommerce-nyp-updated-item', function( event ) {

						if ( bundled_item.$nyp.is( ':visible' ) ) {

							var nyp_price = bundled_item.$nyp.data( 'price' );

							bundle.price_data.prices[ bundled_item.bundled_item_id ]         = nyp_price;
							bundle.price_data.regular_prices[ bundled_item.bundled_item_id ] = nyp_price;

							bundle.$bundle_data.trigger( 'woocommerce-product-bundle-update', [ bundled_item ] );
						}

						event.stopPropagation();
					} );
			}
		};

		/**
		 * Returns the quantity of this bundle.
		 */
		this.get_quantity = function() {
			var qty = bundle.$bundle_quantity.length > 0 ? bundle.$bundle_quantity.val() : 1;
			return isNaN( qty ) ? 1 : parseInt( qty, 10 );
		};

		/**
		 * Returns an availability string for the bundled items.
		 */
		this.get_bundled_items_availability = function() {

			var insufficiently_stocked_items      = [],
			    insufficiently_stocked_items_list = true,
			    backordered_items                 = [],
			    backordered_items_list            = true;

			$.each( bundle.bundled_items, function( index, bundled_item ) {

				if ( bundled_item.has_insufficient_stock() ) {

					insufficiently_stocked_items.push( bundled_item.get_title( true ) );

					if ( ! bundled_item.is_visible() || ! bundled_item.get_title( true ) ) {
						insufficiently_stocked_items_list = false;
						return false;
					}
				}

			} );

			if ( insufficiently_stocked_items.length > 0 ) {

				if ( insufficiently_stocked_items_list ) {
					return wc_bundle_params.i18n_insufficient_stock_list.replace( '%s', wc_pb_format_list( insufficiently_stocked_items, { plain: true, plain_sep: true } ) );
				} else {
					return wc_bundle_params.i18n_insufficient_stock_status;
				}
			}

			if ( bundle.$bundle_form.hasClass( 'bundle_out_of_stock' ) || bundle.$bundle_form.hasClass( 'bundle_insufficient_stock' ) ) {
				return false;
			}

			$.each( bundle.bundled_items, function( index, bundled_item ) {

				if ( bundled_item.is_backordered() ) {

					backordered_items.push( bundled_item.get_title( true ) );

					if ( ! bundled_item.is_visible() || ! bundled_item.get_title( true ) ) {
						backordered_items_list = false;
						return false;
					}
				}

			} );

			if ( backordered_items.length > 0 ) {

				if ( backordered_items_list ) {
					return wc_bundle_params.i18n_on_backorder_list.replace( '%s', wc_pb_format_list( backordered_items, { plain: true, plain_sep: true } ) );
				} else {
					return wc_bundle_params.i18n_on_backorder_status;
				}

			}

			return '';
		};

		/**
		 * Schedules an update of the bundle totals.
		 */
		this.update_bundle = function( triggered_by ) {

			clearTimeout( bundle.update_bundle_timer );

			bundle.update_bundle_timer = setTimeout( function() {
				bundle.update_bundle_task( triggered_by );
			}, 5 );
		};

		/**
		 * Updates the bundle totals.
		 */
		this.update_bundle_task = function( triggered_by ) {

			var has_insufficient_stock     = false,
				bundled_items_availability = false,
				validation_status          = false === bundle.is_initialized ? '' : bundle.api.get_bundle_validation_status(),
				unset_count                = 0,
				unset_titles               = [],
				invalid_addons_count       = 0,
				invalid_addons_titles      = [],
				total_items_qty            = 0,
				nyp_error_count            = 0,
				nyp_error_titles           = [];

			/*
			 * Validate bundle.
			 */

			// Reset validation messages.
			bundle.validation_messages = [];

			// Validate bundled items and prepare price data for totals calculation.
			$.each( bundle.bundled_items, function( index, bundled_item ) {

				var bundled_item_qty = bundled_item.is_selected() ? bundled_item.get_quantity() : 0;

				// Add item qty to total.
				total_items_qty += bundled_item_qty;

				// Check variable products.
				if ( bundled_item.is_variable_product_type() && bundled_item.get_variation_id() === '' ) {
					if ( bundled_item_qty > 0 ) {
						unset_count++;
						if ( bundled_item.is_visible() && bundled_item.get_title( true ) ) {
							unset_titles.push( bundled_item.get_title( true ) );
						}
					}
				}

				// Check addons validity.
				if ( bundled_item.addons_form && ! bundled_item.addons_form.validation.validate() ) {

					if ( bundled_item.has_pending_required_addons() ) {

						// Tip: If a Variable Product has required addons, it is already counted for. Do not re-count it.
						if ( bundled_item.is_visible() && bundled_item.get_title( true ) && $.inArray( bundled_item.get_title( true ), unset_titles ) === -1 ) {
							unset_count++;
							unset_titles.push( bundled_item.get_title( true ) );
						}
					} else {
						invalid_addons_count++;
						if ( bundled_item.is_visible() && bundled_item.get_title( true ) ) {
							invalid_addons_titles.push( bundled_item.get_title( true ) );
						}
					}
				}

				// Check NYP validity.
				if( bundled_item.is_nyp() && ! bundled_item.is_nyp_valid() ) {
					nyp_error_count++;
					if ( bundled_item.is_visible() && bundled_item.get_title( true ) ) {
						nyp_error_titles.push( bundled_item.get_title( true ) );
					}
				}

			} );

			if ( unset_count > 0 ) {

				var select_options_message = '';

				if ( unset_count === unset_titles.length && unset_count < 5 ) {
					select_options_message = wc_bundle_params.i18n_validation_issues_for.replace( '%c', wc_pb_format_list( unset_titles ) ).replace( '%e', wc_bundle_params.i18n_select_options );
				} else {
					select_options_message = wc_bundle_params.i18n_select_options;
				}

				bundle.add_validation_message( select_options_message );
			}

			if ( invalid_addons_count > 0 ) {

				var review_addons_message = '';

				if ( invalid_addons_count === invalid_addons_titles.length && invalid_addons_count < 5 ) {
					review_addons_message = wc_bundle_params.i18n_validation_issues_for.replace( '%c', wc_pb_format_list( invalid_addons_titles ) ).replace( '%e', wc_bundle_params.i18n_review_product_addons );
				} else {
					review_addons_message = wc_bundle_params.i18n_select_options;
				}

				bundle.add_validation_message( review_addons_message );
			}

			if ( nyp_error_count > 0 ) {

				var nyp_amount_message = '';

				if ( nyp_error_count === nyp_error_titles.length && nyp_error_count < 5 ) {
					nyp_amount_message = wc_bundle_params.i18n_validation_issues_for.replace( '%c', wc_pb_format_list( nyp_error_titles ) ).replace( '%e', wc_bundle_params.i18n_enter_valid_price_for );
				} else {
					nyp_amount_message = wc_bundle_params.i18n_enter_valid_price;
				}

				bundle.add_validation_message( nyp_amount_message );
			}

			if ( 0 === total_items_qty && 'no' === bundle.price_data.zero_items_allowed ) {
				bundle.add_validation_message( wc_bundle_params.i18n_zero_qty_error );
			}

			// Bundle not purchasable?
			if ( bundle.price_data.is_purchasable !== 'yes' ) {
				// Show 'i18n_unavailable_text' message.
				bundle.add_validation_message( wc_bundle_params.i18n_unavailable_text );
			} else {
				// Validate 3rd party constraints.
				bundle.$bundle_data.trigger( 'woocommerce-product-bundle-validate', [ bundle ] );
			}

			// Validation status changed?
			if ( validation_status !== bundle.api.get_bundle_validation_status() ) {
				bundle.$bundle_data.trigger( 'woocommerce-product-bundle-validation-status-changed', [ bundle ] );
			}

			/*
			 * Calculate totals.
			 */

			if ( bundle.price_data.is_purchasable === 'yes' ) {
				bundle.update_totals( triggered_by );
			}

			/*
			 * Stock handling.
			 */

			$.each( bundle.bundled_items, function( index, bundled_item ) {
				if ( bundled_item.has_insufficient_stock() ) {
					has_insufficient_stock = true;
				}
			} );


			/*
			 * Validation result handling.
			 */

			if ( bundle.passes_validation() ) {

				// Show add-to-cart button.
				if ( has_insufficient_stock ) {
					bundle.$bundle_button.find( 'button' ).addClass( 'disabled' );
				} else {
					bundle.$bundle_button.find( 'button' ).removeClass( 'disabled' );
				}

				// Hide validation messages.
				setTimeout( function() {
					bundle.$bundle_error.slideUp( 200 );
				}, 1 );

				bundle.$bundle_wrap.trigger( 'woocommerce-product-bundle-show' );

			} else {
				bundle.hide_bundle();
			}

			/**
			 * Override bundle availability.
			 */

			 bundled_items_availability = bundle.get_bundled_items_availability();

			if ( bundled_items_availability ) {
				bundle.$bundle_availability.html( bundled_items_availability );
				bundle.$bundle_availability.slideDown( 200 );
			} else {
				if ( bundle.$initial_stock_status ) {
					bundle.$bundle_availability.html( bundle.$initial_stock_status );
				} else {
					if ( bundle.is_composited() ) {
						bundle.$bundle_availability.find( 'p.stock' ).addClass( 'inactive' );
					}
					bundle.$bundle_availability.slideUp( 200 );
				}
			}

			// If composited, run 'component_selection_content_changed' action to update all models/views.
			if ( bundle.is_composited() ) {

				// CP > 4.0+.
				if ( typeof bundle.composite_data.component.component_selection_model.set_stock_status === 'function' ) {
					bundle.composite_data.component.component_selection_model.set_stock_status( has_insufficient_stock ? 'out-of-stock' : 'in-stock' );
				}

				bundle.composite_data.composite.actions.do_action( 'component_selection_content_changed', [ bundle.composite_data.component ] );
			}

			bundle.$bundle_data.trigger( 'woocommerce-product-bundle-updated', [ bundle ] );
		};

		/**
		 * Hide the add-to-cart button and show validation messages.
		 */
		this.hide_bundle = function( hide_message ) {

			var messages = $( '<ul/>' );

			if ( typeof( hide_message ) === 'undefined' ) {

				var hide_messages = bundle.get_validation_messages();

				if ( hide_messages.length > 0 ) {
					$.each( hide_messages, function( i, message ) {
						messages.append( $( '<li/>' ).html( message ) );
					} );
				} else {
					messages.append( $( '<li/>' ).html( wc_bundle_params.i18n_unavailable_text ) );
				}

			} else {
				messages.append( $( '<li/>' ).html( hide_message.toString() ) );
			}

			bundle.$bundle_error_content.html( messages.html() );
			setTimeout( function() {
				bundle.$bundle_error.slideDown( 200 );
			}, 1 );
			bundle.$bundle_button.find( 'button' ).addClass( 'disabled' );

			bundle.$bundle_wrap.trigger( 'woocommerce-product-bundle-hide' );
		};

		/**
		 * Updates the 'price_data' property with the latest values.
		 */
		this.update_price_data = function() {

			$.each( bundle.bundled_items, function( index, bundled_item ) {

				var cart            = bundled_item.$bundled_item_cart,
				    bundled_item_id = bundled_item.bundled_item_id,
				    item_quantity   = bundled_item.get_quantity();

				bundle.price_data.quantities[ bundled_item_id ] = 0;

				// Set quantity based on optional flag.
				if ( bundled_item.is_selected() && item_quantity > 0 ) {
					bundle.price_data.quantities[ bundled_item_id ] = parseInt( item_quantity, 10 );
				}

				// Store quantity for easy access by 3rd parties.
				cart.data( 'quantity', bundle.price_data.quantities[ bundled_item_id ] );

				// Check variable products.
				if ( bundled_item.is_variable_product_type() && bundled_item.get_variation_id() === '' ) {
					bundle.price_data.prices[ bundled_item_id ]                   = 0.0;
					bundle.price_data.regular_prices[ bundled_item_id ]           = 0.0;
					bundle.price_data.recurring_prices[ bundled_item_id ]         = 0.0;
					bundle.price_data.regular_recurring_prices[ bundled_item_id ] = 0.0;
					bundle.price_data.prices_tax[ bundled_item_id ]               = false;
				}

				bundle.price_data.prices[ bundled_item_id ]                   = Number( bundle.price_data.prices[ bundled_item_id ] );
				bundle.price_data.regular_prices[ bundled_item_id ]           = Number( bundle.price_data.regular_prices[ bundled_item_id ] );

				bundle.price_data.recurring_prices[ bundled_item_id ]         = Number( bundle.price_data.recurring_prices[ bundled_item_id ] );
				bundle.price_data.regular_recurring_prices[ bundled_item_id ] = Number( bundle.price_data.regular_recurring_prices[ bundled_item_id ] );

				// Calculate addons prices.
				if ( bundled_item.has_addons() ) {
					bundled_item.update_addons_prices();
				}

				bundle.price_data.addons_prices[ bundled_item_id ]            = Number( bundle.price_data.addons_prices[ bundled_item_id ] );
				bundle.price_data.regular_addons_prices[ bundled_item_id ]    = Number( bundle.price_data.regular_addons_prices[ bundled_item_id ] );
			} );
		};

		/**
		 * Calculates and updates bundle subtotals.
		 */
		this.update_totals = function( triggered_by ) {

			this.update_price_data();
			this.calculate_subtotals( triggered_by );

			if ( bundle.dirty_subtotals || false === bundle.is_initialized ) {
				bundle.dirty_subtotals = false;
				bundle.calculate_totals();
			}
		};

		/**
		 * Calculates totals by applying tax ratios to raw prices.
		 */
		this.get_taxed_totals = function( price, regular_price, tax_ratios, qty ) {

			qty = typeof( qty ) === 'undefined' ? 1 : qty;

			var tax_ratio_incl = tax_ratios && typeof( tax_ratios.incl ) !== 'undefined' ? Number( tax_ratios.incl ) : false,
				tax_ratio_excl = tax_ratios && typeof( tax_ratios.excl ) !== 'undefined' ? Number( tax_ratios.excl ) : false,
				totals         = {
					price:          qty * price,
					regular_price:  qty * regular_price,
					price_incl_tax: qty * price,
					price_excl_tax: qty * price
				};

			if ( tax_ratio_incl && tax_ratio_excl ) {

				totals.price_incl_tax = wc_pb_number_round( totals.price * tax_ratio_incl );
				totals.price_excl_tax = wc_pb_number_round( totals.price * tax_ratio_excl );

				if ( wc_bundle_params.tax_display_shop === 'incl' ) {
					totals.price         = totals.price_incl_tax;
					totals.regular_price = wc_pb_number_round( totals.regular_price * tax_ratio_incl );
				} else {
					totals.price         = totals.price_excl_tax;
					totals.regular_price = wc_pb_number_round( totals.regular_price * tax_ratio_excl );
				}
			}

			return totals;
		};

		/**
		 * Calculates bundled item subtotals (bundle totals) and updates the corresponding 'price_data' fields.
		 */
		this.calculate_subtotals = function( triggered_by, price_data_array, qty ) {

			var price_data = typeof( price_data_array ) === 'undefined' ? bundle.price_data : price_data_array;

			qty          = typeof( qty ) === 'undefined' ? 1 : parseInt( qty, 10 );
			triggered_by = typeof( triggered_by ) === 'undefined' ? false : triggered_by;

			// Base.
			if ( false === triggered_by ) {

				var base_price            = Number( price_data.base_price ),
					base_regular_price    = Number( price_data.base_regular_price ),
					base_price_tax_ratios = price_data.base_price_tax;

				price_data.base_price_totals = this.get_taxed_totals( base_price, base_regular_price, base_price_tax_ratios, qty );
			}

			// Items.
			$.each( bundle.bundled_items, function( index, bundled_item ) {

				if ( false !== triggered_by && triggered_by.bundled_item_id !== bundled_item.bundled_item_id ) {
					return true;
				}

				var product_qty             = bundled_item.is_sold_individually() && price_data.quantities[ bundled_item.bundled_item_id ] > 0 ? 1 : price_data.quantities[ bundled_item.bundled_item_id ] * qty,
					product_id              = bundled_item.get_product_type() === 'variable' ? bundled_item.get_variation_id() : bundled_item.get_product_id(),
					tax_ratios              = price_data.prices_tax[ bundled_item.bundled_item_id ],
					regular_price           = price_data.regular_prices[ bundled_item.bundled_item_id ] + price_data.regular_addons_prices[ bundled_item.bundled_item_id ],
					price                   = price_data.prices[ bundled_item.bundled_item_id ] + price_data.addons_prices[ bundled_item.bundled_item_id ],
					regular_recurring_price = price_data.regular_recurring_prices[ bundled_item.bundled_item_id ] + price_data.regular_addons_prices[ bundled_item.bundled_item_id ],
					recurring_price         = price_data.recurring_prices[ bundled_item.bundled_item_id ] + price_data.addons_prices[ bundled_item.bundled_item_id ],
					totals                  = {
						price:          0.0,
						regular_price:  0.0,
						price_incl_tax: 0.0,
						price_excl_tax: 0.0
					},
					recurring_totals        = {
						price:          0.0,
						regular_price:  0.0,
						price_incl_tax: 0.0,
						price_excl_tax: 0.0
					};

				if ( wc_bundle_params.calc_taxes === 'yes' ) {

					if ( product_id > 0 && product_qty > 0 ) {

						if ( price > 0 || regular_price > 0 ) {
							totals = bundle.get_taxed_totals( price, regular_price, tax_ratios, product_qty );
						}

						if ( recurring_price > 0 || regular_recurring_price > 0 ) {
							recurring_totals = bundle.get_taxed_totals( recurring_price, regular_recurring_price, tax_ratios, product_qty );
						}
					}

				} else {

					totals.price          = product_qty * price;
					totals.regular_price  = product_qty * regular_price;
					totals.price_incl_tax = product_qty * price;
					totals.price_excl_tax = product_qty * price;

					recurring_totals.price          = product_qty * recurring_price;
					recurring_totals.regular_price  = product_qty * regular_recurring_price;
					recurring_totals.price_incl_tax = product_qty * recurring_price;
					recurring_totals.price_excl_tax = product_qty * recurring_price;
				}

				// Filter bundled item totals.
				totals = bundle.filters.apply_filters( 'bundled_item_totals', [ totals, bundled_item, qty ] );

				// Filter bundled item totals.
				recurring_totals = bundle.filters.apply_filters( 'bundled_item_recurring_totals', [ recurring_totals, bundled_item, qty ] );

				var item_totals_changed = false;

				if ( bundle.totals_changed( price_data[ 'bundled_item_' + bundled_item.bundled_item_id + '_totals' ], totals ) ) {
					item_totals_changed    = true;
					bundle.dirty_subtotals = true;
					price_data[ 'bundled_item_' + bundled_item.bundled_item_id + '_totals' ] = totals;
				}

				if ( bundle.totals_changed( price_data[ 'bundled_item_' + bundled_item.bundled_item_id + '_recurring_totals' ], recurring_totals ) ) {
					item_totals_changed    = true;
					bundle.dirty_subtotals = true;
					price_data[ 'bundled_item_' + bundled_item.bundled_item_id + '_recurring_totals' ] = recurring_totals;
				}

				if ( item_totals_changed ) {
					bundle.$bundle_data.trigger( 'woocommerce-bundled-item-totals-changed', [ bundled_item ] );
				}

			} );

			if ( typeof( price_data_array ) !== 'undefined' ) {
				return price_data;
			}
		};

		/**
		 * Adds bundle subtotals and calculates bundle totals.
		 */
		this.calculate_totals = function( price_data_array ) {

			if ( typeof( price_data_array ) === 'undefined' ) {
				bundle.$bundle_data.trigger( 'woocommerce-product-bundle-calculate-totals', [ bundle ] );
			}

			var price_data     = typeof( price_data_array ) === 'undefined' ? bundle.price_data : price_data_array,
				totals_changed = false;

			// Non-recurring (sub)totals.
			var subtotals, totals = {
				price:          wc_pb_number_round( price_data.base_price_totals.price ),
				regular_price:  wc_pb_number_round( price_data.base_price_totals.regular_price ),
				price_incl_tax: wc_pb_number_round( price_data.base_price_totals.price_incl_tax ),
				price_excl_tax: wc_pb_number_round( price_data.base_price_totals.price_excl_tax )
			};

			$.each( bundle.bundled_items, function( index, bundled_item ) {

				if ( bundled_item.is_unavailable() ) {
					return true;
				}

				var item_totals = price_data[ 'bundled_item_' + bundled_item.bundled_item_id + '_totals' ];

				if ( typeof item_totals !== 'undefined' ) {

					totals.price          += wc_pb_number_round( item_totals.price );
					totals.regular_price  += wc_pb_number_round( item_totals.regular_price );
					totals.price_incl_tax += wc_pb_number_round( item_totals.price_incl_tax );
					totals.price_excl_tax += wc_pb_number_round( item_totals.price_excl_tax );
				}

			} );

			// Recurring (sub)totals, grouped by recurring id.
			var bundled_subs     = bundle.get_bundled_subscriptions(),
				recurring_totals = {};

			if ( bundled_subs ) {

				$.each( bundled_subs, function( index, bundled_sub ) {

					var bundled_item_id = bundled_sub.bundled_item_id;

					if ( price_data.quantities[ bundled_item_id ] === 0 ) {
						return true;
					}

					if ( bundled_sub.get_product_type() === 'variable-subscription' && bundled_sub.get_variation_id() === '' ) {
						return true;
					}

					var recurring_key         = price_data.recurring_keys[ bundled_item_id ],
						recurring_item_totals = price_data[ 'bundled_item_' + bundled_item_id + '_recurring_totals' ];

					if ( typeof( recurring_totals[ recurring_key ] ) === 'undefined' ) {

						recurring_totals[ recurring_key ] = {
							html:           price_data.recurring_html[ bundled_item_id ],
							price:          recurring_item_totals.price,
							regular_price:  recurring_item_totals.regular_price,
							price_incl_tax: recurring_item_totals.price_incl_tax,
							price_excl_tax: recurring_item_totals.price_excl_tax
						};

					} else {

						recurring_totals[ recurring_key ].price          += recurring_item_totals.price;
						recurring_totals[ recurring_key ].regular_price  += recurring_item_totals.regular_price;
						recurring_totals[ recurring_key ].price_incl_tax += recurring_item_totals.price_incl_tax;
						recurring_totals[ recurring_key ].price_excl_tax += recurring_item_totals.price_excl_tax;
					}

				} );
			}

			subtotals = totals;

			// Filter the totals.
			totals = bundle.filters.apply_filters( 'bundle_totals', [ totals, price_data, bundle ] );

			totals_changed = bundle.totals_changed( price_data.totals, totals );

			if ( ! totals_changed && bundled_subs ) {

				var recurring_totals_pre  = JSON.stringify( price_data.recurring_totals ),
					reccuring_totals_post = JSON.stringify( recurring_totals );

				if ( recurring_totals_pre !== reccuring_totals_post ) {
					totals_changed = true;
				}
			}

			// Render.
			if ( totals_changed || false === bundle.is_initialized ) {

				price_data.subtotals        = subtotals;
				price_data.totals           = totals;
				price_data.recurring_totals = recurring_totals;

				if ( typeof( price_data_array ) === 'undefined' ) {
					this.updated_totals();
				}
			}

			return price_data;
		};

		/**
		 * Schedules a UI bundle price string refresh.
		 */
		this.updated_totals = function() {

			clearTimeout( bundle.update_price_timer );

			bundle.update_price_timer = setTimeout( function() {
				bundle.updated_totals_task();
			}, 5 );
		};

		/**
		 * Build the non-recurring price html component.
		 */
		this.get_price_html = function( price_data_array ) {

			var price_data    = typeof( price_data_array ) === 'undefined' ? bundle.price_data : price_data_array,
				recalc_totals = false,
				qty           = bundle.is_composited() ? bundle.composite_data.component.get_selected_quantity() : 1,
				tag           = 'p';

			if ( bundle.has_addons() ) {

				price_data    = $.extend( true, {}, price_data );
				recalc_totals = true;

				var addons_raw_price         = price_data.addons_price ? price_data.addons_price : bundle.get_addons_raw_price(),
					addons_raw_regular_price = price_data.addons_regular_price ? price_data.addons_regular_price : addons_raw_price;

				// Recalculate price html with add-ons price embedded in base price.
				if ( addons_raw_price > 0 ) {
					price_data.base_price = Number( price_data.base_price ) + Number( addons_raw_price );
				}

				if ( addons_raw_regular_price > 0 ) {
					price_data.base_regular_price = Number( price_data.base_regular_price ) + Number( addons_raw_regular_price );
				}
			}

			if ( bundle.is_composited() ) {

				tag = 'span';

				if ( 'yes' === price_data.composited_totals_incl_qty ) {
					recalc_totals = true;
				}
			}

			if ( recalc_totals ) {
				// Recalculate price html with qty embedded.
				price_data = bundle.calculate_subtotals( false, price_data, qty );
				price_data = bundle.calculate_totals( price_data );
			}

			var	bundle_price_html = '',
				total_string      = 'yes' === price_data.show_total_string && wc_bundle_params.i18n_total ? '<span class="total">' + wc_bundle_params.i18n_total + '</span>' : '';

			// Non-recurring price html data.
			var formatted_price         = price_data.totals.price === 0.0 && price_data.show_free_string === 'yes' ? wc_bundle_params.i18n_free : wc_pb_price_format( price_data.totals.price ),
				formatted_regular_price = wc_pb_price_format( price_data.totals.regular_price ),
				formatted_suffix        = bundle.get_formatted_price_suffix( price_data );

			if ( price_data.totals.regular_price > price_data.totals.price ) {
				formatted_price = wc_bundle_params.i18n_strikeout_price_string.replace( '%f', formatted_regular_price ).replace( '%t', formatted_price );
			}

			bundle_price_html = wc_bundle_params.i18n_price_format.replace( '%t', total_string ).replace( '%p', formatted_price ).replace( '%s', formatted_suffix );

			var bundle_recurring_price_html = bundle.get_recurring_price_html();

			if ( ! bundle_recurring_price_html ) {

				bundle_price_html = '<' + tag + ' class="price">' + bundle_price_html + '</' + tag + '>';

			} else {

				var has_up_front_price_component = price_data.totals.regular_price > 0;

				if ( ! has_up_front_price_component ) {
					bundle_price_html = '<' + tag + ' class="price">' + price_data.price_string_recurring.replace( '%r', bundle_recurring_price_html ) + '</' + tag + '>';
				} else {
					bundle_price_html = '<' + tag + ' class="price">' + price_data.price_string_recurring_up_front.replace( '%s', bundle_price_html ).replace( '%r', bundle_recurring_price_html ) + '</' + tag + '>';
				}
			}

			return bundle_price_html;
		};

		/**
		 * Builds the recurring price html component for bundles that contain subscription products.
		 */
		this.get_recurring_price_html = function( price_data_array ) {

			var price_data = typeof( price_data_array ) === 'undefined' ? bundle.price_data : price_data_array;

			var bundle_recurring_price_html = '',
				bundled_subs                = bundle.get_bundled_subscriptions();

			if ( bundled_subs ) {

				var has_up_front_price_component = price_data.totals.regular_price > 0,
				    recurring_totals_data = [];

				for ( var recurring_total_key in price_data.recurring_totals ) {

					if ( ! price_data.recurring_totals.hasOwnProperty( recurring_total_key ) ) {
						continue;
					}

					recurring_totals_data.push( price_data.recurring_totals[ recurring_total_key ] );
				}

				$.each( recurring_totals_data, function( recurring_component_index, recurring_component_data ) {

					var formatted_recurring_price         = recurring_component_data.price == 0 ? wc_bundle_params.i18n_free : wc_pb_price_format( recurring_component_data.price ),
						formatted_regular_recurring_price = wc_pb_price_format( recurring_component_data.regular_price ),
						formatted_recurring_price_html    = '',
						formatted_suffix                  = bundle.get_formatted_price_suffix( price_data, {
							price_incl_tax: recurring_component_data.price_incl_tax,
							price_excl_tax: recurring_component_data.price_excl_tax
						} );

					if ( recurring_component_data.regular_price > recurring_component_data.price ) {
						formatted_recurring_price = wc_bundle_params.i18n_strikeout_price_string.replace( '%f', formatted_regular_recurring_price ).replace( '%t', formatted_recurring_price );
					}

					formatted_recurring_price_html = wc_bundle_params.i18n_price_format.replace( '%t', '' ).replace( '%p', formatted_recurring_price ).replace( '%s', formatted_suffix );
					formatted_recurring_price_html = '<span class="bundled_sub_price_html">' + recurring_component_data.html.replace( '%s', formatted_recurring_price_html ) + '</span>';

					if ( recurring_component_index === recurring_totals_data.length - 1 || ( recurring_component_index === 0 && ! has_up_front_price_component ) ) {
						if ( recurring_component_index > 0 || has_up_front_price_component ) {
							bundle_recurring_price_html = wc_bundle_params.i18n_recurring_price_join_last.replace( '%r', bundle_recurring_price_html ).replace( '%c', formatted_recurring_price_html );
						} else {
							bundle_recurring_price_html = formatted_recurring_price_html;
						}
					} else {
						bundle_recurring_price_html = wc_bundle_params.i18n_recurring_price_join.replace( '%r', bundle_recurring_price_html ).replace( '%c', formatted_recurring_price_html );
					}

				} );
			}

			return bundle_recurring_price_html;
		};

		/**
		 * Determines whether to show a bundle price html string.
		 */
		this.show_price_html = function() {

			if ( bundle.showing_price_html ) {
				return true;
			}

			var show_price = wc_pb_number_round( bundle.price_data.totals.price ) !== wc_pb_number_round( bundle.price_data.raw_bundle_price_min ) || bundle.price_data.raw_bundle_price_min !== bundle.price_data.raw_bundle_price_max;

			if ( bundle.get_bundled_subscriptions() ) {
				$.each( bundle.bundled_items, function( index, bundled_item ) {
					if ( bundle.price_data.recurring_prices[ bundled_item.bundled_item_id ] > 0 && bundle.price_data.quantities[ bundled_item.bundled_item_id ] > 0 ) {
						if ( bundled_item.is_subscription( 'variable' ) || bundled_item.is_optional() || bundled_item.$self.find( '.quantity input[type!=hidden]' ).length ) {
							show_price = true;
							return false;
						}
					}
				} );
			}

			if ( show_price ) {
				$.each( bundle.bundled_items, function( index, bundled_item ) {
					if ( bundled_item.is_unavailable() && bundled_item.is_required() ) {
						show_price = false;
						return false;
					}
				} );
			}

			if ( ! show_price ) {
				$.each( bundle.bundled_items, function( index, bundled_item ) {
					if ( 'yes' === bundle.price_data.has_variable_quantity[ bundled_item.bundled_item_id ] && bundle.price_data[ 'bundled_item_' + bundled_item.bundled_item_id + '_totals' ].price > 0 ) {
						show_price = true;
					}
				} );
			}

			if ( bundle.is_composited() ) {

				if ( ! show_price ) {
					if ( bundle.composite_data.composite.api.is_component_priced_individually( this.composite_data.component.step_id ) ) {
						show_price = true;
					}
				}

				if ( show_price ) {
					if ( false === this.composite_data.component.is_selected_product_price_visible() ) {
						show_price = false;
					} else if ( false === bundle.composite_data.composite.api.is_component_priced_individually( this.composite_data.component.step_id ) ) {
						show_price = false;
					}
				}
			}

			if ( show_price ) {
				bundle.showing_price_html = true;
			}

			return show_price;
		};

		/**
		 * Refreshes the bundle price string in the UI.
		 */
		this.updated_totals_task = function() {

			var show_price = bundle.show_price_html();

			if ( ( bundle.passes_validation() || 'no' === bundle.price_data.hide_total_on_validation_fail ) && show_price ) {

				var bundle_price_html = bundle.get_price_html();

				// Pass the price string through a filter.
				bundle_price_html = bundle.filters.apply_filters( 'bundle_total_price_html', [ bundle_price_html, bundle ] );

				bundle.$bundle_price.html( bundle_price_html );

				bundle.$bundle_price.slideDown( 200 );

			} else {
				bundle.$bundle_price.slideUp( 200 );
			}

			bundle.$bundle_data.trigger( 'woocommerce-product-bundle-updated-totals', [ bundle ] );
		};

		this.updated_addons_handler = function() {
			bundle.updated_totals_task();
		};

		this.has_addons = function() {
			return this.$addons_totals && this.$addons_totals.length > 0;
		};

		this.has_pct_addons = function( bundled_item ) {

			var is_bundled_item  = typeof( bundled_item ) !== 'undefined',
				obj              = is_bundled_item ? bundled_item : this,
				has              = false;

			if ( ! obj.has_addons ) {
				return has;
			}

			var addons = obj.$addons_totals.data( 'price_data' );

			$.each( addons, function( i, addon ) {
				if ( 'percentage_based' === addon.price_type ) {
					has = true;
					return false;
				}

			} );

			return has;
		};

		this.get_addons_raw_price = function( bundled_item, price_prop ) {

			var is_bundled_item  = typeof( bundled_item ) !== 'undefined',
				price_type       = 'regular' === price_prop ? 'regular': '',
				obj              = is_bundled_item ? bundled_item : this,
				qty              = is_bundled_item ? bundled_item.get_quantity() : 1,
				tax_ratios       = is_bundled_item ? bundle.price_data.prices_tax[ bundled_item.bundled_item_id ] : bundle.price_data.base_price_tax,
				addons_raw_price = 0.0;

			if ( ! obj.has_addons() ) {
				return 0;
			}

			if ( ! qty ) {
				return 0;
			}

			if ( is_bundled_item && bundled_item.is_variable_product_type() && bundled_item.get_variation_id() === '' ) {
				return 0;
			}

			if ( bundle.is_composited() ) {
				qty = bundle.composite_data.component.get_selected_quantity();
			}

			var addons = obj.$addons_totals.data( 'price_data' );

			$.each( addons, function( i, addon ) {

				if ( addon.is_custom_price ) {

					var addon_raw_price = 0.0,
						tax_ratio_incl  = tax_ratios && typeof( tax_ratios.incl ) !== 'undefined' ? Number( tax_ratios.incl ) : false,
						tax_ratio_excl  = tax_ratios && typeof( tax_ratios.excl ) !== 'undefined' ? Number( tax_ratios.excl ) : false;

					if ( 'incl' === wc_bundle_params.tax_display_shop && 'no' === wc_bundle_params.prices_include_tax ) {
						addon_raw_price = addon.cost_raw / ( tax_ratio_incl ? tax_ratio_incl : 1 );
					} else if ( 'excl' === wc_bundle_params.tax_display_shop && 'yes' === wc_bundle_params.prices_include_tax ) {
						addon_raw_price = addon.cost_raw / ( tax_ratio_excl ? tax_ratio_excl : 1 );
					} else {
						addon_raw_price = addon.cost_raw;
					}

					addons_raw_price += addon_raw_price / qty;

				} else {

					if ( 'quantity_based' === addon.price_type ) {
						addons_raw_price += addon.cost_raw_pu;
					} else if ( 'flat_fee' === addon.price_type ) {
						addons_raw_price += addon.cost_raw / qty;
					} else if ( 'percentage_based' === addon.price_type ) {

						var raw_price;

						if ( 'regular' === price_type ) {
							raw_price = is_bundled_item ? bundle.price_data.regular_prices[ bundled_item.bundled_item_id ] : bundle.price_data.base_regular_price;
						} else {
							raw_price = is_bundled_item ? bundle.price_data.prices[ bundled_item.bundled_item_id ] : bundle.price_data.base_price;
						}

						addons_raw_price += addon.cost_raw_pct * raw_price;
					}
				}

			} );

			return addons_raw_price;
		};

		/**
		 * Comparison of totals.
		 */
		this.totals_changed = function( totals_pre, totals_post ) {

			if ( typeof( totals_pre ) === 'undefined' || totals_pre.price !== totals_post.price || totals_pre.regular_price !== totals_post.regular_price || totals_pre.price_incl_tax !== totals_post.price_incl_tax || totals_pre.price_excl_tax !== totals_post.price_excl_tax ) {
				return true;
			}

			return false;
		};

		/**
		 * True if the bundle is part of a composite product.
		 */
		this.is_composited = function() {
			return false !== this.composite_data;
		};

		/**
		 * Replace totals in price suffix.
		 */
		this.get_formatted_price_suffix = function( price_data_array, totals ) {

			var price_data = typeof( price_data_array ) === 'undefined' ? bundle.price_data : price_data_array,
				suffix = '';

			totals = typeof( totals ) === 'undefined' ? price_data.totals : totals;

			if ( price_data.suffix_exists ) {

				suffix = price_data.suffix;

				if ( price_data.suffix_contains_price_incl ) {
					suffix = suffix.replace( '{price_including_tax}', wc_pb_price_format( totals.price_incl_tax ) );
				}

				if ( price_data.suffix_contains_price_excl ) {
					suffix = suffix.replace( '{price_excluding_tax}', wc_pb_price_format( totals.price_excl_tax ) );
				}
			}

			return suffix;
		};

		/**
		 * Find and return WC_PB_Bundled_Item objects that are subs.
		 */
		this.get_bundled_subscriptions = function( type ) {

			var bundled_subs = {},
				has_sub      = false;

			$.each( bundle.bundled_items, function( index, bundled_item ) {

				if ( bundled_item.is_subscription( type ) && bundled_item.is_priced_individually() ) {

					bundled_subs[ index ] = bundled_item;
					has_sub               = true;
				}

			} );

			if ( has_sub ) {
				return bundled_subs;
			}

			return false;
		};

		/**
		 * Adds a validation message.
		 */
		this.add_validation_message = function( message ) {

			this.validation_messages.push( message.toString() );
		};

		/**
		 * Validation messages getter.
		 */
		this.get_validation_messages = function() {

			return this.validation_messages;
		};

		/**
		 * Validation state getter.
		 */
		this.passes_validation = function() {

			if ( this.validation_messages.length > 0 ) {
				return false;
			}

			return true;
		};

		/**
		 * Check group mode feature support.
		 */
		this.group_mode_supports = function( $feature ) {
			return $.inArray( $feature, this.price_data.group_mode_features ) > -1;
		};
	}

	/**
	 * Bundled Item object.
	 */
	function WC_PB_Bundled_Item( bundle, $bundled_item, index ) {

		this.initialize = function() {

			this.$self                          = $bundled_item;
			this.$bundled_item_cart             = $bundled_item.find( '.cart' );
			this.$bundled_item_content          = $bundled_item.find( '.bundled_item_optional_content, .bundled_item_cart_content' );
			this.$bundled_item_image            = $bundled_item.find( '.bundled_product_images' );
			this.$bundled_item_title            = $bundled_item.find( '.bundled_product_title_inner' );
			this.$bundled_item_qty              = $bundled_item.find( 'input.bundled_qty' );

			this.$addons_totals                 = $bundled_item.find( '#product-addons-total' );
			this.$nyp                           = $bundled_item.find( '.nyp' );

			this.$attribute_select              = false;
			this.$attribute_select_config       = false;

			this.$reset_bundled_variations      = false;

			this.render_addons_totals_timer     = false;
			this.show_addons_totals             = false;
			this.addons_totals_html             = '';

			this.bundled_item_index             = index;
			this.bundled_item_id                = this.$bundled_item_cart.data( 'bundled_item_id' );
			this.bundled_item_title             = this.$bundled_item_cart.data( 'title' );
			this.bundled_item_title_raw         = this.bundled_item_title ? $( '<div/>' ).html( this.bundled_item_title ).text() : '';
			this.bundled_item_product_title     = this.$bundled_item_cart.data( 'product_title' );
			this.bundled_item_product_title_raw = this.bundled_item_title ? $( '<div/>' ).html( this.bundled_item_title ).text() : '';
			this.bundled_item_optional_suffix   = typeof( this.$bundled_item_cart.data( 'optional_suffix' ) ) === 'undefined' ? wc_bundle_params.i18n_optional : this.$bundled_item_cart.data( 'optional_suffix' );

			this.product_type                   = this.$bundled_item_cart.data( 'type' );
			this.product_id                     = typeof( bundle.price_data.product_ids[ this.bundled_item_id ] ) === 'undefined' ? '' : bundle.price_data.product_ids[ this.bundled_item_id ].toString();
			this.nyp                            = typeof( bundle.price_data.product_ids[ this.bundled_item_id ] ) === 'undefined' ? false : bundle.price_data.is_nyp[ this.bundled_item_id ] === 'yes';
			this.sold_individually              = typeof( bundle.price_data.product_ids[ this.bundled_item_id ] ) === 'undefined' ? false : bundle.price_data.is_sold_individually[ this.bundled_item_id ] === 'yes';
			this.priced_individually            = typeof( bundle.price_data.product_ids[ this.bundled_item_id ] ) === 'undefined' ? false : bundle.price_data.is_priced_individually[ this.bundled_item_id ] === 'yes';
			this.variation_id                   = '';

			this.has_wc_core_gallery_class      = this.$bundled_item_image.hasClass( 'images' );

			if ( typeof( this.bundled_item_id ) === 'undefined' ) {
				this.bundled_item_id = this.$bundled_item_cart.attr( 'data-bundled-item-id' );
			}

			this.initialize_addons();
		};

		this.initialize_addons = function() {

			if ( this.has_addons() ) {

				// Totals visible?
				if ( 1 == this.$addons_totals.data( 'show-sub-total' ) ) {
					// Ensure addons ajax is not triggered at all, as we calculate tax on the client side.
					this.$addons_totals.data( 'show-sub-total', 0 );
					this.show_addons_totals = true;

					this.$bundled_item_cart.trigger( 'woocommerce-product-addons-update' );
				}

			} else {
				this.$addons_totals = false;
			}
		};

		this.get_bundle = function() {
			return bundle;
		};

		this.get_title = function( strip_tags ) {
			strip_tags = typeof( strip_tags ) === 'undefined' ? false : strip_tags;
			return strip_tags ? this.bundled_item_title_raw : this.bundled_item_title;
		};

		this.get_product_title = function( strip_tags ) {
			strip_tags = typeof( strip_tags ) === 'undefined' ? false : strip_tags;
			return strip_tags ? this.bundled_item_product_title_raw : this.bundled_item_product_title;
		};

		this.get_optional_suffix = function() {
			return this.bundled_item_optional_suffix;
		};

		this.get_product_id = function() {
			return this.product_id;
		};

		this.get_variation_id = function() {
			return this.variation_id;
		};

		this.set_variation_id = function( value ) {
			this.variation_id = value.toString();
		};

		this.get_variation_data = function() {
			return this.$bundled_item_cart.data( 'product_variations' );
		};

		this.get_product_type = function() {
			return this.product_type;
		};

		this.is_variable_product_type = function() {
			return this.product_type === 'variable' || this.product_type === 'variable-subscription';
		};

		this.get_quantity = function() {
			var qty = this.$bundled_item_qty.val();
			return isNaN( qty ) ? 0 : parseInt( qty, 10 );
		};

		this.get_selected_quantity = function() {
			return bundle.price_data.quantities[ this.bundled_item_id ];
		};

		this.get_available_quantity = function() {
			return bundle.price_data.quantities_available[ this.bundled_item_id ];
		};

		this.is_in_stock = function() {
			return 'no' !== bundle.price_data.is_in_stock[ this.bundled_item_id ];
		};

		this.has_insufficient_stock = function() {

			if ( ! this.is_selected() || 0 === this.get_selected_quantity() || ( this.is_variable_product_type() && '' === this.get_variation_id() ) ) {
				return false;
			}

			if ( ! this.is_in_stock() || ( '' !== this.get_available_quantity() && this.get_selected_quantity() > this.get_available_quantity() ) ) {
				if ( ! this.backorders_allowed() ) {
					return true;
				}
			}

			return false;
		};

		this.is_backordered = function() {

			if ( ! this.is_selected() || 0 === this.get_selected_quantity() || ( this.is_variable_product_type() && '' === this.get_variation_id() ) ) {
				return false;
			}

			if ( '' === this.get_available_quantity() || this.get_selected_quantity() > this.get_available_quantity() ) {
				if ( this.backorders_allowed() && this.backorders_require_notification() ) {
					return true;
				}
			}

			return false;
		};

		this.backorders_allowed = function() {
			return 'yes' === bundle.price_data.backorders_allowed[ this.bundled_item_id ];
		};

		this.backorders_require_notification = function() {
			return 'yes' === bundle.price_data.backorders_require_notification[ this.bundled_item_id ];
		};

		this.is_optional = function() {
			return ( this.$bundled_item_cart.data( 'optional' ) === 'yes' || this.$bundled_item_cart.data( 'optional' ) === 1 );
		};

		this.is_unavailable = function() {
			return 'yes' === this.$bundled_item_cart.data( 'custom_data' ).is_unavailable;
		};

		this.is_required = function() {
			return ! this.is_optional() && 'no' !== this.$bundled_item_cart.data( 'custom_data' ).is_required;
		};

		this.is_visible = function() {
			return ( this.$bundled_item_cart.data( 'visible' ) === 'yes' || this.$bundled_item_cart.data( 'visible' ) === 1 );
		};

		this.is_selected = function() {

			var selected = true;

			if ( this.is_optional() ) {
				if ( this.$bundled_item_cart.data( 'optional_status' ) === false ) {
					selected = false;
				}
			}

			return selected;
		};

		this.set_selected = function( status ) {

			if ( this.is_optional() ) {
				this.$bundled_item_cart.data( 'optional_status', status );

				if( this.is_nyp() ) {
					this.$nyp.data( 'optional_status', status );
				}
			}
		};

		this.init_scripts = function() {

			// Init PhotoSwipe if present.
			if ( typeof PhotoSwipe !== 'undefined' && 'yes' === wc_bundle_params.photoswipe_enabled ) {
				this.init_photoswipe();
			}

			// Init dependencies.
			this.$self.find( '.bundled_product_optional_checkbox input' ).trigger( 'change' );
			this.$self.find( 'input.bundled_qty' ).trigger( 'change' );

			if ( this.is_variable_product_type() && ! this.$bundled_item_cart.hasClass( 'variations_form' ) ) {

				// Variations reset wrapper.
				this.$reset_bundled_variations = this.$bundled_item_cart.find( '.reset_bundled_variations' );

				if ( this.$reset_bundled_variations.length === 0 ) {
					this.$reset_bundled_variations = false;
				}

				// Initialize variations script.
				this.$bundled_item_cart.addClass( 'variations_form' ).wc_variation_form();

				// Set cached selects.
				this.$attribute_select        = this.$bundled_item_cart.find( '.variations .attribute_options select' );
				this.$attribute_select_config = this.$attribute_select.filter( function() {
					return false === $( this ).parent().hasClass( 'bundled_variation_attribute_options_wrapper' );
				} );

				// Trigger change event.
				if ( this.$attribute_select.length > 0 ) {
					this.$attribute_select.first().trigger( 'change' );
				}
			}

			this.$self.find( 'div' ).stop( true, true );
			this.update_selection_title();
		};

		this.init_photoswipe = function() {

			if ( $.fn.wc_product_gallery ) {
				this.$bundled_item_image.wc_product_gallery( { zoom_enabled: 'yes' === wc_bundle_params.zoom_enabled, flexslider_enabled: false } );
			} else {
				window.console.warn( 'Failed to initialize PhotoSwipe for bundled item images. Your theme declares PhotoSwipe support, but function \'$.fn.wc_product_gallery\' is undefined.' );
			}

			var $placeholder = this.$bundled_item_image.find( 'a.placeholder_image' );

			if ( $placeholder.length > 0 ) {
				$placeholder.on( 'click', function() {
					return false;
				} );
			}
		};

		this.update_selection_title = function( reset ) {

			if ( this.$bundled_item_title.length === 0 ) {
				return false;
			}

			var bundled_item_qty_val = parseInt( this.get_quantity(), 10 );

			if ( isNaN( bundled_item_qty_val ) ) {
				return false;
			}

			var bundled_item_qty_type = this.$bundled_item_qty.attr( 'type' ),
			    bundled_item_qty_min  = 'hidden' === bundled_item_qty_type ? bundled_item_qty_val : parseInt( this.$bundled_item_qty.attr( 'min' ), 10 ),
			    bundled_item_qty_max  = 'hidden' === bundled_item_qty_type ? bundled_item_qty_val : parseInt( this.$bundled_item_qty.attr( 'max' ), 10 );

			bundled_item_qty_min = isNaN( bundled_item_qty_min ) ? -9999 : bundled_item_qty_min;
			bundled_item_qty_max = isNaN( bundled_item_qty_max ) ? 9999 : bundled_item_qty_max;

			reset = typeof( reset ) === 'undefined' ? false : reset;

			if ( ! this.is_selected() ) {
				reset = true;
			}

			if ( reset ) {
				bundled_item_qty_val = 'hidden' === bundled_item_qty_type ? bundled_item_qty_val : parseInt( this.$bundled_item_qty.attr( 'min' ), 10 );
			}

			if ( 'tabular' === bundle.price_data.layout ) {
				bundled_item_qty_min = bundled_item_qty_max = '';
			}

			var is_selection_qty_visible = false;

			if ( reset ) {
				is_selection_qty_visible = bundled_item_qty_min === bundled_item_qty_max && bundled_item_qty_val > 1;
			} else if ( bundled_item_qty_val > 0 ) {
				is_selection_qty_visible = bundled_item_qty_min !== bundled_item_qty_max || bundled_item_qty_val > 1 || 'yes' === wc_bundle_params.force_selection_qty;
			}

			var selection_title           = this.bundled_item_title,
			    selection_qty_string      = is_selection_qty_visible ? wc_bundle_params.i18n_qty_string.replace( '%s', bundled_item_qty_val ) : '',
			    selection_optional_string = ( this.is_optional() && this.get_optional_suffix() !== '' ) ? wc_bundle_params.i18n_optional_string.replace( '%s', this.get_optional_suffix() ) : '',
			    selection_title_incl_qty  = wc_bundle_params.i18n_title_string.replace( '%t', selection_title ).replace( '%q', selection_qty_string ).replace( '%o', selection_optional_string );

			this.$bundled_item_title.html( selection_title_incl_qty );
		};

		this.reset_selection_title = function() {
			this.update_selection_title( true );
		};

		this.is_subscription = function( type ) {

			if ( 'simple' === type ) {
				return this.product_type === 'subscription';
			} else if ( 'variable' === type ) {
				return this.product_type === 'variable-subscription';
			} else {
				return this.product_type === 'subscription' || this.product_type === 'variable-subscription';
			}
		};

		this.has_addons = function() {
			return this.$addons_totals && this.$addons_totals.length > 0;
		};

		/**
		 * Checks if the bundled item has required addons that are pending configuration.
		 *
		 * @returns boolean
		 */
		this.has_pending_required_addons = function() {
			var valid       = false,
				addons_form = this.addons_form;

			if ( addons_form ) {
				var validation_state = addons_form.validation.getValidationState();

				$.each( validation_state, function() {
					if ( ! this.validation && 'required' === this.reason ) {
						valid = true;
						return false;
					}
				});
			}

			return valid;
		};

		this.update_addons_prices = function() {

			var addons_price         = bundle.get_addons_raw_price( this ),
				regular_addons_price = bundle.has_pct_addons( this ) ? bundle.get_addons_raw_price( this, 'regular' ) : addons_price;

			if ( bundle.price_data.addons_prices[ this.bundled_item_id ] !== addons_price || bundle.price_data.regular_addons_prices[ this.bundled_item_id ] !== regular_addons_price ) {
				bundle.price_data.addons_prices[ this.bundled_item_id ]         = addons_price;
				bundle.price_data.regular_addons_prices[ this.bundled_item_id ] = regular_addons_price;
			}
		};

		this.render_addons_totals = function() {

			var bundled_item = this;

			clearTimeout( this.render_addons_totals_timer );

			this.render_addons_totals_timer = setTimeout( function() {
				bundled_item.render_addons_totals_task();
			}, 10 );
		};

		this.render_addons_totals_task = function() {

			if ( ! this.has_addons ) {
				return;
			}

			var addons_price = bundle.price_data.addons_prices[ this.bundled_item_id ];

			if ( this.show_addons_totals ) {

				if ( ! this.is_variable_product_type() || this.get_variation_id() !== '' ) {

					var qty           = this.get_quantity(),
						tax_ratios    = bundle.price_data.prices_tax[ this.bundled_item_id ],
						addons_totals = bundle.get_taxed_totals( addons_price, addons_price, tax_ratios, qty );

					if ( addons_totals.price > 0 ) {

						var price              = Number( bundle.price_data.prices[ this.bundled_item_id ] ),
							total              = price + Number( addons_price ),
							totals             = bundle.get_taxed_totals( total, total, tax_ratios, qty ),
							price_html         = wc_pb_price_format( totals.price ),
							price_html_suffix  = bundle.get_formatted_price_suffix( bundle.price_data, totals ),
							addons_totals_html = '<span class="price">' + '<span class="subtotal">' + wc_bundle_params.i18n_subtotal + '</span>' + price_html + price_html_suffix + '</span>';

						// Save for later use.
						this.addons_totals_html = addons_totals_html;

						this.$addons_totals.html( addons_totals_html ).slideDown( 200 );

					} else {
						this.$addons_totals.slideUp( 200 );
					}

				} else {
					this.$addons_totals.slideUp( 200 );
				}
			}
		};

		this.has_single_variation = function() {

			if ( typeof this.get_variation_data() !== 'undefined' ) {
				return 1 === this.get_variation_data().length;
			}

			return false;
		};

		this.has_configurable_attributes = function() {
			return this.$attribute_select_config.length > 0;
		};

		this.reset_variation_image = function() {
			return bundle.filters.apply_filters( 'bundled_item_reset_variation_image', [ this.is_optional() && ! this.is_selected() && this.has_configurable_attributes(), this ] );
		};

		this.is_nyp = function() {
			return this.nyp;
		};

		this.is_nyp_valid = function() {

			var status = true;

			if ( $.fn.wc_nyp_get_script_object ) {

				var nyp_script = this.$nyp.wc_nyp_get_script_object();

				if ( nyp_script && false === nyp_script.isValid() ) {
					status = false;
				}
			}

			return status;

		};

		this.is_sold_individually = function() {
			return this.sold_individually;
		};

		this.is_priced_individually = function() {
			return this.priced_individually;
		};

		this.maybe_add_wc_core_gallery_class = function() {
			if ( ! this.has_wc_core_gallery_class ) {
				this.$bundled_item_image.addClass( 'images' );
			}
		};

		this.maybe_remove_wc_core_gallery_class = function() {
			if ( ! this.has_wc_core_gallery_class ) {
				this.$bundled_item_image.removeClass( 'images' );
			}
		};

		this.initialize();
	}

	/**
	 * Filters API.
	 */
	function WC_PB_Filters_Manager() {

		/*
		 *--------------------------*
		 *                          *
		 *   Filters Reference      *
		 *                          *
		 *--------------------------*
		 *
		 *
		 * Filter 'bundle_subtotals_data':
		 *
		 * Filters the bundle price data array after calculating subtotals.
		 *
		 * @param  array   price_data   Price data array.
		 * @param  object  bundle       Bundle object.
		 * @return array
		 *
		 * @hooked void
		 *
		 *
		 *
		 * Filter 'bundle_total_price_html':
		 *
		 * Filters the price html total.
		 *
		 * @param  string  totals   Markup to display.
		 * @param  object  bundle       Bundle object.
		 * @return string
		 *
		 * @hooked void
		 */

		var manager   = this,
			filters   = {},
			functions = {

				add_filter: function( hook, callback, priority, context ) {

					var hookObject = {
						callback : callback,
						priority : priority,
						context : context
					};

					var hooks = filters[ hook ];
					if ( hooks ) {
						hooks.push( hookObject );
						hooks = this.sort_filters( hooks );
					} else {
						hooks = [ hookObject ];
					}

					filters[ hook ] = hooks;
				},

				remove_filter: function( hook, callback, context ) {

					var handlers, handler, i;

					if ( ! filters[ hook ] ) {
						return;
					}
					if ( ! callback ) {
						filters[ hook ] = [];
					} else {
						handlers = filters[ hook ];
						if ( ! context ) {
							for ( i = handlers.length; i--; ) {
								if ( handlers[ i ].callback === callback ) {
									handlers.splice( i, 1 );
								}
							}
						} else {
							for ( i = handlers.length; i--; ) {
								handler = handlers[ i ];
								if ( handler.callback === callback && handler.context === context) {
									handlers.splice( i, 1 );
								}
							}
						}
					}
				},

				sort_filters: function( hooks ) {

					var tmpHook, j, prevHook;
					for ( var i = 1, len = hooks.length; i < len; i++ ) {
						tmpHook = hooks[ i ];
						j = i;
						while( ( prevHook = hooks[ j - 1 ] ) &&  prevHook.priority > tmpHook.priority ) {
							hooks[ j ] = hooks[ j - 1 ];
							--j;
						}
						hooks[ j ] = tmpHook;
					}

					return hooks;
				},

				apply_filters: function( hook, args ) {

					var handlers = filters[ hook ], i, len;

					if ( ! handlers ) {
						return args[ 0 ];
					}

					len = handlers.length;

					for ( i = 0; i < len; i++ ) {
						args[ 0 ] = handlers[ i ].callback.apply( handlers[ i ].context, args );
					}

					return args[ 0 ];
				}

			};

		/**
		 * Adds a filter.
		 */
		this.add_filter = function( filter, callback, priority, context ) {

			if ( typeof filter === 'string' && typeof callback === 'function' ) {
				priority = parseInt( ( priority || 10 ), 10 );
				functions.add_filter( filter, callback, priority, context );
			}

			return manager;
		};

		/**
		 * Applies all filter callbacks.
		 */
		this.apply_filters = function( filter, args ) {

			if ( typeof filter === 'string' ) {
				return functions.apply_filters( filter, args );
			}
		};

		/**
		 * Removes the specified filter callback.
		 */
		this.remove_filter = function( filter, callback ) {

			if ( typeof filter === 'string' ) {
				functions.remove_filter( filter, callback );
			}

			return manager;
		};

	}

	/*-----------------------------------------------------------------*/
	/*  Initialization.                                                */
	/*-----------------------------------------------------------------*/

	jQuery( function( $ ) {

		/**
		 * QuickView compatibility.
		 */
		$( 'body' ).on( 'quick-view-displayed', function() {
			$( '.quick-view .bundle_form .bundle_data' ).each( function() {

				var $bundle_data    = $( this ),
					$composite_form = $bundle_data.closest( '.composite_form' );

				// If part of a composite, let the composite initialize it.
				if ( $composite_form.length === 0 ) {
					$bundle_data.wc_pb_bundle_form();
				}

			} );
		} );

		/**
		 * Script initialization on '.bundle_data' jQuery objects.
		 */
		$.fn.wc_pb_bundle_form = function() {

			if ( ! $( this ).hasClass( 'bundle_data' ) ) {
				return true;
			}

			var $bundle_data = $( this ),
				container_id = $bundle_data.data( 'bundle_id' );

			if ( typeof( container_id ) === 'undefined' ) {
				container_id = $bundle_data.attr( 'data-bundle-id' );

				if ( container_id ) {
					$bundle_data.data( 'bundle_id', container_id );
				} else {
					return false;
				}
			}

			var $bundle_form     = $bundle_data.closest( '.bundle_form' ),
				$composite_form  = $bundle_form.closest( '.composite_form' ),
				composite_data   = false,
				bundle_script_id = container_id;

			// If part of a composite product, get a unique id for the script object and prepare variables for integration code.
			if ( $composite_form.length > 0 ) {

				var $component   = $bundle_form.closest( '.component' ),
					component_id = $component.data( 'item_id' );

				if ( component_id > 0 && $.fn.wc_get_composite_script ) {

					var composite_script = $composite_form.wc_get_composite_script();

					if ( false !== composite_script ) {

						var component = composite_script.api.get_step( component_id );

						if ( false !== component ) {
							composite_data = {
								composite: composite_script,
								component: component
							};
							bundle_script_id = component_id;
						}
					}
				}
			}

			if ( typeof( wc_pb_bundle_scripts[ bundle_script_id ] ) !== 'undefined' ) {
				wc_pb_bundle_scripts[ bundle_script_id ].shutdown();
			}

			wc_pb_bundle_scripts[ bundle_script_id ] = new WC_PB_Bundle( { $bundle_form: $bundle_form, $bundle_data: $bundle_data, bundle_id: container_id, composite_data: composite_data } );

			$bundle_form.data( 'script_id', bundle_script_id );

			wc_pb_bundle_scripts[ bundle_script_id ].initialize();
		};

		/*
		 * Initialize form script.
		 */
		$( '.bundle_form .bundle_data' ).each( function() {

			var $bundle_data    = $( this ),
				$composite_form = $bundle_data.closest( '.composite_form' );

			// If part of a composite, let the composite initialize it.
			if ( $composite_form.length === 0 ) {
				$bundle_data.wc_pb_bundle_form();
			}

		} );

	} );

} ) ( jQuery );
