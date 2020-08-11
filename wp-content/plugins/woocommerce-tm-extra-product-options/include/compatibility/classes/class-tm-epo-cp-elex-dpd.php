<?php
/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * WooCommerce Dynamic Pricing & Discounts 
 * https://codecanyon.net/item/woocommerce-dynamic-pricing-discounts/7119279
 *
 * @package Extra Product Options/Compatibility
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_CP_ELEX_DPD {

	/**
	 * The single instance of the class
	 *
	 * @since 4.9.12
	 */
	protected static $_instance = NULL;

	private $order_flag = "tm_has_elex_dpd";

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 4.9.12
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
	 * @since 4.9.12
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'add_compatibility' ) );
		add_filter( 'plugins_loaded', array( $this, 'add_compatibility_settings' ), 2, 1 );

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
	 * @since 4.9.12
	 */
	public function wc_epo_get_settings( $settings = array() ) {
		if ( $this->is_elex_dpd_enabled() ) {
			$settings["tm_epo_elex_dpd_enable"]                      = array( "no", $this, "is_elex_dpd_enabled" );
			$settings["tm_epo_elex_dpd_show_option_prices_in_order"] = array( "no", $this, "is_elex_dpd_enabled" );
		}

		return $settings;
	}

	/**
	 * Check if  WooCommerce Dynamic Pricing & Discounts is enabled
	 *
	 * @since 4.9.12
	 */
	public function is_elex_dpd_enabled() {
		return class_exists( 'Elex_dp_dynamic_pricing_plugin' );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @since 4.9.12
	 */
	public function add_compatibility() {

		if ( ! $this->is_elex_dpd_enabled() ) {
			return;
		}

		if ( THEMECOMPLETE_EPO()->tm_epo_elex_dpd_enable == "no" ) {
			// Flag products in cart
			add_filter( 'woocommerce_add_cart_item', array( $this, 'flag_product_in_cart' ), 1 );
			add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'flag_product_in_cart' ), 1 );

			add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'cart_loaded_from_session_2' ), 2 );
			add_filter( 'woocommerce_product_get_price', array( $this, 'woocommerce_product_get_price_99999' ), 99999, 2 );

		}
		add_filter( 'woocommerce_cart_item_price', array( $this, 'cart_item_price' ), 101, 3 );
		add_action( 'wc_epo_order_item_meta', array( $this, 'wc_epo_order_item_meta' ), 10, 3 );

		add_filter( 'wc_epo_discounted_price', array( $this, 'get_RP_WCDPD' ), 10, 4 );

		add_filter( 'tm_epo_settings_headers', array( $this, 'tm_epo_settings_headers' ), 10, 1 );
		add_filter( 'tm_epo_settings_settings', array( $this, 'tm_epo_settings_settings' ), 10, 1 );

		add_filter( 'wc_epo_product_price_rules', array( $this, 'wc_epo_product_price_rules' ), 10, 2 );

		add_filter( 'epo_can_show_order_price', array( $this, 'epo_can_show_order_price' ), 10, 2 );

	}

	/**
	 * Flag product in cart
	 *
	 * @access public
	 *
	 * @since  4.9.12
	 * @return bool
	 */
	public function flag_product_in_cart( $cart_item_data ) {

		$cart_item_data['data']->tc_cart_key = $cart_item_data['key'];

		return $cart_item_data;
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
		if ( is_cart() || is_checkout() ) {
			if ( ! empty( $product->tc_cart_key ) ) {
				$cart_item_key = $product->tc_cart_key;
				$cart_item     = WC()->cart->cart_contents[ $cart_item_key ];
				if ( ! empty( $cart_item['tmcartepo'] ) && isset( $cart_item['tm_epo_options_prices'] ) && empty( $cart_item['epo_price_override'] ) ) {
					WC()->cart->cart_contents[ $cart_item_key ]['tm_epo_product_after_adjustment'] = $price;
					$price                                                                         = floatval( $price ) + floatval( $cart_item['tm_epo_options_prices'] );
					unset( WC()->cart->cart_contents[ $cart_item_key ]['tm_epo_doing_adjustment'] );
				}
			}
		}

		return $price;
	}


	/**
	 * Check if we can show option prices in the order
	 *
	 * @access public
	 *
	 * @param array $item_meta
	 *
	 * @since  4.9.12
	 * @return bool
	 */
	public function epo_can_show_order_price( $can = TURE, $item_meta = array() ) {

		$order_flag = isset( $item_meta[ $this->order_flag ] ) ? $item_meta[ $this->order_flag ] :( isset($item_meta[ '_' . $this->order_flag ]) ? $item_meta[ '_' . $this->order_flag ] : array());
		if (is_array($order_flag)){
			$order_flag = $order_flag[0];
		}
		$order_flag = maybe_unserialize($order_flag);
		if (is_array($order_flag)){
			$order_flag = $order_flag[0];
		}

		if ( THEMECOMPLETE_EPO()->tm_epo_elex_dpd_show_option_prices_in_order == "no" && ! empty( $order_flag ) ) {
			$can = FALSE;
		}

		return $can;
	}

	/**
	 * Get product price rules
	 *
	 * @since 4.9.12
	 */
	public function wc_epo_product_price_rules( $price = array(), $product ) {
		if ( $this->is_elex_dpd_enabled() ) {
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
	 * @since 4.9.12
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
	 * Add plugin setting (header)
	 *
	 * @since 4.9.12
	 */
	public function tm_epo_settings_headers( $headers = array() ) {
		$headers["elexdpd"] = array( "tcfa tcfa-calendar", esc_html__( 'Elex Dynamic Pricing & Discounts', 'woocommerce-tm-extra-product-options' ) );

		return $headers;
	}

	/**
	 * Add plugin setting (setting)
	 *
	 * @since 4.9.12
	 */
	public function tm_epo_settings_settings( $settings = array() ) {
		$label = esc_html__( 'Elex Dynamic Pricing & Discounts', 'woocommerce-tm-extra-product-options' );;
		$settings["elexdpd"] = array(
			array(
				'type'  => 'tm_title',
				'id'    => 'epo_page_options',
				'title' => $label,
			),
			array(
				'title'    => esc_html__( 'Enable discounts on extra options', 'woocommerce-tm-extra-product-options' ),
				'desc'     => esc_html__( 'Enabling this will apply the product discounts to the extra options as well.', 'woocommerce-tm-extra-product-options' ),
				'id'       => 'tm_epo_elex_dpd_enable',
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
				'title'   => esc_html__( 'Enable showing option prices in the Order', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable this at your own risk since it is not possible to calculate option discounts on the Order page.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_elex_dpd_show_option_prices_in_order',
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
	 * @since 4.9.12
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
	 * Replace cart html prices for WooCommerce Dynamic Pricing & Discounts
	 *
	 * @access public
	 *
	 * @param string $item_price
	 * @param array  $cart_item
	 * @param string $cart_item_key
	 *
	 * @since  4.9.12
	 *
	 * @return string
	 */
	public function cart_item_price( $item_price = "", $cart_item = "", $cart_item_key = "" ) {

		if ( ! isset( $cart_item['tmcartepo'] ) ) {
			return $item_price;
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
			if ( THEMECOMPLETE_EPO()->tm_epo_elex_dpd_enable == "yes" ) {
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

		if ( '' === $price_to_display ) {
			$item_price = apply_filters( 'woocommerce_empty_price_html', '', $product );
		} elseif ( $product->is_on_sale() ) {
			$item_price = wc_format_sale_price( $original_price_to_display, $price_to_display ) . $product->get_price_suffix();
		} else {
			$item_price = wc_price( $price_to_display ) . $product->get_price_suffix();
		}

		return apply_filters( 'woocommerce_get_price_html', $item_price, $product );

	}

	/**
	 * Get WooCommerce Dynamic Pricing & Discounts price for options
	 * modified from get version from Pricing class
	 *
	 * @since 4.9.12
	 */
	private function get_RP_WCDPD_single( $field_price, $cart_item_key, $pricing = NULL, $force = FALSE ) {

		if ( THEMECOMPLETE_EPO()->tm_epo_elex_dpd_enable == 'no' && ! $force ) {
			return $field_price;
		}

		$old_price = $field_price;

		global $xa_hooks, $xa_common_flat_discount, $xa_cart_price, $executed_pids;
		$product = WC()->cart->cart_contents[ $cart_item_key ]['data'];
		$pid     = WC()->cart->cart_contents[ $cart_item_key ]['product_id'];

		$discounted_price = $old_price;

		if ( ! empty( $old_price ) && ( ! empty( $product ) || ! empty( $pid ) ) ) {
			$weight = $product->get_weight();
			global $xa_cart_quantities;
			$parent_id = $pid;
			if ( $product->get_type() == 'variation' ) {
				$parent_id = elex_dp_is_wc_version_gt_eql( '2.7' ) ? $product->get_parent_id() : $product->parent->id;
			}
			if ( isset( $xa_cart_quantities[ $pid ] ) || isset( $xa_cart_quantities[ $parent_id ] ) ) {
				$current_quantity = isset( $xa_cart_quantities[ $pid ] ) ? $xa_cart_quantities[ $pid ] : $xa_cart_quantities[ $parent_id ];
			} else {
				$current_quantity = 0;
			}
			if ( $current_quantity == 0 && class_exists( 'SitePress' ) ) {
				global $sitepress;
				$trid  = $sitepress->get_element_trid( $pid );
				$trans = $sitepress->get_element_translations( $trid );
				foreach ( $trans as $lan ) {
					$all_ids[] = $lan->element_id;
				}
				foreach ( $all_ids as $_pid ) {
					if ( ! empty( $xa_cart_quantities[ $_pid ] ) ) {
						$current_quantity = $xa_cart_quantities[ $_pid ];
						break;
					}
				}
			}
			if ( is_shop() || is_product_category() || is_product() ) {
				$current_quantity ++;
			}
			$objRulesValidator = New Elex_dp_RulesValidator();

			$valid_rules = $objRulesValidator->elex_dp_getValidRulesForProduct( $product, $pid, $current_quantity, $discounted_price, $weight );

			$mode = ! empty( $xa_dp_setting['mode'] ) ? $xa_dp_setting['mode'] : 'first_match';

			if ( is_array( $valid_rules ) ) {
				foreach ( $valid_rules as $rule_type_colon_rule_no => $rule ) {
					//this section supports repeat execution for product rules
					if ( isset( $rule['repeat_rule'] ) && $rule['repeat_rule'] == 'yes' && ! empty( $rule['max'] ) && ! empty( $rule['min'] ) ) {
						$times       = intval( $current_quantity / $rule['max'] );
						$total_price = 0;
						$repeat_qnty = (float) $rule['max'];

						if ( ! empty( $rule['discount_type'] ) && $rule['discount_type'] == 'Flat Discount' ) {
							$xa_common_flat_discount[ $rule['rule_type'] . ":" . $rule['rule_no'] . ":" . $pid ] = floatval( $rule['value'] ) * floatval( $times );
							if ( ! empty( $rule['adjustment'] ) ) {
								$adjusted_qnty    = ! empty( $objRulesValidator->rule_based_quantity[ $rule['rule_type'] . ":" . $rule['rule_no'] ] ) ? $objRulesValidator->rule_based_quantity[ $rule['rule_type'] . ":" . $rule['rule_no'] ] : $current_quantity;
								$discounted_price = $discounted_price + ( (float) $rule['adjustment'] / $adjusted_qnty );
							}
						} else {
							$object_hash = spl_object_hash( $product );
							$object_hash = $object_hash . $pid;
							$r_price     = $this->elex_dp_execute_rule( $objRulesValidator, $discounted_price, $rule_type_colon_rule_no, $rule, $repeat_qnty, $pid, $object_hash );

							$total_price    = $r_price * $times * $repeat_qnty;
							$remaining_qnty = $current_quantity - ( $times * $repeat_qnty );
							if ( $remaining_qnty > 0 ) {
								$total_price = $total_price + ( $remaining_qnty * $xa_cart_price[ $pid ] );
							}
							$discounted_price = $total_price / $current_quantity;
						}

					} else {

						//fix for best discount mode flat discount is not getting calculated]
						if ( ! empty( $rule['discount_type'] ) && $rule['discount_type'] == 'Flat Discount' ) {
							if ( $rule['rule_type'] == 'product_rules' ) {
								$xa_common_flat_discount[ $rule['rule_type'] . ":" . $rule['rule_no'] . ":" . $pid ] = floatval( $rule['value'] );
							} elseif ( $rule['rule_type'] == 'category_rules' ) {
								$cid                                                                                 = ! empty( $rule['selected_cids'] ) ? current( $rule['selected_cids'] ) : '';
								$xa_common_flat_discount[ $rule['rule_type'] . ":" . $rule['rule_no'] . ":" . $cid ] = floatval( $rule['value'] );
							} else {
								$xa_common_flat_discount[ $rule['rule_type'] . ":" . $rule['rule_no'] ] = floatval( $rule['value'] );
							}
							if ( ! empty( $rule['adjustment'] ) ) {
								$adjusted_qnty    = ! empty( $objRulesValidator->rule_based_quantity[ $rule['rule_type'] . ":" . $rule['rule_no'] ] ) ? $objRulesValidator->rule_based_quantity[ $rule['rule_type'] . ":" . $rule['rule_no'] ] : $current_quantity;
								$discounted_price = $discounted_price + ( (float) $rule['adjustment'] / $adjusted_qnty );
							}
						} else {

							$object_hash      = spl_object_hash( $product );
							$object_hash      = $object_hash . $pid;
							$discounted_price = $this->elex_dp_execute_rule( $objRulesValidator, $discounted_price, $rule_type_colon_rule_no, $rule, $current_quantity, $pid, $object_hash );
						}
					}

				}
			}
		}

		if ( $discounted_price != $old_price ) {
			return $discounted_price;
		} else {
			return $field_price;
		}
	}

	/**
	 * Execte rule without cache support
	 *
	 * @since 4.9.12
	 */
	public function elex_dp_execute_rule( $objRulesValidator, $old_price, $rule_type_colon_rule_no, $rule, $current_quantity = 1, $pid = 0, $object_hash = '' ) {

		$new_price = $old_price;
		$data      = explode( ':', $rule_type_colon_rule_no );
		$rule_type = $data[0];
		$rule_no   = $data[1];

		switch ( $rule_type ) {
			case "product_rules":
				$new_price = $objRulesValidator->elex_dp_SimpleExecute( $old_price, $rule_no, $rule, $pid, 1, FALSE, $object_hash );
				break;
			case "category_rules":
				$new_price = $objRulesValidator->elex_dp_Simple_Category_Execute( $old_price, $rule_no, $rule, $pid, 1, FALSE, $object_hash );
				break;

		}

		return $new_price;

	}

	/**
	 * Get WooCommerce Dynamic Pricing & Discounts price rules
	 *
	 * @since 4.9.12
	 */
	public function get_RP_WCDPD( $field_price = NULL, $product, $cart_item_key = NULL, $force = FALSE ) {
		$price = NULL;

		if ( $this->is_elex_dpd_enabled() ) {

			if ( $field_price !== NULL && $cart_item_key !== NULL ) {
				return $this->get_RP_WCDPD_single( $field_price, $cart_item_key, NULL, $force );
			}

			$product_rules = FALSE;

			$objRulesValidator = new Elex_dp_RulesValidator( 'all_match', TRUE, 'product_rules' );

			$price = array();

			$all_rules = array();
			if ( ! $product->is_type( 'variable' ) && ! $product->is_type( 'variation' ) ) {

				$price['is_multiprice'] = FALSE;

				$product_rules = $objRulesValidator->elex_dp_getValidRulesForProduct( $product );

				if ( ! $product_rules ) {
					$product_rules = array();
				}
				$all_rules = array();
				foreach ( $product_rules as $k => $rule ) {
					$all_rules[] = array(
						'product' => $product,
						'rule'    => $rule,
					);
				}

			} else {

				if ( $product->is_type( 'variation' ) ) {
					$product = wc_get_product( $product->get_parent_id() );//no support for WC<3x
				}

				$variation_rules = array();
				foreach ( $product->get_available_variations() as $variation_data ) {
					$variation = wc_get_product( $variation_data['variation_id'] );

					$product_rules = $objRulesValidator->elex_dp_getValidRulesForProduct( $variation );

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

					if ( isset( $_rule['discount_type'] ) && isset( $_rule['value'] ) ) {
						switch ( $_rule['discount_type'] ) {
							case 'Percent Discount':
								$_rule['discount_type'] = 'percentage';
								break;
							case 'Flat Discount':
								$_rule['discount_type'] = 'price';
								break;
							case 'Fixed Price':
								$_rule['discount_type'] = 'fixed';
								break;

							default:
								# code...
								break;
						}
						$table_data[] = array(
							'min'        => $_rule['min'],
							'max'        => $_rule['max'] !== NULL ? $_rule['max'] : '',
							'type'       => $_rule['discount_type'],
							'value'      => $_rule['value'],
							'conditions' => array(),
						);
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

						if ( isset( $_rule['discount_type'] ) && isset( $_rule['value'] ) ) {
							switch ( $_rule['discount_type'] ) {
								case 'Percent Discount':
									$_rule['discount_type'] = 'percentage';
									break;
								case 'Flat Discount':
									$_rule['discount_type'] = 'price';
									break;
								case 'Fixed Price':
									$_rule['discount_type'] = 'fixed';
									break;

								default:
									# code...
									break;
							}
							$table_data[ $vid ][] = array(
								'min'        => $_rule['min'],
								'max'        => $_rule['max'] !== NULL ? $_rule['max'] : '',
								'type'       => $_rule['discount_type'],
								'value'      => $_rule['value'],
								'conditions' => array(),
							);
						}

					}

				}
			}

			$price['rules'] = $table_data;

		}
		if ( $field_price !== NULL ) {
			$price = $field_price;
		}

		return $price;
	}

}
