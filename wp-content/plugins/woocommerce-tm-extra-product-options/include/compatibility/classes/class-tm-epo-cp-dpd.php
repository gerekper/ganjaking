<?php
/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * WooCommerce Dynamic Pricing & Discounts (https://codecanyon.net/item/woocommerce-dynamic-pricing-discounts/7119279)
 *
 * @package Extra Product Options/Compatibility
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_CP_DPD {

	/**
	 * The single instance of the class
	 *
	 * @since 1.0
	 */
	protected static $_instance = NULL;

	private $order_flag = "tm_has_dpd";

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

		add_action( 'init', array( $this, 'add_compatibility' ) );
		add_filter( 'plugins_loaded', array( $this, 'add_compatibility_settings' ), 2, 1 );
		add_filter( 'wc_epo_autoload_path', array( $this, 'wc_epo_autoload_path' ), 10, 2 );
		add_filter( 'wc_epo_autoload_file', array( $this, 'wc_epo_autoload_file' ), 10, 2 );

	}

	public function add_compatibility_settings() {
		add_filter( 'wc_epo_get_settings', array( $this, 'wc_epo_get_settings' ), 10, 1 );
	}

	public function wp_enqueue_scripts() {
		if ( THEMECOMPLETE_EPO()->can_load_scripts() ) {
			wp_enqueue_script( 'themecomplete-comp-dpd', THEMECOMPLETE_EPO_PLUGIN_URL . '/include/compatibility/assets/js/cp-dpd.js', array( 'jquery' ), THEMECOMPLETE_EPO_VERSION, TRUE );
		}
	}

	/**
	 * Add setting in main THEMECOMPLETE_EPO class
	 *
	 * @since 1.0
	 */
	public function wc_epo_get_settings( $settings = array() ) {
		if ( class_exists( 'RP_WCDPD' ) ) {
			$settings["tm_epo_dpd_enable"]                      = array( "no", $this, "is_dpd_enabled" );
			$settings["tm_epo_dpd_prefix"]                      = array( "", $this, "is_dpd_enabled" );
			$settings["tm_epo_dpd_suffix"]                      = array( "", $this, "is_dpd_enabled" );
			$settings["tm_epo_dpd_original_final_total"]        = array( "no", $this, "is_dpd_enabled" );
			$settings["tm_epo_dpd_enable_pricing_table"]        = array( "no", $this, "is_dpd_enabled" );
			$settings["tm_epo_dpd_show_option_prices_in_order"] = array( "no", $this, "is_dpd_enabled" );
			$settings["tm_epo_dpd_string_placement"]            = array( "", $this, "is_dpd_enabled" );
			$settings["tm_epo_dpd_label_css_selector"]          = array( "", $this, "is_dpd_enabled" );
			$settings["tm_epo_dpd_original_price_base"]         = array( "", $this, "is_dpd_enabled" );
		}

		return $settings;
	}

	/**
	 * Check if  WooCommerce Dynamic Pricing & Discounts is enabled
	 *
	 * @since 1.0
	 */
	public function is_dpd_enabled() {
		return class_exists( 'RP_WCDPD' );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @since 1.0
	 */
	public function add_compatibility() {

		if ( ! class_exists( 'RP_WCDPD' ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 4 );

		if ( THEMECOMPLETE_EPO()->tm_epo_dpd_enable == "no" ) {
			add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'cart_loaded_from_session_2' ), 2 );
			if ( version_compare( RP_WCDPD_VERSION, '2.3.4', '>=' ) ) {
				add_filter( 'woocommerce_product_get_price', array( $this, 'woocommerce_product_get_price_99999' ), 99999, 2 );
				add_filter( 'woocommerce_product_variation_get_price', array( $this, 'woocommerce_product_get_price_99999' ), 99999, 2 );
				add_filter( 'rightpress_late_hook_priority', array( $this, 'rightpress_product_price_late_hook_priority' ), 10, 1 );
				add_action( 'woocommerce_before_mini_cart', array( $this, 'woocommerce_before_mini_cart' ) );
				add_action( 'woocommerce_after_mini_cart', array( $this, 'woocommerce_after_mini_cart' ) );
			} else {
				add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'cart_loaded_from_session_99999' ), 99999 );
				add_filter( 'rightpress_product_price_late_hook_priority', array( $this, 'rightpress_product_price_late_hook_priority' ), 10, 1 );

			}
		}
		add_filter( 'woocommerce_cart_item_price', array( $this, 'cart_item_price' ), 99999, 3 );
		add_action( 'wc_epo_order_item_meta', array( $this, 'wc_epo_order_item_meta' ), 10, 3 );

		add_filter( 'wc_epo_discounted_price', array( $this, 'get_RP_WCDPD' ), 10, 4 );

		add_filter( 'tm_epo_settings_headers', array( $this, 'tm_epo_settings_headers' ), 10, 1 );
		add_filter( 'tm_epo_settings_settings', array( $this, 'tm_epo_settings_settings' ), 10, 1 );

		add_filter( 'wc_epo_product_price_rules', array( $this, 'wc_epo_product_price_rules' ), 10, 2 );
		add_filter( 'wc_epo_template_args_tm_totals', array( $this, 'wc_epo_template_args_tm_totals' ), 10, 2 );
		add_action( 'wc_epo_template_tm_totals', array( $this, 'wc_epo_template_tm_totals' ), 10, 1 );

		add_action( 'woocommerce_tm_epo_price_compatibility', array( $this, 'woocommerce_tm_epo_price_compatibility' ), 10, 2 );

		add_filter( 'epo_can_show_order_price', array( $this, 'epo_can_show_order_price' ), 10, 2 );

		add_filter( 'wc_epo_original_price_type_mode', array( $this, 'wc_epo_original_price_type_mode' ), 1, 4 );

	}

	/**
	 * Select correct original price type mode
	 *
	 * @since 5.0.6
	 */
	public function wc_epo_original_price_type_mode( $price = "",  $post_data = array() ) {

		if (THEMECOMPLETE_EPO()->tm_epo_dpd_original_price_base === "undiscounted" && isset($post_data['tcaddtocart'])){
			$product = wc_get_product($post_data['tcaddtocart']);
			$price = $product->get_price();
		}

		return $price;

	}

	/**
	 * Change price in mini cart
	 *
	 * @access public
	 *
	 * @since  5.0
	 * @return bool
	 */
	public function woocommerce_before_mini_cart() {
		$cart_contents = WC()->cart->cart_contents;
		if ( is_array( $cart_contents ) ) {
			foreach ( $cart_contents as $cart_item_key => $cart_item ) {
				if ( ! empty( $cart_item['tmcartepo'] ) && isset( $cart_item['tm_epo_options_prices'] ) && ! empty( $cart_item['tm_epo_doing_adjustment'] ) && empty( $cart_item['epo_price_override'] ) ) {
					WC()->cart->cart_contents[ $cart_item_key ]['data']->tc_in_mini_cart = 1;
				}
			}
		}
		WC()->cart->calculate_totals();
	}

	/**
	 * Change price in mini cart
	 *
	 * @access public
	 *
	 * @since  5.0
	 * @return bool
	 */
	public function woocommerce_after_mini_cart() {
		$cart_contents = WC()->cart->cart_contents;
		if ( is_array( $cart_contents ) ) {
			foreach ( $cart_contents as $cart_item_key => $cart_item ) {
				if ( ! empty( $cart_item['tmcartepo'] ) && isset( $cart_item['tm_epo_options_prices'] ) && ! empty( $cart_item['tm_epo_doing_adjustment'] ) && empty( $cart_item['epo_price_override'] ) ) {
					unset( WC()->cart->cart_contents[ $cart_item_key ]['data']->tc_in_mini_cart );
				}
			}
		}
	}

	/**
	 * Change price in cart
	 *
	 * @access public
	 *
	 * @since  4.9.12
	 * @return bool
	 */
	public function woocommerce_product_get_price_99999( $price, $product ) {
		if ( is_cart() || is_checkout() || ! empty( $product->tc_in_mini_cart ) ) {
			if ( ! empty( $product->rightpress_in_cart ) ) {
				$cart_item_key = $product->rightpress_in_cart;
				if ( isset( WC()->cart->cart_contents[ $cart_item_key ] ) ) {
					$cart_item = WC()->cart->cart_contents[ $cart_item_key ];
					if ( ! empty( $cart_item['tmcartepo'] ) && isset( $cart_item['tm_epo_options_prices'] ) && empty( $cart_item['epo_price_override'] ) ) {
						WC()->cart->cart_contents[ $cart_item_key ]['tm_epo_product_after_adjustment'] = $price; 
						$price                                                                         = floatval( $price ) + floatval( $cart_item['tm_epo_options_prices'] );
						unset( WC()->cart->cart_contents[ $cart_item_key ]['tm_epo_doing_adjustment'] );
					}
				}
			}
		}

		return $price;
	}

	/**
	 * Change late hook priority
	 *
	 * @access public
	 *
	 * @since  4.9.6
	 * @return bool
	 */
	public function rightpress_product_price_late_hook_priority( $priority ) {
		return 99990;
	}

	/**
	 * Check if we can show option prices in the order
	 *
	 * @access public
	 *
	 * @param array $item_meta
	 *
	 * @since  4.8.4
	 * @return bool
	 */
	public function epo_can_show_order_price( $can = TURE, $item_meta = array() ) {
		$order_flag = isset( $item_meta[ $this->order_flag ] ) ? $item_meta[ $this->order_flag ] :( isset($item_meta[ '_' . $this->order_flag ]) ? $item_meta[ '_' . $this->order_flag ] : array());
		if (is_array($order_flag) && count($order_flag)>0){
			$order_flag = $order_flag[0];
		}
		$order_flag = maybe_unserialize($order_flag);
		if (is_array($order_flag) && count($order_flag)>0){
			$order_flag = $order_flag[0];
		}
		if ( THEMECOMPLETE_EPO()->tm_epo_dpd_show_option_prices_in_order == "no" && ! empty( $order_flag ) ) {
			$can = FALSE;
		}

		return $can;
	}

	/**
	 * Get applicable volume rule
	 *
	 * Note: This feature assumes that considering all conditions only one
	 * volume rule will be applicable to one product. If there are more than
	 * one volume rule, the first one in a row will be selected.
	 *
	 * Note: Rules that contain cart related conditions are not considered
	 *
	 * @access public
	 *
	 * @param object $product
	 *
	 * @return array|bool
	 */
	public static function get_applicable_volume_rule( $product ) {
		if ( $matched_rules = RP_WCDPD_Product_Pricing::get_applicable_rules_for_product( $product, array( 'bulk', 'tiered' ), TRUE ) ) {
			return array_shift( $matched_rules );
		}

		return FALSE;
	}

	/**
	 * Get missing quantity range
	 *
	 * @access public
	 *
	 * @param int $from
	 * @param int $to
	 *
	 * @return array
	 */
	public static function get_missing_range( $from, $to ) {
		return array(
			'from'             => $from,
			'to'               => $to,
			'pricing_method'   => 'discount__amount',
			'pricing_value'    => 0,
			'is_missing_range' => TRUE,
		);
	}

	/**
	 * Add missing quantity ranges (gaps in continuity)
	 *
	 * @access public
	 *
	 * @param array  $quantity_ranges
	 * @param object $product
	 *
	 * @return array
	 */
	public static function add_missing_ranges( $quantity_ranges, $product ) {
		$fixed = array();

		// Check if product uses decimal quantities
		$decimal_quantities = RP_WCDPD_Settings::get( 'decimal_quantities' ) && RightPress_Helper::wc_product_uses_decimal_quantities( $product );

		// Get quantity step
		$quantity_step = $decimal_quantities ? RightPress_Helper::get_wc_product_quantity_step( $product ) : 1;

		$last_from = NULL;
		$last_to   = NULL;

		$count = count( $quantity_ranges );
		$i     = 1;

		foreach ( $quantity_ranges as $quantity_range ) {

			// Get from and to
			$from = $quantity_range['from'];
			$to   = $quantity_range['to'];

			// Maybe add first range
			if ( $last_from === NULL && $from > $quantity_step ) {
				$fixed[] = self::get_missing_range( $quantity_step, ( $from - $quantity_step ) );
			}

			// Gap between last to and current from
			if ( $last_to !== NULL && ( $from - $last_to ) > $quantity_step ) {
				$fixed[] = self::get_missing_range( ( $last_to + $quantity_step ), ( $from - $quantity_step ) );
			}

			// Add current range
			$fixed[] = $quantity_range;

			// Set last from and to
			$last_from = $from;
			$last_to   = $to;

			$i ++;
		}

		// Add closing range
		if ( $last_to !== NULL ) {
			$fixed[] = self::get_missing_range( ( $last_to + $quantity_step ), NULL );
		}

		return $fixed;
	}

	/**
	 * Alter product price
	 *
	 * @since 1.0
	 */
	public function woocommerce_tm_epo_price_compatibility( $price, $product ) {

		if ( class_exists( 'RP_WCDPD_Settings' ) && class_exists( 'RP_WCDPD_Promotion_Display_Price_Override' ) && RP_WCDPD_Settings::get( 'promo_display_price_override' ) ) {

			if ( function_exists( 'WC_CP' ) && version_compare( WC_CP()->version, "3.8", "<" ) && themecomplete_get_product_type( $product ) == "composite" && is_callable( array( $product, 'get_base_price' ) ) ) {
				RP_WCDPD_Promotion_Display_Price_Override::get_instance()->remove_all_price_hooks();
				$price = $product->get_base_price();
				RP_WCDPD_Promotion_Display_Price_Override::get_instance()->add_all_price_hooks();
			} else {
				if ( class_exists( 'RP_WCDPD_Settings' ) && RP_WCDPD_Settings::get( 'product_pricing_change_display_prices' ) ) {
					RP_WCDPD_Promotion_Display_Price_Override::get_instance()->remove_all_price_hooks();
					$price = $product->get_price();
					RP_WCDPD_Promotion_Display_Price_Override::get_instance()->add_all_price_hooks();
				}
			}

		} elseif ( version_compare( RP_WCDPD_VERSION, '2.3', '>=' ) && class_exists( 'RightPress_Product_Price_Shop' ) ) {

			if ( function_exists( 'WC_CP' ) && version_compare( WC_CP()->version, "3.8", "<" ) && themecomplete_get_product_type( $product ) == "composite" && is_callable( array( $product, 'get_base_price' ) ) ) {
				RightPress_Product_Price_Shop::start_observation();
				$price = $product->get_base_price();
				RightPress_Product_Price_Shop::get_observed();
			} else {
				if ( class_exists( 'RP_WCDPD_Settings' ) && RP_WCDPD_Settings::get( 'product_pricing_change_display_prices' ) ) {
					RightPress_Product_Price_Shop::start_observation();
					$price = $product->get_price();
					RightPress_Product_Price_Shop::get_observed();
				}
			}


		} elseif ( version_compare( RP_WCDPD_VERSION, '2.2', '>=' ) && class_exists( 'RP_WCDPD_Product_Price_Override' ) ) {

			if ( function_exists( 'WC_CP' ) && version_compare( WC_CP()->version, "3.8", "<" ) && themecomplete_get_product_type( $product ) == "composite" && is_callable( array( $product, 'get_base_price' ) ) ) {
				RP_WCDPD_Product_Price_Override::get_instance()->remove_all_price_hooks();
				$price = $product->get_base_price();
				RP_WCDPD_Product_Price_Override::get_instance()->add_all_price_hooks();
			} else {
				if ( class_exists( 'RP_WCDPD_Settings' ) && RP_WCDPD_Settings::get( 'product_pricing_change_display_prices' ) ) {
					RP_WCDPD_Product_Price_Override::get_instance()->remove_all_price_hooks();
					$price = $product->get_price();
					RP_WCDPD_Product_Price_Override::get_instance()->add_all_price_hooks();
				}
			}

		}

		return $price;
	}

	/**
	 * Add extra html data attributes
	 *
	 * @since 1.0
	 */
	public function wc_epo_template_tm_totals( $args ) {
		$tm_epo_dpd_prefix               = $args['tm_epo_dpd_prefix'];
		$tm_epo_dpd_suffix               = $args['tm_epo_dpd_suffix'];
		$tm_epo_dpd_original_final_total = $args['tm_epo_dpd_original_final_total'];
		$tm_epo_dpd_enable_pricing_table = $args['tm_epo_dpd_enable_pricing_table'];
		$tm_epo_dpd_enable 				 = $args['tm_epo_dpd_enable'];
		$tm_epo_dpd_string_placement     = $args['tm_epo_dpd_string_placement'];
		$tm_epo_dpd_label_css_selector   = $args['tm_epo_dpd_label_css_selector'];
		$tm_epo_dpd_original_price_base  = $args['tm_epo_dpd_original_price_base'];

		echo 'data-tm-epo-dpd-enable-pricing-table="' . esc_attr( $tm_epo_dpd_enable_pricing_table ) . '" data-tm-epo-dpd-original-final-total="' . esc_attr( $tm_epo_dpd_original_final_total ) . '" data-tm-epo-dpd-prefix="' . esc_attr( $tm_epo_dpd_prefix ) . '" data-tm-epo-dpd-suffix="' . esc_attr( $tm_epo_dpd_suffix ) . '" ';

		echo 'data-tm-epo-dpd-string-placement="' . esc_attr( $tm_epo_dpd_string_placement ) . '" ';
		echo 'data-tm-epo-dpd-label-css-selector="' . esc_attr( $tm_epo_dpd_label_css_selector ) . '" ';
		echo 'data-tm-epo-dpd-original-price-base="' . esc_attr( $tm_epo_dpd_original_price_base ) . '" ';

		if ( class_exists( 'RP_WCDPD_Settings' ) && class_exists( 'RP_WCDPD_Promotion_Display_Price_Override' ) && RP_WCDPD_Settings::get( 'promo_display_price_override' ) ) {
			echo 'data-tm-epo-dpd-price-override="1" ';
		}

		if ( defined( 'RP_WCDPD_VERSION' ) && version_compare( RP_WCDPD_VERSION, '2.2', '>=' ) ) {
			echo 'data-tm-epo-dpd-product-price-discounted="1" ';
		}

		echo 'data-tm-epo-dpd-attributes-to-id="' . esc_attr( $args['attributes_to_id'] ) . '" ';
		echo 'data-tm-epo-dpd-enable="' . esc_attr( $tm_epo_dpd_enable ) . '" ';
	}

	/**
	 * Add extra arguments to the totals template
	 *
	 * @since 1.0
	 */
	public function wc_epo_template_args_tm_totals( $args, $product ) {
		$args["tm_epo_dpd_suffix"]               = THEMECOMPLETE_EPO()->tm_epo_dpd_suffix;
		$args["tm_epo_dpd_prefix"]               = THEMECOMPLETE_EPO()->tm_epo_dpd_prefix;
		$args["tm_epo_dpd_original_final_total"] = THEMECOMPLETE_EPO()->tm_epo_dpd_original_final_total;
		$args["tm_epo_dpd_enable_pricing_table"] = THEMECOMPLETE_EPO()->tm_epo_dpd_enable_pricing_table;
		$args["tm_epo_dpd_enable"] 				 = THEMECOMPLETE_EPO()->tm_epo_dpd_enable;
		$args["tm_epo_dpd_string_placement"]     = THEMECOMPLETE_EPO()->tm_epo_dpd_string_placement;
		$args["tm_epo_dpd_label_css_selector"]   = THEMECOMPLETE_EPO()->tm_epo_dpd_label_css_selector;
		$args["tm_epo_dpd_original_price_base"]  = THEMECOMPLETE_EPO()->tm_epo_dpd_original_price_base;


		$args["fields_price_rules"] = ( THEMECOMPLETE_EPO()->tm_epo_dpd_enable == "no" ) ? $args["fields_price_rules"] : 1;

		if ( $args["price_override"] == "1" ) {
			$args["fields_price_rules"] = 1;
		}

		$attributes_to_id = array();
		if ( themecomplete_get_product_type( $product ) == 'variable' ) {

			$attributes_ids = $product->get_attributes();

			$pid = themecomplete_get_id( $product );

			foreach ( $attributes_ids as $attkey => $attvalue ) {


				$terms = wp_get_post_terms( $pid, $attkey, array( 'fields' => 'all' ) );
				if ( ! is_wp_error( $terms ) ) {
					foreach ( $terms as $term ) {
						if ( $term ) {
							$attributes_to_id[ 'attribute_' . $attkey ][ $term->slug ] = $term->term_id;
						}
					}
				}

			}
		}

		$args["attributes_to_id"] = esc_html( wp_json_encode( (array) $attributes_to_id ) );

		return $args;
	}

	/**
	 * Get product price rules
	 *
	 * @since 1.0
	 */
	public function wc_epo_product_price_rules( $price = array(), $product ) {
		if ( class_exists( 'RP_WCDPD' ) ) {
			$check_price = apply_filters( 'wc_epo_discounted_price', NULL, $product, NULL );
			if ( $check_price ) {
				$price['product'] = array();
				if ( $check_price['is_multiprice'] ) {
					foreach ( $check_price['rules'] as $variation_id => $variation_rule ) {
						foreach ( $variation_rule as $rulekey => $pricerule ) {
							$price['product'][ $variation_id ][] = array(
								"min"        => $pricerule["min"],
								"max"        => $pricerule["max"],
								"value"      => ( $pricerule["type"] != "percentage" ) ? apply_filters( 'wc_epo_product_price', $pricerule["value"], "", FALSE ) : $pricerule["value"],
								"type"       => $pricerule["type"],
								'conditions' => isset( $pricerule["conditions"] ) ? $pricerule["conditions"] : array(),
							);
						}
					}
				} else {
					foreach ( $check_price['rules'] as $rulekey => $pricerule ) {
						$price['product'][0][] = array(
							"min"        => $pricerule["min"],
							"max"        => $pricerule["max"],
							"value"      => ( $pricerule["type"] != "percentage" ) ? apply_filters( 'wc_epo_product_price', $pricerule["value"], "", FALSE ) : $pricerule["value"],
							"type"       => $pricerule["type"],
							'conditions' => isset( $pricerule["conditions"] ) ? $pricerule["conditions"] : array(),
						);
					}
				}
			}
			$price['price'] = apply_filters( 'woocommerce_tm_epo_price_compatibility', apply_filters( 'wc_epo_product_price', $product->get_price(), "", FALSE ), $product );
		}

		return $price;
	}

	/**
	 * Add order item meta
	 *
	 * @since 1.0
	 */
	public function wc_epo_order_item_meta( $item_id, $cart_item_key, $values ) {
		if ( ! empty( $values['tmcartepo'] ) ) {
			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7.0', '<' ) ) {
				wc_add_order_item_meta( $item_id, '_' . $this->order_flag, array( 1 ) );
			} else {
				$item = $item_id;
				$item->add_meta_data( '_' . $this->order_flag, array( 1 ) );
			}
		}
	}

	/**
	 * Alter autoloader path
	 *
	 * @since 1.0
	 */
	public function wc_epo_autoload_path( $path, $original_class ) {
		// Composite products sometimes do not load the Discount and Pricing classes
		if ( defined( 'RP_WCDPD_VERSION' ) && version_compare( RP_WCDPD_VERSION, '1.0.13', '<' ) && $original_class == "RP_WCDPD_Pricing" && defined( 'THEMECOMPLETE_EPO_INCLUDED' ) && defined( 'RP_WCDPD_PLUGIN_PATH' ) ) {
			$path = RP_WCDPD_PLUGIN_PATH . 'includes/classes/';
		}

		return $path;
	}

	/**
	 * Alter autoloader file
	 *
	 * @since 1.0
	 */
	public function wc_epo_autoload_file( $file, $original_class ) {
		// Composite products sometimes do not load the Discount and Pricing classes
		if ( defined( 'RP_WCDPD_VERSION' ) && version_compare( RP_WCDPD_VERSION, '1.0.13', '<' ) && $original_class == "RP_WCDPD_Pricing" && defined( 'THEMECOMPLETE_EPO_INCLUDED' ) && defined( 'RP_WCDPD_PLUGIN_PATH' ) ) {
			$file = 'Pricing.php';
		}

		return $file;
	}

	/**
	 * Add plugin setting (header)
	 *
	 * @since 1.0
	 */
	public function tm_epo_settings_headers( $headers = array() ) {
		$headers["dpd"] = array( "tcfa tcfa-percentage", esc_html__( 'Dynamic Pricing & Discounts', 'woocommerce-tm-extra-product-options' ) );

		return $headers;
	}

	/**
	 * Add plugin setting (setting)
	 *
	 * @since 1.0
	 */
	public function tm_epo_settings_settings( $settings = array() ) {
		$label = esc_html__( 'Dynamic Pricing & Discounts', 'woocommerce-tm-extra-product-options' );;
		$settings["dpd"] = array(
			array(
				'type'  => 'tm_title',
				'id'    => 'epo_page_options',
				'title' => $label,
			),
			array(
				'title'    => esc_html__( 'Enable discounts on extra options', 'woocommerce-tm-extra-product-options' ),
				'desc'     => esc_html__( 'Enabling this will apply the product discounts to the extra options as well.', 'woocommerce-tm-extra-product-options' ),
				'id'       => 'tm_epo_dpd_enable',
				'class'    => 'chosen_select',
				'css'      => 'min-width:300px;',
				'default'  => 'no',
				'type'     => 'select',
				'options'  => array(
					'no'  => esc_html__( 'Disable', 'woocommerce-tm-extra-product-options' ),
					'yes' => esc_html__( 'Enable', 'woocommerce-tm-extra-product-options' ),
				),
				'desc_tip' => FALSE,
			),

			array(
				'title'    => esc_html__( 'Base original price type according to', 'woocommerce-tm-extra-product-options' ),
				'desc'     => esc_html__( 'Set to what those price types will base their original price on.', 'woocommerce-tm-extra-product-options' ),
				'id'       => 'tm_epo_dpd_original_price_base',
				'class'    => 'chosen_select',
				'css'      => 'min-width:300px;',
				'default'  => 'no',
				'type'     => 'select',
				'options'  => array(
					''  => esc_html__( 'No change', 'woocommerce-tm-extra-product-options' ),
					'undiscounted' => esc_html__( 'Undiscounted product price', 'woocommerce-tm-extra-product-options' ),
				),
				'desc_tip' => FALSE,
			),

			array(
				'title'   => esc_html__( 'Enable alteration of pricing table', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to enable the inclusion of option prices to the pricing table.', 'woocommerce-tm-extra-product-options' ) . '</span>',
				'id'      => 'tm_epo_dpd_enable_pricing_table',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Enable original final total display', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to enable the display of the undiscounted final total', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_dpd_original_final_total',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			array(
				'title'    => esc_html__( 'Prefix label', 'woocommerce-tm-extra-product-options' ),
				'desc'     => esc_html__( 'Display a prefix label on product page.', 'woocommerce-tm-extra-product-options' ),
				'id'       => 'tm_epo_dpd_prefix',
				'default'  => '',
				'type'     => 'text',
				'desc_tip' => FALSE,
			),
			array(
				'title'    => esc_html__( 'Suffix label', 'woocommerce-tm-extra-product-options' ),
				'desc'     => esc_html__( 'Display a suffix label on product page.', 'woocommerce-tm-extra-product-options' ),
				'id'       => 'tm_epo_dpd_suffix',
				'default'  => '',
				'type'     => 'text',
				'desc_tip' => FALSE,
			),
			array(
				'title'    => esc_html__( 'Label CSS selector', 'woocommerce-tm-extra-product-options' ),
				'desc'     => esc_html__( 'Enter a CSS selector to override the default label where the discount string will be displayed.', 'woocommerce-tm-extra-product-options' ),
				'id'       => 'tm_epo_dpd_label_css_selector',
				'default'  => '',
				'type'     => 'text',
				'desc_tip' => FALSE,
			),
			array(
				'title'    => esc_html__( 'Discount string placement', 'woocommerce-tm-extra-product-options' ),
				'desc'     => esc_html__( 'Select the placement of the discounted string compared to the label.', 'woocommerce-tm-extra-product-options' ),
				'id'       => 'tm_epo_dpd_string_placement',
				'class'    => 'chosen_select',
				'css'      => 'min-width:300px;',
				'default'  => 'no',
				'type'     => 'select',
				'options'  => array(
					'after'  => esc_html__( 'After', 'woocommerce-tm-extra-product-options' ),
					'before' => esc_html__( 'Before', 'woocommerce-tm-extra-product-options' ),
				),
				'desc_tip' => FALSE,
			),
			array(
				'title'   => esc_html__( 'Enable showing option prices in the Order', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable this at your own risk since it is not possible to calculate option discounts on the Order page.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_dpd_show_option_prices_in_order',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			array( 'type' => 'tm_sectionend', 'id' => 'epo_page_options' ),

		);

		return $settings;
	}

	/**
	 * Alter cart contents
	 *
	 * @since 1.0
	 */
	public function cart_loaded_from_session_2() {

		$cart_contents = WC()->cart->cart_contents;

		if ( is_array( $cart_contents ) ) {
			foreach ( $cart_contents as $cart_item_key => $cart_item ) {
				if ( ! empty( $cart_item['tmcartepo'] ) && isset( $cart_item['tm_epo_product_original_price'] ) && empty( $cart_item['epo_price_override'] ) ) {
					if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7.0', '<' ) ) {
						WC()->cart->cart_contents[ $cart_item_key ]['data']->price = $cart_item['tm_epo_product_original_price'];
					} else {
						WC()->cart->cart_contents[ $cart_item_key ]['data']->set_price( $cart_item['tm_epo_product_original_price'] );
					}
					WC()->cart->cart_contents[ $cart_item_key ]['tm_epo_doing_adjustment'] = TRUE;
				}
			}
		}

	}

	/**
	 * Alter cart contents
	 *
	 * @since 1.0
	 */
	public function cart_loaded_from_session_99999() {

		$cart_contents = WC()->cart->cart_contents;
		if ( is_array( $cart_contents ) ) {
			foreach ( $cart_contents as $cart_item_key => $cart_item ) {
				if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7.0', '<' ) ) {
					$current_product_price = WC()->cart->cart_contents[ $cart_item_key ]['data']->price;
				} else {
					$current_product_price = WC()->cart->cart_contents[ $cart_item_key ]['data']->get_price();
				}

				if ( ! empty( $cart_item['tmcartepo'] ) && isset( $cart_item['tm_epo_options_prices'] ) && ! empty( $cart_item['tm_epo_doing_adjustment'] ) && empty( $cart_item['epo_price_override'] ) ) {
					WC()->cart->cart_contents[ $cart_item_key ]['tm_epo_product_after_adjustment'] = $current_product_price;
					WC()->cart->cart_contents[ $cart_item_key ]['data']->set_price( WC()->cart->cart_contents[ $cart_item_key ]['data']->get_price() + $cart_item['tm_epo_options_prices'] );
					unset( WC()->cart->cart_contents[ $cart_item_key ]['tm_epo_doing_adjustment'] );
				}
			}
		}

	}

	/**
	 * Replace cart html prices for WooCommerce Dynamic Pricing & Discounts
	 *
	 * @access public
	 *
	 * @param string $item_price
	 * @param array  $cart_item
	 * @param string $cart_item_key
	 *
	 * @return string
	 */
	public function cart_item_price( $item_price = "", $cart_item = "", $cart_item_key = "" ) {

		if ( ! isset( $cart_item['tmcartepo'] ) ) {
			return $item_price;
		}
		if ( ! isset( $cart_item['rp_wcdpd'] ) ) {
			if ( ! isset( $cart_item['rp_wcdpd_data'] ) ) {
				if ( ! isset( $cart_item['data']->rightpress_in_cart ) ) {
					return $item_price;
				}
			}
		}

		// Get price to display
		$price = THEMECOMPLETE_EPO_CART()->get_price_for_cart( FALSE, $cart_item, "", NULL, 0, 1 );

		// Format price to display
		$price_to_display = $price;

		$product = $cart_item['data'];
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7.0', '<' ) ) {
			if ( is_object( $product ) && property_exists( $product, "price" ) ) {
				$float_price_to_display = floatval( $cart_item['data']->price );
			} else {
				$float_price_to_display = floatval( $product->price );
			}
		} else {
			$float_price_to_display = floatval( $product->get_price() );
		}

		if ( THEMECOMPLETE_EPO()->tm_epo_cart_field_display == "advanced" ) {
			$original_price_to_display       = THEMECOMPLETE_EPO_CART()->get_price_for_cart( $cart_item['tm_epo_product_original_price'], $cart_item, "", NULL, 0, 1 );
			$float_original_price_to_display = floatval( $cart_item['tm_epo_product_original_price'] );
			if ( THEMECOMPLETE_EPO()->tm_epo_dpd_enable == "yes" ) {
				$price                  = $this->get_RP_WCDPD( $cart_item['tm_epo_product_original_price'], wc_get_product( themecomplete_get_id( $cart_item['data'] ) ), $cart_item_key );
				$price_to_display       = THEMECOMPLETE_EPO_CART()->get_price_for_cart( $price, $cart_item, "", NULL, 0, 1 );
				$float_price_to_display = floatval( $price );
			} else {
				$price                  = $cart_item['data']->get_price();
				$price                  = $price - $cart_item['tm_epo_options_prices'];
				$price_to_display       = THEMECOMPLETE_EPO_CART()->get_price_for_cart( $price, $cart_item, "", NULL, 0, 1 );
				$float_price_to_display = floatval( $price );
			}
		} else {
			$original_price_to_display       = THEMECOMPLETE_EPO_CART()->get_price_for_cart( $cart_item['tm_epo_product_price_with_options'], $cart_item, "", NULL, 0, 1 );
			$float_original_price_to_display = floatval( $cart_item['tm_epo_product_price_with_options'] );
		}

		if ( isset( $float_price_to_display ) && isset( $float_original_price_to_display ) && $float_price_to_display === $float_original_price_to_display ) {
			return $item_price;
		}

		$item_price = '<span class="tc-epo-cart-price"><del>' . $original_price_to_display . '</del> <ins>' . $price_to_display . '</ins></span>';

		return $item_price;
	}

	/**
	 * Get WooCommerce Dynamic Pricing & Discounts price for options
	 * modified from get version from Pricing class
	 *
	 * @since 1.0
	 */
	private function get_RP_WCDPD_single( $field_price, $cart_item_key, $pricing = NULL, $force = FALSE ) {

		if ( THEMECOMPLETE_EPO()->tm_epo_dpd_enable == 'no' && ! $force ) {
			return $field_price;
		}

		$price          = $field_price;
		$original_price = $price;

		if ( ! $pricing ) {
			// This runs on versions >=2
			$product_pricing = RP_WCDPD_Settings::get( 'product_pricing' );
			$cart_item       = WC()->cart->cart_contents[ $cart_item_key ];

			if ( ! isset( $cart_item['rp_wcdpd_data'] ) ) {
				if ( class_exists( 'RightPress_Product_Price_Cart' ) && method_exists( 'RightPress_Product_Price_Cart', 'get_cart_item_price_changes' ) ) {
					$price_changes = RightPress_Product_Price_Cart::get_cart_item_price_changes( $cart_item_key );
					if ( ! isset( $price_changes['original_price'] ) ) {
						return $field_price;
					}
					$initial_product_price = $price_changes['original_price'];
				} else {
					return $field_price;
				}
			} else {
				$initial_product_price = $cart_item['rp_wcdpd_data']['initial_price'];
			}

			if ( (float) $initial_product_price > 0 ) {

				$product_price = $initial_product_price;
				if ( class_exists( 'RP_WCDPD_WC_Cart' ) and method_exists( 'RP_WCDPD_WC_Cart', 'get_cart_item_price_for_display' ) ) {
					$product_price = RP_WCDPD_WC_Cart::get_cart_item_price_for_display( $cart_item );
				} else if ( class_exists( 'RightPress_Product_Price_Display' ) and method_exists( 'RightPress_Product_Price_Display', 'prepare_product_price_for_display' ) ) {
					$product_price = (float) RightPress_Product_Price_Display::prepare_product_price_for_display( $cart_item['data'], NULL, TRUE );
				}

				// $product price is already being displayed with appropriate tax status
				// while $initial_product_price is not

				if ( $cart_item['data']->is_taxable() ) {

					$tax_display_cart = get_option( 'woocommerce_tax_display_cart' );

					if ( $tax_display_cart == 'excl' ) {

						if ( floatval( $initial_product_price ) != 0 ) {
							$initial_product_price = themecomplete_get_price_excluding_tax( $cart_item['data'], array( 'qty' => 10000, 'price' => $initial_product_price ) ) / 10000;
						}

					} else {

						if ( floatval( $initial_product_price ) != 0 ) {
							$initial_product_price = themecomplete_get_price_including_tax( $cart_item['data'], array( 'qty' => 10000, 'price' => $initial_product_price ) ) / 10000;
						}

					}

				}

				$price = ( $product_price / $initial_product_price ) * $price;
			}

		} else {
			if ( ! isset( $pricing->items[ $cart_item_key ] ) ) {
				return $field_price;
			}

			if ( in_array( $pricing->pricing_settings['apply_multiple'], array( 'all', 'first' ) ) ) {

				foreach ( $pricing->apply['global'] as $rule_key => $apply ) {
					if ( $deduction = $pricing->apply_rule_to_item( $rule_key, $apply, $cart_item_key, $pricing->items[ $cart_item_key ], FALSE, $price ) ) {

						if ( $apply['if_matched'] == 'other' && isset( $pricing->applied ) && isset( $pricing->applied['global'] ) ) {
							if ( count( $pricing->applied['global'] ) > 1 || ! isset( $pricing->applied['global'][ $rule_key ] ) ) {
								continue;
							}
						}

						$pricing->applied['global'][ $rule_key ] = 1;
						$price                                   = $price - $deduction;
					}
				}

			} elseif ( $pricing->pricing_settings['apply_multiple'] == 'biggest' ) {

				$price_deductions = array();

				foreach ( $pricing->apply['global'] as $rule_key => $apply ) {

					if ( $apply['if_matched'] == 'other' && isset( $pricing->applied ) && isset( $pricing->applied['global'] ) ) {
						if ( count( $pricing->applied['global'] ) > 1 || ! isset( $pricing->applied['global'][ $rule_key ] ) ) {
							continue;
						}
					}

					if ( $deduction = $pricing->apply_rule_to_item( $rule_key, $apply, $cart_item_key, $pricing->items[ $cart_item_key ], FALSE ) ) {
						$price_deductions[ $rule_key ] = $deduction;
					}
				}

				if ( ! empty( $price_deductions ) ) {
					$max_deduction                           = max( $price_deductions );
					$rule_key                                = array_search( $max_deduction, $price_deductions );
					$pricing->applied['global'][ $rule_key ] = 1;
					$price                                   = $price - $max_deduction;
				}

			}
		}

		if ( $price != $original_price ) {
			return $price;
		} else {
			return $field_price;
		}
	}

	/**
	 * Get WooCommerce Dynamic Pricing & Discounts price rules
	 *
	 * @since 1.0
	 */
	public function get_RP_WCDPD( $field_price = NULL, $product, $cart_item_key = NULL, $force = FALSE ) {
		$price = NULL;

		if ( class_exists( 'RP_WCDPD' ) && class_exists( 'RP_WCDPD_Pricing' ) ) {

			$tm_RP_WCDPD = RP_WCDPD::get_instance();

			$selected_rule = NULL;

			$dpd_version_compare  = version_compare( RP_WCDPD_VERSION, '1.0.13', '<' );
			$dpd_version_compare2 = version_compare( RP_WCDPD_VERSION, '2.0', '>=' );

			if ( $field_price !== NULL && $cart_item_key !== NULL ) {
				if ( $dpd_version_compare2 ) {
					return $this->get_RP_WCDPD_single( $field_price, $cart_item_key, NULL, $force );
				} else {
					return $this->get_RP_WCDPD_single( $field_price, $cart_item_key, $tm_RP_WCDPD->pricing, $force );
				}
			}

			// Iterate over pricing rules and use the first one that has this product in conditions (or does not have if condition "not in list")
			if ( ! $dpd_version_compare2 && isset( $tm_RP_WCDPD->opt['pricing']['sets'] )
			     && count( $tm_RP_WCDPD->opt['pricing']['sets'] )
			) {
				foreach ( $tm_RP_WCDPD->opt['pricing']['sets'] as $rule_key => $rule ) {
					if ( $rule['method'] == 'quantity' && $validated_rule = RP_WCDPD_Pricing::validate_rule( $rule ) ) {
						if ( $dpd_version_compare ) {
							if ( $validated_rule['selection_method'] == 'all' && $tm_RP_WCDPD->user_matches_rule( $validated_rule['user_method'], $validated_rule['roles'] ) ) {
								$selected_rule = $validated_rule;
								break;
							}
							if ( $validated_rule['selection_method'] == 'categories_include' && count( array_intersect( $tm_RP_WCDPD->get_product_categories( themecomplete_get_id( $product ) ), $validated_rule['categories'] ) ) > 0 && $tm_RP_WCDPD->user_matches_rule( $validated_rule['user_method'], $validated_rule['roles'] ) ) {
								$selected_rule = $validated_rule;
								break;
							}
							if ( $validated_rule['selection_method'] == 'categories_exclude' && count( array_intersect( $tm_RP_WCDPD->get_product_categories( themecomplete_get_id( $product ) ), $validated_rule['categories'] ) ) == 0 && $tm_RP_WCDPD->user_matches_rule( $validated_rule['user_method'], $validated_rule['roles'] ) ) {
								$selected_rule = $validated_rule;
								break;
							}
							if ( $validated_rule['selection_method'] == 'products_include' && in_array( themecomplete_get_id( $product ), $validated_rule['products'] ) && $tm_RP_WCDPD->user_matches_rule( $validated_rule['user_method'], $validated_rule['roles'] ) ) {
								$selected_rule = $validated_rule;
								break;
							}
							if ( $validated_rule['selection_method'] == 'products_exclude' && ! in_array( themecomplete_get_id( $product ), $validated_rule['products'] ) && $tm_RP_WCDPD->user_matches_rule( $validated_rule['user_method'], $validated_rule['roles'] ) ) {
								$selected_rule = $validated_rule;
								break;
							}
						} else {
							if ( $validated_rule['selection_method'] == 'all' && $tm_RP_WCDPD->user_matches_rule( $validated_rule ) ) {
								$selected_rule = $validated_rule;
								break;
							}
							if ( $validated_rule['selection_method'] == 'categories_include' && count( array_intersect( $tm_RP_WCDPD->get_product_categories( themecomplete_get_id( $product ) ), $validated_rule['categories'] ) ) > 0 && $tm_RP_WCDPD->user_matches_rule( $validated_rule ) ) {
								$selected_rule = $validated_rule;
								break;
							}
							if ( $validated_rule['selection_method'] == 'categories_exclude' && count( array_intersect( $tm_RP_WCDPD->get_product_categories( themecomplete_get_id( $product ) ), $validated_rule['categories'] ) ) == 0 && $tm_RP_WCDPD->user_matches_rule( $validated_rule ) ) {
								$selected_rule = $validated_rule;
								break;
							}
							if ( $validated_rule['selection_method'] == 'products_include' && in_array( themecomplete_get_id( $product ), $validated_rule['products'] ) && $tm_RP_WCDPD->user_matches_rule( $validated_rule ) ) {
								$selected_rule = $validated_rule;
								break;
							}
							if ( $validated_rule['selection_method'] == 'products_exclude' && ! in_array( themecomplete_get_id( $product ), $validated_rule['products'] ) && $tm_RP_WCDPD->user_matches_rule( $validated_rule ) ) {
								$selected_rule = $validated_rule;
								break;
							}
						}
					}
				}
			}

			if ( $dpd_version_compare2 ) {
				$price = array();

				$all_rules = array();
				if ( ! $product->is_type( 'variable' ) && ! $product->is_type( 'variation' ) ) {

					$price['is_multiprice'] = FALSE;

					$product_rules = RP_WCDPD_Product_Pricing::get_applicable_rules_for_product( $product, NULL, TRUE );

					if ( ! $product_rules ) {
						$product_rules = array();
					}
					$all_rules = array();
					foreach ( $product_rules as $k => $rule ) {
						$apply_this_rule = TRUE;
						if ( isset( $rule['group_products'] ) && is_array( $rule['group_products'] ) && isset( $rule['method'] ) && ( $rule['method'] == 'group_repeat' || $rule['method'] == 'group' ) ) {
							$this_product_id = themecomplete_get_id( $product );
							foreach ( $rule['group_products'] as $grouprule ) {
								if ( $grouprule['type'] == 'product__product' ) {
									$method_option = $grouprule['method_option'];
									$products      = $grouprule['products'];

									if ( $method_option == 'in_list' ) {
										if ( ! in_array( $this_product_id, $products ) ) {
											$apply_this_rule = FALSE;
											break;
										}
									}
								}
							}
						}
						if ( $apply_this_rule ) {
							$all_rules[] = array(
								'product' => $product,
								'rule'    => $rule,
							);
						}
					}

				} else {


					if ( $product->is_type( 'variation' ) ) {
						$product = RightPress_WC_Legacy::product_variation_get_parent( $product );
					}

					$variation_rules = array();
					foreach ( $product->get_available_variations() as $variation_data ) {
						$variation = wc_get_product( $variation_data['variation_id'] );

						$product_rules = RP_WCDPD_Product_Pricing::get_applicable_rules_for_product( $variation, NULL, TRUE );
						if ( ! $product_rules ) {
							$product_rules = array();
						}
						foreach ( $product_rules as $k => $rule ) {
							$variation_rules[ $variation_data['variation_id'] ][] = array(
								'product' => $variation,
								'rule'    => $rule,
							);
						}

					}

					$all_rules              = $variation_rules;
					$price['is_multiprice'] = TRUE;
				}
				$table_data = array();
				if ( ! $price['is_multiprice'] ) {
					foreach ( $all_rules as $single ) {
						$_product = $single['product'];
						$_rule    = $single['rule'];
						if ( ! $_rule ) {
							continue;
						}

						$original_price = $_product->get_price();
						if ( isset( $_rule['quantity_ranges'] ) ) {
							$quantity_ranges = $_rule['quantity_ranges'];
							if ( RP_WCDPD_Settings::get( 'promo_volume_pricing_table_missing_ranges' ) === 'display' ) {
								$quantity_ranges = $this->add_missing_ranges( $quantity_ranges, $_product );
							}

							foreach ( $quantity_ranges as $quantity_range ) {

								switch ( $quantity_range['pricing_method'] ) {
									case 'discount__percentage':
										$quantity_range['pricing_method'] = 'percentage';
										break;
									case 'discount__amount':
										$quantity_range['pricing_method'] = 'price';
										break;
									case 'fixed__price':
										$quantity_range['pricing_method'] = 'fixed';
										break;

									default:
										# code...
										break;
								}
								$table_data[] = array(
									'min'        => $quantity_range['from'],
									'max'        => $quantity_range['to'],
									'type'       => $quantity_range['pricing_method'],
									'value'      => $quantity_range['pricing_value'],
									'conditions' => isset( $_rule['conditions'] ) ? $_rule['conditions'] : array(),
								);

							}
						} else {
							if ( isset( $_rule['pricing_method'] ) && isset( $_rule['pricing_value'] ) ) {
								$table_data[] = array(
									'min'        => 1,
									'max'        => '',
									'type'       => $_rule['pricing_method'],
									'value'      => $_rule['pricing_value'],
									'conditions' => isset( $_rule['conditions'] ) ? $_rule['conditions'] : array(),
								);
							} elseif ( isset( $_rule['group_pricing_method'] ) && isset( $_rule['group_pricing_value'] ) ) {
								$table_data[] = array(
									'min'        => 1,
									'max'        => '',
									'type'       => $_rule['group_pricing_method'],
									'value'      => $_rule['group_pricing_value'],
									'conditions' => isset( $_rule['conditions'] ) ? $_rule['conditions'] : array(),
								);
							}

						}

					}
				} else {
					foreach ( $all_rules as $vid => $vidsingle ) {


						foreach ( $vidsingle as $single ) {

							$_product = $single['product'];
							$_rule    = $single['rule'];
							if ( ! $_rule ) {
								continue;
							}

							$original_price = $_product->get_price();
							if ( isset( $_rule['quantity_ranges'] ) ) {
								$quantity_ranges = $_rule['quantity_ranges'];
								if ( RP_WCDPD_Settings::get( 'promo_volume_pricing_table_missing_ranges' ) === 'display' ) {
									$quantity_ranges = $this->add_missing_ranges( $quantity_ranges, $_product );
								}

								foreach ( $quantity_ranges as $quantity_range ) {

									switch ( $quantity_range['pricing_method'] ) {
										case 'discount__percentage':
											$quantity_range['pricing_method'] = 'percentage';
											break;
										case 'discount__amount':
											$quantity_range['pricing_method'] = 'price';
											break;
										case 'fixed__price':
											$quantity_range['pricing_method'] = 'fixed';
											break;

										default:
											# code...
											break;
									}
									$table_data[ $vid ][] = array(
										'min'        => $quantity_range['from'],
										'max'        => $quantity_range['to'],
										'type'       => $quantity_range['pricing_method'],
										'value'      => $quantity_range['pricing_value'],
										'conditions' => isset( $_rule['conditions'] ) ? $_rule['conditions'] : array(),
									);

								}
							} else {
								if ( isset( $_rule['pricing_method'] ) && isset( $_rule['pricing_value'] ) ) {
									$table_data[ $vid ][] = array(
										'min'        => 1,
										'max'        => '',
										'type'       => $_rule['pricing_method'],
										'value'      => $_rule['pricing_value'],
										'conditions' => isset( $_rule['conditions'] ) ? $_rule['conditions'] : array(),
									);
								} elseif ( isset( $_rule['group_pricing_method'] ) && isset( $_rule['group_pricing_value'] ) ) {
									$table_data[ $vid ][] = array(
										'min'        => 1,
										'max'        => '',
										'type'       => $_rule['group_pricing_method'],
										'value'      => $_rule['group_pricing_value'],
										'conditions' => isset( $_rule['conditions'] ) ? $_rule['conditions'] : array(),
									);
								}

							}
						}

					}
				}

				$price['rules'] = $table_data;

			} elseif ( is_array( $selected_rule ) ) {

				// Quantity
				if ( $selected_rule['method'] == 'quantity' && isset( $selected_rule['pricing'] ) && in_array( $selected_rule['quantities_based_on'], array( 'exclusive_product', 'exclusive_variation', 'exclusive_configuration' ) ) ) {

					if ( themecomplete_get_product_type( $product ) == 'variable' || themecomplete_get_product_type( $product ) == 'variable-subscription' ) {
						$product_variations = $product->get_available_variations();
					}

					// For variable products only - check if prices differ for different variations
					$multiprice_variable_product = FALSE;

					if ( ( themecomplete_get_product_type( $product ) == 'variable' || themecomplete_get_product_type( $product ) == 'variable' ) && ! empty( $product_variations ) ) {
						$last_product_variation = array_slice( $product_variations, - 1 );
						if ( class_exists( 'RightPress_WC_Legacy' ) && class_exists( 'RightPress_Helper' ) ) {
							$last_product_variation_object = RightPress_Helper::wc_get_product( $last_product_variation[0]['variation_id'] );
						} else {
							$last_product_variation_object = new WC_Product_Variable( $last_product_variation[0]['variation_id'] );
						}
						$last_product_variation_price = $last_product_variation_object->get_price();

						foreach ( $product_variations as $variation ) {
							if ( class_exists( 'RightPress_WC_Legacy' ) && class_exists( 'RightPress_Helper' ) ) {
								$variation_object = RightPress_Helper::wc_get_product( $variation['variation_id'] );
							} else {
								$variation_object = new WC_Product_Variable( $variation['variation_id'] );
							}

							if ( $variation_object->get_price() != $last_product_variation_price ) {
								$multiprice_variable_product = TRUE;
								break;
							}
						}
					}

					if ( $multiprice_variable_product ) {
						$variation_table_data = array();

						foreach ( $product_variations as $variation ) {
							if ( class_exists( 'RightPress_WC_Legacy' ) && class_exists( 'RightPress_Helper' ) ) {
								$variation_product = RightPress_Helper::wc_get_product( $variation['variation_id'] );
							} else {
								$variation_product = new WC_Product_Variation( $variation['variation_id'] );
							}
							$variation_table_data[ $variation['variation_id'] ] = $tm_RP_WCDPD->pricing_table_calculate_adjusted_prices( $selected_rule['pricing'], $variation_product->get_price() );
						}
						$price                  = array();
						$price['is_multiprice'] = TRUE;
						$price['rules']         = $variation_table_data;
					} else {
						if ( themecomplete_get_product_type( $product ) == 'variable' && ! empty( $product_variations ) ) {
							if ( class_exists( 'RightPress_WC_Legacy' ) && class_exists( 'RightPress_Helper' ) ) {
								$variation_product = RightPress_Helper::wc_get_product( $last_product_variation[0]['variation_id'] );
							} else {
								$variation_product = new WC_Product_Variation( $last_product_variation[0]['variation_id'] );
							}
							$table_data = $tm_RP_WCDPD->pricing_table_calculate_adjusted_prices( $selected_rule['pricing'], $variation_product->get_price() );
						} else {
							$table_data = $tm_RP_WCDPD->pricing_table_calculate_adjusted_prices( $selected_rule['pricing'], $product->get_price() );
						}
						$price                  = array();
						$price['is_multiprice'] = FALSE;
						$price['rules']         = $table_data;
					}
				}

			}
		}
		if ( $field_price !== NULL ) {
			$price = $field_price;
		}

		return $price;
	}

}
