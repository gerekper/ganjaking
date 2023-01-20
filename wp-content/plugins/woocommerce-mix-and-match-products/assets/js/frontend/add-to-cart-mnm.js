/* exported wc_mnm_woocommerce_number_format */
/* NB: ignore jshint defined but not used for deprecated function */

/**
 * Product data metabox.
 *
 * @package WooCommerce Mix and Match Products/Scripts
 *
 * global wc_mnm_params
 */

/*-----------------------------------------------------------------*/
/*  Global script variable.                                        */
/*-----------------------------------------------------------------*/

var wc_mnm_scripts = {};

/*-----------------------------------------------------------------*/
/*  Global utility variables + functions.                          */
/*-----------------------------------------------------------------*/

/**
 * Converts numbers to formatted price strings. Respects WC price format settings.
 *
 * @param float price The value to format
 * @param object args {
 * 			decimal_sep:       string
 *			currency_position: string
 *			currency_symbol:   string
 *			args.:        bool,
 *			num_decimals:      int,
 *			html:              bool,
 * }
 */
function wc_mnm_price_format(price, args) {
	var default_args = {
		decimal_sep: wc_mnm_params.currency_format_decimal_sep,
		currency_position: wc_mnm_params.currency_position,
		currency_symbol: wc_mnm_params.currency_symbol,
		trim_zeros: wc_mnm_params.currency_format_trim_zeros,
		num_decimals: wc_mnm_params.currency_format_num_decimals,
		html: true
	};

	if ('object' !== typeof (args)) {
		// Backcompatibility for boolean args (plain == true meant no HTML).
		args = true === args ? { html: false } : {};
	}

	args = Object.assign(default_args, args);

	price = wc_mnm_number_format(price, args);

	if (args.trim_zeros === 'yes' && args.num_decimals > 0) {
		for (var i = 0; i < args.num_decimals; i++) {
			args.decimal_sep = args.decimal_sep + '0';
		}
		price = price.replace(args.decimal_sep, '');
	}

	var formatted_price = price,
		formatted_symbol = args.html ? '<span class="woocommerce-Price-currencySymbol">' + args.currency_symbol + '</span>' : args.currency_symbol;

	if ('left' === args.currency_position) {
		formatted_price = formatted_symbol + formatted_price;
	} else if ('right' === args.currency_position) {
		formatted_price = formatted_price + formatted_symbol;
	} else if ('left_space' === args.currency_position) {
		formatted_price = formatted_symbol + ' ' + formatted_price;
	} else if ('right_space' === args.currency_position) {
		formatted_price = formatted_price + ' ' + formatted_symbol;
	}

	formatted_price = args.html ? '<span class="woocommerce-Price-amount amount">' + formatted_price + '</span>' : formatted_price;

	return formatted_price;

}

/**
 * Formats price strings according to WC settings.
 *
 * @see float wc_mnm_price_format()
 * @deprecated 1.12.0
 */
function wc_mnm_woocommerce_number_format(price, args) {
	return wc_mnm_price_format(price, args);
}

/**
 * Formats price values according to WC settings.
 *
 * @param float number The value to format
 * @param object args {
 * 			decimal_sep:       string
 *			currency_position: string
 *			currency_symbol:   string
 *			args.:        bool,
 *			num_decimals:      int,
 *			html:              bool,
 * }
 */
function wc_mnm_number_format(number, args) {

	var default_args = {
		decimal_sep: wc_mnm_params.currency_format_decimal_sep,
		thousands_sep: wc_mnm_params.currency_format_thousand_sep,
		num_decimals: wc_mnm_params.currency_format_num_decimals
	};

	args = Object.assign(default_args, args);

	var n = number, c = isNaN(args.num_decimals = Math.abs(args.num_decimals)) ? 2 : args.num_decimals;
	var d = args.decimal_sep === undefined ? ',' : args.decimal_sep;
	var t = args.thousands_sep === undefined ? '.' : args.thousands_sep, s = n < 0 ? '-' : '';
	var i = parseInt(n = Math.abs(+n || 0).toFixed(c), 10) + '', j = (j = i.length) > 3 ? j % 3 : 0;

	return s + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : '');
}

/**
 * Rounds price values according to WC settings.
 *
 * @param float number
 * @param int precision
 */
function wc_mnm_number_round(number, precision) {

	precision = 'undefined' !== typeof precision ? parseInt(precision) : wc_mnm_params.currency_format_precision_decimals;

	var factor = Math.pow(10, precision),
		tempNumber = number * factor,
		roundedTempNumber = Math.round(tempNumber);

	return roundedTempNumber / factor;
}

/**
 * Container script object getter.
 */
jQuery.fn.wc_get_mnm_script = function () {

	var $mnm_form = jQuery(this);

	if (!$mnm_form.hasClass('mnm_form')) {
		return false;
	}

	var script_id = $mnm_form.data('script_id');

	if (typeof (wc_mnm_scripts[script_id]) !== 'undefined') {
		return wc_mnm_scripts[script_id];
	}

	return false;
};

/*-----------------------------------------------------------------*/
/*  Encapsulation.                                                 */
/*-----------------------------------------------------------------*/

(function ($) {

	/**
	 * Main container object.
	 */
	function WC_MNM_Container($form) {

		var container = this;
		this.$mnm_form = $form;
		this.is_initialized = false;

		this.api = {

			/**
			 * Get container quantities config.
			 *
			 * @param mixed version, options: 'v2'
			 *
			 * @return v2 returns an array of objects []object {
			 * 		product_id: int,
			 *  	product_qty: int
			 * }
			 *
			 * @return v1 returns an object of product ID => product Qty { product_id: product_quantity }
			 *
			 */
			get_container_config: function (version) {

				var config = [];

				if ('v2' === version) {
					if (Array.isArray(container.container_config)) {
						config = container.container_config;
					} else if ('object' === typeof (container.container_config)) {

						// If extensions are manually editing container.container_config, we'll need to remap it to v2 to make calculate_subtotals|calculate_totals work internally.
						Object.keys(container.container_config).foreach(
							function (index) {
								if (Number.isInteger(container.container_config[index])) {
									config.push({ product_id: parseInt(index, 10), product_qty: parseInt(container.container_config[index], 10) });
								}
							}
						);

					}

				} else {

					// Handle backcompat for folks who may have been calling container.api.get_container_config()
					config = {};

					// This will port current config back to v1 object.
					if (Array.isArray(container.container_config)) {
						container.container_config.forEach(
							function (data) {
								config[data.product_id] = data.product_qty;
							}
						);
					} else {
						config = container.container_config;
					}

				}

				return config;
			},

			/**
			 * Get container total price(s).
			 *
			 * @return obj|int
			 */
			get_container_price: function (type) {
				var totals = container.price_data.totals;

				if (type !== 'undefined' && totals.hasOwnProperty(type)) {
					return Number(totals[type]);
				} else {
					return totals;
				}
			},

			/**
			 * Get container size.
			 *
			 * @return int
			 */
			get_container_size: function () {
				return parseInt(container.container_size, 10);
			},

			/**
			 * Get min container size.
			 *
			 * @return mixed int|false
			 */
			get_min_container_size: function () {
				if ('undefined' !== typeof (container.min_container_size) && '' !== container.min_container_size) {
					return parseInt(container.min_container_size, 10);
				}

				return false;
			},

			/**
			 * Get max container size.
			 *
			 * @return mixed int|false
			 */
			get_max_container_size: function () {
				if ('undefined' !== typeof (container.max_container_size) && '' !== container.max_container_size) {
					return parseInt(container.max_container_size, 10);
				}

				return false;
			},

			/**
			 * Get the current status messages for the container.
			 *
			 * @return array
			 */
			get_status_messages: function () {
				return container.get_messages('status');
			},

			/**
			 * Get the current validation status of the container.
			 *
			 * @return string ('pass' | 'fail')
			 */
			get_validation_status: function () {
				return container.passes_validation() ? 'pass' : 'fail';
			},

			/**
			 * Get the current validation messages for the container.
			 *
			 * @return array
			 */
			get_validation_messages: function () {
				return container.get_messages('error');
			},

			/**
			 * Is priced per-product?
			 */
			is_priced_per_product: function () {
				return container.price_data.per_product_pricing === 'yes';
			},

			/**
			 * Is purchasable?
			 */
			is_purchasable: function () {
				return container.price_data.is_purchasable === 'yes';
			},

			/**
			 * Is in_stock?
			 */
			is_in_stock: function () {
				return container.price_data.is_in_stock === 'yes';
			},

			/**
			 * Set container size.
			 *
			 * @return int
			 */
			set_container_size: function (size) {
				container.container_size = parseInt(size, 10);
			},

			/**
			 * Set container config.
			 *
			 * @param []object {
			 * 		product_id: int,
			 *  	product_qty: int
			 * }
			 *
			 * OR alternatively...
			 *
			 * Array of product ID keys with quantity values.
			 * [ product_id => product_qty ]
			 *
			 *
			 * @return []
			 */
			set_container_config: function (config) {

				var new_config = [];

				// Add up quantities.
				$.each(
					config,
					function (index, data) {

						if (Number.isInteger(data)) {
							data = { product_id: index, product_qty: parseInt(data, 10) };
						}

						new_config.push(data);

					}
				);

				container.container_config = new_config;
			}
		};

		/**
		 * Add validation/status message.
		 */

		this.add_message = function (message, type) {

			if (type === 'error') {
				this.validation_messages.push(message.toString());
			} else {
				this.status_messages.push(message.toString());
			}

		};

		/**
		 * Attach child-item-level event handlers.
		 */
		this.bind_child_item_event_handlers = function (child_item) {

			child_item.$self

				/**
				 * Update totals upon changing quantities.
				 */
				.on(
					'input.wc-mnm-form',
					':input.qty',
					function () {
						clearTimeout(child_item.child_item_timer);
						var $input = $(this);
						child_item.child_item_timer = setTimeout(
							function () {
								$input.trigger('change.wc-mnm-form');
							},
							500
						);

					}
				)
				.on(
					'change.wc-mnm-form',
					':input.qty',
					function () {
						child_item.update_quantity();
					}
				);

		};

		/**
		 * Container-Level Event Handlers.
		 */
		this.bind_event_handlers = function () {

			if (container.has_addons()) {
				container.$mnm_data.on('updated_addons', container.updated_addons_handler);
			}

			// Upon clicking reset link.
			container.$mnm_reset.on('click.wc-mnm-form', function () {

				if (window.confirm(wc_mnm_params.i18n_confirm_reset)) {
					container.$mnm_form.trigger('wc-mnm-container-reset');
				}

			});

			container.$mnm_form.on('wc-mnm-container-reset', container.reset);

		};

		/**
		 * Calculates child item subtotals (container totals) and updates the corresponding 'price_data' fields.
		 */
		this.calculate_subtotals = function (triggered_by, price_data_array, qty) {

			var price_data = typeof (price_data_array) === 'undefined' ? container.price_data : price_data_array;

			qty = typeof (qty) === 'undefined' ? 1 : parseInt(qty, 10);
			triggered_by = typeof (triggered_by) === 'undefined' ? false : triggered_by;

			// Base.
			if (false === triggered_by) {

				var base_price = Number(price_data.base_price),
					base_regular_price = Number(price_data.base_regular_price),
					base_price_tax_ratios = price_data.base_price_tax;

				price_data.base_price_totals = price_data.base_price_subtotals = this.get_taxed_totals(base_price, base_regular_price, base_price_tax_ratios, qty);
			}

			$.each(
				container.api.get_container_config('v2'),
				function (index, data) {

					var { product_id, product_qty } = data;

					if (triggered_by.hasOwnProperty('mnm_item_id ') && triggered_by.mnm_item_id !== product_id) {
						return true;
					}

					// Non-purchasable items don't have prices in the price_data.
					var tax_ratios = price_data.prices_tax.hasOwnProperty(product_id) ? price_data.prices_tax[product_id] : { incl: 1, excl: 1 },
						regular_price = price_data.regular_prices.hasOwnProperty(product_id) ? price_data.regular_prices[product_id] : 0.0,
						price = price_data.prices.hasOwnProperty(product_id) ? price_data.prices[product_id] : 0.0,

						totals = {
							price: 0.0,
							regular_price: 0.0,
							price_incl_tax: 0.0,
							price_excl_tax: 0.0
						};

					if (wc_mnm_params.calc_taxes === 'yes') {

						if (product_qty > 0 && (price > 0 || regular_price > 0)) {
							totals = container.get_taxed_totals(price, regular_price, tax_ratios, product_qty);
						}

					} else {

						totals.price = product_qty * price;
						totals.regular_price = product_qty * regular_price;
						totals.price_incl_tax = product_qty * price;
						totals.price_excl_tax = product_qty * price;

					}

					if (container.totals_changed(price_data.child_item_subtotals[product_id], totals)) {
						container.dirty_subtotals = true;
						price_data.child_item_subtotals[product_id] = totals;
						price_data.child_item_totals[product_id] = totals;
					}

				}
			);

			return price_data;

		};

		/**
		 * Adds container subtotals and calculates container totals.
		 */
		this.calculate_totals = function (price_data_array) {

			var price_data = typeof (price_data_array) === 'undefined' ? container.price_data : price_data_array,
				totals_changed = false;

			// Non-recurring (sub)totals.
			var totals = {
				price: price_data.base_price_totals.price,
				regular_price: price_data.base_price_totals.regular_price,
				price_incl_tax: price_data.base_price_totals.price_incl_tax,
				price_excl_tax: price_data.base_price_totals.price_excl_tax
			},
				subtotals = {
					price: price_data.base_price_subtotals.price,
					regular_price: price_data.base_price_subtotals.regular_price,
					price_incl_tax: price_data.base_price_subtotals.price_incl_tax,
					price_excl_tax: price_data.base_price_subtotals.price_excl_tax
				};

			$.each(
				container.api.get_container_config('v2'),
				function (index, data) {

					var { product_id } = data;

					var item_totals = price_data.child_item_totals[product_id],
						item_subtotals = price_data.child_item_subtotals[product_id];

					if (typeof item_totals !== 'undefined') {

						totals.price += item_totals.price;
						totals.regular_price += item_totals.regular_price;
						totals.price_incl_tax += item_totals.price_incl_tax;
						totals.price_excl_tax += item_totals.price_excl_tax;
					}

					if (typeof item_subtotals !== 'undefined') {

						subtotals.price += item_subtotals.price;
						subtotals.regular_price += item_subtotals.regular_price;
						subtotals.price_incl_tax += item_subtotals.price_incl_tax;
						subtotals.price_excl_tax += item_subtotals.price_excl_tax;
					}

				}
			);

			totals_changed = container.totals_changed(price_data.totals, totals) || container.totals_changed(price_data.subtotals, subtotals);

			// Render.
			if (totals_changed || false === container.is_initialized) {

				price_data.subtotals = subtotals;
				price_data.totals = totals;

				if (typeof (price_data_array) === 'undefined') {
					this.updated_totals();
				}
			}

			return price_data;
		};

		/**
		 * Replace totals in price suffix.
		 */
		this.get_formatted_price_suffix = function (price_data_array, totals) {

			var price_data = typeof (price_data_array) === 'undefined' ? container.price_data : price_data_array,
				suffix = '';

			totals = typeof (totals) === 'undefined' ? price_data.totals : totals;

			if (price_data.suffix_exists) {

				suffix = price_data.suffix;

				if (price_data.suffix_contains_price_incl) {
					suffix = suffix.replace('{price_including_tax}', wc_mnm_price_format(totals.price_incl_tax));
				}

				if (price_data.suffix_contains_price_excl) {
					suffix = suffix.replace('{price_excluding_tax}', wc_mnm_price_format(totals.price_excl_tax));
				}
			}

			return suffix;
		};

		/**
		 * Get validation/status messages.
		 */

		this.get_messages = function (type) {

			var messages = [];

			if (type === 'all') {
				messages = $.merge(this.status_messages, this.validation_messages);
			} else if (type === 'error') {
				messages = this.validation_messages;
			} else {
				messages = this.status_messages;
			}

			return messages;

		};

		/**
		 * Build the non-recurring price html component.
		 */
		this.get_price_html = function (price_data_array, config) {

			var price_data = 'undefined' === typeof (price_data_array) ? container.price_data : price_data_array,
				container_price_html = '',
				default_config = {
					'show_total_string': wc_mnm_number_round(price_data.totals.price) !== wc_mnm_number_round(price_data.raw_container_price_min) || price_data.raw_container_price_min !== price_data.raw_container_price_max,
					'tag': 'p'
				};

			config = 'undefined' === typeof (config) ? {} : config;
			config = $.extend(default_config, config);

			var total_string = config.show_total_string ? '<span class="total">' + wc_mnm_params.i18n_total + '</span>' : '';

			// Non-recurring price html data.
			var formatted_price = price_data.totals.price === 0.0 && price_data.show_free_string === 'yes' ? wc_mnm_params.i18n_free : wc_mnm_price_format(price_data.totals.price),
				formatted_regular_price = wc_mnm_price_format(price_data.totals.price === price_data.subtotals.price ? price_data.totals.regular_price : price_data.subtotals.price),
				formatted_suffix = container.get_formatted_price_suffix(price_data);

			if (price_data.totals.regular_price > price_data.totals.price) {
				formatted_price = wc_mnm_params.i18n_strikeout_price_string.replace('%f', formatted_regular_price).replace('%t', formatted_price);
			}

			container_price_html = wc_mnm_params.i18n_price_format.replace('%t', total_string).replace('%p', formatted_price).replace('%s', formatted_suffix);
			container_price_html = '<' + config.tag + ' class="price">' + price_data.price_string.replace('%s', container_price_html) + '</' + config.tag + '>';

			return container_price_html;
		};

		/**
		 * Build the price + current quantity html component.
		 */
		this.get_status_html = function (price_data_array, config) {

			var price_data = 'undefined' === typeof (price_data_array) ? container.price_data : price_data_array,
				default_config = {
					'show_total_string': wc_mnm_number_round(price_data.totals.price) !== wc_mnm_number_round(price_data.raw_container_price_min) || price_data.raw_container_price_min !== price_data.raw_container_price_max,
					'tag': 'span'
				};

			config = 'undefined' === typeof (config) ? {} : config;
			config = $.extend(default_config, config);

			var mode = container.$mnm_cart.data('validation_mode');

			// Attempt to grab formatted total from data attributes, for compat alternative validation mini-extensions.
			var qty = container.$mnm_cart.data('total_' + mode);
			var formatted_total = container.$mnm_cart.data('formatted_total_' + mode);

			// If not, rely on quantity count.
			if ('undefined' === typeof qty || 'undefined' === typeof formatted_total) {

				qty = container.api.get_container_size();

				if (container.api.get_max_container_size()) {
					formatted_total = 1 === container.api.get_max_container_size() ? wc_mnm_params.i18n_quantity_format_counter_single : wc_mnm_params.i18n_quantity_format_counter;
					formatted_total = formatted_total.replace('%max', container.api.get_max_container_size());
				} else {
					formatted_total = 1 === qty ? wc_mnm_params.i18n_quantity_format_single : wc_mnm_params.i18n_quantity_format;
				}

				formatted_total = formatted_total.replace('%s', qty);

			}

			return wc_mnm_params.i18n_status_format.replace('%v', container.get_price_html(price_data, config)).replace('%s', formatted_total);
		};

		/**
		 * Calculates totals by applying tax ratios to raw prices.
		 */
		this.get_taxed_totals = function (price, regular_price, tax_ratios, qty) {

			qty = typeof (qty) === 'undefined' ? 1 : qty;

			var tax_ratio_incl = 'undefined' !== typeof tax_ratios && tax_ratios.hasOwnProperty('incl') ? Number(tax_ratios.incl) : false,
				tax_ratio_excl = 'undefined' !== typeof tax_ratios && tax_ratios.hasOwnProperty('excl') ? Number(tax_ratios.excl) : false,
				totals = {
					price: qty * price,
					regular_price: qty * regular_price,
					price_incl_tax: qty * price,
					price_excl_tax: qty * price
				};

			if (tax_ratio_incl && tax_ratio_excl) {

				totals.price_incl_tax = wc_mnm_number_round(totals.price * tax_ratio_incl);
				totals.price_excl_tax = wc_mnm_number_round(totals.price * tax_ratio_excl);

				if (wc_mnm_params.tax_display_shop === 'incl') {
					totals.price = totals.price_incl_tax;
					totals.regular_price = wc_mnm_number_round(totals.regular_price * tax_ratio_incl);
				} else {
					totals.price = totals.price_excl_tax;
					totals.regular_price = wc_mnm_number_round(totals.regular_price * tax_ratio_excl);
				}
			}

			return totals;
		};

		/**
		 * Returns the quantity of this container.
		 */
		this.get_quantity = function () {
			var qty = container.$mnm_quantity.length > 0 ? container.$mnm_quantity.val() : 1;
			return isNaN(qty) ? 1 : parseInt(qty, 10);
		};

		/**
		 * Does this container have addons support?
		 */
		this.has_addons = function () {
			return this.$addons_totals && this.$addons_totals.length > 0;
		};

		/**
		 * True if there are status messages to display.
		 */
		this.has_status_messages = function () {

			if (this.status_messages.length > 0) {
				return true;
			}
			return false;
		};

		/**
		 * Object initialization.
		 */
		this.initialize = function () {

			this.$mnm_data = this.$mnm_form.find('.mnm_data');
			this.$mnm_cart = this.$mnm_data; // For backcompat.

			if ('undefined' === typeof this.$mnm_data) {
				return false;
			}

			this.container_id = this.$mnm_data.data('container_id');

			if ('undefined' === typeof this.container_id) {
				return false;
			}

			// Store script ID.
			this.$mnm_form.data('script_id', this.container_id);

			// Find relevant elements.
			this.$mnm_items = this.$mnm_form.find('.mnm_item');
			this.$mnm_price = this.$mnm_form.find('.mnm_price');
			this.$mnm_reset = this.$mnm_form.find('.mnm_reset');
			this.$mnm_button = this.$mnm_form.find('.single_add_to_cart_button');

			if (!this.$mnm_button.length) {
				this.$mnm_button = this.$mnm_form.find(':submit');
			}

			this.$mnm_message = this.$mnm_data.find('.mnm_message');
			this.$mnm_message_content = this.$mnm_message.find('.mnm_message_content');
			this.$mnm_quantity = this.$mnm_data.find('.mnm_wrap input.qty');

			this.$addons_totals = this.$mnm_data.find('#product-addons-total');
			this.show_addons_totals = false;

			this.child_items = [];

			this.price_data = [];

			if (this.$mnm_data.data('price_data')) {
				this.price_data = this.$mnm_data.data('price_data');
			}

			this.container_size = 0;
			this.min_container_size = this.$mnm_data.data('min_container_size');
			this.max_container_size = this.$mnm_data.data('max_container_size');
			this.container_config = [];

			this.update_mnm_timer = false;
			this.update_price_timer = false;

			this.validation_context = this.$mnm_form.data('validation_context') || 'add-to-cart'; // NB: Context is intentionally on the <form> element for future compat with Variable MNM.
			this.validation_mode = this.$mnm_data.data('validation_mode') || 'quantity';
			this.validation_messages = [];
			this.status_messages = [];

			/**
			 * Initial states and loading.
			 */

			// Insert notice container if none exists.
			if (this.$mnm_message.length === 0) {
				if (this.$mnm_message.length > 0) {
					this.$mnm_message.remove();
				}
				this.$mnm_price.after('<div class="mnm_message woocommerce-message" aria-live="polite"><ul class="msg mnm_message_content"></ul></div></div>');
				this.$mnm_message = this.$mnm_data.find('.mnm_message');
				this.$mnm_message_content = this.$mnm_message.find('.mnm_message_content');
			}

			// Price suffix data.
			this.price_data.suffix_exists = wc_mnm_params.price_display_suffix !== '';
			this.price_data.suffix = wc_mnm_params.price_display_suffix !== '' ? ' <small class="woocommerce-price-suffix">' + wc_mnm_params.price_display_suffix + '</small>' : '';
			this.price_data.suffix_contains_price_incl = wc_mnm_params.price_display_suffix.indexOf('{price_including_tax}') > -1;
			this.price_data.suffix_contains_price_excl = wc_mnm_params.price_display_suffix.indexOf('{price_excluding_tax}') > -1;

			// Totals visible in Addons 3.0.x?
			if (1 === this.$addons_totals.data('show-sub-total') && wc_mnm_params.addons_three_support === 'yes') {
				// Ensure addons ajax is not triggered at all, as we calculate tax on the client side.
				this.$addons_totals.data('show-sub-total', 0);
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
			this.$mnm_form.trigger('wc-mnm-initializing', [this]);

			// Update config and prices.
			this.update_container_task();

			this.is_initialized = true;

			// Let 3rd party know that we are ready to rock.
			this.$mnm_form.trigger('wc-mnm-initialized', [this]);

		};

		/**
		 * Initialize child item objects.
		 */
		this.init_child_items = function () {

			container.$mnm_items.each(
				function (index) {

					container.child_items[index] = new WC_MNM_Child_Item(container, $(this), index);

					container.bind_child_item_event_handlers(container.child_items[index]);

				}
			);
		};

		/**
		 * False if there are validation messages to display.
		 */
		this.passes_validation = function () {

			if (this.validation_messages.length > 0) {
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
		this.update_addons_totals = function (triggered_by) {

			// When container is updated, tell addons the price.
			if (typeof (triggered_by) === 'undefined') {
				container.$addons_totals.data('price', container.api.get_container_price('price'));
				container.$mnm_data.trigger('woocommerce-product-addons-update');
				return false;
			}

			// Triggered by addons.
			if (container.show_addons_totals) {

				var price_html = '',
					html = '',
					price_data = $.extend(true, {}, container.price_data),
					qty = container.get_quantity(),
					tax_ratios = price_data.base_price_tax,
					addons = container.$addons_totals.data('price_data'),
					addons_total = 0.0,
					addons_length = 0,
					addons_prices = {
						price: 0.0,
						regular_price: 0.0,
						price_incl_tax: 0.0,
						price_excl_tax: 0.0
					},
					combined_totals = {
						price: 0.0,
						regular_price: 0.0,
						price_incl_tax: 0.0,
						price_excl_tax: 0.0
					};

				// Calculate Addons Totals.
				if (typeof (addons) !== 'undefined' && addons.length > 0) {
					addons_length = addons.length;
					for (var i = 0; i < addons_length; i++) {
						addons_total += Number(addons[i].cost);
					}
				}

				// Quantity is 1 as addons already calculates totals based on quantity.
				addons_prices = container.get_taxed_totals(addons_total, addons_total, tax_ratios, 1);

				// Update addons prices in container.
				price_data.addons_prices = addons_prices;

				if (addons_prices.price > 0) {

					combined_totals.price = qty * price_data.subtotals.price + addons_prices.price;
					combined_totals.regular_price = qty * price_data.subtotals.price + addons_prices.price;
					combined_totals.price_incl_tax = qty * price_data.subtotals.price_incl_tax + addons_prices.price_incl_tax;
					combined_totals.price_excl_tax = qty * price_data.subtotals.price_excl_tax + addons_prices.price_excl_tax;

					price_data.subtotals = combined_totals;
					price_data.totals = combined_totals;

					// Done!
					var config = {
						'tag': 'span',
						'show_total_string': false
					};

					price_html = container.get_price_html(price_data, config);

					// Alternative Addons Markup.
					html = '<dl class="product-addon-totals"><dt>' + wc_mnm_params.i18n_addon_total + '</dt><dd><strong><span class="amount">' + wc_mnm_price_format(addons_prices.price) + '</span></strong></dd>';
					html += '<dt>' + wc_mnm_params.i18n_addons_total + '</dt><dd><strong>' + price_html + '</strong></dd></dl>';

					container.$addons_totals.html(html);

				} else {
					container.$addons_totals.empty();
				}

			}

		};

		/**
		 * Schedules an update of the container totals.
		 */
		this.update_container = function (triggered_by, config) {

			clearTimeout(container.update_mnm_timer);

			container.update_mnm_timer = setTimeout(
				function () {
					container.update_container_task(triggered_by, config);
				},
				10
			);

		},


			/**
			 * Updates the container totals.
			 */
			this.update_container_task = function (triggered_by, config) {

				// Reset status/error messages state.
				this.reset_messages();

				// Get config.
				this.update_quantities(triggered_by, config);

				// Validate total quantites.
				this.validate();

				// Calculate totals.
				if (false === this.is_initialized || (container.api.is_purchasable() && container.api.is_priced_per_product())) {
					this.update_totals(triggered_by);
				}

				// Update status/notices.
				if (container.api.is_purchasable() && container.api.is_in_stock()) {
					this.update_ui();
				}

				this.$mnm_form.trigger('wc-mnm-form-updated', [this]);

			};

		/**
		 * Updates the container quantities.
		 */
		this.update_quantities = function (triggered_by, config) {

			var total_qty = 0;
			var new_config = [];

			if ('undefined' === typeof (config)) {

				// Add up quantities.
				$.each(
					container.child_items,
					function (index, child_item) {

						var product_qty = child_item.get_quantity();
						var product_id = child_item.get_item_id();

						new_config.push(
							{
								product_id: product_id,
								product_qty: product_qty
							}
						);

						total_qty += product_qty;

					}
				);

			} else {

				// Add up quantities.
				$.each(
					config,
					function (index, data) {

						if (Number.isInteger(data)) {
							data = { product_id: index, product_qty: parseInt(data, 10) };
						}

						new_config.push(data);
						total_qty += data.product_qty;
					}
				);

			}

			// Set the new config.
			container.api.set_container_config(new_config);

			// Set the container size.
			container.api.set_container_size(total_qty);

			// Serialize the config to the Add to cart button for ajax add to cart compat.
			this.$mnm_button.data(this.$mnm_data.data('input_name'), this.api.get_container_config(false));

			this.$mnm_form.trigger('wc-mnm-container-quantities-updated', [this, triggered_by]);

		};

		/**
		 * Refresh totals after changes to addons.
		 */
		this.updated_addons_handler = function (triggered_by) {
			container.update_addons_totals(triggered_by);
			triggered_by.stopPropagation();
		};

		/**
		 * Schedules a UI container price string refresh.
		 */
		this.updated_totals = function () {

			clearTimeout(container.update_price_timer);

			container.update_price_timer = setTimeout(
				function () {
					container.updated_totals_task();
				},
				100
			);
		};

		/**
		 * Refreshes the container price string in the UI.
		 * Price update is moved to update_ui to account for 2.0 status string.
		 */
		this.updated_totals_task = function () {

			// Addons compatibility.
			if (container.has_addons()) {
				container.update_addons_totals();
			}

			container.$mnm_form.trigger('wc-mnm-updated-totals', [container]);

		};

		/**
		 * Updates the container display.
		 */
		this.update_ui = function () {

			var container_status_html = container.get_status_html();

			// Update price.
			if (container_status_html !== '') {
				this.$mnm_price.html(container_status_html);
				this.$mnm_price.slideDown(200);
			} else {
				// Hide price.
				this.$mnm_price.slideUp(200);
			}

			if (this.passes_validation()) {

				// Enable add to cart button.
				this.$mnm_button.prop('disabled', false).removeClass('disabled');
				this.$mnm_form.trigger('wc-mnm-display-add-to-cart-button', [container]);

			} else {

				// Disable add to cart button.
				this.$mnm_button.prop('disabled', true).addClass('disabled');
				this.$mnm_form.trigger('wc-mnm-hide-add-to-cart-button', [container]);
			}

			// Display the status/error messages.
			if (this.has_status_messages() || !this.passes_validation()) {

				var $messages = $('<ul/>');
				var messages = this.get_messages('all');

				if (messages.length > 0) {
					$.each(
						messages,
						function (i, message) {
							$messages.append($('<li/>').html(message));
						}
					);
				}

				this.$mnm_message_content.html($messages.html());

				this.$mnm_message.slideDown(200);

			} else {
				this.$mnm_message.slideUp(200);
			}

			// Change message style based on validation.
			this.$mnm_data.toggleClass('passes_validation', this.passes_validation());
			this.$mnm_message.toggleClass('woocommerce-error', !this.passes_validation());

			// Hide/Show Reset Link.
			if (container.api.get_container_size() > 0) {
				this.$mnm_reset.show();
			} else {
				this.$mnm_reset.hide();
			}

		};

		/**
		 * Reset form to intial state.
		 */
		this.reset = function () {

			// Loop through child items.
			$.each(
				container.child_items,
				function (index, child_item) {
					child_item.reset();
				}
			);

			if (false !== container.$mnm_reset.triggerHandler('wc-mnm-reset-configuration', [container])) {
				container.update_container();
			}

		};

		/**
		 * Reset messages on update start.
		 */
		this.reset_messages = function (type) {

			if ('undefined' === typeof type) {
				this.validation_messages = [];
				this.status_messages = [];
			} else if ('error' === type) {
				this.validation_messages = [];
			} else if ('status' === type) {
				this.status_messages = [];
			}

		};

		/**
		 * Quantity total message builder.
		 */
		this.selected_quantity_message = function (qty) {
			var message = qty === 1 ? wc_mnm_params.i18n_qty_message_single : wc_mnm_params.i18n_qty_message;
			return message.replace('%s', qty);
		};

		/**
		 * Shuts down events, actions and filters managed by this script object.
		 */
		this.shutdown = function () {
			this.$mnm_form.find('*').off('.wc-mnm-form');
		};

		/**
		 * Comparison of totals.
		 */
		this.totals_changed = function (totals_pre, totals_post) {

			if (typeof (totals_pre) === 'undefined' || totals_pre.price !== totals_post.price || totals_pre.regular_price !== totals_post.regular_price || totals_pre.price_incl_tax !== totals_post.price_incl_tax || totals_pre.price_excl_tax !== totals_post.price_excl_tax) {
				return true;
			}

			return false;
		};

		/**
		 * Calculates and updates container subtotals.
		 */
		this.update_totals = function (triggered_by) {

			this.calculate_subtotals(triggered_by);

			if (container.dirty_subtotals || false === container.is_initialized) {
				container.dirty_subtotals = false;
				container.calculate_totals();
			}

		};

		/**
		 * Validates if this container's requirements are met and can be added to the cart.
		 */
		this.validate = function () {

			var min_container_size = this.api.get_min_container_size();
			var max_container_size = this.api.get_max_container_size();
			var total_qty = this.api.get_container_size();
			var qty_message = this.selected_quantity_message(total_qty); // "Selected X total".
			var error_message = '';
			var valid_message = '';
			var validation_status = container.is_initialized ? '' : container.api.get_validation_status();

			// Validation.
			switch (true) {
				// Validate a fixed size container.
				case min_container_size === max_container_size:

					valid_message = 'undefined' !== typeof wc_mnm_params['i18n_' + this.validation_context + '_valid_fixed_message'] ? wc_mnm_params['i18n_' + this.validation_context + '_valid_fixed_message'] : wc_mnm_params.i18n_valid_fixed_message;

					if (total_qty !== min_container_size) {
						error_message = min_container_size === 1 ? wc_mnm_params.i18n_qty_error_single : wc_mnm_params.i18n_qty_error;
						error_message = error_message.replace('%s', min_container_size);
					}

					break;

				// Validate that a container has fewer than the maximum number of items.
				case max_container_size > 0 && min_container_size === 0:

					valid_message = 'undefined' !== typeof wc_mnm_params['i18n_' + this.validation_context + '_valid_max_message'] ? wc_mnm_params['i18n_' + this.validation_context + '_valid_max_message'] : wc_mnm_params.i18n_valid_max_message;

					if (total_qty > max_container_size) {
						error_message = max_container_size > 1 ? wc_mnm_params.i18n_max_qty_error : wc_mnm_params.i18n_max_qty_error_singular;
					}

					break;

				// Validate a range.
				case max_container_size > 0 && min_container_size > 0:

					valid_message = 'undefined' !== typeof wc_mnm_params['i18n_' + this.validation_context + '_valid_range_message'] ? wc_mnm_params['i18n_' + this.validation_context + '_valid_range_message'] : wc_mnm_params.i18n_valid_range_message;

					if (total_qty < min_container_size || total_qty > max_container_size) {
						error_message = wc_mnm_params.i18n_min_max_qty_error;
					}
					break;

				// Validate that a container has minimum number of items.
				case min_container_size >= 0:

					valid_message = 'undefined' !== typeof wc_mnm_params['i18n_' + this.validation_context + '_valid_min_message'] ? wc_mnm_params['i18n_' + this.validation_context + '_valid_min_message'] : wc_mnm_params.i18n_valid_min_message;

					if (total_qty < min_container_size) {
						error_message = min_container_size > 1 ? wc_mnm_params.i18n_min_qty_error : wc_mnm_params.i18n_min_qty_error_singular;
					}

					break;

			}

			// Add error message.
			if (error_message !== '') {

				error_message = error_message.replace('%max', max_container_size).replace('%min', min_container_size);

				this.add_message(error_message.replace('%v', qty_message), 'error');

				// Add selected qty status message if there are no error messages.
			} else if (valid_message !== '') {

				valid_message = valid_message.replace('%max', max_container_size).replace('%min', min_container_size);

				this.add_message(valid_message.replace('%v', qty_message));
			}

			// Let mini extensions add their own error/status messages.
			this.$mnm_form.trigger('wc-mnm-validation', [container, total_qty]);

			// Validation status changed?
			if (validation_status !== container.api.get_validation_status()) {
				this.$mnm_form.triggerHandler('wc-mnm-validation-status-changed', [container]);
			}

		};

		/*-----------------------------------------------------------------*/
		/*  Deprecated    .                                                */
		/*-----------------------------------------------------------------*/

		/**
		 * Get min container size.
		 *
		 * @return mixed int|false
		 */
		this.get_min_container_size = function () {
			return this.api.get_min_container_size();
		};

		/**
		 * Get max container size.
		 *
		 * @return mixed int|false
		 */
		this.get_max_container_size = function () {
			return this.api.get_max_container_size();
		};

		/**
		 * Object initialization.
		 */
		this.init = function () {
			this.initialize();
		};

		/**
		 * Schedules an update of the container totals.
		 */
		this.update = function (triggered_by) {
			this.update_container(triggered_by);
		};

	} // End WC_MNM_Container.

	/**
	 * Child Item object.
	 */
	function WC_MNM_Child_Item(container, $mnm_item, index) {

		this.initialize = function () {

			this.$self = $mnm_item;
			this.$mnm_item_qty = $mnm_item.find(':input.qty');
			this.$item_qty_div = $mnm_item.find('.quantity');
			this.$mnm_item_data = $mnm_item.find('.mnm-item-data');
			this.$mnm_item_images = $mnm_item.find('.mnm_child_product_images');

			this.child_item_timer = false;
			this.child_item_index = index;
			this.mnm_item_index = index;
			this.mnm_item_id = this.$mnm_item_data.data('mnm_item_id');

			this.sold_individually = typeof (container.price_data.is_sold_individually[this.mnm_item_id]) === 'undefined' ? false : container.price_data.is_sold_individually[this.mnm_item_id] === 'yes';

			// Set original quantity.
			this.$mnm_item_data.data('original_quantity', this.get_quantity());

			this.init_scripts();

		};

		this.get_item_id = function () {
			return this.mnm_item_id;
		};

		this.get_quantity = function () {
			var qty,
				type = this.get_type();

			switch (type) {
				case 'checkbox':
					qty = this.$mnm_item_qty.is(':checked') ? this.$mnm_item_qty.val() : 0;
					break;
				case 'select':
					qty = this.$mnm_item_qty.children('option:selected').val();
					break;
				default:
					qty = this.$mnm_item_qty.val();
			}

			return qty ? parseInt(qty, 10) : 0;
		};

		this.get_original_quantity = function () {
			var original_quantity = this.$mnm_item_data.data('original_quantity');
			return original_quantity ? parseInt(original_quantity, 10) : 0;
		};

		this.get_prev_quantity = function () {
			var qty = this.$self.data('prev_quantity');
			return qty ? parseInt(qty, 10) : this.get_original_quantity();
		};

		this.update_quantity = function (qty) {

			// Restrict to min/max limits.
			var $msg_html = this.$item_qty_div.find('.wc_mnm_child_item_error'),
				msg = '',
				type = this.get_type(),
				current_qty = qty || this.get_quantity(),
				new_qty = qty || this.get_quantity(),
				prev_qty = this.get_prev_quantity(),
				min = parseFloat(this.$mnm_item_qty.attr('min')),
				max = parseFloat(this.$mnm_item_qty.attr('max')),
				step = parseFloat(this.$mnm_item_qty.attr('step')),
				container_max = container.api.get_max_container_size(),
				container_size = container.api.get_container_size(),
				potential_size = container_size + (current_qty - prev_qty);

			if (!$msg_html.length) {
				this.$item_qty_div.append('<div class="wc_mnm_child_item_error" aria-live="polite" />');
				$msg_html = this.$item_qty_div.find('.wc_mnm_child_item_error');
			}

			// Max can't be higher than the container size.
			if (container_max > 0) {
				max = Math.min(max, container_max);
			}

			// Validate individual quantity limits and prevent over-filling container.
			if (container_max > 0 && potential_size > container_max) {

				if (container_size >= container_max) {
					new_qty = prev_qty - (container_size - container_max);
					new_qty = new_qty > 0 ? new_qty : 0;
				} else {
					new_qty = Math.min(container_max - container_size, max);
				}

				msg = wc_mnm_params.i18n_child_item_max_container_qty_message.replace('%d', container_max);
			} else if (min >= 0 && current_qty < min) {
				new_qty = min;
				msg = wc_mnm_params.i18n_child_item_min_qty_message.replace('%d', min);
			} else if (max > 0 && current_qty > max) {
				new_qty = max;
				msg = wc_mnm_params.i18n_child_item_max_qty_message.replace('%d', max);
			} else if (step > 1 && current_qty % step) {
				new_qty = current_qty - (current_qty % step);
				msg = wc_mnm_params.i18n_child_item_step_qty_message.replace('%d', step);
			}

			if (msg) {
				$msg_html.html('<span>' + msg + '</span>').addClass('show');
			}

			this.child_item_timer = setTimeout(
				function () {
					$msg_html.removeClass('show');
				}, 2000);

			// Get the quantity from various types of inputs.
			switch (type) {
				case 'checkbox':
					this.$mnm_item_qty.prop('checked', this.$mnm_item_qty.val() && new_qty === parseInt(this.$mnm_item_qty.val()));
					break;
				case 'select':
					this.$mnm_item_qty.children('option:selected').val(); // @todo - Support for Select Layout plugin.
					break;
				default:
					this.$mnm_item_qty.val(new_qty);
			}

			// Update the container if there was a change.
			if (new_qty !== prev_qty) {
				container.update_container_task(this);
			}

			this.$self.data('prev_quantity', new_qty);

		};

		this.get_type = function () {
			var type = 'input';

			if (this.$mnm_item_qty.is(':checkbox')) {
				type = 'checkbox';
			} else if (this.$mnm_item_qty.is('select')) {
				type = 'select';
			} else if (this.$mnm_item_qty.is(':hidden')) {
				type = 'hidden';
			}

			return type;
		};

		// Reset behaves more like "clear".
		this.reset = function () {

			var type = this.get_type();

			switch (type) {
				case 'checkbox':
					this.$mnm_item_qty.prop('checked', false);
					break;
				case 'select':
					this.$mnm_item_qty.val(this.$mnm_item_qty.children(':first-child').val());
					break;
				case 'hidden':
					// Intentionally do nothing on hidden inputs. Min=max=value and does not change.
					break;
				default:
					var min = parseFloat(this.$mnm_item_qty.attr('min'));
					min = min > 0 ? min : '';
					this.$mnm_item_qty.val(min);
			}
		};

		this.is_sold_individually = function () {
			return this.sold_individually;
		};

		this.init_scripts = function () {

			// Init PhotoSwipe if present.
			if ('undefined' !== typeof PhotoSwipe && 'yes' === wc_mnm_params.photoswipe_enabled) {
				this.init_photoswipe();
			}

		};

		/**
		 * Launch popups for child images.
		 */
		this.init_photoswipe = function () {

			if ('undefined' !== typeof $.fn.wc_product_gallery) {
				this.$mnm_item_images.wc_product_gallery({ zoom_enabled: false, flexslider_enabled: false });
			} else {
				window.console.log('Failed to initialize PhotoSwipe for mix and match child item images. Your theme declares PhotoSwipe support, but function \'$.fn.wc_product_gallery\' is undefined.');
			}

		};

		this.initialize();

	} // End WC_MNM_Child_Item.

	/*-----------------------------------------------------------------*/
	/*  Page Ready.                                                    */
	/*-----------------------------------------------------------------*/

	jQuery(
		function ($) {

			/*-----------------------------------------------------------------*/
			/*  Compatibility .                                                */
			/*-----------------------------------------------------------------*/

			/**
			* QuickView compatibility.
			*/
			$('body').on(
				'quick-view-displayed',
				function () {

					$('.mnm_form').each(
						function () {
							$(this).wc_mnm_form();
						}
					);

				}
			);

			/**
			* PayPal Express Smart buttons compatibility.
			*/
			$('.mnm_form').on(
				'wc-mnm-initialized',
				function (e, wc_mnm) {

					if (!wc_mnm.passes_validation()) {
						$('#woo_pp_ec_button_product').trigger('disable');
					}

					wc_mnm.$mnm_form.on(
						'wc-mnm-display-add-to-cart-button',
						function () {
							$('#woo_pp_ec_button_product').trigger('enable');
						}
					);

					wc_mnm.$mnm_form.on(
						'wc-mnm-hide-add-to-cart-button',
						function () {
							$('#woo_pp_ec_button_product').trigger('disable');
						}
					);

					$(document).on(
						'wc_ppec_validate_product_form',
						function (e, is_valid, $form) {

							var wc_mnm = $form.wc_get_mnm_script();

							if ('object' === typeof wc_mnm) {
								is_valid = wc_mnm.passes_validation();
							}

							return is_valid;

						}
					);

				}
			);

			/*-----------------------------------------------------------------*/
			/*  Initialization.                                                */
			/*-----------------------------------------------------------------*/

			/**
			* Script initialization on '.mnm_form' jQuery objects.
			*/
			$.fn.wc_mnm_form = function () {

				var $mnm_form = $(this),
					$mnm_data = $mnm_form.find('.mnm_data'),
					container_id = $mnm_data.data('container_id');

				if (typeof ($mnm_data) === 'undefined') {
					return false;
				}

				if (typeof (container_id) === 'undefined') {
					container_id = $mnm_data.attr('data-container_id');

					if (container_id) {
						$mnm_data.data('container_id', container_id);
					} else {
						return false;
					}
				}


				if ('undefined' !== typeof this.data('script_id') && 'undefined' !== typeof (wc_mnm_scripts[this.data('script_id')])) {
					wc_mnm_scripts[this.data('script_id')].shutdown();
				}

				var container = new WC_MNM_Container(this);
				container.initialize();

				if (container && container.is_initialized) {
					wc_mnm_scripts[container.container_id] = container;
				}

				return this;

			};

			/**
			 * Initialize form script.
			 */
			$(document).on('wc-mnm-initialize.mix-and-match', '.mnm_form', function () {
				$(this).wc_mnm_form();
			});

			$('.mnm_form').each(
				function () {
					$(this).trigger('wc-mnm-initialize.mix-and-match');
				}
			);

		}
	);

})(jQuery);
