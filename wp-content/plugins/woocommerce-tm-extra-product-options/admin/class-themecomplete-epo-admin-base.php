<?php
/**
 * Extra Product Options admin setup
 *
 * @package Extra Product Options/Admin
 * @version 6.0
 * phpcs:disable WordPress.DB.DirectDatabaseQuery
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options admin setup
 *
 * @package Extra Product Options/Admin
 * @version 6.0
 */
final class THEMECOMPLETE_EPO_Admin_Base {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_Admin_Base|null
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		// Add Admin tab in products.
		add_filter( 'woocommerce_product_data_tabs', [ $this, 'register_data_tab' ] );
		add_action( 'woocommerce_product_data_panels', [ $this, 'register_data_panels' ] );

		// Load css and javascript files.
		add_action( 'admin_enqueue_scripts', [ $this, 'register_admin_scripts' ] );

		// Remove Extra Product Options from deleted Products.
		add_action( 'delete_post', [ $this, 'delete_post' ] );

		// Remove Extra Product Options via remove button.
		add_action( 'wp_ajax_woocommerce_tm_remove_epo', [ $this, 'remove_price' ] );
		add_action( 'wp_ajax_woocommerce_tm_remove_epos', [ $this, 'remove_prices' ] );

		// Load Extra Product Options.
		add_action( 'wp_ajax_woocommerce_tm_load_epos', [ $this, 'load_prices' ] );

		// Add Extra Product Options via add button.
		add_action( 'wp_ajax_woocommerce_tm_add_epo', [ $this, 'add_price' ] );

		// Save Extra Product Options meta data.
		add_action( 'woocommerce_process_product_meta', [ $this, 'save_meta' ], 50 );

		// Duplicate Extra Product Options.
		add_action( 'woocommerce_product_duplicate', [ $this, 'duplicate_product' ], 50, 2 );

		// Show action links on the plugin screen.
		add_filter( 'plugin_row_meta', [ $this, 'plugin_row_meta' ], 10, 2 );
		add_filter( 'plugin_action_links_' . THEMECOMPLETE_EPO_PLUGIN_NAME_HOOK, [ $this, 'plugin_action_links' ] );

		// Display options on admin Order page.
		add_action( 'woocommerce_order_item_line_item_html', [ $this, 'tm_woocommerce_order_item_line_item_html' ], 10, 2 );
		// Update option date on the order upon saving the order on admin Order page.
		add_action( 'woocommerce_saved_order_items', [ $this, 'tm_woocommerce_saved_order_items' ], 10, 2 );

		// For settings page.
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_math', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_css_code', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_js_code', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_separator_cart_text', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_multiple_separator_cart_text', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_replacement_free_price_text', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_global_required_indicator', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_force_select_text', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_no_zero_priced_products_text', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_no_negative_priced_products_text', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_update_cart_text', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_edit_options_text', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_additional_options_text', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_popup_section_button_text', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_close_button_text', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_empty_cart_text', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_final_total_text', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_options_unit_price_text', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_options_total_text', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_vat_options_total_text', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_fees_total_text', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_reset_variation_text', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_closetext', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_currenttext', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_slider_prev_text', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_slider_next_text', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_this_field_is_required_text', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_characters_remaining_text', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_uploading_files_text', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_uploading_message_text', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_select_file_text', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_add_button_text_associated_products', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_remove_button_text_associated_products', [ $this, 'tm_return_raw' ], 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_add_button_text_repeater', [ $this, 'tm_return_raw' ], 10, 3 );

		add_action( 'woocommerce_json_search_found_products', [ $this, 'woocommerce_json_search_found_products' ], 10, 1 );

		// Hide associated products in the order.
		add_action( 'woocommerce_order_item_visible', [ $this, 'woocommerce_order_item_visible' ], 10, 2 );

		// Enable shortcodes on various properties.
		add_filter( 'wc_epo_enable_shortocde', [ $this, 'enable_shortcodes' ], 10, 3 );

	}

	/**
	 * Enable shortcodes on an element property
	 *
	 * @param mixed   $property The option property.
	 * @param mixed   $original_property The original option property.
	 * @param integer $post_id The post id where the filter was used.
	 *
	 * @since 6.0.4
	 */
	public function enable_shortcodes( $property = '', $original_property = '', $post_id = 0 ) {

		if ( is_array( $property ) ) {
			foreach ( $property as $key => $value ) {
				$property[ $key ] = themecomplete_do_shortcode( $value );
			}
		} else {
			$property = themecomplete_do_shortcode( $property );
		}
		return $property;

	}

	/**
	 * Hide associated products in the order
	 *
	 * @param boolean $visible If the product should be visible.
	 * @param array   $order_item The order item object.
	 * @since 6.2
	 */
	public function woocommerce_order_item_visible( $visible, $order_item ) {

		if ( isset( $order_item['_associated_hidden'] ) && ! empty( $order_item['_associated_hidden'] ) ) {
			$visible = false;
		}

		return $visible;

	}

	/**
	 * Filter product rearch results in the builder
	 *
	 * @param object $product The product object.
	 * @since 5.0
	 */
	public function woocommerce_json_search_found_products( $product ) {
		if ( isset( $_REQUEST['tcmode'] ) && 'builder' === $_REQUEST['tcmode'] ) { // phpcs:ignore WordPress.Security.NonceVerification
			$exclude_ids = ! empty( $_GET['exclude'] ) ? array_map( 'absint', (array) wp_unslash( $_GET['exclude'] ) ) : []; // phpcs:ignore WordPress.Security.NonceVerification
			foreach ( $exclude_ids as $id ) {
				unset( $product[ $id ] );
			}
		}

		return $product;
	}

	/**
	 * Returns the provided raw value
	 *
	 * @param string $value The value.
	 * @param array  $option Array of options.
	 * @param string $raw_value The raw value.
	 * @since 1.0
	 */
	public function tm_return_raw( $value, $option, $raw_value ) {
		$raw_value = wp_slash( $raw_value );
		return $raw_value;
	}

	/**
	 * Update option date on the order upon saving the order on admin Order page
	 *
	 * @param integer $order_id The order id.
	 * @param array   $items The items array.
	 * @since 1.0
	 */
	public function tm_woocommerce_saved_order_items( $order_id = 0, $items = [] ) {

		if ( apply_filters( 'wc_epo_no_saved_order_items', false ) ) {
			return;
		}

		// @phpstan-ignore-next-line
		if ( isset( $_POST ) && isset( $_POST['order_status'] ) && 'wc-refunded' === $_POST['order_status'] ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		if ( is_array( $items ) && isset( $items['tm_epo'] ) ) {

			$order              = THEMECOMPLETE_EPO_HELPER()->tm_get_order_object();
			$order_currency     = is_callable( [ $order, 'get_currency' ] ) ? $order->get_currency() : $order->get_order_currency();
			$mt_prefix          = $order_currency;
			$order_items        = $order->get_items();
			$order_taxes        = $order->get_taxes();
			$prices_include_tax = themecomplete_order_get_att( $order, 'prices_include_tax' );

			foreach ( $items['tm_epo'] as $item_id => $epos ) {

				$item_meta = function_exists( 'wc_get_order_item_meta' ) ? wc_get_order_item_meta( $item_id, '', false ) : $order->get_item_meta( $item_id );

				$qty           = (float) $item_meta['_qty'][0];
				$line_total    = floatval( $item_meta['_line_total'][0] );
				$line_subtotal = isset( $item_meta['_line_subtotal'] ) ? floatval( $item_meta['_line_subtotal'][0] ) : $line_total;

				$has_epo = is_array( $item_meta )
								&& isset( $item_meta['_tmcartepo_data'] )
								&& isset( $item_meta['_tmcartepo_data'][0] )
								&& isset( $item_meta['_tm_epo'] );

				$has_fee = is_array( $item_meta )
						&& isset( $item_meta['_tmcartfee_data'] )
						&& isset( $item_meta['_tmcartfee_data'][0] );

				$saved_epos          = false;
				$original_saved_epos = false;
				if ( $has_epo || $has_fee ) {
					$saved_epos          = themecomplete_maybe_unserialize( $item_meta['_tmcartepo_data'][0] );
					$original_saved_epos = $saved_epos;
				}

				$do_update = false;

				if ( $saved_epos ) {

					$_product = themecomplete_get_product_from_item( $order_items[ $item_id ], $order );

					foreach ( $epos as $key => $epo ) {

						if ( isset( $items['tm_item_id'] ) && isset( $items['tm_key'] ) && (string) $items['tm_key'] === (string) $key && (string) $items['tm_item_id'] === (string) $item_id ) {

							$option_price_before = $this->order_price_exluding_tax( $saved_epos[ $key ]['price'], $prices_include_tax, $order, $order_taxes, $order_items, $item_id );
							$line_total          = $line_total - $option_price_before;
							$line_subtotal       = $line_subtotal - $option_price_before;
							unset( $saved_epos[ $key ] );
							$do_update = true;

						} else {
							$new_currency             = false;
							$_current_currency_prices = $saved_epos[ $key ]['price_per_currency'];

							if ( '' !== $mt_prefix
								&& '' !== $_current_currency_prices
								&& is_array( $_current_currency_prices )
								&& isset( $_current_currency_prices[ $mt_prefix ] )
								&& '' !== $_current_currency_prices[ $mt_prefix ]
							) {
								// don't change price as it is currency custom.
								$new_currency = true;

							}
							if ( isset( $epo['quantity'] ) && $epo['quantity'] !== $original_saved_epos[ $key ]['quantity'] ) {
								$epo_qty = $saved_epos[ $key ]['quantity'];

								$option_price_before = $this->order_price_exluding_tax( $saved_epos[ $key ]['price'], $prices_include_tax, $order, $order_taxes, $order_items, $item_id );
								$line_total          = $line_total - ( $option_price_before * $qty );
								$line_subtotal       = $line_subtotal - ( $option_price_before * $qty );
								$tax_price           = $this->order_get_tax_price( $option_price_before, false, $prices_include_tax, $order, $order_taxes, $order_items, $item_id );

								$saved_epos_price_of_one = (float) $saved_epos[ $key ]['price'];
								if ( ! empty( $saved_epos[ $key ]['quantity'] ) ) {
									$saved_epos_price_of_one = (float) $saved_epos[ $key ]['price'] / (float) $saved_epos[ $key ]['quantity'];
								}

								$saved_epos[ $key ]['quantity'] = $epo['quantity'];
								$epo_qty                        = $saved_epos[ $key ]['quantity'];

								$saved_epos[ $key ]['price'] = $saved_epos_price_of_one * (float) $saved_epos[ $key ]['quantity'];

								$option_price_after = $this->order_price_exluding_tax( $saved_epos[ $key ]['price'], $prices_include_tax, $order, $order_taxes, $order_items, $item_id );
								$line_total         = $line_total + ( $option_price_after * $qty );
								$line_subtotal      = $line_subtotal + ( $option_price_after * $qty );
								$do_update          = true;
								$tax_price          = $this->order_get_tax_price( $option_price_after, false, $prices_include_tax, $order, $order_taxes, $order_items, $item_id );

								if ( $new_currency ) {
									$saved_epos[ $key ]['price_per_currency'][ $mt_prefix ] = $saved_epos[ $key ]['price'];
									$_current_currency_prices[ $mt_prefix ]                 = $saved_epos[ $key ]['price'];
								}
							}

							if ( isset( $epo['price'] ) ) {

								if ( ! $new_currency ) {
									$epo['price']        = $this->order_price_including_tax( $epo['price'], $prices_include_tax, $order, $order_taxes, $order_items, $item_id );
									$epo['price']        = apply_filters( 'wc_epo_remove_current_currency_price', $epo['price'], THEMECOMPLETE_EPO()->get_saved_element_price_type( $saved_epos[ $key ] ), get_option( 'woocommerce_currency' ), $order_currency, $_current_currency_prices, isset( $saved_epos[ $key ]['key'] ) ? $saved_epos[ $key ]['key'] : null );
									$option_price_before = $this->order_price_exluding_tax( $saved_epos[ $key ]['price'], $prices_include_tax, $order, $order_taxes, $order_items, $item_id );
								} else {
									$option_price_before = $_current_currency_prices[ $mt_prefix ];
									if ( $prices_include_tax ) {
										$option_price_before = $this->order_price_exluding_tax( $option_price_before, $prices_include_tax, $order, $order_taxes, $order_items, $item_id );
									}
								}

								$line_total    = $line_total - ( $option_price_before * $qty );
								$line_subtotal = $line_subtotal - ( $option_price_before * $qty );

								$saved_epos[ $key ]['price'] = (float) $epo['price'] * (float) $saved_epos[ $key ]['quantity'];
								if ( ! $new_currency ) {
									$saved_epos[ $key ]['price'] = $this->order_price_exluding_tax( $saved_epos[ $key ]['price'], $prices_include_tax, $order, $order_taxes, $order_items, $item_id );
								}

								$tax_price = $this->order_get_tax_price( $saved_epos[ $key ]['price'], false, $prices_include_tax, $order, $order_taxes, $order_items, $item_id );

								if ( $new_currency ) {
									$saved_epos[ $key ]['price_per_currency'][ $mt_prefix ] = $saved_epos[ $key ]['price'] + $tax_price;
								}
								$option_price_after = $saved_epos[ $key ]['price'];

								$line_total    = $line_total + ( $option_price_after * $qty );
								$line_subtotal = $line_subtotal + ( $option_price_after * $qty );

								$saved_epos[ $key ]['price'] = $saved_epos[ $key ]['price'] + $tax_price;
								if ( $new_currency ) {
									$saved_epos[ $key ]['price_per_currency'][ $mt_prefix ] = $saved_epos[ $key ]['price'];
								}
								$do_update = true;

							}

							if ( isset( $epo['value'] ) ) {

								$saved_epos[ $key ]['value'] = $epo['value'];

								if ( isset( $saved_epos[ $key ]['multiple'] ) && isset( $saved_epos[ $key ]['key'] ) ) {

									$current_product_id  = isset( $item_meta['_product_id'][0] ) ? $item_meta['_product_id'][0] : null;
									$original_product_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $current_product_id, 'product' ) );

									if ( THEMECOMPLETE_EPO_WPML()->get_lang() === THEMECOMPLETE_EPO_WPML()->get_default_lang() && (int) $original_product_id !== (int) $current_product_id ) {
										$current_product_id = $original_product_id;
									}

									if ( $current_product_id ) {

										$get_saved_order_multiple_keys = THEMECOMPLETE_EPO_HELPER()->get_saved_order_multiple_keys( $current_product_id );

										if ( isset( $get_saved_order_multiple_keys[ 'options_' . $saved_epos[ $key ]['section'] ] ) ) {
											$new_key = array_search( $epo['value'], $get_saved_order_multiple_keys[ 'options_' . $saved_epos[ $key ]['section'] ], true );
											if ( $new_key ) {
												$saved_epos[ $key ]['key'] = $new_key;
											} else {
												$saved_epos[ $key ]['key'] = '';
											}
										}
									} else {
										$saved_epos[ $key ]['key'] = '';
									}
								}

								$do_update = true;

							}
						}
					}
				}

				if ( $do_update ) {

					wc_update_order_item_meta( $item_id, '_line_total', wc_format_decimal( $line_total ) );
					wc_update_order_item_meta( $item_id, '_line_subtotal', wc_format_decimal( $line_subtotal ) );

					wc_update_order_item_meta( $item_id, '_tmcartepo_data', $saved_epos );

					wp_cache_delete( $item_id, 'order_item_meta' );

				}
			}
		}

	}

	/**
	 * Get price with tax
	 *
	 * $price must be without tax
	 *
	 * @param float   $price The price.
	 * @param boolean $prices_include_tax The order id.
	 * @param object  $order The order object.
	 * @param array   $order_taxes The order taxes.
	 * @param array   $order_items The order items.
	 * @param integer $item_id The item id.
	 * @since 1.0
	 */
	public function order_price_including_tax( $price, $prices_include_tax, $order, $order_taxes, $order_items, $item_id ) {

		$tax_price = $this->order_get_tax_price( $price, false, $prices_include_tax, $order, $order_taxes, $order_items, $item_id );

		return (float) $price + (float) $tax_price;

	}

	/**
	 * Get price without tax
	 *
	 * $price must be with tax
	 *
	 * @param float   $price The price.
	 * @param boolean $prices_include_tax The order id.
	 * @param object  $order The order object.
	 * @param array   $order_taxes The order taxes.
	 * @param array   $order_items The order items.
	 * @param integer $item_id The item id.
	 *
	 * @since 1.0
	 */
	public function order_price_exluding_tax( $price, $prices_include_tax, $order, $order_taxes, $order_items, $item_id ) {

		$tax_price = $this->order_get_tax_price( $price, true, $prices_include_tax, $order, $order_taxes, $order_items, $item_id );

		return (float) $price - (float) $tax_price;

	}

	/**
	 * Get the tax price
	 *
	 * @param float   $price The price.
	 * @param boolean $price_has_tax If the price has tax included.
	 * @param boolean $prices_include_tax The order id.
	 * @param object  $order The order object.
	 * @param array   $order_taxes The order taxes.
	 * @param array   $order_items The order items.
	 * @param integer $item_id The item id.
	 * @since 1.0
	 */
	public function order_get_tax_price( $price, $price_has_tax, $prices_include_tax, $order, $order_taxes, $order_items, $item_id ) {

		$tax_data  = wc_tax_enabled() ? themecomplete_maybe_unserialize( isset( $order_items[ $item_id ]['line_tax_data'] ) ? $order_items[ $item_id ]['line_tax_data'] : '' ) : false;
		$tax_price = 0;

		if ( ! empty( $tax_data ) && $prices_include_tax ) {

			$tax_based_on = get_option( 'woocommerce_tax_based_on' );

			$default  = '';
			$country  = '';
			$state    = '';
			$postcode = '';
			$city     = '';
			if ( 'billing' === $tax_based_on ) {
				$country  = $order->get_billing_country();
				$state    = $order->get_billing_state();
				$postcode = $order->get_billing_postcode();
				$city     = $order->get_billing_city();
			} elseif ( 'shipping' === $tax_based_on ) {
				$country  = $order->get_shipping_country();
				$state    = $order->get_shipping_state();
				$postcode = $order->get_shipping_postcode();
				$city     = $order->get_shipping_city();
			}

			// Default to base.
			if ( 'base' === $tax_based_on || ! isset( $country ) || empty( $country ) ) {
				$default  = wc_get_base_location();
				$country  = $default['country'];
				$state    = $default['state'];
				$postcode = '';
				$city     = '';
			}

			$tax_class = $order_items[ $item_id ]['tax_class'];
			$tax_rates = WC_Tax::find_rates(
				[
					'country'   => $country,
					'state'     => $state,
					'postcode'  => $postcode,
					'city'      => $city,
					'tax_class' => $tax_class,
				]
			);

			$epo_line_taxes = WC_Tax::calc_tax( (float) $price, $tax_rates, $price_has_tax );

			foreach ( $order_taxes as $tax_item ) {
				$tax_item_id = $tax_item['rate_id'];
				if ( is_callable( [ $tax_item, 'get_rate_id' ] ) ) {
					$tax_item_id = $tax_item->get_rate_id();
				}
				if ( isset( $epo_line_taxes[ $tax_item_id ] ) ) {
					$tax_price = $tax_price + $epo_line_taxes[ $tax_item_id ];
				}
			}
		}

		return $tax_price;
	}

	/**
	 * Display options on admin Order page
	 *
	 * @param integer $item_id The item id.
	 * @param array   $item The item array.
	 * @since 1.0
	 */
	public function tm_woocommerce_order_item_line_item_html( $item_id = 0, $item = [] ) {
		$get_post_type = get_post_type();
		if ( ! $get_post_type && isset( $_REQUEST['order_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$get_post_type = get_post_type( absint( wp_unslash( $_REQUEST['order_id'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
		}
		$order_post_types = THEMECOMPLETE_EPO()->tm_epo_order_post_types;
		if ( ! is_array( $order_post_types ) ) {
			$order_post_types = [ 'shop_order' ];
		}
		if ( ! in_array( $get_post_type, $order_post_types, true ) ) {
			return;
		}

		$order = THEMECOMPLETE_EPO_HELPER()->tm_get_order_object();
		if ( $order ) {
			$order_currency = is_callable( [ $order, 'get_currency' ] ) ? $order->get_currency() : $order->get_order_currency();
		} else {
			$order_currency = get_woocommerce_currency();
		}
		$mt_prefix = $order_currency;

		$_product    = themecomplete_get_product_from_item( $item, $order );
		$item_meta   = function_exists( 'wc_get_order_item_meta' ) ? wc_get_order_item_meta( $item_id, '', false ) : $order->get_item_meta( $item_id );
		$order_taxes = $order->get_taxes();

		$has_epo = is_array( $item_meta )
				&& isset( $item_meta['_tmcartepo_data'] )
				&& isset( $item_meta['_tmcartepo_data'][0] )
				&& isset( $item_meta['_tm_epo'] );

		$has_fee = is_array( $item_meta )
				&& isset( $item_meta['_tmcartfee_data'] )
				&& isset( $item_meta['_tmcartfee_data'][0] );

		if ( $has_epo || $has_fee ) {
			$current_product_id  = $item['product_id'];
			$original_product_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $current_product_id, 'product' ) );
			if ( THEMECOMPLETE_EPO_WPML()->get_lang() === THEMECOMPLETE_EPO_WPML()->get_default_lang() && $original_product_id !== $current_product_id ) {
				$current_product_id = $original_product_id;
			}
			$wpml_translation_by_id = THEMECOMPLETE_EPO_WPML()->get_wpml_translation_by_id( $current_product_id );
		}

		if ( $has_epo ) {

			$epos = themecomplete_maybe_unserialize( $item_meta['_tmcartepo_data'][0] );

			if ( $epos && is_array( $epos ) ) {

				$header_title = esc_html__( 'Extra Product Options', 'woocommerce-tm-extra-product-options' );
				include 'views/html-tm-epo-order-item-header.php';

				foreach ( $epos as $key => $epo ) {

					if ( $epo && is_array( $epo ) ) {
						$type         = THEMECOMPLETE_EPO()->get_saved_element_price_type( $epo );
						$new_currency = false;
						if ( isset( $epo['price_per_currency'] ) ) {
							$_current_currency_prices = $epo['price_per_currency'];
							if ( '' !== $mt_prefix
								&& '' !== $_current_currency_prices
								&& is_array( $_current_currency_prices )
								&& isset( $_current_currency_prices[ $mt_prefix ] )
								&& '' !== $_current_currency_prices[ $mt_prefix ]
							) {

								$new_currency = true;
								$epo['price'] = $_current_currency_prices[ $mt_prefix ];

							}
						}
						if ( ! $new_currency ) {
							$epo['price'] = apply_filters( 'wc_epo_get_current_currency_price', $epo['price'], $type, null, $order_currency );
						}

						if ( ! isset( $epo['quantity'] ) ) {
							$epo['quantity'] = 1;
						}

						if ( isset( $wpml_translation_by_id[ $epo['section'] ] ) ) {
							$epo['name'] = $wpml_translation_by_id[ $epo['section'] ];
						}

						// normal (local) mode.
						if ( ! isset( $epo['price_per_currency'] ) && taxonomy_exists( $epo['name'] ) ) {
							$epo['name'] = wc_attribute_label( $epo['name'] );
						}

						$epo_name = apply_filters( 'tm_translate', $epo['name'] );

						if ( isset( $wpml_translation_by_id[ 'options_' . $epo['section'] ] )
							&& is_array( $wpml_translation_by_id[ 'options_' . $epo['section'] ] )
							&& ! empty( $epo['multiple'] )
							&& ! empty( $epo['key'] )
						) {

							$pos = strrpos( $epo['key'], '_' );

							if ( false !== $pos ) {

								$av = array_values( $wpml_translation_by_id[ 'options_' . $epo['section'] ] );

								if ( isset( $av[ substr( $epo['key'], $pos + 1 ) ] ) ) {

									$epo['value'] = $av[ substr( $epo['key'], $pos + 1 ) ];

								}
							}
						}
						$epo['value']  = apply_filters( 'wc_epo_enable_shortocde', $epo['value'], $epo['value'], false );
						$display_value = THEMECOMPLETE_EPO_HELPER()->entity_decode( $epo['value'] );
						$display_value = THEMECOMPLETE_EPO_HELPER()->recursive_implode( $display_value, THEMECOMPLETE_EPO()->tm_epo_multiple_separator_cart_text );

						if ( ! empty( $epo['use_images'] ) && ! empty( $epo['images'] ) && 'images' === $epo['use_images'] ) {
							$display_value = '<div class="cpf-img-on-cart"><img alt="' . esc_attr( wp_strip_all_tags( $epo_name ) ) . '" class="attachment-shop_thumbnail wp-post-image epo-option-image" src="' . apply_filters( 'tm_image_url', $epo['images'] ) . '" /></div>' . esc_attr( $display_value );
						}

						$display_value = apply_filters( 'tm_translate', $display_value );

						if ( isset( $epo['element'] ) && isset( $epo['element']['type'] ) && 'upload' === $epo['element']['type'] ) {
							$check = wp_check_filetype( $epo['value'] );
							if ( ! empty( $check['ext'] ) ) {
								$image_exts = [ 'jpg', 'jpeg', 'jpe', 'gif', 'png' ];
								if ( in_array( $check['ext'], $image_exts, true ) ) {
									$display_value = '<a target="_blank" href="' . esc_url( $display_value ) . '"><span class="cpf-img-on-cart"><img alt="' . esc_attr( wp_strip_all_tags( $epo_name ) ) . '" class="attachment-shop_thumbnail wp-post-image epo-option-image epo-upload-image" src="' .
													apply_filters( 'tm_image_url', $epo['value'] ) . '" /></span></a>';
								}
							}
						}

						if ( isset( $epo['multiple_values'] ) && ! empty( $epo['multiple_values'] ) ) {
							$display_value_array = explode( $epo['multiple_values'], $display_value );
							$display_value       = '';
							foreach ( $display_value_array as $d => $dv ) {
								$display_value .= '<span class="cpf-data-on-cart">' . $dv . '</span>';
							}
						}

						$epo_value = make_clickable( $display_value );

						if ( isset( $epo['element'] ) && 'textarea' === $epo['element']['type'] ) {
							$epo_value = trim( $epo_value );

							$epo_value = str_replace( [ "\r\n", "\r" ], "\n", $epo_value );

							$epo_value = preg_replace( "/\n\n+/", "\n\n", $epo_value );

							$epo_value = array_map( 'wc_clean', explode( "\n", $epo_value ) );

							$epo_value = implode( "\n", $epo_value );

							$epo_value = wpautop( $epo_value );
						}

						$epo_quantity = sprintf( '%s <small>(%s &times; %s)</small>', $epo['quantity'] * (float) $item_meta['_qty'][0], $epo['quantity'], (float) $item_meta['_qty'][0] );
						$epo_quantity = apply_filters( 'wc_epo_html_tm_epo_order_item_epo_quantity', $epo_quantity, $epo['quantity'], $item, $_product );

						if ( apply_filters( 'wc_epo_html_tm_epo_order_item_is_other_fee', false, $type ) ) {
							$epo_edit_value    = false;
							$edit_buttons      = false;
							$epo_edit_cost     = false;
							$epo_edit_quantity = false;
							$epo_is_fee        = false;
						} else {
							$epo_edit_value    = true;
							$edit_buttons      = true;
							$epo_edit_cost     = true;
							$epo_edit_quantity = true;
							$epo_is_fee        = false;
						}

						$epo['price'] = floatval( $epo['price'] );
						include 'views/html-tm-epo-order-item.php';
					}
				}
			}
		}

		if ( $has_fee ) {

			$epos = themecomplete_maybe_unserialize( $item_meta['_tmcartfee_data'][0] );

			if ( isset( $epos[0] ) ) {
				$epos = $epos[0];
			} else {
				$epos = false;
			}

			if ( $epos && ! empty( $epos[0] ) && is_array( $epos ) ) {

				$header_title = esc_html__( 'Extra Product Options Fees', 'woocommerce-tm-extra-product-options' );
				include 'views/html-tm-epo-order-item-header.php';

				foreach ( $epos as $key => $epo ) {

					if ( $epo && is_array( $epo ) ) {
						if ( ! isset( $epo['quantity'] ) ) {
							$epo['quantity'] = 1;
						}
						if ( isset( $wpml_translation_by_id[ $epo['section'] ] ) ) {
							$epo['name'] = $wpml_translation_by_id[ $epo['section'] ];
						}
						$epo['value'] = apply_filters( 'wc_epo_enable_shortocde', $epo['value'], $epo['value'], false );
						if ( isset( $wpml_translation_by_id[ 'options_' . $epo['section'] ] ) && is_array( $wpml_translation_by_id[ 'options_' . $epo['section'] ] ) && ! empty( $epo['multiple'] ) && ! empty( $epo['key'] ) ) {
							$pos = strrpos( $epo['key'], '_' );
							if ( false !== $pos ) {
								$av = array_values( $wpml_translation_by_id[ 'options_' . $epo['section'] ] );
								if ( isset( $av[ substr( $epo['key'], $pos + 1 ) ] ) ) {
									$epo['value'] = $av[ substr( $epo['key'], $pos + 1 ) ];
									if ( ! empty( $epo['use_images'] ) && ! empty( $epo['images'] ) && 'images' === $epo['use_images'] ) {
										$epo['value'] = '<div class="cpf-img-on-cart"><img alt="' . esc_attr( wp_strip_all_tags( $epo['name'] ) ) . '" class="attachment-shop_thumbnail wp-post-image epo-option-image" src="' . apply_filters( 'tm_image_url', $epo['images'] ) . '" /></div>' . $epo['value'];
									}
								}
							}
						}

						$epo_name     = apply_filters( 'tm_translate', $epo['name'] );
						$epo_value    = apply_filters( 'tm_translate', $epo['value'] );
						$epo_value    = make_clickable( $epo_value );
						$epo_quantity = $epo['quantity'];
						$epo_quantity = apply_filters( 'wc_epo_html_tm_epo_order_item_epo_quantity', $epo_quantity, $epo['quantity'], $item, $_product );

						$epo_edit_value    = false;
						$edit_buttons      = false;
						$epo_edit_cost     = false;
						$epo_edit_quantity = false;
						$epo_is_fee        = true;
						$epo['price']      = floatval( $epo['price'] );
						include 'views/html-tm-epo-order-item.php';

					}
				}
			}
		}

	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @param mixed $links Plugin Action links.
	 *
	 * @return array
	 */
	public static function plugin_action_links( $links ) {

		$action_links = [
			'settings' => '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=' . THEMECOMPLETE_EPO_ADMIN_SETTINGS_ID ) ) . '" aria-label="' . esc_attr__( 'View Extra Product Options settings', 'woocommerce-tm-extra-product-options' ) . '">' . esc_html__( 'Settings', 'woocommerce-tm-extra-product-options' ) . '</a>',
		];

		return array_merge( $action_links, $links );

	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param mixed $links Plugin Row Meta.
	 * @param mixed $file  Plugin Base file.
	 *
	 * @return array
	 */
	public static function plugin_row_meta( $links, $file ) {

		if ( THEMECOMPLETE_EPO_PLUGIN_NAME_HOOK === $file ) {

			$plugin_name = esc_html__( 'Extra Product Options & Add-Ons for WooCommerce', 'woocommerce-tm-extra-product-options' );
			$row_meta    = [
				'view-details' => sprintf(
					'<a href="%s" class="thickbox open-plugin-details-modal" aria-label="%s" data-title="%s">%s</a>',
					esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . THEMECOMPLETE_EPO_FILE_SLUG . '&TB_iframe=true&width=772&height=717' ) ),
					/* translators: %s: Plugin name - Extra Product Options & Add-Ons for WooCommerce. */
					esc_attr( sprintf( esc_html__( 'More information about %s', 'woocommerce-tm-extra-product-options' ), $plugin_name ) ),
					esc_attr( $plugin_name ),
					esc_html__( 'View details', 'woocommerce-tm-extra-product-options' )
				),
				'docs'         => '<a href="' . esc_url( 'https://themecomplete.com/documentation/woocommerce-tc-extra-product-options/' ) . '" aria-label="' . esc_attr__( 'View Extra Product Options documentation', 'woocommerce-tm-extra-product-options' ) . '">' . esc_html__( 'Docs', 'woocommerce-tm-extra-product-options' ) . '</a>',
				'support'      => '<a href="' . esc_url( 'https://support.themecomplete.com/forums/forum/extra-product-options/' ) . '" aria-label="' . esc_attr__( 'Visit premium customer support', 'woocommerce-tm-extra-product-options' ) . '">' . esc_html__( 'Premium Support', 'woocommerce-tm-extra-product-options' ) . '</a>',
			];

			return array_merge( $links, $row_meta );

		}

		return (array) $links;

	}

	/**
	 * Get a product from the database to duplicate
	 *
	 * This is needed since the respsective function in woocommerce is private.
	 *
	 * @access private
	 *
	 * @param mixed $id The product id.
	 *
	 * @return WP_Post|bool
	 * @todo   Returning false? Need to check for it in...
	 * @see    duplicate_product
	 */
	private function get_product_to_duplicate( $id ) {

		$id = absint( $id );

		if ( ! $id ) {
			return false;
		}

		global $wpdb;

		$post = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE ID=%d", $id ) );

		if ( isset( $post->post_type ) && 'revision' === $post->post_type ) {
			$id   = iseet( $post->post_parent ) ? $post->post_parent : 0;
			$post = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE ID=%d", $id ) );
		}

		return $post[0];

	}

	/**
	 * Function to create the duplicate of the product.
	 *
	 * @param mixed   $post The post object.
	 * @param integer $parent The parent post id.
	 * @param string  $post_status The post status.
	 *
	 * @return int
	 */
	public function cloned_duplicate_product( $post, $parent = 0, $post_status = '' ) {

		global $wpdb;

		$new_post_author   = wp_get_current_user();
		$new_post_date     = current_time( 'mysql' );
		$new_post_date_gmt = get_gmt_from_date( $new_post_date );

		if ( $parent > 0 ) {
			$post_parent = $parent;
			$post_status = $post_status ? $post_status : 'publish';
			$suffix      = '';
			$post_title  = $post->post_title;
		} else {
			$post_parent = $post->post_parent;
			$post_status = $post_status ? $post_status : 'draft';
			$suffix      = ' ' . esc_html__( '(Copy)', 'woocommerce' );
			$post_title  = $post->post_title . $suffix;
		}

		// Insert the new template in the post table.
		$wpdb->insert(
			$wpdb->posts,
			[
				'post_author'           => $new_post_author->ID,
				'post_date'             => $new_post_date,
				'post_date_gmt'         => $new_post_date_gmt,
				'post_content'          => $post->post_content,
				'post_content_filtered' => $post->post_content_filtered,
				'post_title'            => $post_title,
				'post_excerpt'          => $post->post_excerpt,
				'post_status'           => $post_status,
				'post_type'             => $post->post_type,
				'comment_status'        => $post->comment_status,
				'ping_status'           => $post->ping_status,
				'post_password'         => $post->post_password,
				'to_ping'               => $post->to_ping,
				'pinged'                => $post->pinged,
				'post_modified'         => $new_post_date,
				'post_modified_gmt'     => $new_post_date_gmt,
				'post_parent'           => $post_parent,
				'menu_order'            => $post->menu_order,
				'post_mime_type'        => $post->post_mime_type,
			]
		);

		$new_post_id = $wpdb->insert_id;

		// Set title for variations.
		if ( 'product_variation' === $post->post_type ) {
			/* translators: %1 variation id %2 parent product title*/
			$post_title = sprintf( esc_html__( 'Variation #%1$s of %2$s', 'woocommerce' ), absint( $new_post_id ), esc_html( get_the_title( $post_parent ) ) );
			$wpdb->update(
				$wpdb->posts,
				[
					'post_title' => $post_title,
				],
				[
					'ID' => $new_post_id,
				]
			);
		}

		// Set name and GUID.
		if ( ! in_array( $post_status, [ 'draft', 'pending', 'auto-draft' ], true ) ) {
			$wpdb->update(
				$wpdb->posts,
				[
					'post_name' => wp_unique_post_slug( sanitize_title( $post_title, $new_post_id ), $new_post_id, $post_status, $post->post_type, $post_parent ),
					'guid'      => get_permalink( $new_post_id ),
				],
				[
					'ID' => $new_post_id,
				]
			);
		}

		// Copy the taxonomies.
		$this->cloned_duplicate_post_taxonomies( $post->ID, $new_post_id, $post->post_type );

		// Copy the meta information.
		$this->cloned_duplicate_post_meta( $post->ID, $new_post_id );

		// Copy the children (variations).
		$exclude = apply_filters( 'woocommerce_duplicate_product_exclude_children', false );

		if ( ! $exclude ) {
			$children_products = get_children( 'post_parent=' . $post->ID . '&post_type=product_variation' );
			if ( $children_products ) {
				foreach ( $children_products as $child ) {
					$this->cloned_duplicate_product( $this->get_product_to_duplicate( $child->ID ), $new_post_id, $child->post_status );
				}
			}
		}

		// Clear cache.
		clean_post_cache( $new_post_id );

		return $new_post_id;

	}

	/**
	 * Copy the taxonomies of a post to another post.
	 *
	 * @param mixed $id The ID(s) of the object(s) to retrieve.
	 * @param mixed $new_id The object to relate to.
	 * @param mixed $post_type The post type.
	 */
	private function cloned_duplicate_post_taxonomies( $id, $new_id, $post_type ) {

		$exclude    = array_filter( apply_filters( 'woocommerce_duplicate_product_exclude_taxonomies', [] ) );
		$taxonomies = array_diff( get_object_taxonomies( $post_type ), $exclude );

		foreach ( $taxonomies as $taxonomy ) {
			$post_terms       = wp_get_object_terms( $id, $taxonomy );
			$post_terms_count = count( $post_terms );

			for ( $i = 0; $i < $post_terms_count; $i ++ ) {
				wp_set_object_terms( $new_id, $post_terms[ $i ]->slug, $taxonomy, true );
			}
		}

	}

	/**
	 * Copy the meta information of a post to another post.
	 *
	 * @param mixed $id The post id.
	 * @param mixed $new_id The new post id.
	 */
	private function cloned_duplicate_post_meta( $id, $new_id ) {

		global $wpdb;

		$sql     = "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d";
		$exclude = array_map( 'esc_sql', array_filter( apply_filters( 'woocommerce_duplicate_product_exclude_meta', [ 'total_sales', '_wc_average_rating', '_wc_rating_count', '_wc_review_count', '_sku' ] ) ) );

		if ( count( $exclude ) ) {
			$sql .= " AND meta_key NOT IN ( '" . implode( "','", $exclude ) . "' )";
		}

		$post_meta = $wpdb->get_results( $wpdb->prepare( $sql, absint( $id ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL

		if ( count( $post_meta ) ) {
			$sql_query_sel = [];
			$sql_query     = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";

			foreach ( $post_meta as $post_meta_row ) {
				$sql_query_sel[] = $wpdb->prepare( 'SELECT %d, %s, %s', $new_id, $post_meta_row->meta_key, $post_meta_row->meta_value );
			}

			$sql_query .= implode( ' UNION ALL ', $sql_query_sel );
			$wpdb->query( $sql_query ); // phpcs:ignore WordPress.DB.PreparedSQL
		}

	}

	/**
	 * Copy the options for duplicated products
	 *
	 * @param mixed $new_id The new post id.
	 * @param mixed $post The post object.
	 * @since 1.0
	 */
	public function duplicate_product( $new_id, $post ) {

		$post_id     = themecomplete_get_id( $post );
		$tm_meta     = themecomplete_get_post_meta( $post_id, 'tm_meta', true );
		$tm_meta_cpf = themecomplete_get_post_meta( $post_id, 'tm_meta_cpf', true );

		// WC 2.7x $new_id isn't numeric.
		if ( is_object( $new_id ) ) {
			$new_id = themecomplete_get_id( $new_id );
		}

		if ( ! empty( $tm_meta )
			&& is_array( $tm_meta )
			&& isset( $tm_meta['tmfbuilder'] )
			&& is_array( $tm_meta['tmfbuilder'] )
		) {
			themecomplete_update_post_meta( $new_id, 'tm_meta', THEMECOMPLETE_EPO_HELPER()->recreate_element_ids( $tm_meta ) );
		}

		if ( ! empty( $tm_meta_cpf )
			&& is_array( $tm_meta_cpf )
		) {
			themecomplete_update_post_meta( $new_id, 'tm_meta_cpf', $tm_meta_cpf );
		}

		if ( class_exists( 'WC_Admin_Duplicate_Product' ) ) {

			$dup = new WC_Admin_Duplicate_Product();

			$children_products = get_children( 'post_parent=' . $post_id . '&post_type=' . THEMECOMPLETE_EPO_LOCAL_POST_TYPE );
			if ( $children_products ) {

				$new_rules_ids = [];

				foreach ( $children_products as $child ) {
					if ( is_callable( [ $dup, 'duplicate_product' ] ) ) {
						$new_rules_ids[] = $dup->duplicate_product( $child, $new_id, $child->post_status );
					} else {
						$new_rules_ids[] = $this->cloned_duplicate_product( $child, $new_id, $child->post_status );
					}
				}

				$new_rules_ids = array_filter( $new_rules_ids );

				if ( ! empty( $new_rules_ids ) ) {

					$children_products = get_children( 'post_parent=' . $post_id . '&post_type=product_variation&order=ASC' );

					if ( $children_products ) {

						$old_variations_ids = [];

						foreach ( $children_products as $child ) {
							$old_variations_ids[ $child->menu_order ] = themecomplete_get_id( $child );
						}

						$old_variations_ids = array_filter( $old_variations_ids );
						$children_products  = get_children( 'post_parent=' . $new_id . '&post_type=product_variation&order=ASC' );

						if ( $children_products ) {

							$new_variations_ids = [];

							foreach ( $children_products as $child ) {
								$new_variations_ids[ $child->menu_order ] = themecomplete_get_id( $child );
							}

							$new_variations_ids = array_filter( $new_variations_ids );

							if ( ! empty( $old_variations_ids ) && ! empty( $new_variations_ids ) ) {

								foreach ( $new_rules_ids as $rule_id ) {

									$_regular_price    = themecomplete_get_post_meta( $rule_id, '_regular_price', true );
									$new_regular_price = [];

									/*
									 * $key = attirbute
									 * $k = variation
									 * $v = price
									 */
									if ( is_array( $_regular_price ) ) {
										foreach ( $_regular_price as $key => $value ) {
											if ( is_array( $value ) ) {
												foreach ( $value as $k => $v ) {
													if ( ! isset( $new_regular_price[ $key ] ) ) {
														$new_regular_price[ $key ] = [];
													}
													$_new_key = array_search( $k, $old_variations_ids ); // phpcs:ignore WordPress.PHP.StrictInArray
													if ( false !== $_new_key && null !== $_new_key ) {
														$_new_key = $new_variations_ids[ $_new_key ];
													}
													if ( false !== $_new_key && null !== $_new_key ) {
														$new_regular_price[ $key ][ $_new_key ] = $v;
													}
												}
											}
										}
									}

									update_post_meta( $rule_id, '_regular_price', $new_regular_price );

								}
							}
						}
					}
				}
			}
		}

	}

	/**
	 * Add Admin tab in products
	 *
	 * @param array $tabs The tabs array.
	 * @since 1.0
	 */
	public function register_data_tab( $tabs = [] ) {
		$enable        = false;
		$enabled_roles = get_option( 'tm_epo_global_hide_product_enabled' );
		if ( false !== $enabled_roles ) {
			if ( ! is_array( $enabled_roles ) ) {
				$enabled_roles = [ $enabled_roles ];
			}
			$current_user = wp_get_current_user();
			if ( $current_user instanceof WP_User ) {
				if ( is_super_admin( $current_user->ID ) ) {
					$enable = true;
				} else {
					$roles = $current_user->roles;
					if ( is_array( $roles ) ) {
						foreach ( $roles as $key => $value ) {
							if ( 'administrator' === $value || in_array( $value, $enabled_roles, true ) ) {
								$enable = true;
								break;
							}
						}
					}
				}
			}
		} else {
			// Revert to default functionality if the tm_epo_global_hide_product_enabled
			// does not exist.
			$enable = true;
		}
		if ( $enable ) {
			// Adds the new tab.
			$tabs['tc-admin-extra-product-options'] = [
				'label'  => esc_html__( 'Extra Product Options', 'woocommerce-tm-extra-product-options' ),
				'target' => 'tc-admin-extra-product-options',
				'class'  => [ 'tc-epo-woocommerce-tab', 'hide_if_grouped' ],
			];
		}

		return $tabs;

	}

	/**
	 * Add data panel in products
	 *
	 * @since 1.0
	 */
	public function register_data_panels() {

		global $post, $post_id, $tm_is_ajax;
		$tm_is_ajax = false;
		include 'views/html-tm-global-epo.php';

	}

	/**
	 * Check if we are in a product screen
	 *
	 * @since 1.0
	 */
	public function in_product() {

		$screen = get_current_screen();
		if ( in_array( $screen->id, apply_filters( 'wc_epo_admin_in_product', [ 'product', 'edit-product', 'shop_order' ] ), true ) ) {
			return true;
		}

		return false;

	}

	/**
	 * Check if we are in a shop order
	 *
	 * @since 5.0.2
	 */
	public function in_shop_order() {
		$in_shop_order = false;
		// required as this runs on ajax order as well.
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( $screen && in_array( $screen->id, apply_filters( 'wc_epo_admin_in_shop_order_screen_ids', [ 'edit-shop_order', 'shop_order' ] ), true ) ) {
				$in_shop_order = true;
			}
		}

		return apply_filters( 'wc_epo_admin_in_shop_order', $in_shop_order );

	}

	/**
	 * Check if we are in settings page
	 *
	 * @since 1.0
	 */
	public function in_settings_page() {

		$wc_screen_id = sanitize_title( esc_attr__( 'WooCommerce', 'woocommerce' ) );
		$screen       = get_current_screen();
		$wcsids       = $wc_screen_id . '_page_wc-settings';

		if ( isset( $_GET['tab'] ) && THEMECOMPLETE_EPO_ADMIN_SETTINGS_ID === $_GET['tab'] && in_array( $screen->id, [ $wcsids ], true ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return true;
		}

		if ( sanitize_title( esc_attr__( 'Extra Product Options', 'woocommerce-tm-extra-product-options' ) ) . '_page_tcepo-settings' === $screen->id ) {
			return true;
		}

		return false;

	}

	/**
	 * Register css styles
	 *
	 * @since 1.0
	 */
	public function register_admin_styles() {

		$ext = '.min';

		if ( 'dev' === THEMECOMPLETE_EPO()->tm_epo_global_js_css_mode ) {
			$ext = '';
		}

		if ( $this->in_shop_order() ) {
			wp_enqueue_style( 'themecomplete-epo-admin-order', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/admin/tm-epo-admin-order' . $ext . '.css', false, THEMECOMPLETE_EPO_VERSION );
		} elseif ( $this->in_product() ) {
			wp_enqueue_style( 'themecomplete-epo-admin', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/admin/tm-epo-admin' . $ext . '.css', false, THEMECOMPLETE_EPO_VERSION );
			THEMECOMPLETE_EPO_ADMIN_GLOBAL()->register_admin_styles( 1 );
		} elseif ( $this->in_settings_page() ) {
			remove_all_actions( 'admin_notices' );
			if ( class_exists( 'WC_Admin_Notices' ) && method_exists( 'WC_Admin_Notices', 'remove_all_notices' ) ) {
				WC_Admin_Notices::remove_all_notices();
			}
			// The version of the fontawesome is customized.
			wp_enqueue_style( 'themecomplete-fontawesome', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/fontawesome' . $ext . '.css', false, '5.12', 'screen' );
			wp_enqueue_style( 'themecomplete-animate', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/animate' . $ext . '.css', false, THEMECOMPLETE_EPO_VERSION );
			wp_enqueue_style( 'toastr', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/admin/toastr' . $ext . '.css', false, '2.1.4', 'screen' );
			wp_enqueue_style( 'themecomplete-epo-admin-font', THEMECOMPLETE_EPO_ADMIN_GLOBAL()->admin_font_url(), [], '1.0.0' );
			wp_enqueue_style( 'themecomplete-epo-admin-settings', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/css/admin/tm-epo-admin-settings' . $ext . '.css', false, THEMECOMPLETE_EPO_VERSION );
		}
	}

	/**
	 * Add scripts
	 *
	 * @since 1.0
	 */
	public function register_admin_scripts() {

		global $wp_query, $post;

		$ext = '.min';

		if ( THEMECOMPLETE_EPO()->tm_epo_global_js_css_mode === 'dev' ) {
			$ext = '';
		}

		$this->register_admin_styles();

		if ( $this->in_shop_order() ) {
			wp_register_script( 'themecomplete-epo-admin-order', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/admin/tm-epo-admin-order' . $ext . '.js', [ 'jquery' ], THEMECOMPLETE_EPO_VERSION, true );
			wp_enqueue_script( 'themecomplete-epo-admin-order' );
		} elseif ( $this->in_product() ) {
			wp_register_script( 'themecomplete-epo-admin-metaboxes', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/admin/tm-epo-admin' . $ext . '.js', [ 'jquery' ], THEMECOMPLETE_EPO_VERSION, true );
			$params = [
				'post_id'                => isset( $post->ID ) ? sprintf( '%d', $post->ID ) : '',
				'plugin_url'             => THEMECOMPLETE_EPO_PLUGIN_URL,
				// WPML 3.3.x fix.
				'ajax_url'               => strtok( admin_url( 'admin-ajax' . '.php' ), '?' ), // phpcs:ignore Generic.Strings.UnnecessaryStringConcat
				'add_tm_epo_nonce'       => wp_create_nonce( 'add-tm-epo' ),
				'delete_tm_epo_nonce'    => wp_create_nonce( 'delete-tm-epo' ),
				'check_attributes_nonce' => wp_create_nonce( 'check_attributes' ),
				'load_tm_epo_nonce'      => wp_create_nonce( 'load-tm-epo' ),
				'i18n_no_variations'     => esc_html__( 'There are no saved variations yet.', 'woocommerce-tm-extra-product-options' ),
				'i18n_max_tmcp'          => esc_html__( 'You cannot add any more extra options.', 'woocommerce-tm-extra-product-options' ),
				'i18n_remove_tmcp'       => esc_html__( 'Are you sure you want to remove this option?', 'woocommerce-tm-extra-product-options' ),
				'i18n_missing_tmcp'      => esc_html__( 'Before adding Extra Product Options, add and save some attributes on the Attributes tab.', 'woocommerce-tm-extra-product-options' ),
				'i18n_fixed_type'        => esc_html__( 'Fixed amount', 'woocommerce-tm-extra-product-options' ),
				'i18n_percent_type'      => esc_html__( 'Percent of the original price', 'woocommerce-tm-extra-product-options' ),
				'i18n_error_title'       => esc_html__( 'Error', 'woocommerce-tm-extra-product-options' ),
			];
			wp_localize_script( 'themecomplete-epo-admin-metaboxes', 'TMEPOADMINJS', $params );
			wp_enqueue_script( 'themecomplete-epo-admin-metaboxes' );

			THEMECOMPLETE_EPO_ADMIN_GLOBAL()->register_admin_scripts( 1 );
		} elseif ( $this->in_settings_page() ) {
			wp_register_script( 'themecomplete-api', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/tm-api' . $ext . '.js', '', THEMECOMPLETE_EPO_VERSION, true );
			wp_register_script( 'jquery-tcfloatbox', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.tcfloatbox' . $ext . '.js', '', THEMECOMPLETE_EPO_VERSION, true );
			wp_register_script( 'jquery-tctooltip', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.tctooltip' . $ext . '.js', '', THEMECOMPLETE_EPO_VERSION, true );
			wp_register_script( 'themecomplete-tabs', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/jquery.tctabs' . $ext . '.js', '', THEMECOMPLETE_EPO_VERSION, true );
			wp_register_script( 'toastr', THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/admin/toastr' . $ext . '.js', '', '2.1.4', true );
			wp_register_script(
				'themecomplete-epo-admin-settings',
				THEMECOMPLETE_EPO_PLUGIN_URL . '/assets/js/admin/tm-epo-admin-settings' . $ext . '.js',
				[
					'jquery',
					'json2',
					'themecomplete-api',
					'toastr',
					'themecomplete-tabs',
					'jquery-tcfloatbox',
					'jquery-tctooltip',
				],
				THEMECOMPLETE_EPO_VERSION,
				true
			);
			$params = [
				'plugin_url'            => THEMECOMPLETE_EPO_PLUGIN_URL,
				'settings_nonce'        => wp_create_nonce( 'settings-nonce' ),
				// WPML 3.3.x fix.
				'ajax_url'              => strtok( admin_url( 'admin-ajax' . '.php' ), '?' ), // phpcs:ignore Generic.Strings.UnnecessaryStringConcat
				'i18n_invalid_request'  => esc_html__( 'Invalid request!', 'woocommerce-tm-extra-product-options' ),
				'i18n_epo'              => esc_html__( 'Extra Product Options', 'woocommerce-tm-extra-product-options' ),
				'i18n_mn_delete_folder' => esc_html__( 'Are you sure you want to delete this folder and all of its contents?', 'woocommerce-tm-extra-product-options' ),
				'i18n_mn_delete_file'   => esc_html__( 'Are you sure you want to delete this file?', 'woocommerce-tm-extra-product-options' ),
				'i18n_error_title'      => esc_html__( 'Error', 'woocommerce-tm-extra-product-options' ),
				'i18n_reset_settings'   => esc_html__( 'Are you sure you want to reset the settings?', 'woocommerce-tm-extra-product-options' ),
				'i18n_yes'              => esc_html__( 'Yes', 'woocommerce-tm-extra-product-options' ),
				'i18n_no'               => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ),
				'i18n_cancel'           => esc_html__( 'Cancel', 'woocommerce-tm-extra-product-options' ),
				'i18n_constant_name'    => esc_html__( 'Constant name', 'woocommerce-tm-extra-product-options' ),
				'i18n_constant_value'   => esc_html__( 'Constant value', 'woocommerce-tm-extra-product-options' ),
				'i18n_must_concent'     => esc_html__( 'Please check the consent checkbox to give your permission to send your data to the server and try again.', 'woocommerce-tm-extra-product-options' ),
				'i18n_sending_data'     => esc_html__( 'Connecting to activation server ...', 'woocommerce-tm-extra-product-options' ),
			];
			wp_localize_script( 'themecomplete-epo-admin-settings', 'TMEPOADMINSETTINGSJS', $params );
			wp_enqueue_script( 'themecomplete-epo-admin-settings' );
		}

	}

	/**
	 * Delete normal mode options when a product is deleted
	 *
	 * @param integer $id The post id.
	 * @since 1.0
	 */
	public function delete_post( $id ) {

		global $woocommerce, $wpdb;

		if ( ! current_user_can( 'delete_posts' ) ) {
			return;
		}

		if ( $id > 0 ) {
			$post_type = get_post_type( $id );
			switch ( $post_type ) {
				case 'product':
					$child_product_variations = get_children( 'post_parent=' . $id . '&post_type=' . THEMECOMPLETE_EPO_LOCAL_POST_TYPE );
					if ( $child_product_variations ) {
						foreach ( $child_product_variations as $child ) {
							wp_delete_post( $child->ID, true );
						}
					}
					wc_delete_product_transients();
					break;
				case THEMECOMPLETE_EPO_LOCAL_POST_TYPE:
					wc_delete_product_transients();
					break;
			}
		}
	}

	/**
	 * Remove Extra Product Options via remove button
	 *
	 * @since 1.0
	 */
	public function remove_price() {

		if ( ! current_user_can( 'delete_posts' ) ) {
			return;
		}

		check_ajax_referer( 'delete-tm-epo', 'security' );

		if ( isset( $_POST['tmcpid'] ) ) {
			$tmcpid = absint( wp_unslash( $_POST['tmcpid'] ) );
			$tmcp   = get_post( $tmcpid );

			if ( $tmcp && THEMECOMPLETE_EPO_LOCAL_POST_TYPE === $tmcp->post_type ) {
				wp_delete_post( $tmcpid );
			}
		}

		die();

	}

	/**
	 * Remove Extra Product Options via remove button
	 *
	 * @since 1.0
	 */
	public function remove_prices() {

		if ( ! current_user_can( 'delete_posts' ) ) {
			return;
		}

		check_ajax_referer( 'delete-tm-epo', 'security' );

		if ( isset( $_POST['tmcpids'] ) ) {
			$tmcpids = (array) wp_unslash( $_POST['tmcpids'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			foreach ( $tmcpids as $tmcpid ) {
				$tmcp = get_post( $tmcpid );
				if ( $tmcp && THEMECOMPLETE_EPO_LOCAL_POST_TYPE === $tmcp->post_type ) {
					wp_delete_post( $tmcpid );
				}
			}
		}

		die();

	}

	/**
	 * Load Extra Product Options
	 *
	 * @since 1.0
	 */
	public function load_prices() {

		check_ajax_referer( 'load-tm-epo', 'security' );

		global $post, $post_id, $tm_is_ajax;

		$tm_is_ajax = true;

		if ( isset( $_POST['post_id'] ) ) {
			$post_id = absint( $_POST['post_id'] ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			include 'views/html-tm-epo.php';
		}

		die();

	}

	/**
	 * Get Attributes
	 *
	 * @param string $value The currentvalue.
	 * @param string $key The current key.
	 * @param array  $attributes The attributes array.
	 * @see   add_price
	 * @since 4.8.6
	 */
	public function alter_attributes( &$value, $key, $attributes ) {
		if ( $attributes[ $value ]['is_variation'] ) {
			$value = '';
		}
	}

	/**
	 * Add Extra Product Options via add button
	 *
	 * @since 1.0
	 */
	public function add_price() {

		check_ajax_referer( 'add-tm-epo', 'security' );

		if ( isset( $_POST['post_id'] ) && isset( $_POST['loop'] ) && isset( $_POST['att_id'] ) ) {
			$post_id = absint( wp_unslash( $_POST['post_id'] ) );
			$loop    = absint( wp_unslash( $_POST['loop'] ) );
			$att_id  = absint( wp_unslash( $_POST['att_id'] ) );

			$attributes  = themecomplete_get_attributes( $post_id );
			$_attributes = array_keys( $attributes );
			array_walk( $_attributes, [ $this, 'alter_attributes' ], $attributes );

			// $_attributes holds the number of all available attributes we can use.
			$_attributes = array_diff( $_attributes, [ '' ] );

			// check if we can insert a post.
			$args = [
				'post_type'   => THEMECOMPLETE_EPO_LOCAL_POST_TYPE,
				'post_status' => [ 'private', 'publish' ],
				'numberposts' => -1,
				'orderby'     => 'menu_order',
				'order'       => 'asc',
				'post_parent' => $post_id,
				// phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_query'  => [
					[
						'key'     => 'tmcp_attribute',
						'value'   => $_attributes,
						'compare' => 'IN',
					],
				],
			];

			$tmepos = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );

			if ( is_array( $tmepos ) && is_array( $_attributes ) && count( $tmepos ) >= count( $_attributes ) ) {
				die( 'max' );
			}

			// else add a new extra option.
			$tmcp = [
				'post_title'   => 'Product #' . $post_id . ' Extra Product Option',
				'post_content' => '',
				'post_status'  => 'publish',
				'post_parent'  => $post_id,
				'post_author'  => get_current_user_id(),
				'post_type'    => THEMECOMPLETE_EPO_LOCAL_POST_TYPE,
			];

			$tmcp_id = wp_insert_post( $tmcp );

			if ( $tmcp_id ) {
				update_post_meta( $tmcp_id, 'tmcp_attribute', $att_id );
				update_post_meta( $tmcp_id, 'tmcp_attribute_is_taxonomy', $attributes[ $att_id ]['is_taxonomy'] );
				$tmcp_post_status = 'publish';
				$tmcp_data        = themecomplete_get_post_meta( $tmcp_id );
				$tmcp_required    = 0;
				$tmcp_hide_price  = 0;
				$tmcp_limit       = '';

				// Get Attributes.
				$attributes = themecomplete_get_attributes( $post_id );

				// Get parent data.
				$parent_data = [
					'id'         => $post_id,
					'attributes' => $attributes,
				];

				// Get Variations.
				$args       = [
					'post_type'   => 'product_variation',
					'post_status' => [ 'private', 'publish' ],
					'numberposts' => -1,
					'orderby'     => 'menu_order',
					'order'       => 'asc',
					'post_parent' => $post_id,
				];
				$variations = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );

				include 'views/html-tm-epo-admin.php';
			}
		}

		die();

	}

	/**
	 * Save Extra Product Options meta data
	 *
	 * @param integer $post_id The post id.
	 * @since 1.0
	 */
	public function save_meta( $post_id ) {
		global $woocommerce, $wpdb;

		$attributes = themecomplete_get_attributes( $post_id );

		if ( isset( $_POST['product-type'] ) || isset( $_POST['variable_sku'] ) || isset( $_POST['_sku'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$_post_id                = isset( $_POST['tmcp_post_id'] ) ? wp_unslash( $_POST['tmcp_post_id'] ) : []; // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$tmcp_regular_price      = isset( $_POST['tmcp_regular_price'] ) ? wp_unslash( $_POST['tmcp_regular_price'] ) : []; // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$tmcp_regular_price_type = isset( $_POST['tmcp_regular_price_type'] ) ? wp_unslash( $_POST['tmcp_regular_price_type'] ) : []; // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$tmcp_enabled            = isset( $_POST['tmcp_enabled'] ) ? wp_unslash( $_POST['tmcp_enabled'] ) : []; // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$tmcp_required           = isset( $_POST['tmcp_required'] ) ? wp_unslash( $_POST['tmcp_required'] ) : []; // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$tmcp_hide_price         = isset( $_POST['tmcp_hide_price'] ) ? wp_unslash( $_POST['tmcp_hide_price'] ) : []; // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$tmcp_limit              = isset( $_POST['tmcp_limit'] ) ? wp_unslash( $_POST['tmcp_limit'] ) : []; // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$tmcp_menu_order         = isset( $_POST['tmcp_menu_order'] ) ? wp_unslash( $_POST['tmcp_menu_order'] ) : []; // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$tmcp_attribute          = isset( $_POST['tmcp_attribute'] ) ? wp_unslash( $_POST['tmcp_attribute'] ) : []; // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$tmcp_type               = isset( $_POST['tmcp_type'] ) ? wp_unslash( $_POST['tmcp_type'] ) : []; // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$tm_meta_cpf             = isset( $_POST['tm_meta_cpf'] ) ? wp_unslash( $_POST['tm_meta_cpf'] ) : []; // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			// update custom product settings.
			themecomplete_update_post_meta( $post_id, 'tm_meta_cpf', $tm_meta_cpf );

			if ( isset( $_POST['tm_meta_serialized'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				$tm_metas = wp_unslash( $_POST['tm_meta_serialized'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification
				$tm_metas = rawurldecode( $tm_metas );
				$tm_metas = nl2br( $tm_metas );
				$tm_metas = json_decode( $tm_metas, true );

				if ( $tm_metas || ( is_array( $tm_metas ) ) ) {
					$import = get_transient( 'tc_import_csv' );
					if ( false !== $import ) {
						if ( ! empty( $import ) ) {
							$import_override = get_transient( 'tc_import_override' );
							if ( false !== $import_override ) {
								unset( $tm_metas['tm_meta']['tmfbuilder'] );
								$tm_metas = THEMECOMPLETE_EPO_ADMIN_GLOBAL()->import_array_merge( $tm_metas, $import );
								delete_transient( 'tc_import_override' );
							} else {
								$tm_metas = THEMECOMPLETE_EPO_ADMIN_GLOBAL()->import_array_merge( $tm_metas, $import );
							}
							delete_transient( 'tc_import_csv' );
						}
					}

					$old_data = themecomplete_get_post_meta( $post_id, 'tm_meta', true );

					if ( ! empty( $tm_metas ) && is_array( $tm_metas ) && isset( $tm_metas['tm_meta'] ) && is_array( $tm_metas['tm_meta'] ) ) {
						$tm_meta = $tm_metas['tm_meta'];
						themecomplete_save_post_meta( $post_id, $tm_meta, $old_data, 'tm_meta' );
					} else {
						themecomplete_save_post_meta( $post_id, false, $old_data, 'tm_meta' );
					}
				}
			} elseif ( isset( $_POST['tm_meta_serialized_wpml'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				$tm_metas = wp_unslash( $_POST['tm_meta_serialized_wpml'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification
				$tm_metas = rawurldecode( $tm_metas );
				$tm_metas = nl2br( $tm_metas );
				$tm_metas = json_decode( $tm_metas, true );
				if ( $tm_metas ) {

					$old_data = themecomplete_get_post_meta( $post_id, 'tm_meta_wpml', true );

					if ( ! empty( $tm_metas ) && is_array( $tm_metas ) && isset( $tm_metas['tm_meta'] ) && is_array( $tm_metas['tm_meta'] ) ) {
						$tm_meta = $tm_metas['tm_meta'];
						themecomplete_save_post_meta( $post_id, $tm_meta, $old_data, 'tm_meta_wpml' );
					} else {
						themecomplete_save_post_meta( $post_id, false, $old_data, 'tm_meta_wpml' );
					}
				}
			}

			if ( ! empty( $_post_id ) ) {
				global $wpdb;
				if ( ! is_array( $_post_id ) ) {
					$_post_id = [ $_post_id ];
				}
				$max_loop = max( array_keys( $_post_id ) );
				for ( $i = 0; $i <= $max_loop; $i ++ ) {

					if ( ! isset( $_post_id[ $i ] ) ) {
						continue;
					}

					$tmcp_id = absint( $_post_id[ $i ] );

					if ( $tmcp_id ) {
						// Enabled or disabled.
						$post_status = isset( $tmcp_enabled[ $i ] ) ? 'publish' : 'private';

						// Generate a useful post title.
						/* translators: %1 option id # option title */
						$post_title = sprintf( esc_html__( 'Extra Product Option #%1$s of %2$s', 'woocommerce-tm-extra-product-options' ), absint( $tmcp_id ), esc_html( get_the_title( $post_id ) ) );

						$data  = wp_slash(
							[
								'post_status' => $post_status,
								'post_title'  => $post_title,
								'menu_order'  => $tmcp_menu_order[ $i ],
							]
						);
						$data  = wp_unslash( $data );
						$where = [ 'ID' => $tmcp_id ];
						if ( false === $wpdb->update( $wpdb->posts, $data, $where ) ) {
							return new WP_Error( 'db_update_error', esc_html__( 'Could not update post in the database', 'woocommerce-tm-extra-product-options' ), $wpdb->last_error );
						}

						// Price handling.
						$clean_prices      = [];
						$clean_prices_type = [];
						if ( isset( $tmcp_regular_price[ $i ] ) ) {
							foreach ( $tmcp_regular_price[ $i ] as $key => $value ) {
								foreach ( $value as $k => $v ) {
									if ( '' !== $v ) {
										$clean_prices[ $key ][ $k ] = wc_format_decimal( $v );
									}
								}
							}
						}
						if ( isset( $tmcp_regular_price_type[ $i ] ) ) {
							foreach ( $tmcp_regular_price_type[ $i ] as $key => $value ) {
								foreach ( $value as $k => $v ) {
									$clean_prices_type[ $key ][ $k ] = $v;
								}
							}
						}

						// Update post meta.
						$regular_price      = $clean_prices;
						$regular_price_type = $clean_prices_type;
						update_post_meta( $tmcp_id, '_regular_price', $regular_price );
						update_post_meta( $tmcp_id, '_regular_price_type', $regular_price_type );

						$post_required   = isset( $tmcp_required[ $i ] ) ? 1 : '';
						$post_hide_price = isset( $tmcp_hide_price[ $i ] ) ? 1 : '';
						$post_limit      = isset( $tmcp_limit[ $i ] ) ? $tmcp_limit[ $i ] : '';
						update_post_meta( $tmcp_id, 'tmcp_required', $post_required );
						update_post_meta( $tmcp_id, 'tmcp_hide_price', $post_hide_price );
						update_post_meta( $tmcp_id, 'tmcp_limit', $post_limit );
						update_post_meta( $tmcp_id, 'tmcp_attribute', $tmcp_attribute[ $i ] );
						update_post_meta( $tmcp_id, 'tmcp_attribute_is_taxonomy', $attributes[ $tmcp_attribute[ $i ] ]['is_taxonomy'] );
						update_post_meta( $tmcp_id, 'tmcp_type', $tmcp_type[ $i ] );

					}
				}
			}
		}
	}
}
