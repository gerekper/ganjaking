<?php
/**
 * Main class.
 *
 * @package YITH WooCommerce Dynamic Pricing and Discounts Premium
 * @since   1.0.0
 * @version 1.6.0
 * @author  YITH
 *
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWDPD_VERSION' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Implements features of YITH WooCommerce Dynamic Pricing and Discounts
 *
 * @class   YITH_WC_Dynamic_Pricing
 * @package YITH WooCommerce Dynamic Pricing and Discounts
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_WC_Dynamic_Pricing' ) ) {

	/**
	 * Class YITH_WC_Dynamic_Pricing
	 */
	class YITH_WC_Dynamic_Pricing {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WC_Dynamic_Pricing
		 */

		protected static $instance;

		/**
		 * The name for the plugin options
		 *
		 * @access public
		 * @var string
		 * @since  1.0.0
		 */
		public $plugin_options = 'yit_ywdpd_options';

		public $validated_rules = array();

		public $exclusion_rules = array();

		public $adjust_rules = array();

		public $adjust_counter = array();

		public $pricing_rules_options = array();

		public $cart_rules_options = array();

		protected $check_discount = array();

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WC_Dynamic_Pricing
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {

			$this->pricing_rules_options = include YITH_YWDPD_DIR . 'plugin-options/pricing-rules-options.php';
			$this->cart_rules_options    = include YITH_YWDPD_DIR . 'plugin-options/cart-rules-options.php';

			/* plugin */
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

			$wpml_extend_to_translated_object = $this->get_option( 'wpml_extend_to_translated_object', 'no' );
			if ( defined( 'ICL_SITEPRESS_VERSION' ) && ywdpd_is_true( $wpml_extend_to_translated_object ) ) {
				add_filter( 'ywdpd_pricing_rules_filtered', array( $this, 'adjust_rules_for_wpml' ) );
			}

			add_filter(
				'ywdpd_change_dynamic_price',
				array(
					$this,
					'calculate_role_price_for_fix_dynamic_price',
				),
				10,
				3
			);

			add_action( 'yith_dynamic_pricing_after_apply_discounts', array( $this, 'change_default_price' ), 20, 1 );

			if ( defined( 'ELEMENTOR_VERSION' ) ) {
				require_once YITH_YWDPD_INC . '/compatibility/elementor/class.yith-wc-dynamic-elementor.php';
			}

		}


		/**
		 * @param $rules
		 *
		 * @return mixed
		 */
		function adjust_rules_for_wpml( $rules ) {
			global $sitepress;
			$has_wpml = ! empty( $sitepress ) ? true : false;

			if ( ! $has_wpml ) {
				return $rules;
			}

			foreach ( $rules as &$rule ) {
				if ( isset( $rule['apply_to'] ) && isset( $rule[ 'apply_to_' . $rule['apply_to'] ] ) ) {
					$rule[ 'apply_to_' . $rule['apply_to'] ] = YITH_WC_Dynamic_Pricing_Helper()->wpml_product_list_adjust( $rule[ 'apply_to_' . $rule['apply_to'] ], $rule['apply_to'] );
				}

				if ( isset( $rule['apply_adjustment'] ) && isset( $rule[ 'apply_adjustment_' . $rule['apply_adjustment'] ] ) ) {
					$rule[ 'apply_adjustment_' . $rule['apply_adjustment'] ] = YITH_WC_Dynamic_Pricing_Helper()->wpml_product_list_adjust( $rule[ 'apply_adjustment_' . $rule['apply_adjustment'] ], $rule['apply_adjustment'] );
				}
			}

			return $rules;
		}

		/**
		 * Return pricing rules filtered and validates
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		function get_pricing_rules() {

			$pricing_rules = (array) $this->filter_valid_rules( $this->recover_pricing_rules() );

			return $pricing_rules;
		}

		/**
		 * @return array
		 */
		function get_gift_rules() {

			$all_rules = $this->get_pricing_rules();

			$gift_rules = array();

			if ( ! empty( $all_rules ) ) {

				foreach ( $all_rules as $key => $rule ) {

					if ( isset( $rule['discount_mode'] ) && 'gift_products' == $rule['discount_mode'] ) {

						$gift_rules[ $key ] = $rule;
					}
				}
			}

			return $gift_rules;
		}

		/**
		 * @return array
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		function recover_pricing_rules() {
			if ( get_option( 'ywdpd_updated_to_cpt' ) !== 'yes' ) {
				$pricing_rules = $this->get_option( 'pricing-rules' );
			} else {
				$pricing_rules = ywdpd_recover_rules( 'pricing' );
			}

			return $pricing_rules;
		}

		/**
		 * Return pricing rules validates
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @param $pricing_rules
		 *
		 * @return array
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		function filter_valid_rules( $pricing_rules ) {

			if ( ! $pricing_rules || ! is_array( $pricing_rules ) ) {
				return;
			}

			foreach ( $pricing_rules as $key => $rule ) {

				// check if the rule is active of the value of discount_amount is empty
				if ( ! ywdpd_is_true( $rule['active'] ) ) {
					continue;
				}

				// DATE SCHEDULE VALIDATION
				if ( isset( $rule['schedule_from'] ) && isset( $rule['schedule_to'] ) && ( $rule['schedule_from'] != '' || $rule['schedule_to'] != '' ) ) {
					if ( ! YITH_WC_Dynamic_Pricing_Helper()->validate_schedule( $rule['schedule_from'], $rule['schedule_to'] ) ) {
						continue;
					}
				}

				// USER VALIDATION
				if ( isset( $rule['user_rules'] ) && ( $rule['user_rules'] != 'everyone' && isset( $rule[ 'user_rules_' . $rule['user_rules'] ] ) && ! YITH_WC_Dynamic_Pricing_Helper()->validate_user( $rule['user_rules'], $rule[ 'user_rules_' . $rule['user_rules'] ] ) ) ) {
					continue;
				}

				// PRODUCTS VALIDATION (APPLY TO) check if the list of products or categories is empty
				if ( isset( $rule['apply_to'] ) && $rule['apply_to'] != 'all_products' && isset( $rule[ 'apply_to_' . $rule['apply_to'] ] ) && ! is_array( $rule[ 'apply_to_' . $rule['apply_to'] ] ) ) {
					continue;
				}
				// PRODUCTS VALIDATION (ADJUSTMENT) check if the list of products or categories is empty
				if ( isset( $rule['apply_adjustment'] ) && ( $rule['apply_adjustment'] != 'all_products' && $rule['apply_adjustment'] != 'same_product' ) && isset( $rule[ 'apply_adjustment_' . $rule['apply_adjustment'] ] ) && ! is_array( $rule[ 'apply_adjustment_' . $rule['apply_adjustment'] ] ) ) {
					continue;
				}

				// CHECK IF IS A GIFT PRODUCT RULE
				if ( ( isset( $rule['discount_mode'] ) && 'gift_products' == $rule['discount_mode'] ) && ( $rule['apply_adjustment'] != 'all_products' && $rule['apply_adjustment'] != 'same_product' ) && isset( $rule[ 'apply_adjustment_' . $rule['apply_adjustment'] ] ) && ! is_array( $rule[ 'apply_adjustment_' . $rule['apply_adjustment'] ] ) ) {
					continue;
				}

				// DISCOUNT RULES VALIDATION
				if ( isset( $rule['discount_mode'] ) && $rule['discount_mode'] == 'bulk' ) {
					if ( isset( $rule['rules'] ) ) {
						foreach ( $rule['rules'] as $index => $discount_rule ) {

							if ( $discount_rule['min_quantity'] == '' || $discount_rule['min_quantity'] == 0 ) {
								$rule['rules'][ $index ]['min_quantity'] = 1;
							}

							if ( $discount_rule['max_quantity'] == '' ) {
								$rule['rules'][ $index ]['max_quantity'] = '*';
							}

							if ( isset( $discount_rule['type_discount'] ) && $discount_rule['type_discount'] == 'percentage' && $discount_rule['discount_amount'] > 0 ) {
								$rule['rules'][ $index ]['discount_amount'] = floatval( $discount_rule['discount_amount'] ) / 100;
							}
						}
					}
				} elseif ( isset( $rule['discount_mode'] ) && $rule['discount_mode'] == 'special_offer' ) {
					$special_offer = $rule['so-rule'];

					if ( $special_offer['purchase'] == '' || $special_offer['purchase'] == 0 ) {
						$rule['so-rule']['purchase'] = 1;
					}

					if ( $special_offer['receive'] == '' ) {
						$rule['so-rule']['receive'] = '*';
					}

					if ( $special_offer['type_discount'] == 'percentage' && $special_offer['discount_amount'] > 0 ) {
						$rule['so-rule']['discount_amount'] = $special_offer['discount_amount'] / 100;
					}
				}

				$this->validated_rules[ $key ] = $rule;

			}

			$this->validated_rules = apply_filters( 'ywdpd_pricing_rules_filtered', $this->validated_rules );

			return $this->validated_rules;
		}

		/**
		 * Add applied rules to single cart item
		 *
		 * @param $cart_item_key
		 * @param $cart_item
		 *
		 * @return bool
		 * @author Emanuela Castorina
		 *
		 * @since  1.0.0
		 */
		function get_applied_rules_to_product( $cart_item_key, $cart_item ) {

			$exclude = apply_filters( 'ywdpd_get_applied_rules_to_product_exclude', empty( $cart_item ), $cart_item );

			if ( $exclude ) {
				return false;
			}

			foreach ( $this->validated_rules as $key_rule => $rule ) {

				// DISCOUNT CAN BE COMBINED WITH COUPON
				$with_other_coupons = isset( $rule['disable_with_other_coupon'] ) && ywdpd_is_true( $rule['disable_with_other_coupon'] );
				if ( $with_other_coupons && ywdpd_check_cart_coupon() ) {
					continue;
				}

				if ( ! YITH_WC_Dynamic_Pricing_Helper()->validate_apply_to( $key_rule, $rule, $cart_item_key, $cart_item ) ) {
					continue;
				}
			}
		}


		/**
		 * Add applied rules to single cart item
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		function get_exclusion_rules() {
			if ( ! empty( $this->exclusion_rules ) ) {
				return $this->exclusion_rules;
			}

			$exclusion_rules = array();

			foreach ( $this->validated_rules as $rule ) {
				if ( $rule['discount_mode'] == 'exclude_items' ) {
					$exclusion_rules[] = $rule;
				}
			}

			$this->exclusion_rules = $exclusion_rules;

			return $this->exclusion_rules;
		}

		/**
		 * @param WC_Product $product
		 *
		 * @return bool
		 */
		function check_discount( $product ) {

			if ( apply_filters( 'ywdpd_exclude_products_from_discount', false, $product ) ) {
				return false;
			}
			/*
			elseif ( isset( $this->check_discount[ $product->get_id() ] ) ) {
				return $this->check_discount[ $product->get_id() ];
			}
			**/

			$return          = false;
			$is_single_check = apply_filters( 'ywdpd_check_if_single_page', is_single() );

			foreach ( $this->validated_rules as $rule ) {
				if ( ( YITH_WC_Dynamic_Pricing_Helper()->valid_product_to_apply_bulk( $rule, $product, true ) && YITH_WC_Dynamic_Pricing_Helper()->valid_product_to_adjustment_bulk( $rule, $product, true ) ) && ( $is_single_check || ( isset( $rule['show_in_loop'] ) && ywdpd_is_true( $rule['show_in_loop'] ) && $rule['apply_adjustment'] == 'same_product' ) ) ) {

					$return = true;
				}
			}

			$this->check_discount[ $product->get_id() ] = $return;

			return $return;
		}

		/**
		 * @param $default_price
		 * @param $product
		 *
		 * @return int
		 */
		function get_discount_price( $default_price, $product ) {

			if ( empty( $default_price ) ) {
				return $default_price;
			}

			$current_difference = 0;

			$discount_price = floatval( $default_price );

			foreach ( $this->validated_rules as $rule ) {
				$even_onsale = isset( $rule['apply_on_sale'] ) && ywdpd_is_true( $rule['apply_on_sale'] );
				$sale_price  = yit_get_prop( $product, 'sale_price', true, 'edit' );

				if ( $sale_price != '' && $sale_price != $default_price && ! $even_onsale ) {
					continue;
				}

				if ( YITH_WC_Dynamic_Pricing_Helper()->valid_product_to_apply_bulk( $rule, $product, false ) && ( is_single() || ( isset( $rule['show_in_loop'] ) && ywdpd_is_true( $rule['show_in_loop'] ) && $rule['apply_adjustment'] == 'same_product' ) ) ) {

					// $is_exclusive = ( isset( $rule['apply_with_other_rules'] ) && $rule['apply_with_other_rules'] == 1 ) ? 0 : 1;

					if ( $rule && isset( $rule['rules'] ) && $rule['rules'] ) {
						foreach ( $rule['rules'] as $qty_rule ) {
							if ( $qty_rule['min_quantity'] == 1 && is_numeric( $qty_rule['discount_amount'] ) ) {
								switch ( $qty_rule['type_discount'] ) {
									case 'percentage':
										$current_difference = $discount_price * $qty_rule['discount_amount'];
										break;
									case 'price':
										$current_difference = $qty_rule['discount_amount'];
										break;
									case 'fixed-price':
										$current_difference = $discount_price - $qty_rule['discount_amount'];
										break;
									default:
								}
							}
						}

						$discount_price = ( ( $discount_price - $current_difference ) < 0 ) ? 0 : ( $discount_price - $current_difference );

					}

					// if( $is_exclusive ){
					break;
					// }
				}
			}

			return apply_filters( 'yith_ywdpd_get_discount_price', $discount_price );
		}

		/**
		 * Return all adjustments to single cart item
		 *
		 * @param      $cart_item
		 * @param      $cart_item_key
		 *
		 * @param bool $reset
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function apply_discount( $cart_item, $cart_item_key, $reset = false ) {

			$this->adjust_counter = $reset ? array() : $this->adjust_counter;

			$discounts     = $cart_item['ywdpd_discounts'];
			$product_id    = ( isset( $cart_item['variation_id'] ) && $cart_item['variation_id'] != '' ) ? $cart_item['variation_id'] : $cart_item['product_id'];
			$product       = wc_get_product( $product_id );
			$has_exclusive = $this->has_exclusive( $discounts );
			$back          = false;
			remove_filter(
				'woocommerce_product_get_price',
				array(
					YITH_WC_Dynamic_Pricing_Frontend(),
					'get_price',
				),
				10
			);
			$default_price = $cart_item['data']->get_price();
			$price         = $current_price = $default_price;
			$difference    = 0;

			foreach ( $discounts as $discount ) {

				if ( ! isset( $discount['discount_amount'] ) || ! isset( $discount['discount_mode'] ) ) {
					continue;
				}

				$dm  = $discount['discount_amount'];
				$key = $discount['key'];

				if ( $dm == 'exclude' ) {
					$price      = $current_price = $default_price;
					$difference = 0;
				}

				if ( ! $discount['onsale'] && ( ( $product->get_sale_price() !== '' and $product->get_sale_price() > 0 ) && $product->get_sale_price() !== $product->get_regular_price() ) ) {
					continue;
				}

				// check if the discount has an exclusive rule
				if ( $has_exclusive && ! $discount['exclusive'] ) {
					continue;
				}

				$current_difference = apply_filters( 'ywdpd_apply_discount_current_difference', 0, $discount, $dm, $cart_item, $cart_item_key, $price );
				if ( $discount['discount_mode'] == 'bulk' && isset( $dm['type'] ) ) {
					switch ( $dm['type'] ) {
						case 'percentage':
							$current_difference = $price * $dm['amount'];
							$back               = true;
							break;
						case 'price':
							$current_difference = $dm['amount'];
							break;
						case 'fixed-price':
							$amount             = apply_filters( 'ywdpd_maybe_should_be_converted', $dm['amount'] );
							$current_difference = $price - $dm['amount'];
							break;
						default:
					}
				} elseif ( $discount['discount_mode'] == 'special_offer' && isset( $dm['type'] ) ) {

					// error_log( 'id '. $cart_item['product_id'] );
					// error_log('available_quantity '.$cart_item['available_quantity']);
					// calculate new price
					$parent_id = yit_get_base_product_id( $product );

					if ( $dm['same_product'] && ( in_array(
						$dm['quantity_based'],
						array(
							'cart_line',
							'single_variation_product',
						)
					) ) ) {
						$adj_counter = $this->adjust_counter[ $key ] = $dm['total_target'];
					} elseif ( $dm['same_product'] && ( $dm['quantity_based'] == 'single_product' && $product->is_type( 'variation' ) ) ) {
						$adj_counter = $this->adjust_counter[ $key . $parent_id ] = isset( $this->adjust_counter[ $key . $parent_id ] ) ? $this->adjust_counter[ $key . $parent_id ] : $dm['total_target'];
					} else {
						$adj_counter = $this->adjust_counter[ $key ] = isset( $this->adjust_counter[ $key ] ) ? $this->adjust_counter[ $key ] : $dm['total_target'];

					}

					$a = ( $adj_counter > $cart_item['quantity'] ) ? $cart_item['quantity'] : $adj_counter;

					$full_price_quantity = $cart_item['available_quantity'] - $a;
					$discount_quantity   = $a;
					$normal_line_total   = $cart_item['quantity'] * $price;

					switch ( $dm['type'] ) {
						case 'percentage':
							$difference_s       = $price - $price * $dm['amount'];
							$line_total         = ( $discount_quantity * $difference_s ) + ( $full_price_quantity * $price );
							$current_difference = ( $normal_line_total - $line_total ) / $cart_item['quantity'];
							$current_difference = $current_difference >= 0 ? $current_difference : 0;
							break;

						case 'price':
							$difference_s       = floatval( $price ) - floatval( $dm['amount'] );
							$difference_s       = $difference_s >= 0 ? $difference_s : 0;
							$line_total         = ( $discount_quantity * $difference_s ) + ( $full_price_quantity * $price );
							$current_difference = ( $normal_line_total - $line_total ) / $cart_item['quantity'];
							$current_difference = $current_difference >= 0 ? $current_difference : 0;
							break;
						case 'fixed-price':
							$difference_s       = apply_filters( 'ywdpd_maybe_should_be_converted', $dm['amount'] );
							$line_total         = ( $discount_quantity * $difference_s ) + ( $full_price_quantity * $price );
							$current_difference = ( $normal_line_total - $line_total ) / $cart_item['quantity'];
							$current_difference = $current_difference >= 0 ? $current_difference : 0;
							break;
						default:
					}

					if ( $dm['same_product'] && $dm['quantity_based'] == 'single_product' && $product->is_type( 'variation' ) ) {
						if ( $dm['total_target'] >= $cart_item['quantity'] ) {
							$this->adjust_counter[ $key . $parent_id ]                        = $this->adjust_counter[ $key . $parent_id ] - $cart_item['quantity'];
							WC()->cart->cart_contents[ $cart_item_key ]['available_quantity'] = 0;
						} else {
							WC()->cart->cart_contents[ $cart_item_key ]['available_quantity'] = $cart_item['quantity'] - $adj_counter;
							$this->adjust_counter[ $key . $parent_id ]                        = 0;
						}
					} else {
						if ( $dm['total_target'] > $cart_item['quantity'] ) {
							$this->adjust_counter[ $key ]                                    -= $cart_item['quantity'];
							WC()->cart->cart_contents[ $cart_item_key ]['available_quantity'] = 0;
						} else {
							WC()->cart->cart_contents[ $cart_item_key ]['available_quantity'] = $cart_item['quantity'] - $adj_counter;
							$this->adjust_counter[ $key ]                                     = 0;
						}
					}
				}

				$difference += $current_difference;

				$price = ( ( $default_price - $difference ) < 0 ) ? 0 : ( $default_price - $difference );

				if ( apply_filters( 'ywdpd_round_total_price', true ) ) {
					$price = round( $price, wc_get_price_decimals() );
				}

				WC()->cart->cart_contents[ $cart_item_key ]['ywdpd_discounts'][ $discount['key'] ]['status']           = 'applied';
				WC()->cart->cart_contents[ $cart_item_key ]['ywdpd_discounts'][ $discount['key'] ]['discount_applied'] = $current_difference;
				WC()->cart->cart_contents[ $cart_item_key ]['ywdpd_discounts'][ $discount['key'] ]['current_price']    = $price;
				WC()->cart->cart_contents[ $cart_item_key ]['ywdpd_discounts'][ $discount['key'] ]['discount_type']    = $dm['type'];

				$price = apply_filters( 'ywdpd_change_dynamic_price', $price, $cart_item_key, $discount );
				// check if the discount has an exclusive rule
				if ( $has_exclusive && $discount['exclusive'] && $difference != 0 ) {
					break;
				}
			}
			remove_filter(
				'woocommerce_product_get_price',
				array(
					YITH_WC_Dynamic_Pricing_Frontend(),
					'get_price',
				),
				10
			);
			WC()->cart->cart_contents[ $cart_item_key ]['ywdpd_discounts']['default_price'] = ( WC()->cart->tax_display_cart == 'excl' ) ? yit_get_price_excluding_tax( $product ) : yit_get_price_including_tax( $product );
			add_filter(
				'woocommerce_product_get_price',
				array(
					YITH_WC_Dynamic_Pricing_Frontend(),
					'get_price',
				),
				10,
				2
			);

			if ( class_exists( 'WOOCS' ) && $back ) {
				global $WOOCS; //phpcs:ignore
				if ( $WOOCS->current_currency != $WOOCS->default_currency and $WOOCS->is_multiple_allowed ) { //phpcs:ignore
					$currencies = $WOOCS->get_currencies(); //phpcs:ignore
					$price      = $price / $currencies[ $WOOCS->current_currency ]['rate']; //phpcs:ignore
				}
			}

			WC()->cart->cart_contents[ $cart_item_key ]['data']->set_price( $price );
			$product = WC()->cart->cart_contents[ $cart_item_key ]['data'];
			yit_set_prop( $product, 'has_dynamic_price', true );

			do_action( 'yith_dynamic_pricing_after_apply_discounts', $cart_item_key );
		}

		/**
		 * @param $discounts
		 *
		 * @return bool
		 */
		function has_exclusive( $discounts ) {
			foreach ( $discounts as $discount ) {
				if ( isset( $discount['exclusive'] ) && $discount['exclusive'] == 1 ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Check if a product has specific categories
		 *
		 * @param $product_id
		 * @param $categories
		 * @param $min_amount
		 *
		 * @return bool
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 *
		 * @deprecated
		 */
		function product_categories_validation( $product_id, $categories, $min_amount ) {
			$categories_cart         = YITH_WC_Dynamic_Pricing_Helper()->cart_categories;
			$intersect_cart_category = array_intersect( $categories, $categories_cart );

			$return = false;

			if ( is_array( $intersect_cart_category ) ) {
				$categories_counter         = YITH_WC_Dynamic_Pricing_Helper()->categories_counter;
				$categories_of_item         = wc_get_product_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );
				$intersect_product_category = array_intersect( $categories_of_item, $categories );

				if ( is_array( $intersect_product_category ) ) {
					$tot = 0;
					foreach ( $categories as $cat ) {
						$tot += $categories_counter[ $cat ];
					}

					if ( $tot >= $min_amount ) {
						$return = true;
					}
				}
			}

			return $return;

		}

		/**
		 * Check if a product has specific tags
		 *
		 * @param $product_id
		 * @param $tags
		 * @param $min_amount
		 *
		 * @return bool
		 * @since  1.1.0
		 * @author Emanuela Castorina
		 *
		 * @deprecated
		 */
		function product_tags_validation( $product_id, $tags, $min_amount ) {
			$tags_cart          = YITH_WC_Dynamic_Pricing_Helper()->cart_tags;
			$intersect_cart_tag = array_intersect( $tags, $tags_cart );

			$return = false;

			if ( is_array( $intersect_cart_tag ) ) {
				$tags_counter          = YITH_WC_Dynamic_Pricing_Helper()->tags_counter;
				$tags_of_item          = wc_get_product_terms( $product_id, 'product_tag', array( 'fields' => 'ids' ) );
				$intersect_product_tag = array_intersect( $tags_of_item, $tags );

				if ( is_array( $intersect_product_tag ) ) {
					$tot = 0;
					foreach ( $tags as $tag ) {
						$tot += $tags_counter[ $tag ];
					}

					if ( $tot >= $min_amount ) {
						$return = true;
					}
				}
			}

			return $return;

		}

		/**
		 * Load YIT Plugin Framework
		 *
		 * @return void
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once $plugin_fw_file;
				}
			}
		}

		/**
		 * Get options from db
		 *
		 * @access  public
		 *
		 * @param string $option
		 *
		 * @param bool   $value
		 * @return mixed
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 */
		public function get_option( $option, $value = false ) {
			// get all options
			$options = get_option( $this->plugin_options );

			if ( isset( $options[ $option ] ) ) {
				return $options[ $option ];
			}

			return $value;
		}

		/**
		 * Calculate role price.
		 *
		 * @param float $price Price.
		 * @param string $cart_item_key Cart item key.
		 * @param array $discount Discount.
		 * @return mixed
		 */
		public function calculate_role_price_for_fix_dynamic_price( $price, $cart_item_key, $discount ) {

			if ( function_exists( 'YITH_Role_Based_Prices_Product' ) ) {
				if ( ! is_null( WC()->cart ) && isset( WC()->cart->cart_contents[ $cart_item_key ]['ywdpd_discounts'][ $discount['key'] ] ) ) {

					$dynamic_type = WC()->cart->cart_contents[ $cart_item_key ]['ywdpd_discounts'][ $discount['key'] ]['discount_type'];
					/**
					 * @var WC_Product $product
					 */
					$product = WC()->cart->cart_contents[ $cart_item_key ]['data'];

					$user_role = YITH_Role_Based_Prices_Product()->user_role['role'];

					if ( 'fixed-price' == $dynamic_type ) {

						yit_set_prop( $product, 'dynamic_fixed_price', $price );

						add_filter( 'ywcrbp_product_price_choose', array( $this, 'change_base_price' ), 10, 2 );

						YITH_Role_Based_Prices_Product()->load_global_rules();

						$global_rules = YITH_Role_Based_Type()->get_price_rule_by_user_role( $user_role, false );
						$new_price    = ywcrbp_calculate_product_price_role( $product, $global_rules, $user_role, $price );

						if ( 'no_price' !== $new_price ) {

							$price = $new_price;
							WC()->cart->cart_contents[ $cart_item_key ]['ywdpd_discounts'][ $discount['key'] ]['current_price'] = $price;
						}
						remove_filter( 'ywcrbp_product_price_choose', array( $this, 'change_base_price' ), 10 );

					}
				}
			}

			return $price;
		}

		/**
		 * Change base price.
		 *
		 * @param float      $price Price.
		 * @param WC_Product $product Product.
		 * @return float|mixed
		 */
		public function change_base_price( $price, $product ) {

			$dynamic_price = yit_get_prop( $product, 'dynamic_fixed_price', true );

			if ( $dynamic_price ) {
				$price = $dynamic_price;

			}

			return $price;
		}

		/**
		 * Change default price.
		 *
		 * @param string $cart_item_key Cart item key.
		 */
		public function change_default_price( $cart_item_key ) {

			$product = WC()->cart->cart_contents[ $cart_item_key ]['data'];

			$dynamic_price = yit_get_prop( $product, 'dynamic_fixed_price', true );
			if ( $dynamic_price ) {
				WC()->cart->cart_contents[ $cart_item_key ]['ywdpd_discounts']['default_price'] = $dynamic_price;
			}
		}


	}
}

/**
 * Unique access to instance of YITH_WC_Dynamic_Pricing class
 *
 * @return YITH_WC_Dynamic_Pricing
 */
function YITH_WC_Dynamic_Pricing() { //phpcs:ignore
	return YITH_WC_Dynamic_Pricing::get_instance();
}

