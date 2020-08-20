<?php
/**
 * Extra Product Options admin setup
 *
 * @package Extra Product Options/Admin
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_Admin_base {

	public $plugin_url;

	/**
	 * The single instance of the class
	 *
	 * @since 1.0
	 */
	protected static $_instance = NULL;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		$this->plugin_url = untrailingslashit( plugins_url( '/', dirname( __FILE__ ) ) );

		// Add Admin tab in products 
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'register_data_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'register_data_panels' ) );

		// Load css and javascript files 
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

		// Remove Extra Product Options from deleted Products 
		add_action( 'delete_post', array( $this, 'delete_post' ) );

		// Remove Extra Product Options via remove button 
		add_action( 'wp_ajax_woocommerce_tm_remove_epo', array( $this, 'remove_price' ) );
		add_action( 'wp_ajax_woocommerce_tm_remove_epos', array( $this, 'remove_prices' ) );

		// Load Extra Product Options 
		add_action( 'wp_ajax_woocommerce_tm_load_epos', array( $this, 'load_prices' ) );

		// Add Extra Product Options via add button 
		add_action( 'wp_ajax_woocommerce_tm_add_epo', array( $this, 'add_price' ) );

		// Save Extra Product Options meta data 
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_meta' ), 50 );

		// Duplicate TM Extra Product Options 
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7.0', '<' ) ) {
			add_action( 'woocommerce_duplicate_product', array( $this, 'duplicate_product' ), 50, 2 );
		} else {
			// WC 2.7x
			add_action( 'woocommerce_product_duplicate', array( $this, 'duplicate_product' ), 50, 2 );
		}

		// Show action links on the plugin screen 
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
		add_filter( 'plugin_action_links_' . THEMECOMPLETE_EPO_PLUGIN_NAME_HOOK, array( $this, 'plugin_action_links' ) );

		// Display options on admin Order page
		add_action( 'woocommerce_order_item_' . 'line_item' . '_html', array( $this, 'tm_woocommerce_order_item_line_item_html' ), 10, 2 );
		// Update option date on the order upon saving the order on admin Order page
		add_action( 'woocommerce_saved_order_items', array( $this, 'tm_woocommerce_saved_order_items' ), 10, 2 );

		// For settings page
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_css_code', array( $this, 'tm_return_raw' ), 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_js_code', array( $this, 'tm_return_raw' ), 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_separator_cart_text', array( $this, 'tm_return_raw' ), 10, 3 );
		add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_multiple_separator_cart_text', array( $this, 'tm_return_raw' ), 10, 3 );

		add_action( 'woocommerce_json_search_found_products', array( $this, 'woocommerce_json_search_found_products' ), 10, 1 );

	}

	/**
	 * Filter product rearch results in the builder
	 *
	 * @since 5.0
	 */
	public function woocommerce_json_search_found_products( $product ) {
		if ( isset( $_REQUEST["tcmode"] ) && $_REQUEST["tcmode"] === "builder" ) {
			$exclude_ids = ! empty( $_GET['exclude'] ) ? array_map( 'absint', (array) wp_unslash( $_GET['exclude'] ) ) : array();
			foreach ( $exclude_ids as $id ) {
				unset( $product[ $id ] );
			}
		}

		return $product;
	}

	/**
	 * Returns the provided raw value
	 *
	 * @since 1.0
	 */
	public function tm_return_raw( $value, $option, $raw_value ) {
		return $raw_value;
	}

	/**
	 * Update option date on the order upon saving the order on admin Order page
	 *
	 * @since 1.0
	 */
	public function tm_woocommerce_saved_order_items( $order_id = 0, $items = array() ) {

		if ( apply_filters( 'wc_epo_no_saved_order_items', FALSE ) ) {
			return;
		}

		if ( isset( $_POST ) && isset( $_POST['order_status'] ) && $_POST['order_status'] == 'wc-refunded' ) {
			return;
		}

		$legacy_order = 0;

		if ( is_array( $items ) && isset( $items['tm_epo'] ) ) {

			$order              = THEMECOMPLETE_EPO_HELPER()->tm_get_order_object();
			$order_currency     = is_callable( array( $order, 'get_currency' ) ) ? $order->get_currency() : $order->get_order_currency();
			$mt_prefix          = $order_currency;
			$order_items        = $order->get_items();
			$order_taxes        = $order->get_taxes();
			$prices_include_tax = themecomplete_order_get_att( $order, 'prices_include_tax' );

			foreach ( $items['tm_epo'] as $item_id => $epos ) {

				$item_meta     = function_exists( 'wc_get_order_item_meta' ) ? wc_get_order_item_meta( $item_id, '', FALSE ) : $order->get_item_meta( $item_id );
				$qty           = (float) $item_meta['_qty'][0];
				$line_total    = floatval( $item_meta['_line_total'][0] );
				$line_subtotal = isset( $item_meta['_line_subtotal'] ) ? floatval( $item_meta['_line_subtotal'][0] ) : $line_total;
				$has_epo       = is_array( $item_meta )
				                 && isset( $item_meta['_tmcartepo_data'] )
				                 && isset( $item_meta['_tmcartepo_data'][0] )
				                 && isset( $item_meta['_tm_epo'] );

				$has_fee = is_array( $item_meta )
				           && isset( $item_meta['_tmcartfee_data'] )
				           && isset( $item_meta['_tmcartfee_data'][0] );

				$saved_epos = FALSE;
				if ( $has_epo || $has_fee ) {
					$saved_epos = maybe_unserialize( $item_meta['_tmcartepo_data'][0] );
				}

				$do_update = FALSE;

				if ( $saved_epos ) {

					$_product = themecomplete_get_product_from_item( $order_items[ $item_id ], $order );

					foreach ( $epos as $key => $epo ) {

						if ( isset( $items['tm_item_id'] ) && isset( $items['tm_key'] ) && $items['tm_key'] == $key && $items['tm_item_id'] == $item_id ) {

							$option_price_before = $this->order_price_exluding_tax( $saved_epos[ $key ]['price'], $legacy_order, $prices_include_tax, $order, $order_taxes, $order_items, $item_id );
							$line_total          = $line_total - $option_price_before;
							$line_subtotal       = $line_subtotal - $option_price_before;
							unset( $saved_epos[ $key ] );
							$do_update = TRUE;

						} else {

							if ( isset( $epo['quantity'] ) ) {

								$option_price_before = $this->order_price_exluding_tax( $saved_epos[ $key ]['price'], $legacy_order, $prices_include_tax, $order, $order_taxes, $order_items, $item_id );
								$line_total          = $line_total - ( $option_price_before * $qty );
								$line_subtotal       = $line_subtotal - ( $option_price_before * $qty );

								if ( $saved_epos[ $key ]['quantity'] >= 0 ) {
									// No change is there is saved quantity
									// Might change in future versions
								} else {
									$saved_epos[ $key ]['price'] = ( $saved_epos[ $key ]['price'] / $saved_epos[ $key ]['quantity'] ) * $epo['quantity'];
								}
								$saved_epos[ $key ]['quantity'] = $epo['quantity'];

								$option_price_after = $this->order_price_exluding_tax( $saved_epos[ $key ]['price'], $legacy_order, $prices_include_tax, $order, $order_taxes, $order_items, $item_id );
								$line_total         = $line_total + ( $option_price_after * $qty );
								$line_subtotal      = $line_subtotal + ( $option_price_after * $qty );
								$do_update          = TRUE;
							}

							if ( isset( $epo['price'] ) ) {

								$new_currency             = FALSE;
								$_current_currency_prices = $saved_epos[ $key ]['price_per_currency'];

								if ( $mt_prefix !== ''
								     && $_current_currency_prices !== ''
								     && is_array( $_current_currency_prices )
								     && isset( $_current_currency_prices[ $mt_prefix ] )
								     && $_current_currency_prices[ $mt_prefix ] != ''
								) {
									//don't change price as it is currency custom
									$new_currency = TRUE;

								}

								if ( ! $new_currency ) {
									$epo['price'] = $option_price_before = $this->order_price_including_tax( $epo['price'], $legacy_order, $prices_include_tax, $order, $order_taxes, $order_items, $item_id );
									$epo['price'] = apply_filters( 'wc_epo_remove_current_currency_price', $epo['price'], THEMECOMPLETE_EPO()->get_saved_element_price_type( $saved_epos[ $key ] ), get_option( 'woocommerce_currency' ), $order_currency, $_current_currency_prices, isset( $saved_epos[ $key ]['key'] ) ? $saved_epos[ $key ]['key'] : NULL );
								}

								$option_price_before = $this->order_price_exluding_tax( $saved_epos[ $key ]['price'], $legacy_order, $prices_include_tax, $order, $order_taxes, $order_items, $item_id );
								$line_total          = $line_total - ( $option_price_before * $qty );
								$line_subtotal       = $line_subtotal - ( $option_price_before * $qty );

								$saved_epos[ $key ]['price'] = (float) $epo['price'] * (float) $saved_epos[ $key ]['quantity'];
								if ( ! $new_currency ) {
									$saved_epos[ $key ]['price'] = $this->order_price_exluding_tax( $saved_epos[ $key ]['price'], $legacy_order, $prices_include_tax, $order, $order_taxes, $order_items, $item_id );
								}

								$tax_price = $this->order_get_tax_price( $saved_epos[ $key ]['price'], FALSE, $legacy_order, $prices_include_tax, $order, $order_taxes, $order_items, $item_id );

								if ( $new_currency ) {
									$saved_epos[ $key ]['price_per_currency'][ $mt_prefix ] = $saved_epos[ $key ]['price'] + $tax_price;
								}

								$line_total    = $line_total + ( $saved_epos[ $key ]['price'] * $qty );
								$line_subtotal = $line_subtotal + ( $saved_epos[ $key ]['price'] * $qty );

								$saved_epos[ $key ]['price'] = $saved_epos[ $key ]['price'] + $tax_price;

								$do_update = TRUE;

							}

							if ( isset( $epo['value'] ) ) {

								$saved_epos[ $key ]['value'] = $epo['value'];

								if ( isset( $saved_epos[ $key ]['multiple'] ) && isset( $saved_epos[ $key ]['key'] ) ) {

									$current_product_id  = isset( $item_meta['_product_id'][0] ) ? $item_meta['_product_id'][0] : NULL;
									$original_product_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $current_product_id, 'product' ) );

									if ( THEMECOMPLETE_EPO_WPML()->get_lang() == THEMECOMPLETE_EPO_WPML()->get_default_lang() && $original_product_id != $current_product_id ) {
										$current_product_id = $original_product_id;
									}

									if ( $current_product_id ) {

										$get_saved_order_multiple_keys = THEMECOMPLETE_EPO_HELPER()->get_saved_order_multiple_keys( $current_product_id );

										if ( isset( $get_saved_order_multiple_keys[ "options_" . $saved_epos[ $key ]['section'] ] ) ) {
											$new_key = array_search( $epo['value'], $get_saved_order_multiple_keys[ "options_" . $saved_epos[ $key ]['section'] ] );
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

								$do_update = TRUE;

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
	 * @since 1.0
	 */
	public function order_price_including_tax( $price, $legacy_order, $prices_include_tax, $order, $order_taxes, $order_items, $item_id ) {

		$tax_price = $this->order_get_tax_price( $price, FALSE, $legacy_order, $prices_include_tax, $order, $order_taxes, $order_items, $item_id );

		return (float) $price + (float) $tax_price;

	}

	/**
	 * Get price without tax
	 *
	 * $price must be with tax
	 *
	 * @since 1.0
	 */
	public function order_price_exluding_tax( $price, $legacy_order, $prices_include_tax, $order, $order_taxes, $order_items, $item_id ) {

		$tax_price = $this->order_get_tax_price( $price, TRUE, $legacy_order, $prices_include_tax, $order, $order_taxes, $order_items, $item_id );

		return (float) $price - (float) $tax_price;

	}

	/**
	 * Get the tax price
	 *
	 * @since 1.0
	 */
	public function order_get_tax_price( $price, $price_has_tax, $legacy_order, $prices_include_tax, $order, $order_taxes, $order_items, $item_id ) {

		$tax_data  = empty( $legacy_order ) && wc_tax_enabled() ? maybe_unserialize( isset( $order_items[ $item_id ]['line_tax_data'] ) ? $order_items[ $item_id ]['line_tax_data'] : '' ) : FALSE;
		$tax_price = 0;

		if ( ! empty( $tax_data ) && $prices_include_tax ) {

			$tax_based_on = get_option( 'woocommerce_tax_based_on' );

			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7.0', '<' ) ) {
				if ( 'billing' === $tax_based_on ) {
					$country  = $order->billing_country;
					$state    = $order->billing_state;
					$postcode = $order->billing_postcode;
					$city     = $order->billing_city;
				} elseif ( 'shipping' === $tax_based_on ) {
					$country  = $order->shipping_country;
					$state    = $order->shipping_state;
					$postcode = $order->shipping_postcode;
					$city     = $order->shipping_city;
				}
			} else {
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
			}

			// Default to base
			if ( 'base' === $tax_based_on || ! isset( $country ) || empty( $country ) ) {
				$default  = wc_get_base_location();
				$country  = $default['country'];
				$state    = $default['state'];
				$postcode = '';
				$city     = '';
			}

			$tax_class = $order_items[ $item_id ]['tax_class'];
			$tax_rates = WC_Tax::find_rates( array(
				'country'   => $country,
				'state'     => $state,
				'postcode'  => $postcode,
				'city'      => $city,
				'tax_class' => $tax_class,
			) );

			$epo_line_taxes = WC_Tax::calc_tax( (float) $price, $tax_rates, $price_has_tax );

			foreach ( $order_taxes as $tax_item ) {
				$tax_item_id = $tax_item['rate_id'];
				if ( is_callable( array( $tax_item, 'get_rate_id' ) ) ) {
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
	 * @since 1.0
	 */
	public function tm_woocommerce_order_item_line_item_html( $item_id = "", $item = array() ) {

		$order = THEMECOMPLETE_EPO_HELPER()->tm_get_order_object();
		if ($order){
			$order_currency = is_callable( array( $order, 'get_currency' ) ) ? $order->get_currency() : $order->get_order_currency();
		} else {
			$order_currency = get_woocommerce_currency();
		}
		$mt_prefix      = $order_currency;

		$_product    = themecomplete_get_product_from_item( $item, $order );
		$item_meta   = function_exists( 'wc_get_order_item_meta' ) ? wc_get_order_item_meta( $item_id, '', FALSE ) : $order->get_item_meta( $item_id );
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
			if ( THEMECOMPLETE_EPO_WPML()->get_lang() == THEMECOMPLETE_EPO_WPML()->get_default_lang() && $original_product_id != $current_product_id ) {
				$current_product_id = $original_product_id;
			}
			$wpml_translation_by_id = THEMECOMPLETE_EPO_WPML()->get_wpml_translation_by_id( $current_product_id );
		}

		if ( $has_epo ) {

			$epos = maybe_unserialize( $item_meta['_tmcartepo_data'][0] );

			if ( $epos && is_array( $epos ) ) {

				$header_title = esc_html__( 'Extra Product Options', 'woocommerce-tm-extra-product-options' );
				include( 'views/html-tm-epo-order-item-header.php' );

				foreach ( $epos as $key => $epo ) {

					if ( $epo && is_array( $epo ) ) {
						$type         = THEMECOMPLETE_EPO()->get_saved_element_price_type( $epo );
						$new_currency = FALSE;
						if ( isset( $epo['price_per_currency'] ) ) {
							$_current_currency_prices = $epo['price_per_currency'];
							if ( $mt_prefix !== ''
							     && $_current_currency_prices !== ''
							     && is_array( $_current_currency_prices )
							     && isset( $_current_currency_prices[ $mt_prefix ] )
							     && $_current_currency_prices[ $mt_prefix ] != ''
							) {

								$new_currency = TRUE;
								$epo['price'] = $_current_currency_prices[ $mt_prefix ];

							}
						}
						if ( ! $new_currency ) {
							$epo['price'] = apply_filters( 'wc_epo_get_current_currency_price', $epo['price'], $type, TRUE, NULL, $order_currency );
						}

						if ( ! isset( $epo['quantity'] ) ) {
							$epo['quantity'] = 1;
						}

						if ( isset( $wpml_translation_by_id[ $epo['section'] ] ) ) {
							$epo['name'] = $wpml_translation_by_id[ $epo['section'] ];
						}

						// normal (local) mode
						if ( ! isset( $epo['price_per_currency'] ) && taxonomy_exists( $epo['name'] ) ) {
							$epo['name'] = wc_attribute_label( $epo['name'] );
						}

						$epo_name = apply_filters( 'tm_translate', $epo['name'] );

						if ( isset( $wpml_translation_by_id[ "options_" . $epo['section'] ] )
						     && is_array( $wpml_translation_by_id[ "options_" . $epo['section'] ] )
						     && ! empty( $epo['multiple'] )
						     && ! empty( $epo['key'] )
						) {

							$pos = strrpos( $epo['key'], '_' );

							if ( $pos !== FALSE ) {

								$av = array_values( $wpml_translation_by_id[ "options_" . $epo['section'] ] );

								if ( isset( $av[ substr( $epo['key'], $pos + 1 ) ] ) ) {

									$epo['value'] = $av[ substr( $epo['key'], $pos + 1 ) ];

								}

							}

						}

						$display_value = $epo['value'];

						if ( is_array( $epo['value'] ) ) {
							$display_value = array_map( 'html_entity_decode', $display_value, version_compare( phpversion(), '5.4', '<' ) ? ENT_COMPAT : ( ENT_COMPAT | ENT_HTML401 ), 'UTF-8' );
						} else {
							$display_value = html_entity_decode( $display_value, version_compare( phpversion(), '5.4', '<' ) ? ENT_COMPAT : ( ENT_COMPAT | ENT_HTML401 ), 'UTF-8' );
						}

						if ( ! empty( $epo['use_images'] ) && ! empty( $epo['images'] ) && $epo['use_images'] == "images" ) {
							$display_value = '<div class="cpf-img-on-cart"><img alt="' . esc_attr( strip_tags( $epo_name ) ) . '" class="attachment-shop_thumbnail wp-post-image epo-option-image" src="' . apply_filters( "tm_image_url", $epo['images'] ) . '" /></div>' . esc_attr( $display_value );
						}

						$display_value = apply_filters( 'tm_translate', $display_value );

						if ( isset( $epo['element'] ) && isset( $epo['element']['type'] ) && $epo['element']['type'] == 'upload' ) {
							$check = wp_check_filetype( $epo['value'] );
							if ( ! empty( $check['ext'] ) ) {
								$image_exts = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png' );
								if ( in_array( $check['ext'], $image_exts ) ) {
									$display_value = '<a target="_blank" href="' . esc_url( $display_value ) . '"><span class="cpf-img-on-cart"><img alt="' . esc_attr( strip_tags( $epo_name ) ) . '" class="attachment-shop_thumbnail wp-post-image epo-option-image epo-upload-image" src="' .
									                 apply_filters( "tm_image_url", $epo['value'] ) . '" /></span></a>';
								}
							}
						}

						if ( ! empty( $epo['multiple_values'] ) ) {
							$display_value_array = explode( $epo['multiple_values'], $display_value );
							$display_value       = "";
							foreach ( $display_value_array as $d => $dv ) {
								$display_value .= '<span class="cpf-data-on-cart">' . $dv . '</span>';
							}
						}


						$epo_value = make_clickable( $display_value );

						if ( isset( $epo['element'] ) && $epo['element']['type'] === 'textarea' ) {
							$epo_value = trim( $epo_value );

							$epo_value = str_replace( array( "\r\n", "\r" ), "\n", $epo_value );

							$epo_value = preg_replace( "/\n\n+/", "\n\n", $epo_value );

							$epo_value = array_map( 'wc_clean', explode( "\n", $epo_value ) );

							$epo_value = implode( "\n", $epo_value );

							$epo_value = wpautop( $epo_value );
						}

						$epo_quantity = ( $epo['quantity'] * (float) $item_meta['_qty'][0] ) . ' <small>(' . $epo['quantity'] . '&times;' . (float) $item_meta['_qty'][0] . ')</small>';
						$epo_quantity = apply_filters( 'wc_epo_html_tm_epo_order_item_epo_quantity', $epo_quantity, $epo['quantity'], $item, $_product );

						if ( apply_filters( 'wc_epo_html_tm_epo_order_item_is_othjer_fee', FALSE, $type ) ) {
							$epo_edit_value    = FALSE;
							$edit_buttons      = FALSE;
							$epo_edit_cost     = FALSE;
							$epo_edit_quantity = FALSE;
							$epo_is_fee        = FALSE;
						} else {
							$epo_edit_value    = TRUE;
							$edit_buttons      = TRUE;
							$epo_edit_cost     = TRUE;
							$epo_edit_quantity = TRUE;
							$epo_is_fee        = FALSE;
						}

						$epo['price'] = floatval( $epo['price'] );
						include( 'views/html-tm-epo-order-item.php' );
					}

				}

			}

		}

		if ( $has_fee ) {

			$epos = maybe_unserialize( $item_meta['_tmcartfee_data'][0] );

			if ( isset( $epos[0] ) ) {
				$epos = $epos[0];
			} else {
				$epos = FALSE;
			}

			if ( $epos && is_array( $epos ) && ! empty( $epos[0] ) ) {

				$header_title = esc_html__( 'Extra Product Options Fees', 'woocommerce-tm-extra-product-options' );
				include( 'views/html-tm-epo-order-item-header.php' );

				foreach ( $epos as $key => $epo ) {

					if ( $epo && is_array( $epo ) ) {
						if ( ! isset( $epo['quantity'] ) ) {
							$epo['quantity'] = 1;
						}
						if ( isset( $wpml_translation_by_id[ $epo['section'] ] ) ) {
							$epo['name'] = $wpml_translation_by_id[ $epo['section'] ];
						}
						if ( isset( $wpml_translation_by_id[ "options_" . $epo['section'] ] ) && is_array( $wpml_translation_by_id[ "options_" . $epo['section'] ] ) && ! empty( $epo['multiple'] ) && ! empty( $epo['key'] ) ) {
							$pos = strrpos( $epo['key'], '_' );
							if ( $pos !== FALSE ) {
								$av = array_values( $wpml_translation_by_id[ "options_" . $epo['section'] ] );
								if ( isset( $av[ substr( $epo['key'], $pos + 1 ) ] ) ) {
									$epo['value'] = $av[ substr( $epo['key'], $pos + 1 ) ];
									if ( ! empty( $epo['use_images'] ) && ! empty( $epo['images'] ) && $epo['use_images'] == "images" ) {
										$epo['value'] = '<div class="cpf-img-on-cart"><img alt="' . esc_attr( strip_tags( $epo['name'] ) ) . '" class="attachment-shop_thumbnail wp-post-image epo-option-image" src="' . apply_filters( "tm_image_url", $epo['images'] ) . '" /></div>' . $epo['value'];
									}
								}
							}
						}

						$epo_name     = apply_filters( 'tm_translate', $epo['name'] );
						$epo_value    = apply_filters( 'tm_translate', $epo['value'] );
						$epo_value    = make_clickable( $epo_value );
						$epo_quantity = ( $epo['quantity'] * (float) $item_meta['_qty'][0] ) . ' <small>(' . $epo['quantity'] . '&times;' . (float) $item_meta['_qty'][0] . ')</small>';
						$epo_quantity = apply_filters( 'wc_epo_html_tm_epo_order_item_epo_quantity', $epo_quantity, $epo['quantity'], $item, $_product );

						$epo_edit_value    = FALSE;
						$edit_buttons      = FALSE;
						$epo_edit_cost     = FALSE;
						$epo_edit_quantity = FALSE;
						$epo_is_fee        = TRUE;
						$epo['price']      = floatval( $epo['price'] );
						include( 'views/html-tm-epo-order-item.php' );

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

		$action_links = array(
			'settings' => '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=' . THEMECOMPLETE_EPO_ADMIN_SETTINGS_ID ) ) . '" aria-label="' . esc_attr__( 'View Extra Product Options settings', 'woocommerce-tm-extra-product-options' ) . '">' . esc_html__( 'Settings', 'woocommerce-tm-extra-product-options' ) . '</a>',
		);

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

			$plugin_name = esc_html__( 'WooCommerce TM Extra Product Options', 'woocommerce-tm-extra-product-options' );
			$row_meta    = array(
				'view-details' => sprintf( '<a href="%s" class="thickbox open-plugin-details-modal" aria-label="%s" data-title="%s">%s</a>',
					esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . THEMECOMPLETE_EPO_FILE_SLUG . '&TB_iframe=true&width=772&height=717' ) ),
					/* translators: %s: Plugin name - WooCommerce TM Extra Product Options. */
					esc_attr( sprintf( esc_html__( 'More information about %s', 'woocommerce-tm-extra-product-options' ), $plugin_name ) ),
					esc_attr( $plugin_name ),
					esc_html__( 'View details', 'woocommerce-tm-extra-product-options' )
				),
				'docs'         => '<a href="' . esc_url( 'https://epo.themecomplete.com/documentation/woocommerce-tm-extra-product-options/index.html' ) . '" aria-label="' . esc_attr__( 'View Extra Product Options documentation', 'woocommerce-tm-extra-product-options' ) . '">' . esc_html__( 'Docs', 'woocommerce-tm-extra-product-options' ) . '</a>',
				'support'      => '<a href="' . esc_url( 'https://support.themecomplete.com/forums/forum/extra-product-options/' ) . '" aria-label="' . esc_attr__( 'Visit premium customer support', 'woocommerce-tm-extra-product-options' ) . '">' . esc_html__( 'Premium Support', 'woocommerce-tm-extra-product-options' ) . '</a>',
			);

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
	 * @param mixed $id
	 *
	 * @return WP_Post|bool
	 * @todo   Returning false? Need to check for it in...
	 * @see    duplicate_product
	 */
	private function get_product_to_duplicate( $id ) {

		$id = absint( $id );

		if ( ! $id ) {
			return FALSE;
		}

		global $wpdb;

		$post = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE ID=%d", $id ) );

		if ( isset( $post->post_type ) && $post->post_type == "revision" ) {
			$id   = $post->post_parent;
			$post = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE ID=%d", $id ) );
		}

		return $post[0];

	}

	/**
	 * Function to create the duplicate of the product.
	 *
	 * @param mixed  $post
	 * @param int    $parent      (default: 0)
	 * @param string $post_status (default: '')
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

		// Insert the new template in the post table
		$wpdb->insert(
			$wpdb->posts,
			array(
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
				'post_mime_type'        => $post->post_mime_type
			)
		);

		$new_post_id = $wpdb->insert_id;

		// Set title for variations
		if ( 'product_variation' === $post->post_type ) {
			$post_title = sprintf( esc_html__( 'Variation #%s of %s', 'woocommerce' ), absint( $new_post_id ), esc_html( get_the_title( $post_parent ) ) );
			$wpdb->update(
				$wpdb->posts,
				array(
					'post_title' => $post_title,
				),
				array(
					'ID' => $new_post_id
				)
			);
		}

		// Set name and GUID
		if ( ! in_array( $post_status, array( 'draft', 'pending', 'auto-draft' ) ) ) {
			$wpdb->update(
				$wpdb->posts,
				array(
					'post_name' => wp_unique_post_slug( sanitize_title( $post_title, $new_post_id ), $new_post_id, $post_status, $post->post_type, $post_parent ),
					'guid'      => get_permalink( $new_post_id ),
				),
				array(
					'ID' => $new_post_id
				)
			);
		}

		// Copy the taxonomies
		$this->cloned_duplicate_post_taxonomies( $post->ID, $new_post_id, $post->post_type );

		// Copy the meta information
		$this->cloned_duplicate_post_meta( $post->ID, $new_post_id );

		// Copy the children (variations)
		$exclude = apply_filters( 'woocommerce_duplicate_product_exclude_children', FALSE );

		if ( ! $exclude && ( $children_products = get_children( 'post_parent=' . $post->ID . '&post_type=product_variation' ) ) ) {
			foreach ( $children_products as $child ) {
				$this->cloned_duplicate_product( $this->get_product_to_duplicate( $child->ID ), $new_post_id, $child->post_status );
			}
		}

		// Clear cache
		clean_post_cache( $new_post_id );

		return $new_post_id;

	}

	/**
	 * Copy the taxonomies of a post to another post.
	 *
	 * @param mixed $id
	 * @param mixed $new_id
	 * @param mixed $post_type
	 */
	private function cloned_duplicate_post_taxonomies( $id, $new_id, $post_type ) {

		$exclude    = array_filter( apply_filters( 'woocommerce_duplicate_product_exclude_taxonomies', array() ) );
		$taxonomies = array_diff( get_object_taxonomies( $post_type ), $exclude );

		foreach ( $taxonomies as $taxonomy ) {
			$post_terms       = wp_get_object_terms( $id, $taxonomy );
			$post_terms_count = sizeof( $post_terms );

			for ( $i = 0; $i < $post_terms_count; $i ++ ) {
				wp_set_object_terms( $new_id, $post_terms[ $i ]->slug, $taxonomy, TRUE );
			}
		}

	}

	/**
	 * Copy the meta information of a post to another post.
	 *
	 * @param mixed $id
	 * @param mixed $new_id
	 */
	private function cloned_duplicate_post_meta( $id, $new_id ) {

		global $wpdb;

		$sql     = $wpdb->prepare( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d", absint( $id ) );
		$exclude = array_map( 'esc_sql', array_filter( apply_filters( 'woocommerce_duplicate_product_exclude_meta', array( 'total_sales', '_wc_average_rating', '_wc_rating_count', '_wc_review_count', '_sku' ) ) ) );

		if ( sizeof( $exclude ) ) {
			$sql .= " AND meta_key NOT IN ( '" . implode( "','", $exclude ) . "' )";
		}

		$post_meta = $wpdb->get_results( $sql );

		if ( sizeof( $post_meta ) ) {
			$sql_query_sel = array();
			$sql_query     = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";

			foreach ( $post_meta as $post_meta_row ) {
				$sql_query_sel[] = $wpdb->prepare( "SELECT %d, %s, %s", $new_id, $post_meta_row->meta_key, $post_meta_row->meta_value );
			}

			$sql_query .= implode( " UNION ALL ", $sql_query_sel );
			$wpdb->query( $sql_query );
		}

	}

	/**
	 * Copy the options for duplicated products
	 *
	 * @since 1.0
	 */
	public function duplicate_product( $new_id, $post ) {

		$post_id     = themecomplete_get_id( $post );
		$tm_meta     = themecomplete_get_post_meta( $post_id, 'tm_meta', TRUE );
		$tm_meta_cpf = themecomplete_get_post_meta( $post_id, 'tm_meta_cpf', TRUE );

		// WC 2.7x $new_id isn't numeric
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

			if ( $children_products = get_children( 'post_parent=' . $post_id . '&post_type=' . THEMECOMPLETE_EPO_LOCAL_POST_TYPE ) ) {

				$new_rules_ids = array();

				foreach ( $children_products as $child ) {
					if ( is_callable( array( $dup, 'duplicate_product' ) ) ) {
						$new_rules_ids[] = $dup->duplicate_product( $child, $new_id, $child->post_status );
					} else {
						$new_rules_ids[] = $this->cloned_duplicate_product( $child, $new_id, $child->post_status );
					}
				}

				$new_rules_ids = array_filter( $new_rules_ids );

				if ( ! empty( $new_rules_ids ) ) {

					$children_products = get_children( 'post_parent=' . $post_id . '&post_type=product_variation&order=ASC' );

					if ( $children_products ) {

						$old_variations_ids = array();

						foreach ( $children_products as $child ) {
							$old_variations_ids[ $child->menu_order ] = themecomplete_get_id( $child );
						}

						$old_variations_ids = array_filter( $old_variations_ids );
						$children_products  = get_children( 'post_parent=' . $new_id . '&post_type=product_variation&order=ASC' );

						if ( $children_products ) {

							$new_variations_ids = array();

							foreach ( $children_products as $child ) {
								$new_variations_ids[ $child->menu_order ] = themecomplete_get_id( $child );
							}

							$new_variations_ids = array_filter( $new_variations_ids );

							if ( ! empty( $old_variations_ids ) && ! empty( $new_variations_ids ) ) {

								foreach ( $new_rules_ids as $rule_id ) {

									$_regular_price = get_post_meta( $rule_id, '_regular_price', TRUE );
									/*
									 * $key = attirbute
									 * $k = variation
									 * $v = price
									 */
									$new_regular_price = array();

									if ( is_array( $_regular_price ) ) {
										foreach ( $_regular_price as $key => $value ) {
											if ( is_array( $value ) ) {
												foreach ( $value as $k => $v ) {
													if ( ! isset( $new_regular_price[ $key ] ) ) {
														$new_regular_price[ $key ] = array();
													}
													$_new_key = array_search( $k, $old_variations_ids );
													if ( $_new_key !== FALSE && $_new_key !== NULL ) {
														$_new_key = $new_variations_ids[ $_new_key ];
													}
													if ( $_new_key !== FALSE && $_new_key !== NULL ) {
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
	 * @since 1.0
	 */
	public function register_data_tab( $tabs ) {

		// Adds the new tab
		$tabs['tm_extra_product_options'] = array(
			'label'  => esc_html__( 'Extra Product Options', 'woocommerce-tm-extra-product-options' ),
			'target' => 'tm_extra_product_options',
			'class'  => array( 'tm_epo_class', 'hide_if_grouped' ),
		);

		return $tabs;

	}

	/**
	 * Add data panel in products
	 *
	 * @since 1.0
	 */
	public function register_data_panels() {

		global $post, $post_id, $tm_is_ajax;

		$post_id    = $post->ID;
		$tm_is_ajax = FALSE;
		include( 'views/html-tm-global-epo.php' );

	}

	/**
	 * Check if we are in a product screen
	 *
	 * @since 1.0
	 */
	public function in_product() {

		$screen = get_current_screen();
		if ( in_array( $screen->id, apply_filters( 'wc_epo_admin_in_product', array( 'product', 'edit-product', 'shop_order' ) ) ) ) {
			return TRUE;
		}

		return FALSE;

	}

	/**
	 * Check if we are in a shop order
	 *
	 * @since 5.0.2
	 */
	public function in_shop_order() {

		// required as this runs on ajax order as well
		if ( function_exists('get_current_screen') ){
			$screen = get_current_screen();
			if ( $screen && in_array( $screen->id, apply_filters( 'wc_epo_admin_in_shop_order', array( 'edit-shop_order', 'shop_order' ) ) ) ) {
				return TRUE;
			}	
		}
		

		return FALSE;

	}	

	/**
	 * Check if we are in settings page
	 *
	 * @since 1.0
	 */
	public function in_settings_page() {

		$wc_screen_id = sanitize_title( esc_attr__( 'WooCommerce', 'woocommerce' ) );
		$screen       = get_current_screen();
		$wcsids       = wc_get_screen_ids();

		if ( is_array( $wcsids ) && isset( $wcsids[3] ) && isset( $wcsids[2] ) && $wcsids[2] == $wc_screen_id . '_page_wc-shipping' ) {
			$wcsids = $wcsids[3];
		} elseif ( is_array( $wcsids ) && isset( $wcsids[2] ) ) {
			$wcsids = $wcsids[2];
		} else {
			$wcsids = $wc_screen_id . '_page_wc-settings';
		}

		if ( isset( $_GET['tab'] ) && $_GET['tab'] == THEMECOMPLETE_EPO_ADMIN_SETTINGS_ID && in_array( $screen->id, array( $wcsids ) ) ) {
			return TRUE;
		}

		if ( $screen->id === sanitize_title( esc_attr__( 'Extra Product Options', 'woocommerce-tm-extra-product-options' ) ) . "_page_tcepo-settings" ) {
			return TRUE;
		}

		return FALSE;

	}

	/**
	 * Register css styles
	 *
	 * @since 1.0
	 */
	public function register_admin_styles() {

		$ext = ".min";

		if ( THEMECOMPLETE_EPO()->tm_epo_global_js_css_mode == "dev" ) {
			$ext = "";
		}

		if ( $this->in_shop_order() ) {
			wp_enqueue_style( 'themecomplete-epo-admin-order', $this->plugin_url . '/assets/css/admin/tm-epo-admin-order' . $ext . '.css' );
		} elseif ( $this->in_product() ) {
			wp_enqueue_style( 'themecomplete-epo-admin', $this->plugin_url . '/assets/css/admin/tm-epo-admin' . $ext . '.css' );
			THEMECOMPLETE_EPO_ADMIN_GLOBAL()->register_admin_styles( 1 );
		} elseif ( $this->in_settings_page() ) {
			remove_all_actions( 'admin_notices' );
			if ( class_exists( 'WC_Admin_Notices' ) && method_exists( 'WC_Admin_Notices', 'remove_all_notices' ) ) {
				WC_Admin_Notices::remove_all_notices();
			}
			// The version of the fontawesome is customized
			wp_enqueue_style( 'themecomplete-fontawesome', $this->plugin_url . '/assets/css/fontawesome' . $ext . '.css', FALSE, '5.12', 'screen' );
			wp_enqueue_style( 'themecomplete-animate', $this->plugin_url . '/assets/css/animate' . $ext . '.css' );
			wp_enqueue_style( 'toastr', $this->plugin_url . '/assets/css/admin/toastr' . $ext . '.css', FALSE, '2.1.4', 'screen' );
			wp_enqueue_style( 'themecomplete-epo-admin-font', THEMECOMPLETE_EPO_ADMIN_GLOBAL()->admin_font_url(), array(), '1.0.0' );
			wp_enqueue_style( 'themecomplete-epo-admin-settings', $this->plugin_url . '/assets/css/admin/tm-epo-admin-settings' . $ext . '.css' );
		}

	}

	/**
	 * Add scripts
	 *
	 * @since 1.0
	 */
	public function register_admin_scripts() {

		global $wp_query, $post;

		$ext = ".min";

		if ( THEMECOMPLETE_EPO()->tm_epo_global_js_css_mode == "dev" ) {
			$ext = "";
		}

		$this->register_admin_styles();

		if ( $this->in_shop_order() ) {
			wp_register_script( 'themecomplete-epo-admin-order', $this->plugin_url . '/assets/js/admin/tm-epo-admin-order' . $ext . '.js', array( 'jquery' ), THEMECOMPLETE_EPO_VERSION );
			wp_enqueue_script( 'themecomplete-epo-admin-order' );
		} elseif ( $this->in_product() ) {
			wp_register_script( 'themecomplete-epo-admin-metaboxes', $this->plugin_url . '/assets/js/admin/tm-epo-admin' . $ext . '.js', array( 'jquery' ), THEMECOMPLETE_EPO_VERSION );
			$params = array(
				'post_id'                => isset( $post->ID ) ? $post->ID : '',
				'plugin_url'             => $this->plugin_url,
				'ajax_url'               => strtok( admin_url( 'admin-ajax' . '.php' ), '?' ),//WPML 3.3.x fix
				'add_tm_epo_nonce'       => wp_create_nonce( "add-tm-epo" ),
				'delete_tm_epo_nonce'    => wp_create_nonce( "delete-tm-epo" ),
				'check_attributes_nonce' => wp_create_nonce( "check_attributes" ),
				'load_tm_epo_nonce'      => wp_create_nonce( "load-tm-epo" ),
				'i18n_no_variations'     => esc_html__( 'There are no saved variations yet.', 'woocommerce-tm-extra-product-options' ),
				'i18n_max_tmcp'          => esc_html__( 'You cannot add any more extra options.', 'woocommerce-tm-extra-product-options' ),
				'i18n_remove_tmcp'       => esc_html__( 'Are you sure you want to remove this option?', 'woocommerce-tm-extra-product-options' ),
				'i18n_missing_tmcp'      => esc_html__( 'Before adding Extra Product Options, add and save some attributes on the Attributes tab.', 'woocommerce-tm-extra-product-options' ),
				'i18n_fixed_type'        => esc_html__( 'Fixed amount', 'woocommerce-tm-extra-product-options' ),
				'i18n_percent_type'      => esc_html__( 'Percent of the original price', 'woocommerce-tm-extra-product-options' ),
				'i18n_error_title'       => esc_html__( 'Error', 'woocommerce-tm-extra-product-options' ),
			);
			wp_localize_script( 'themecomplete-epo-admin-metaboxes', 'TMEPOADMINJS', $params );
			wp_enqueue_script( 'themecomplete-epo-admin-metaboxes' );

			THEMECOMPLETE_EPO_ADMIN_GLOBAL()->register_admin_scripts( 1 );
		} elseif ( $this->in_settings_page() ) {
			wp_register_script( 'themecomplete-api', $this->plugin_url . '/assets/js/tm-api' . $ext . '.js', '', THEMECOMPLETE_EPO_VERSION );
			wp_register_script( 'jquery-tcfloatbox', $this->plugin_url . '/assets/js/jquery.tcfloatbox' . $ext . '.js', '', THEMECOMPLETE_EPO_VERSION );
			wp_register_script( 'jquery-tctooltip', $this->plugin_url . '/assets/js/jquery.tctooltip' . $ext . '.js', '', THEMECOMPLETE_EPO_VERSION );
			wp_register_script( 'themecomplete-tabs', $this->plugin_url . '/assets/js/admin/jquery.tctabs' . $ext . '.js', '', THEMECOMPLETE_EPO_VERSION );
			wp_register_script( 'toastr', $this->plugin_url . '/assets/js/admin/toastr' . $ext . '.js', '', '2.1.4' );
			wp_register_script( 'themecomplete-epo-admin-settings', $this->plugin_url . '/assets/js/admin/tm-epo-admin-settings' . $ext . '.js',
				array( 'jquery',
				       'json2',
				       'themecomplete-api',
				       'toastr',
				       'themecomplete-tabs',
				       'jquery-tcfloatbox',
				       'jquery-tctooltip',
				), THEMECOMPLETE_EPO_VERSION );
			$params = array(
				'plugin_url'            => $this->plugin_url,
				'settings_nonce'        => wp_create_nonce( "settings-nonce" ),
				'ajax_url'              => strtok( admin_url( 'admin-ajax' . '.php' ), '?' ),//WPML 3.3.x fix
				'i18n_invalid_request'  => esc_html__( 'Invalid request!', 'woocommerce-tm-extra-product-options' ),
				'i18n_epo'              => esc_html__( 'Extra Product Options', 'woocommerce-tm-extra-product-options' ),
				'i18n_mn_delete_folder' => esc_html__( 'Are you sure you want to delete this folder and all of its contents?', 'woocommerce-tm-extra-product-options' ),
				'i18n_mn_delete_file'   => esc_html__( 'Are you sure you want to delete this file?', 'woocommerce-tm-extra-product-options' ),
				'i18n_error_title'      => esc_html__( 'Error', 'woocommerce-tm-extra-product-options' ),
			);
			wp_localize_script( 'themecomplete-epo-admin-settings', 'TMEPOADMINSETTINGSJS', $params );
			wp_enqueue_script( 'themecomplete-epo-admin-settings' );
		}

	}

	/**
	 * Delete normal mode options when a product is deleted
	 *
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
				case 'product' :
					$child_product_variations = get_children( 'post_parent=' . $id . '&post_type=' . THEMECOMPLETE_EPO_LOCAL_POST_TYPE );
					if ( $child_product_variations ) {
						foreach ( $child_product_variations as $child ) {
							wp_delete_post( $child->ID, TRUE );
						}
					}
					wc_delete_product_transients();
					break;
				case THEMECOMPLETE_EPO_LOCAL_POST_TYPE :
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
		$tmcpid = intval( $_POST['tmcpid'] );
		$tmcp   = get_post( $tmcpid );

		if ( $tmcp && $tmcp->post_type == THEMECOMPLETE_EPO_LOCAL_POST_TYPE ) {
			wp_delete_post( $tmcpid );
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
		$tmcpids = (array) $_POST['tmcpids'];

		foreach ( $tmcpids as $tmcpid ) {
			$tmcp = get_post( $tmcpid );
			if ( $tmcp && $tmcp->post_type == THEMECOMPLETE_EPO_LOCAL_POST_TYPE ) {
				wp_delete_post( $tmcpid );
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

		$tm_is_ajax = TRUE;

		if ( isset( $_POST['post_id'] ) ) {
			$post_id = intval( $_POST['post_id'] );
			include 'views/html-tm-epo.php';
		}

		die();

	}

	/**
	 * Get Attributes
	 *
	 * @see   add_price
	 * @since 4.8.6
	 */
	public function _tm_alter_attributes( &$item1, $key, $attributes ) {
		if ( $attributes[ $item1 ]['is_variation'] ) {
			$item1 = "";
		}
	}

	/**
	 * Add Extra Product Options via add button
	 *
	 * @since 1.0
	 */
	public function add_price() {

		check_ajax_referer( 'add-tm-epo', 'security' );

		$post_id = intval( $_POST['post_id'] );
		$loop    = intval( $_POST['loop'] );
		$att_id  = ( $_POST['att_id'] );

		$attributes  = themecomplete_get_attributes( $post_id );
		$_attributes = array_keys( $attributes );
		array_walk( $_attributes, array( $this, '_tm_alter_attributes' ), $attributes );

		// $_attributes holds the number of all available attributes we can use
		$_attributes = array_diff( $_attributes, array( '' ) );

		// check if we can insert a post
		$args = array(
			'post_type'   => THEMECOMPLETE_EPO_LOCAL_POST_TYPE,
			'post_status' => array( 'private', 'publish' ),
			'numberposts' => - 1,
			'orderby'     => 'menu_order',
			'order'       => 'asc',
			'post_parent' => $post_id,
			'meta_query'  => array(
				array(
					'key'     => 'tmcp_attribute',
					'value'   => $_attributes,
					'compare' => 'IN',
				),
			),
		);

		$tmepos = get_posts( $args );

		if ( is_array( $tmepos ) && is_array( $_attributes ) && count( $tmepos ) >= count( $_attributes ) ) {
			die( 'max' );
		}

		// else add a new extra option
		$tmcp = array(
			'post_title'   => 'Product #' . $post_id . ' Extra Product Option',
			'post_content' => '',
			'post_status'  => 'publish',
			'post_parent'  => $post_id,
			'post_author'  => get_current_user_id(),
			'post_type'    => THEMECOMPLETE_EPO_LOCAL_POST_TYPE,
		);

		$tmcp_id = wp_insert_post( $tmcp );

		if ( $tmcp_id ) {
			update_post_meta( $tmcp_id, 'tmcp_attribute', $att_id );
			update_post_meta( $tmcp_id, 'tmcp_attribute_is_taxonomy', $attributes[ $att_id ]['is_taxonomy'] );
			$tmcp_post_status = 'publish';
			$tmcp_data        = get_post_meta( $tmcp_id );
			$tmcp_required    = 0;
			$tmcp_hide_price  = 0;
			$tmcp_limit       = "";

			// Get Attributes
			$attributes = themecomplete_get_attributes( $post_id );

			// Get parent data
			$parent_data = array(
				'id'         => $post_id,
				'attributes' => $attributes,
			);

			// Get Variations
			$args       = array(
				'post_type'   => 'product_variation',
				'post_status' => array( 'private', 'publish' ),
				'numberposts' => - 1,
				'orderby'     => 'menu_order',
				'order'       => 'asc',
				'post_parent' => $post_id,
			);
			$variations = get_posts( $args );

			include 'views/html-tm-epo-admin.php';
		}

		die();

	}

	/**
	 * Save Extra Product Options meta data
	 *
	 * @since 1.0
	 */
	public function save_meta( $post_id ) {
		global $woocommerce, $wpdb;

		$attributes = themecomplete_get_attributes( $post_id );

		if ( isset( $_POST['product-type'] ) || isset( $_POST['variable_sku'] ) || isset( $_POST['_sku'] ) ) {
			$_post_id                = isset( $_POST['tmcp_post_id'] ) ? $_POST['tmcp_post_id'] : array();
			$tmcp_regular_price      = isset( $_POST['tmcp_regular_price'] ) ? $_POST['tmcp_regular_price'] : array();
			$tmcp_regular_price_type = isset( $_POST['tmcp_regular_price_type'] ) ? $_POST['tmcp_regular_price_type'] : array();
			$tmcp_enabled            = isset( $_POST['tmcp_enabled'] ) ? $_POST['tmcp_enabled'] : array();
			$tmcp_required           = isset( $_POST['tmcp_required'] ) ? $_POST['tmcp_required'] : array();
			$tmcp_hide_price         = isset( $_POST['tmcp_hide_price'] ) ? $_POST['tmcp_hide_price'] : array();
			$tmcp_limit              = isset( $_POST['tmcp_limit'] ) ? $_POST['tmcp_limit'] : array();
			$tmcp_menu_order         = isset( $_POST['tmcp_menu_order'] ) ? $_POST['tmcp_menu_order'] : array();
			$tmcp_attribute          = isset( $_POST['tmcp_attribute'] ) ? $_POST['tmcp_attribute'] : array();
			$tmcp_type               = isset( $_POST['tmcp_type'] ) ? $_POST['tmcp_type'] : array();
			$tm_meta_cpf             = isset( $_POST['tm_meta_cpf'] ) ? $_POST['tm_meta_cpf'] : array();

			// update custom product settings
			themecomplete_update_post_meta( $post_id, 'tm_meta_cpf', $tm_meta_cpf );

			if ( isset( $_POST['tm_meta_serialized'] ) ) {
				$tm_metas = $_POST['tm_meta_serialized'];
				$tm_metas = stripslashes_deep( $tm_metas );
				$tm_metas = rawurldecode( $tm_metas );
				$tm_metas = nl2br( $tm_metas );
				$tm_metas = json_decode( $tm_metas, TRUE );

				if ( $tm_metas || ( is_array( $tm_metas ) ) ) {
					if ( ! isset( $_SESSION ) ) {
						session_start();
					}
					$import = FALSE;
					if ( isset( $_SESSION['import_csv'] ) ) {
						$import = $_SESSION['import_csv'];
					}
					if ( ! empty( $import ) ) {
						if ( ! empty( $_SESSION['import_override'] ) ) {
							unset( $tm_metas['tm_meta']['tmfbuilder'] );
							$tm_metas = THEMECOMPLETE_EPO_ADMIN_GLOBAL()->import_array_merge( $tm_metas, $import );
							unset( $_SESSION['import_override'] );
						} else {
							$tm_metas = THEMECOMPLETE_EPO_ADMIN_GLOBAL()->import_array_merge( $tm_metas, $import );
						}
						unset( $_SESSION['import_csv'] );
					}

					$old_data = themecomplete_get_post_meta( $post_id, 'tm_meta', TRUE );

					if ( ! empty( $tm_metas ) && is_array( $tm_metas ) && isset( $tm_metas['tm_meta'] ) && is_array( $tm_metas['tm_meta'] ) ) {
						$tm_meta = $tm_metas['tm_meta'];
						THEMECOMPLETE_EPO_ADMIN_GLOBAL()->tm_save_meta( $post_id, $tm_meta, $old_data, 'tm_meta' );
					} else {
						THEMECOMPLETE_EPO_ADMIN_GLOBAL()->tm_save_meta( $post_id, FALSE, $old_data, 'tm_meta' );
					}
				}
			} elseif ( isset( $_POST['tm_meta_serialized_wpml'] ) ) {
				$tm_metas = $_POST['tm_meta_serialized_wpml'];
				$tm_metas = stripslashes_deep( $tm_metas );
				$tm_metas = rawurldecode( $tm_metas );
				$tm_metas = nl2br( $tm_metas );
				$tm_metas = json_decode( $tm_metas, TRUE );
				if ( $tm_metas ) {

					$old_data = themecomplete_get_post_meta( $post_id, 'tm_meta_wpml', TRUE );

					if ( ! empty( $tm_metas ) && is_array( $tm_metas ) && isset( $tm_metas['tm_meta'] ) && is_array( $tm_metas['tm_meta'] ) ) {
						$tm_meta = $tm_metas['tm_meta'];
						THEMECOMPLETE_EPO_ADMIN_GLOBAL()->tm_save_meta( $post_id, $tm_meta, $old_data, 'tm_meta_wpml' );
					} else {
						THEMECOMPLETE_EPO_ADMIN_GLOBAL()->tm_save_meta( $post_id, FALSE, $old_data, 'tm_meta_wpml' );
					}
				}
			}

			if ( ! empty( $_post_id ) ) {
				global $wpdb;
				$max_loop = max( array_keys( $_post_id ) );
				for ( $i = 0; $i <= $max_loop; $i ++ ) {

					if ( ! isset( $_post_id[ $i ] ) ) {
						continue;
					}

					$tmcp_id = absint( $_post_id[ $i ] );

					if ( $tmcp_id ) {
						// Enabled or disabled
						$post_status = isset( $tmcp_enabled[ $i ] ) ? 'publish' : 'private';

						// Generate a useful post title
						$post_title = sprintf( esc_html__( 'TM Extra Product Option #%s of %s', 'woocommerce-tm-extra-product-options' ), absint( $tmcp_id ), esc_html( get_the_title( $post_id ) ) );

						$data  = wp_slash( array(
							'post_status' => $post_status,
							'post_title'  => $post_title,
							'menu_order'  => $tmcp_menu_order[ $i ],
						) );
						$data  = wp_unslash( $data );
						$where = array( 'ID' => $tmcp_id );
						if ( FALSE === $wpdb->update( $wpdb->posts, $data, $where ) ) {
							return new WP_Error( 'db_update_error', esc_html__( 'Could not update post in the database', 'woocommerce-tm-extra-product-options' ), $wpdb->last_error );
						}

						// Price handling
						$clean_prices      = array();
						$clean_prices_type = array();
						if ( isset( $tmcp_regular_price[ $i ] ) ) {
							foreach ( $tmcp_regular_price[ $i ] as $key => $value ) {
								foreach ( $value as $k => $v ) {
									if ( $v !== '' ) {
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

						// Update post meta
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
