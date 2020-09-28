/* global wc_mnm_params */

/*-----------------------------------------------------------------*/
/*  Global script variable.                                        */
/*-----------------------------------------------------------------*/

var wc_mnm_scripts = {};

/*-----------------------------------------------------------------*/
/*  Global utility variables + functions.                          */
/*-----------------------------------------------------------------*/

/**
 * Converts numbers to formatted price strings. Respects WC price format settings.
 */
function wc_mnm_price_format( price, plain ) {
	plain = typeof( plain ) === 'undefined' ? false : plain;
	return wc_mnm_woocommerce_number_format( wc_mnm_number_format( price ), plain );
}

/**
 * Formats price strings according to WC settings.
 */
function wc_mnm_woocommerce_number_format( price, plain ) {

	var remove 		= wc_mnm_params.currency_format_decimal_sep;
	var position 	= wc_mnm_params.currency_position;
	var symbol 		= wc_mnm_params.currency_symbol;
	var trim_zeros 	= wc_mnm_params.currency_format_trim_zeros;
	var decimals 	= wc_mnm_params.currency_format_num_decimals;

	plain = typeof( plain ) === 'undefined' ? false : plain;

	if ( trim_zeros === 'yes' && decimals > 0 ) {
		for (var i = 0; i < decimals; i++) { remove = remove + '0'; }
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
function wc_mnm_number_format( number ) {

	var decimals 		= wc_mnm_params.currency_format_num_decimals;
	var decimal_sep 	= wc_mnm_params.currency_format_decimal_sep;
	var thousands_sep 	= wc_mnm_params.currency_format_thousand_sep;

	var n = number, c = isNaN(decimals = Math.abs(decimals)) ? 2 : decimals;
	var d = decimal_sep === undefined ? ',' : decimal_sep;
	var t = thousands_sep === undefined ? '.' : thousands_sep, s = n < 0 ? '-' : '';
	var i = parseInt(n = Math.abs(+n || 0).toFixed(c), 10) + '', j = (j = i.length) > 3 ? j % 3 : 0;

	return s + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : '');
}

/**
 * Rounds price values according to WC settings.
 */
function wc_mnm_number_round( number ) {

	var precision         = wc_mnm_params.currency_format_precision_decimals,
		factor            = Math.pow( 10, precision ),
		tempNumber        = number * factor,
		roundedTempNumber = Math.round( tempNumber );

	return roundedTempNumber / factor;
}

/**
 * Container script object getter.
 */
jQuery.fn.wc_get_mnm_script = function() {

	var $mnm_form = jQuery( this );

	if ( ! $mnm_form.hasClass( 'mnm_form' ) ) {
		return false;
	}

	var script_id = $mnm_form.data( 'script_id' );

	if ( typeof( wc_mnm_scripts[ script_id ] ) !== 'undefined' ) {
		return wc_mnm_scripts[ script_id ];
	}

	return false;
};

/*-----------------------------------------------------------------*/
/*  Encapsulation.                                                 */
/*-----------------------------------------------------------------*/

( function( $ ) {

	/**
	 * Main container object.
	 */
	function WC_MNM_Container( data ) {

		var container             = this;

		this.container_id         = data.container_id;

		this.$mnm_form            = data.$mnm_form;
		this.$mnm_data            = data.$mnm_data;
		this.$mnm_cart            = data.$mnm_data; // For backcompat.
		
		this.$mnm_items           =  data.$mnm_form.find( '.mnm_item' );
		this.$mnm_price           =  data.$mnm_form.find( '.mnm_price' );
		this.$mnm_reset           =  data.$mnm_form.find( '.mnm_reset' );

		this.$mnm_button          = data.$mnm_data.find( '.mnm_add_to_cart_button' );
		this.$mnm_message         = data.$mnm_data.find( '.mnm_message' );
		this.$mnm_message_content = this.$mnm_message.find( '.mnm_message_content' );
		this.$mnm_quantity        = this.$mnm_data.find( '.mnm_button_wrap input.qty' );

		this.$addons_totals       = this.$mnm_data.find( '#product-addons-total' );
		this.show_addons_totals   = false;

		this.child_items          = {};

		this.price_data           = data.$mnm_data.data( 'price_data' );

		this.container_size       = 0;
		this.min_container_size   = data.$mnm_data.data( 'min_container_size' );
		this.max_container_size   = data.$mnm_data.data( 'max_container_size' );
		this.container_config     = {};

		this.update_mnm_timer     = false;
		this.update_price_timer   = false;

		this.validation_messages  = [];
		this.status_messages      = [];

		this.is_initialized       = false;

		this.api                  = {

			/**
			 * Get container quantities config.
			 *
			 * return array
			 */
			get_container_config: function() {
				return ( typeof( container.container_config ) === 'object' ) ? container.container_config : {};
			},

			/**
			 * Get container total price(s).
			 *
			 * return obj|int
			 */
			get_container_price: function( type ) {
				var totals = container.price_data.totals;

				if( type !== 'undefined' && totals.hasOwnProperty( type ) ) {
					return Number( totals[ type ] );
				} else {
					return totals;
				}
			},

			/**
			 * Get container size.
			 *
			 * return int
			 */
			get_container_size: function() {
				return parseInt( container.container_size, 10 );
			},

			/**
			 * Get min container size.
			 *
			 * return mixed int|false
			 */
			get_min_container_size: function() {
				if ( typeof( container.min_container_size ) !== 'undefined' && container.min_container_size !== '' ) {
					return parseInt( container.min_container_size, 10 );
				}

				return false;
			},

			/**
			 * Get max container size.
			 * return mixed int|false
			 */
			get_max_container_size: function() {
				if ( typeof( container.max_container_size ) !== 'undefined' && container.max_container_size !== '') {
					return parseInt( container.max_container_size, 10 );
				}

				return false;
			},

			/**
			 * Get the current status messages for the container.
			 *
			 * @return array
			 */
			get_status_messages: function() {
				return container.get_messages( 'status' );
			},

			/**
			 * Get the current validation status of the container.
			 *
			 * @return string ('pass' | 'fail')
			 */
			get_validation_status: function() {
				return container.passes_validation() ? 'pass' : 'fail';
			},

			/**
			 * Get the current validation messages for the container.
			 *
			 * @return array
			 */
			get_validation_messages: function() {
				return container.get_messages( 'error' );
			},

			/**
			 * Is priced per-product?
			 */
			is_priced_per_product: function() {
				return container.price_data.per_product_pricing === 'yes';
			},

			/**
			 * Is purchasable?
			 */
			is_purchasable: function() {
				return container.price_data.is_purchasable === 'yes';
			},

			/**
			 * Set container size.
			 *
			 * return int
			 */
			set_container_size: function( size ) {
				container.container_size = parseInt( size, 10 );
			}
		};

		/**
		 * Add validation/status message.
		 */

		this.add_message = function( message, type ) {

			if ( type === 'error' ) {
				this.validation_messages.push( message.toString() );
			} else {
				this.status_messages.push( message.toString() );
			}

		};

		/**
		 * Attach child-item-level event handlers.
		 */
		this.bind_child_item_event_handlers = function( child_item ) {

			child_item.$self

				/**
				 * Update totals upon changing quantities.
				 */
				.on( 'input change', ':input', function( e ) {

					// Restrict to min/max limits.
					var $input = $( this ),
						min    = parseFloat( $input.attr( 'min' ) ),
						max    = parseFloat( $input.attr( 'max' ) );

					// Max can't be higher than the container size.
					if ( container.api.get_max_container_size() > 0 ) {
						max = Math.min( max, parseFloat( container.api.get_max_container_size() ) );
					}

					if ( e.type === 'change' && min >= 0 && ( parseFloat( $input.val() ) < min || isNaN( parseFloat( $input.val() ) ) ) ) {
						$input.val( min );
					}

					if ( e.type === 'change' && max > 0 && parseFloat( $input.val() ) > max ) {
						$input.val( max );
					}

					container.update_container( child_item );
				} );

		};

		/**
		 * Container-Level Event Handlers.
		 */

		this.bind_event_handlers = function() {

			if ( container.has_addons() ) {
				container.$mnm_data.on( 'updated_addons', container.updated_addons_handler );
			}

			container.$mnm_reset

				// Upon clicking reset link.
				.on( 'click', function( e ) {
					container.reset( e );
				} );

		};


		/**
		 * Calculates child item subtotals (container totals) and updates the corresponding 'price_data' fields.
		 */
		this.calculate_subtotals = function( triggered_by, price_data_array, qty ) {

			var price_data = typeof( price_data_array ) === 'undefined' ? container.price_data : price_data_array;

			qty          = typeof( qty ) === 'undefined' ? 1 : parseInt( qty, 10 );
			triggered_by = typeof( triggered_by ) === 'undefined' ? false : triggered_by;

			// Base.
			if ( false === triggered_by ) {

				var base_price            = Number( price_data.base_price ),
					base_regular_price    = Number( price_data.base_regular_price ),
					base_price_tax_ratios = price_data.base_price_tax;

				price_data.base_price_totals = price_data.base_price_subtotals = this.get_taxed_totals( base_price, base_regular_price, base_price_tax_ratios, qty );
			}

			// Items.
			$.each( container.child_items, function( index, child_item ) {

				if ( false !== triggered_by && triggered_by.mnm_item_id !== child_item.mnm_item_id ) {
					return true;
				}

				var mnm_item_id 		    = child_item.get_item_id(),
					product_qty             = child_item.is_sold_individually() && container.container_config[ mnm_item_id ] > 0 ? 1 : container.container_config[ mnm_item_id ] * qty,
					tax_ratios              = price_data.prices_tax[ mnm_item_id ],
					regular_price           = price_data.regular_prices[ mnm_item_id ],
					price                   = price_data.prices[ mnm_item_id ],
	
					totals                  = {
						price:          0.0,
						regular_price:  0.0,
						price_incl_tax: 0.0,
						price_excl_tax: 0.0
				};

				if ( wc_mnm_params.calc_taxes === 'yes' ) {

					if ( mnm_item_id > 0 && product_qty > 0 ) {

						if ( price > 0 || regular_price > 0 ) {
							totals = container.get_taxed_totals( price, regular_price, tax_ratios, product_qty );
						}

					}

				} else {

					totals.price                    = product_qty * price;
					totals.regular_price            = product_qty * regular_price;
					totals.price_incl_tax           = product_qty * price;
					totals.price_excl_tax           = product_qty * price;

				}

				if ( container.totals_changed( price_data.child_item_subtotals[ mnm_item_id ], totals ) ) {
					container.dirty_subtotals = true;
					price_data.child_item_subtotals[ mnm_item_id ] = totals;
					price_data.child_item_totals[ mnm_item_id ]    = totals;
				}

			} );

			return price_data;
			
		};

		/**
		 * Adds container subtotals and calculates container totals.
		 */
		this.calculate_totals = function( price_data_array ) {

			var price_data     = typeof( price_data_array ) === 'undefined' ? container.price_data : price_data_array,
				totals_changed = false;

			// Non-recurring (sub)totals.
			var totals = {
				price:          price_data.base_price_subtotals.price,
				regular_price:  price_data.base_price_subtotals.regular_price,
				price_incl_tax: price_data.base_price_subtotals.price_incl_tax,
				price_excl_tax: price_data.base_price_subtotals.price_excl_tax
			},
				subtotals = {
					price:          price_data.base_price_totals.price,
					regular_price:  price_data.base_price_totals.regular_price,
					price_incl_tax: price_data.base_price_totals.price_incl_tax,
					price_excl_tax: price_data.base_price_totals.price_excl_tax
			};

			$.each( container.child_items, function( index, child_item ) {

				var mnm_item_id = child_item.get_item_id(),
					item_totals    = price_data.child_item_totals[ mnm_item_id ],
					item_subtotals = price_data.child_item_subtotals[ mnm_item_id ];

				if ( typeof item_totals !== 'undefined' ) {

					totals.price          += item_totals.price;
					totals.regular_price  += item_totals.regular_price;
					totals.price_incl_tax += item_totals.price_incl_tax;
					totals.price_excl_tax += item_totals.price_excl_tax;
				}

				if ( typeof item_subtotals !== 'undefined' ) {

					subtotals.price          += item_subtotals.price;
					subtotals.regular_price  += item_subtotals.regular_price;
					subtotals.price_incl_tax += item_subtotals.price_incl_tax;
					subtotals.price_excl_tax += item_subtotals.price_excl_tax;
				}

			} );

			totals_changed = container.totals_changed( price_data.totals, totals ) || container.totals_changed( price_data.subtotals, subtotals );

			// Render.
			if ( totals_changed || false === container.is_initialized ) {

				price_data.subtotals           = subtotals;
				price_data.totals              = totals;

				if ( typeof( price_data_array ) === 'undefined' ) {
					this.updated_totals();
				}
			}

			return price_data;
		};

		/**
		 * Replace totals in price suffix.
		 */
		this.get_formatted_price_suffix = function( price_data_array, totals ) {

			var price_data = typeof( price_data_array ) === 'undefined' ? container.price_data : price_data_array,
				suffix = '';

			totals = typeof( totals ) === 'undefined' ? price_data.totals : totals;

			if ( price_data.suffix_exists ) {

				suffix = price_data.suffix;

				if ( price_data.suffix_contains_price_incl ) {
					suffix = suffix.replace( '{price_including_tax}', wc_mnm_price_format( totals.price_incl_tax ) );
				}

				if ( price_data.suffix_contains_price_excl ) {
					suffix = suffix.replace( '{price_excluding_tax}', wc_mnm_price_format( totals.price_excl_tax ) );
				}
			}

			return suffix;
		};

		/**
		 * Get validation/status messages.
		 */

		this.get_messages = function( type ) {

			var messages = [];

			if ( type === 'all' ) {
				messages = $.merge( this.status_messages, this.validation_messages );
			} else if ( type === 'error' ) {
				messages = this.validation_messages;
			} else {
				messages = this.status_messages;
			}

			return messages;

		};

		/**
		 * Build the non-recurring price html component.
		 */
		this.get_price_html = function( price_data_array ) {

			var price_data = typeof( price_data_array ) === 'undefined' ? container.price_data : price_data_array,
				tag        = 'p';

			var	container_price_html = '',
				show_total_string = ( wc_mnm_number_round( price_data.totals.price ) !== wc_mnm_number_round( price_data.raw_container_price_min ) || price_data.raw_container_price_min !== price_data.raw_container_price_max ),
				total_string      = show_total_string ? '<span class="total">' + wc_mnm_params.i18n_total + '</span>' : '';

			// Non-recurring price html data.
			var formatted_price         = price_data.totals.price === 0.0 && price_data.show_free_string === 'yes' ? wc_mnm_params.i18n_free : wc_mnm_price_format( price_data.totals.price ),
				formatted_regular_price = wc_mnm_price_format( price_data.totals.price === price_data.subtotals.price ? price_data.totals.regular_price : price_data.subtotals.price ),
				formatted_suffix        = container.get_formatted_price_suffix( price_data );

			if ( price_data.totals.regular_price > price_data.totals.price ) {
				formatted_price = wc_mnm_params.i18n_strikeout_price_string.replace( '%f', formatted_regular_price ).replace( '%t', formatted_price );
			}

			container_price_html = wc_mnm_params.i18n_price_format.replace( '%t', total_string ).replace( '%p', formatted_price ).replace( '%s', formatted_suffix );
			container_price_html = '<' + tag + ' class="price">' + price_data.price_string.replace( '%s', container_price_html ) + '</' + tag + '>';

			return container_price_html;
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

				totals.price_incl_tax = wc_mnm_number_round( totals.price * tax_ratio_incl );
				totals.price_excl_tax = wc_mnm_number_round( totals.price * tax_ratio_excl );

				if ( wc_mnm_params.tax_display_shop === 'incl' ) {
					totals.price         = totals.price_incl_tax;
					totals.regular_price = wc_mnm_number_round( totals.regular_price * tax_ratio_incl );
				} else {
					totals.price         = totals.price_excl_tax;
					totals.regular_price = wc_mnm_number_round( totals.regular_price * tax_ratio_excl );
				}
			}

			return totals;
		};

		/**
		 * Returns the quantity of this container.
		 */
		this.get_quantity = function() {
			var qty = container.$mnm_quantity.length > 0 ? container.$mnm_quantity.val() : 1;
			return isNaN( qty ) ? 1 : parseInt( qty, 10 );
		};

		/**
		 * Does this container have addons support?
		 */
		this.has_addons = function() {
			return this.$addons_totals && this.$addons_totals.length > 0;
		};

		/**
		 * True if there are status messages to display.
		 */
		this.has_status_messages = function() {

			if ( this.status_messages.length > 0 ) {
				return true;
			}
			return false;
		};

		/**
		 * Object initialization.
		 */
		this.initialize = function() {

			/**
			 * Initial states and loading.
			 */

			// Insert notice container if none exists.
			if ( this.$mnm_message.length === 0 ) {
				if ( this.$mnm_message.length > 0 ) {
					this.$mnm_message.remove();
				}
				this.$mnm_price.after( '<div class="mnm_message"><div class="woocommerce-info"><ul class="msg mnm_message_content"></ul></div></div>' );
				this.$mnm_message         = this.$mnm_data.find( '.mnm_message' );
				this.$mnm_message_content = this.$mnm_message.find( '.mnm_message_content' );
			}

			// Price suffix data.
			this.price_data.suffix_exists              = wc_mnm_params.price_display_suffix !== '';
			this.price_data.suffix                     = wc_mnm_params.price_display_suffix !== '' ? ' <small class="woocommerce-price-suffix">' + wc_mnm_params.price_display_suffix + '</small>' : '';
			this.price_data.suffix_contains_price_incl = wc_mnm_params.price_display_suffix.indexOf( '{price_including_tax}' ) > -1;
			this.price_data.suffix_contains_price_excl = wc_mnm_params.price_display_suffix.indexOf( '{price_excluding_tax}' ) > -1;

			// Totals visible in Addons 3.0.x?
			if ( 1 === this.$addons_totals.data( 'show-sub-total' ) && wc_mnm_params.addons_three_support === 'yes' ) {
				// Ensure addons ajax is not triggered at all, as we calculate tax on the client side.
				this.$addons_totals.data( 'show-sub-total', 0 );
				this.show_addons_totals = true;
			}

			/**
			 * Bind event handlers.
			 */

			this.bind_event_handlers();

			/**
			 * Init Child Items.
			 */

			this.init_child_items();

			/**
			 * Initialize.
			 */

			this.$mnm_form.trigger( 'wc-mnm-initializing', [ this ] );

			// Update config and prices.
			this.update_container_task();

			this.is_initialized           = true;

			// Let 3rd party know that we are ready to rock.
			this.$mnm_form.trigger( 'wc-mnm-initialized', [ this ] );

		};

		/**
		 * Initialize child item objects.
		 */
		this.init_child_items = function() {

			container.$mnm_items.each( function( index ) {

				container.child_items[ index ] = new WC_MNM_Child_Item( container, $( this ), index );

				container.bind_child_item_event_handlers( container.child_items[ index ] );

			} );
		};

		/**
		 * False if there are validation messages to display.
		 */
		this.passes_validation = function() {

			if ( this.validation_messages.length > 0 ) {
				return false;
			}

			return true;
		};

		/**
		 * Trigger addons update to refresh addons totals.
		 */
		/**
		 * Render add-ons totals here to prevent Add-Ons from firing an Ajax request and showing incorrect totals.
		 */
		this.update_addons_totals = function( triggered_by ) {

			// When container is updated, tell addons the price.
			if( typeof( triggered_by ) === 'undefined' ) {
				container.$addons_totals.data( 'price', container.api.get_container_price( 'price' ) );
				container.$mnm_data.trigger( 'woocommerce-product-addons-update' );
				return false;
			}

			// Triggered by addons.
			if ( container.show_addons_totals ) {

				var price_data            = $.extend( true, {}, container.price_data ),
					qty                   = container.get_quantity(),
					tax_ratios            = price_data.base_price_tax,
					addons 	  			  = container.$addons_totals.data( 'price_data' ),
					addons_raw_price      = 0;

				// Calculate Addons Totals.
				if( typeof( addons ) !== 'undefined' && addons.length > 0 ) {

					for( var i=0; i < addons.length; i++ ) {
						addons_raw_price += Number( addons[i].cost_raw );
					}

				}

				if ( addons_raw_price > 0 ) {

					var addons_prices      = container.get_taxed_totals( addons_raw_price, addons_raw_price, tax_ratios, qty ),
						price_html         = '',
						price_html_suffix  = '',
						html = '';

					// Recalculate price html with add-ons price embedded in base price.
					price_data.base_price = Number( price_data.base_price ) + Number( addons_raw_price );

					price_data = container.calculate_subtotals( false, price_data, qty );
					price_data = container.calculate_totals( price_data );

					// Done!
					price_html        = wc_mnm_price_format( price_data.totals.price );
					price_html_suffix = container.get_formatted_price_suffix( price_data );

					// Alternative Addons Markup.
					html = '<dl class="product-addon-totals"><dt>' + wc_mnm_params.i18n_addon_total + '</dt><dd><strong><span class="amount">' + wc_mnm_price_format( addons_prices.price ) + '</span></strong></dd>';
					html += '<dt>' + wc_mnm_params.i18n_addons_total + '</dt><dd><strong>' + price_html + price_html_suffix + '</strong></dd></dl>';

					container.$addons_totals.html( html );

				} else {
					container.$addons_totals.empty();
				}

			}

		};

		/**
		 * Schedules an update of the container totals.
		 */
		this.update_container = function( triggered_by, config ) {
			
			clearTimeout( container.update_mnm_timer );

			container.update_mnm_timer = setTimeout( function() {
				container.update_container_task( triggered_by, config );
			}, 10 );

		},

		/**
		 * Updates the container totals.
		 */		
		this.update_container_task = function( triggered_by, config ) {
	
			// Reset status/error messages state.
			this.reset_messages();

			// Get config.
			this.update_quantities( triggered_by, config );
			
			// Validate total quantites.
			this.validate();

			// Calculate totals.
			if ( container.api.is_purchasable() && container.api.is_priced_per_product() ) {
				this.update_totals( triggered_by );
			}

			// Update error messages.
			this.update_ui();

			this.$mnm_form.trigger( 'wc-mnm-form-updated', [ this ] );

		};

		/**
		 * Updates the container quantities.
		 */		
		this.update_quantities = function( triggered_by, config ) {

			var total_qty           = 0;

			if ( typeof( config ) === 'undefined' ) {

				// Add up quantities.
				$.each( container.child_items, function( index, child_item ) {

					var item_quantity = child_item.get_quantity();
					var item_id = child_item.get_item_id();
					
					container.container_config[ item_id ] = item_quantity;
					total_qty += item_quantity;

				} );

			} else {

				// Add up quantities.
				$.each( config, function( item_id, item_quantity ) {
					container.container_config[ item_id ] = parseInt( item_quantity, 10 );
					total_qty += item_quantity;
				} );

			}

			// Set the container size.
			container.api.set_container_size( total_qty );

			// Serialize the config to the Add to cart button for ajax add to cart compat.
			this.$mnm_button.data( this.$mnm_data.data( 'input_name' ), this.api.get_container_config() );

			this.$mnm_form.trigger( 'wc-mnm-container-quantities-updated', [ this ] );

		};

		/**
		 * Refresh totals after changes to addons.
		 */	
		this.updated_addons_handler = function( triggered_by ) {
			container.update_addons_totals( triggered_by );
			triggered_by.stopPropagation();
		};

		/**
		 * Schedules a UI container price string refresh.
		 */
		this.updated_totals = function() {

			clearTimeout( container.update_price_timer );

			container.update_price_timer = setTimeout( function() {
				container.updated_totals_task();
			}, 10 );
		};

		/**
		 * Refreshes the container price string in the UI.
		 */
		this.updated_totals_task = function() {

			if ( container.api.is_priced_per_product() ) {

				var container_price_html = container.get_price_html();

				// Update price.
				if ( container_price_html !== '' ) {
					this.$mnm_price.html( container_price_html );
					// Show price.
					this.$mnm_price.slideDown( 200 );
				} else {
					// Hide price.
					this.$mnm_price.slideUp( 200 );
				}

			}

			// Addons compatibility.
			if ( container.has_addons() ) {
				container.update_addons_totals();
			}

			container.$mnm_form.trigger( 'wc-mnm-updated-totals', [ container ] );

		};

		/**
		 * Updates the container display.
		 */		
		this.update_ui = function() {

			if ( this.passes_validation() ) {

				// Add selected qty status message if there are no error messages and infinite container is used.
				if ( container.api.get_max_container_size() === false ) {
					this.add_message( this.selected_quantity_message( container.api.get_container_size() ) );
				}

				// Enable add to cart button.
				this.$mnm_button.removeAttr( 'disabled' ).removeClass( 'disabled' );
				this.$mnm_form.trigger( 'wc-mnm-display-add-to-cart-button', [ container ] );

			} else {

				// Disable add to cart button.
				this.$mnm_button.attr( 'disabled', true ).addClass( 'disabled' );
				this.$mnm_form.trigger( 'wc-mnm-hide-add-to-cart-button', [ container ] );
			}

			// Display the status/error messages.
			if ( this.has_status_messages() || false === this.passes_validation() ) {

				var $messages = $( '<ul/>' );
				var messages  = this.get_messages( 'all' );

				if ( messages.length > 0 ) {
					$.each( messages, function( i, message ) {
						$messages.append( $( '<li/>' ).html( message ) );
					} );
				}

				this.$mnm_message_content.html( $messages.html() );
				this.$mnm_message.slideDown( 200 );

			} else {
				this.$mnm_message.slideUp( 200 );
			}

			// Hide/Show Reset Link.
			if( container.api.get_container_size() > 0 ) {
				this.$mnm_reset.show();
			} else {
				this.$mnm_reset.hide();
			}

		};

		/**
		 * Reset form to intial state.
		 */

		this.reset = function( event ) {

			event.preventDefault();

			// Loop through child items.
			$.each( this.child_items, function( index, child_item ) {
				child_item.reset();
			} );

			container.$mnm_reset.trigger( 'wc-mnm-reset-configuration', [ container ] );

			// Manually trigger the update method.
			this.update_container();

		};

		/**
		 * Reset messages on update start.
		 */

		this.reset_messages = function() {
			this.validation_messages = [];
			this.status_messages     = [];
		};

		/**
		 * Failed qty validation message builder.
		 */

		this.selected_quantity_message = function( qty ) {

			var message = qty === 1 ? wc_mnm_params.i18n_qty_message_single : wc_mnm_params.i18n_qty_message;
			return message.replace( '%s', qty );
		};

		/**
		 * Shuts down events, actions and filters managed by this script object.
		 */
		this.shutdown = function() {
			this.$mnm_form.find( '*' ).off();
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
		 * Calculates and updates container subtotals.
		 */
		this.update_totals = function( triggered_by ) {

			this.calculate_subtotals( triggered_by );
			
			if ( container.dirty_subtotals || false === container.is_initialized ) {
				container.dirty_subtotals = false;
				container.calculate_totals();
			}
	
		};

		/**
		 * Validates if this container's requirements are met and can be added to the cart.
		 */
		this.validate = function() {
			
			var min_container_size = this.api.get_min_container_size();
			var max_container_size = this.api.get_max_container_size();
			var total_qty          = this.api.get_container_size();
			var error_message      = '';
			var validation_status  = container.is_initialized ? '' : container.api.get_validation_status();

			// Validation.
			if( min_container_size === max_container_size && total_qty !== min_container_size ){
				error_message = min_container_size === 1 ? wc_mnm_params.i18n_qty_error_single : wc_mnm_params.i18n_qty_error;
				error_message = error_message.replace( '%s', min_container_size );
			}
			// Validate a range.
	    	else if( max_container_size > 0 && min_container_size > 0 && ( total_qty < min_container_size || total_qty > max_container_size ) ){
				error_message = wc_mnm_params.i18n_min_max_qty_error.replace( '%max', max_container_size ).replace( '%min', min_container_size );
			}
			// Validate that a container has minimum number of items.
			else if( min_container_size > 0 && total_qty < min_container_size ){
				error_message = min_container_size > 1 ? wc_mnm_params.i18n_min_qty_error : wc_mnm_params.i18n_min_qty_error_singular;
				error_message = error_message.replace( '%min', min_container_size );
				// Validate that a container has fewer than the maximum number of items.
			} else if ( max_container_size > 0 && total_qty > max_container_size ){
				error_message = max_container_size > 1 ? wc_mnm_params.i18n_max_qty_error : wc_mnm_params.i18n_max_qty_error_singular;
				error_message = error_message.replace( '%max', max_container_size );
			}

			// Add error message.
			if ( error_message !== '' ) {
				// "Selected X total".
				var selected_qty_message = this.selected_quantity_message( total_qty );

				// Add error message, replacing placeholders with current values.
				this.add_message( error_message.replace( '%v', selected_qty_message ), 'error' );
			}

			// Let mini extensions add their own error/status messages.
			this.$mnm_form.trigger( 'wc-mnm-validation', [ container, total_qty ] );

			// Validation status changed?
			if ( validation_status !== container.api.get_validation_status() ) {
				this.$mnm_form.triggerHandler( 'wc-mnm-validation-status-changed', [ container ] );
			}

		};


		/*-----------------------------------------------------------------*/
		/*  Deprecated    .                                                */
		/*-----------------------------------------------------------------*/

		/**
		 * Get min container size.
		 *
		 * return mixed int|false
		 */
		this.get_min_container_size = function() {
			return this.api.get_min_container_size();
		};

		/**
		 * Get max container size.
		 * return mixed int|false
		 */
		this.get_max_container_size = function() {
			return this.api.get_max_container_size();
		};

		/**
		 * Object initialization.
		 */
		this.init = function() {
			this.initialize();
		};

		/**
		 * Schedules an update of the container totals.
		 */
		this.update = function( triggered_by ) {
			this.update_container( triggered_by );
		};

	} // End WC_MNM_Container.

	/**
     * Child Item object.
     */
	function WC_MNM_Child_Item( container, $mnm_item, index ) {

		this.initialize = function() {

			this.$mnm_item_qty     = $mnm_item.find( ':input.qty' );
			this.$self             = $mnm_item;
			this.$mnm_item_data    = $mnm_item.find( '.mnm-item-data' );
			this.$mnm_item_images  = $mnm_item.find( '.mnm_child_product_images' );

			this.mnm_item_index    = index;
			this.mnm_item_id       = this.$mnm_item_data.data( 'mnm_item_id' );

			this.sold_individually = typeof( container.price_data.is_sold_individually[ this.mnm_item_id ] ) === 'undefined' ? false : container.price_data.is_sold_individually[ this.mnm_item_id ] === 'yes';

			this.init_scripts();

		};

		this.get_item_id = function() {
			return this.mnm_item_id;
		};

		this.get_quantity = function() {
			var qty, 
			    type = this.get_type();

			switch( type ) {
				case 'checkbox':
					qty = this.$mnm_item_qty.is( ':checked' ) ? this.$mnm_item_qty.val() : 0;
				break;
				case 'select':
					qty = this.$mnm_item_qty.children( 'option:selected' ).val();
				break;
				default:
					qty = this.$mnm_item_qty.val();
			}

			return qty ? parseInt( qty, 10 ) : 0;
		};
		this.get_original_quantity = function() {
			var original_quantity;
			original_quantity = this.$mnm_item_data.data( 'original_quantity' );
			return original_quantity ? parseInt( original_quantity, 10 ) : 0;
		};
		this.get_type = function() {
			var type = 'input';

			if( this.$mnm_item_qty.is( ':checkbox' ) ) {
				type = 'checkbox';
			} else if ( this.$mnm_item_qty.is( 'select' ) ) {
				type = 'select';
			}

			return type;
		};

		this.reset = function() {

			var original_value = this.get_original_quantity(),
			    type           = this.get_type(),
			    is_checked;

			switch( type ) {
				case 'checkbox':
					is_checked = original_value === parseInt( this.$mnm_item_qty.val(), 10 );
					this.$mnm_item_qty.prop( 'checked', is_checked );
				break;
				case 'select':
					original_value = 0 !== typeof original_value ? original_value : this.$mnm_item_qty.children( ':first-child' ).val();
					this.$mnm_item_qty.val( original_value );
				break;
				default:
					original_value = original_value !== '' ? parseInt( original_value, 10 ) : '';
					this.$mnm_item_qty.val( original_value );
			}
		};

		this.is_sold_individually = function() {
			return this.sold_individually;
		};

		this.init_scripts = function() {

			// Init PhotoSwipe if present.
			if ( typeof PhotoSwipe !== 'undefined' && 'yes' === wc_mnm_params.photoswipe_enabled ) {
				this.init_photoswipe();
			}

		};

		/**
		 * Launch popups for child images.
		 */
		this.init_photoswipe = function() {
			this.$mnm_item_images.each( function() {	
				$(this).wc_product_gallery( { zoom_enabled: false, flexslider_enabled: false } );
			} );
		};

		this.initialize();

	} // End WC_MNM_Child_Item.

	/*-----------------------------------------------------------------*/
	/*  Initialization.                                                */
	/*-----------------------------------------------------------------*/

	jQuery( document ).ready( function($) {

		/**
	 	 * Script initialization on '.mnm_form' jQuery objects.
	 	 */
		$.fn.wc_mnm_form = function() {

			var $mnm_form = $( this ),
				$mnm_data = $mnm_form.find( '.mnm_data' ),
				container_id = $mnm_data.data( 'container_id' );

			if( typeof( $mnm_data ) === 'undefined' ) {
				return false;
			}

			if ( typeof( container_id ) === 'undefined' ) {
				container_id = $mnm_data.attr( 'data-container_id' );

				if ( container_id ) {
					$mnm_data.data( 'container_id', container_id );
				} else {
					return false;
				}
			}

			if ( typeof( wc_mnm_scripts[ container_id ] ) !== 'undefined' ) {
				wc_mnm_scripts[ container_id ].shutdown();
			}

			wc_mnm_scripts[ container_id ] = new WC_MNM_Container( { $mnm_form: $mnm_form, $mnm_data: $mnm_data, container_id: container_id } );

			$mnm_form.data( 'script_id', container_id );

			wc_mnm_scripts[ container_id ].initialize();

		};
		
		/*
		 * Initialize form script.
		 */
		$( '.mnm_form' ).each( function() {
			$(this).wc_mnm_form();
		} );

		/*-----------------------------------------------------------------*/
		/*  Compatibility .                                                */
		/*-----------------------------------------------------------------*/

		/**
		 * QuickView compatibility.
		 */
		$( 'body' ).on( 'quick-view-displayed', function() {

			$( '.mnm_form' ).each( function() {
				$(this).wc_mnm_form();
			} );

		} );

        /**
		 * PayPal Express Smart buttons compatibility.
		 */
		$( '.mnm_form' ).on( 'wc-mnm-initialized', function( e, wc_mnm ) {

			if( ! wc_mnm.passes_validation() ) {
				$( '#woo_pp_ec_button_product' ).trigger( 'disable' );
			}
			
			wc_mnm.$mnm_form.on( 'wc-mnm-display-add-to-cart-button', function() {
				$( '#woo_pp_ec_button_product' ).trigger( 'enable' );
			});

			wc_mnm.$mnm_form.on( 'wc-mnm-hide-add-to-cart-button', function() {
				$( '#woo_pp_ec_button_product' ).trigger( 'disable' );
			});

			$( document ).on( 'wc_ppec_validate_product_form', function( e, is_valid, $form ) {

				var wc_mnm = $form.wc_get_mnm_script();

				if ( 'object' === typeof wc_mnm ) {
					is_valid = wc_mnm.passes_validation();
				}
				
				return is_valid;

			});

		});

	} );

} ) ( jQuery );
