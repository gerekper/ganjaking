<?php
/**
 * Frontend class.
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
 * Implements frontend features of YITH WooCommerce Dynamic Pricing and Discounts
 *
 * @class   YITH_WC_Dynamic_Pricing_Frontend
 * @package YITH WooCommerce Dynamic Pricing and Discounts
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_WC_Dynamic_Pricing_Frontend' ) ) {

	/**
	 * Class YITH_WC_Dynamic_Pricing_Frontend
	 */
	class YITH_WC_Dynamic_Pricing_Frontend {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WC_Dynamic_Pricing_Frontend
		 */
		protected static $instance;

		/**
		 * Product filter.
		 * @var string
		 */
		public $get_product_filter;

		/**
		 * The pricing rules
		 *
		 * @access public
		 * @var array
		 * @since  1.0.0
		 */
		public $pricing_rules = array();

		/**
		 * Table of rules.
		 * @var array
		 */
		public $table_rules = array();

		/**
		 * Price filter list.
		 * @var array
		 */
		public $has_get_price_filter = array();

		/**
		 * Price html filter list.
		 * @var array
		 */
		public $has_get_price_html_filter = array();

		/**
		 * Cart processed flag.
		 * @var bool
		 */
		public $cart_processed = false;

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WC_Dynamic_Pricing_Frontend
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

			$enabled = YITH_WC_Dynamic_Pricing()->get_option( 'enabled' );

			if ( ! ywdpd_is_true( $enabled ) ) {
				return;
			}

			$posted = $_REQUEST;

			$this->get_product_filter = 'product_';

			if ( ( ! empty( $posted['add-to-cart'] ) && is_numeric( $posted['add-to-cart'] ) ) ||
			     ( isset( $posted['action'] ) && 'woocommerce_add_to_cart' == $posted['action'] ) ||
			     ( isset( $posted['wc-ajax'] ) && 'add_to_cart' == $posted['wc-ajax'] )
			) {
				add_action( 'woocommerce_add_to_cart', array( $this, 'cart_process_discounts' ), 99 );

			} else {
				if ( empty( $posted['apply_coupon'] ) || empty( $posted['coupon_code'] ) ) {
					add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'cart_process_discounts' ), 99 );
				} else {
					add_action( 'woocommerce_applied_coupon', array( $this, 'cart_process_discounts' ), 9 );
				}
			}

			add_action( 'yith_wacp_before_popup_content', array( $this, 'cart_process_discounts' ), 99 );

			// Filters to format prices.
			add_filter( 'woocommerce_get_price_html', array( &$this, 'get_price_html' ), 10, 2 );
			add_filter( 'woocommerce_get_variation_price_html', array( &$this, 'get_price_html' ), 10, 2 );
			add_filter( 'woocommerce_product_get_price', array( $this, 'get_price' ), 10, 2 );
			add_filter( 'woocommerce_product_variation_get_price', array( $this, 'get_price' ), 10, 2 );
			add_filter( 'woocommerce_cart_item_price', array( $this, 'replace_cart_item_price' ), 100, 3 );
			add_filter( 'woocommerce_coupon_error', array( $this, 'remove_coupon_cart_message' ), 10, 3 );
			add_filter( 'woocommerce_coupon_message', array( $this, 'remove_coupon_cart_message' ), 10, 3 );

			add_shortcode( 'yith_ywdpd_quantity_table', array( $this, 'table_quantity_shortcode' ) );
			add_shortcode( 'yith_ywdpd_product_note', array( $this, 'product_note_shortcode' ) );

			add_action( 'init', array( $this, 'init' ), 10 );

			$priority = ( function_exists( 'YITH_WCCL_Frontend' ) ) ? 5 : 10;

			// custom styles and javascripts.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ), $priority );

		}

		/**
		 * Init function.
		 */
		public function init() {
			$this->pricing_rules = YITH_WC_Dynamic_Pricing()->get_pricing_rules();

			// Quantity table.
			$show_quantity_table = YITH_WC_Dynamic_Pricing()->get_option( 'show_quantity_table' );
			if ( ywdpd_is_true( $show_quantity_table ) ) {
				$this->table_quantity_init();
				add_filter( 'woocommerce_available_variation', array(
					$this,
					'add_params_to_available_variation'
				), 10, 3 );
			}

			// Notes on products.
			$show_note_on_products = YITH_WC_Dynamic_Pricing()->get_option( 'show_note_on_products' );
			if ( ywdpd_is_true( $show_note_on_products ) ) {
				$this->note_on_products_init();
			}

		}

		/**
		 * Remove from cart only dynamic coupons
		 *
		 * @since  1.2.0
		 * @author Emanuela Castorina
		 */
		public function remove_dynamic_coupons() {
			$applied_coupons = WC()->cart->get_applied_coupons();
			foreach ( $applied_coupons as $applied_coupon ) {
				$cp   = new WC_Coupon( $applied_coupon );
				$meta = $cp->get_meta( 'ywdpd_coupon', true );
				if ( ! empty( $meta ) ) {
					WC()->cart->remove_coupon( $cp->get_code() );
				}
			}
		}

		/**
		 * Remove coupon cart message.
		 *
		 * @param string $error Error Message.
		 * @param string $msg_code Code message.
		 * @param WC_Coupon $coupon Coupon.
		 *
		 * @return bool
		 */
		public function remove_coupon_cart_message( $error, $msg_code, $coupon ) {

			if ( preg_match_all( '/discount_[0-9]*/', $error ) ) {
				return false;
			}

			return $error;
		}

		/**
		 * Process dynamic pricing in cart
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function cart_process_discounts() {

			if ( empty( WC()->cart->cart_contents ) || $this->cart_processed ) {
				return;
			}

			remove_action( 'woocommerce_applied_coupon', array( $this, 'cart_process_discounts' ), 9 );
			$reset  = apply_filters( 'ywdpd_reset_previous_discounts', current_action() == 'yith_wacp_before_popup_content' );
			$posted = $_REQUEST;
			do_action( 'ywdpd_before_cart_process_discounts' );

			$remove_item = isset( $posted['remove_item'] ) ? $posted['remove_item'] : false;
			$this->remove_dynamic_coupons();

			WC()->session->set( 'refresh_totals', true );

			$cart_sort      = array();
			$bundled_cart   = array();
			$composite_cart = array();
			$mix_match_cart = array();

			// empty old discounts and reset the available quantity.
			foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {

				if ( $cart_item_key == $remove_item ) {
					continue;
				}

				// if the product is a bundle or a bundle item.
				if ( isset( $cart_item['bundled_by'] ) || isset( $cart_item['cartstamp'] ) ) {
					$bundled_cart[ $cart_item_key ] = WC()->cart->cart_contents[ $cart_item_key ];
				} elseif ( isset( $cart_item['mnm_config'] ) || isset( $cart_item['mnm_container'] ) ) {
					$mix_match_cart[ $cart_item_key ] = WC()->cart->cart_contents[ $cart_item_key ];
				} elseif ( isset( $cart_item['yith_wcp_component_data'] ) || isset( $cart_item['yith_wcp_child_component_data'] ) ) {
					$composite_cart[ $cart_item_key ] = WC()->cart->cart_contents[ $cart_item_key ];
				} else {
					WC()->cart->cart_contents[ $cart_item_key ]['available_quantity'] = $cart_item['quantity'];
					if ( isset( WC()->cart->cart_contents[ $cart_item_key ]['ywdpd_discounts'] ) ) {
						if ( $reset && isset( WC()->cart->cart_contents[ $cart_item_key ]['ywdpd_discounts']['default_price'] ) ) {
							$cart_product = WC()->cart->cart_contents[ $cart_item_key ]['data'];
							$cart_product->set_price( wc_prices_include_tax() ? wc_get_price_including_tax( $cart_product ) : wc_get_price_excluding_tax( $cart_product ) );
						}

						unset( WC()->cart->cart_contents[ $cart_item_key ]['ywdpd_discounts'] );
					}
					$cart_sort[ $cart_item_key ] = WC()->cart->cart_contents[ $cart_item_key ];
				}
			}

			@uasort( $cart_sort, 'YITH_WC_Dynamic_Pricing_Helper::sort_by_price' );

			if ( ( ! class_exists( 'WC_Composite_Products' ) && ! class_exists( 'YITH_WCP' ) && ! apply_filters( 'ywdpd_skip_cart_sorting', false ) ) || apply_filters( 'ywdpd_force_cart_sorting', false ) ) {
				WC()->cart->cart_contents = $cart_sort;
			}
			remove_filter( 'woocommerce_product_get_price', array( $this, 'get_price' ), 10 );
			// add processed pricing rules on each cart item.
			foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
				if ( ! YITH_WC_Dynamic_Pricing_Helper()->check_cart_item_filter_exclusion( $cart_item ) ) {
					YITH_WC_Dynamic_Pricing()->get_applied_rules_to_product( $cart_item_key, $cart_item );
				}
			}

			// apply the discount to each cart item.
			WC()->cart->calculate_totals();
			foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
				if ( isset( $cart_item['ywdpd_discounts'] ) && isset( $cart_item['data'] ) ) {
					YITH_WC_Dynamic_Pricing()->apply_discount( $cart_item, $cart_item_key, $reset );
				}
			}

			WC()->cart->cart_contents = array_merge( WC()->cart->cart_contents, $bundled_cart, $composite_cart, $mix_match_cart );
			WC()->cart->calculate_totals();
			if ( ! isset( $posted['remove_coupon'] ) ) {
				YITH_WC_Dynamic_Discounts()->apply_discount();

			}

			if ( isset( $posted['apply_coupon'] ) ) {
				unset( $_REQUEST['apply_coupon'] );
			}

			do_action( 'ywdpd_after_cart_process_discounts' );

			$this->cart_processed = true;
			add_filter( 'woocommerce_product_get_price', array( $this, 'get_price' ), 10, 2 );
			add_action( 'woocommerce_applied_coupon', array( $this, 'cart_process_discounts' ), 9 );
		}

		/**
		 * Replace the price in the cart
		 *
		 * @param float $price Price.
		 * @param array $cart_item Cart item.
		 * @param string $cart_item_key Cart item key.
		 *
		 * @return mixed|string
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 *
		 */
		public function replace_cart_item_price( $price, $cart_item, $cart_item_key ) {

			do_action( 'ywdpd_before_replace_cart_item_price', $price, $cart_item, $cart_item_key );

			if ( ! isset( $cart_item['ywdpd_discounts'] ) || ! isset( $cart_item['data'] ) || ! isset( WC()->cart ) || YITH_WC_Dynamic_Pricing_Helper()->check_cart_item_filter_exclusion( $cart_item ) ) {
				return $price;
			}

			$old_price = $price;
			remove_filter( 'woocommerce_product_get_price', array( $this, 'get_price' ), 10 );
			foreach ( $cart_item['ywdpd_discounts'] as $discount ) {
				if ( isset( $discount['status'] ) && 'applied' == $discount['status'] ) {
					if ( ( (float) $cart_item['ywdpd_discounts']['default_price'] > (float) $cart_item['data']->get_price() ) && wc_price( $cart_item['ywdpd_discounts']['default_price'] ) !== WC()->cart->get_product_price( $cart_item['data'] ) ) {
						$price = '<del>' . wc_price( $cart_item['ywdpd_discounts']['default_price'] ) . '</del> ' . WC()->cart->get_product_price( $cart_item['data'] );
						break;
					} else {
						return $price;
					}
				}
			}

			$price = apply_filters( 'ywdpd_replace_cart_item_price', $price, $old_price, $cart_item, $cart_item_key );

			WC()->cart->calculate_totals();

			return $price;
		}

		/**
		 * Add custom params to variations
		 *
		 * @access public
		 *
		 * @param array $args Arguments.
		 * @param WC_Product $product Product.
		 * @param WC_Product $variation Variation.
		 *
		 * @return array
		 * @since  1.1.1
		 */
		public function add_params_to_available_variation( $args, $product, $variation ) {

			$args['table_price'] = $this->table_quantity( $variation );

			return $args;
		}

		/**
		 * Show table quantity in the single product if there's a pricing rule
		 *
		 * @since  1.0.0
		 */
		public function show_note_on_products( $product = false ) {

			if( ! $product ){
				global $product;
			}

			$valid_rules = $this->pricing_rules;

			if ( empty( $valid_rules ) || YITH_WC_Dynamic_Pricing_Helper()->is_in_exclusion_rule( array( 'product_id' => $product->get_id() ) ) ) {
				return;
			}

			$has_exclusive   = false;
			$exclusive_count = 0;

			foreach ( $valid_rules as $rule ) {
				if ( isset( $rule['apply_with_other_rules'] ) && ! ywdpd_is_true( $rule['apply_with_other_rules'] ) ) {
					$has_exclusive = true;
				}
			}


			foreach ( $valid_rules as $rule ) {

				// check if the discount has an exclusive rule.
				if ( $has_exclusive && ( ( isset( $rule['apply_with_other_rules'] ) && ywdpd_is_true( $rule['apply_with_other_rules'] ) ) || 1 == $exclusive_count ) ) {
					continue;
				}

				$show_onsale = apply_filters( 'ywdpd_show_note_on_sale', isset( $rule['apply_on_sale'] ) && ywdpd_is_true( $rule['apply_on_sale'] ), $rule );

				if ( ! $show_onsale && ( $product->get_sale_price() !== $product->get_regular_price() && $product->get_sale_price() === $product->get_price() ) ) {
					continue;
				}

				if ( apply_filters(
					'ywdpd_show_note_apply_to',
					isset( $rule['table_note_apply_to'] ) && '' != $rule['table_note_apply_to'] && in_array(
						$rule['discount_mode'],
						array(
							'bulk',
							'special_offer',
						)
					) && YITH_WC_Dynamic_Pricing_Helper()->valid_product_to_apply(
						$rule,
						$product,
						true
					),
					$rule,
					$product
				)
				) {
					$exclusive_count = 1;
					echo '<div class="show_note_on_apply_products">' . wp_kses_post( stripslashes( ywdpd_get_note( $rule['table_note_apply_to'] ) ) ) . '</div>';
				}

				if ( isset( $rule['table_note_adjustment_to'] ) && '' != $rule['table_note_adjustment_to'] && in_array(
						$rule['discount_mode'],
						array(
							'bulk',
							'special_offer',
						)
					) && YITH_WC_Dynamic_Pricing_Helper()->valid_product_to_adjust( $rule, array( 'product_id' => $product->get_id() ) )
				) {
					$exclusive_count = 1;
					echo '<div class="show_note_on_apply_products">' . wp_kses_post( stripslashes( ywdpd_get_note( $rule['table_note_adjustment_to'] ) ) ) . '</div>';
				}
			}
		}

		/**
		 * Get html price.
		 *
		 * @param float $price Price.
		 * @param WC_Product|WC_Product_Variable $product Product.
		 *
		 * @return mixed|string
		 */
		public function get_price_html( $price, $product ) {

			global $woocommerce_loop;

			if ( ( ( is_cart() || is_checkout() ) && is_null( $woocommerce_loop ) ) || ! YITH_WC_Dynamic_Pricing()->check_discount( $product ) ) {
				return $price;
			}

			$product_id = $product->get_id();

			if ( array_key_exists( $product_id, $this->has_get_price_html_filter ) || apply_filters( 'ywdpd_get_price_html_exclusion', false, $price, $product ) ) {
				return isset( $this->has_get_price_html_filter[ $product_id ] ) ? $this->has_get_price_html_filter[ $product_id ] : $price;
			}

			do_action( 'yith_ywdpd_get_price_and_discount_before' );

			$display_regular_price = wc_get_price_to_display(
				$product,
				array(
					'qty'   => 1,
					'price' => $product->get_price( 'edit' ),
				)
			);

			$display_regular_price = apply_filters( 'ywdpd_maybe_should_be_converted', $display_regular_price );

			$price_format        = YITH_WC_Dynamic_Pricing()->get_option( 'price_format', '<del>%original_price%</del> %discounted_price%' );
			$new_price           = $price_format;
			$percentual_discount = '';
			$discount_html       = '';

			if ( $product->is_type( 'variable' ) ) {

				$prices = array(
					$product->get_variation_price( 'min', true ),
					$product->get_variation_price( 'max', true ),
				);

				$min_variation_regular_price = $this->get_min_regular_variation_price( $product );
				$max_variation_regular_price = $this->get_max_regular_variation_price( $product );

				remove_filter( 'woocommerce_product_get_price', array( $this, 'get_price' ), 10 );
				$show_minimum_price = YITH_WC_Dynamic_Pricing()->get_option( 'show_minimum_price' );
				if ( ywdpd_is_true( $show_minimum_price ) ) {
					$discount     = $this->get_minimum_price( $product );
					$discount_max = $this->get_maximum_price( $product );
				} else {
					$discount_max = $this->get_maximum_price( $product, 1 );
					$discount     = $this->get_minimum_price( $product, 1 );
				}
				add_filter( 'woocommerce_product_get_price', array( $this, 'get_price' ), 10, 2 );

				if ( $prices[0] == $prices[1] && $min_variation_regular_price == $prices[0] ) {

					$display_regular_price = wc_get_price_to_display(
						$product,
						array(
							'qty'   => 1,
							'price' => $this->get_min_regular_variation_price( $product ),
						)
					);

					remove_filter( 'woocommerce_product_get_price', array( $this, 'get_price' ), 10 );
					$show_minimum_price = YITH_WC_Dynamic_Pricing()->get_option( 'show_minimum_price' );
					if ( ywdpd_is_true( $show_minimum_price ) ) {
						$discount = wc_get_price_to_display(
							$product,
							array(
								'qty'   => 1,
								'price' => $this->get_minimum_price( $product ),
							)
						);
					} else {
						$discount = wc_get_price_to_display(
							$product,
							array(
								'qty'   => 1,
								'price' => $this->get_minimum_price(
									$product,
									1
								),
							)
						);
					}

					add_filter( 'woocommerce_product_get_price', array( $this, 'get_price' ), 10, 2 );

					$discount_html = wc_price( $discount );

					if ( $display_regular_price ) {
						$per_disc = 100 - ( (float) $discount / $display_regular_price * 100 );
						if ( $per_disc > 0 ) {
							$percentual_discount = apply_filters( 'ywdpd_percentual_discount', '-' . number_format( $per_disc, 2, '.', '' ) . '%', $per_disc );
						}
					}
				} else {

					if ( $discount != $min_variation_regular_price || $discount_max != $max_variation_regular_price ) {
						$dp_min_variation_regular_price = wc_get_price_to_display( $product, array( 'price' => $min_variation_regular_price ) );

						$dp_max_variation_regular_price = wc_get_price_to_display( $product, array( 'price' => $max_variation_regular_price ) );

						if ( $min_variation_regular_price < $max_variation_regular_price ) {
							$display_regular_price = apply_filters( 'ywdpd_change_variable_products_html_regular_price', wc_price( $dp_min_variation_regular_price ) . '-' . wc_price( $dp_max_variation_regular_price ), $dp_min_variation_regular_price, $dp_max_variation_regular_price );

						} else {
							$display_regular_price = wc_price( $dp_min_variation_regular_price );
						}

						if ( $this->has_markup_rule( $product ) ) {
							$display_regular_price = '';
						}

						$new_price = str_replace( '%original_price%', $display_regular_price, $new_price );

						$dp_discount     = wc_get_price_to_display( $product, array( 'price' => $discount ) );
						$dp_discount_max = wc_get_price_to_display( $product, array( 'price' => $discount_max ) );

						if ( $discount_max != $discount ) {
							$discount_html = apply_filters( 'ywdpd_change_variable_products_html_discount_price', wc_price( $dp_discount ) . '-' . wc_price( $dp_discount_max ), $dp_discount, $dp_discount_max );
						} else {
							$discount_html = wc_price( $dp_discount );
						}

						if ( 0 !== $min_variation_regular_price && 0.00 != $min_variation_regular_price ) {
							$per_disc = 100 - ( $discount / $min_variation_regular_price * 100 );
							if ( $per_disc > 0 ) {
								$percentual_discount = apply_filters( 'ywdpd_percentual_discount', '-' . number_format( $per_disc, 2, '.', '' ) . '%', $per_disc );
							}
						}
					} else {
						$discount  = false;
						$new_price = $price;

					}
				}
			} else {

				remove_filter( 'woocommerce_product_get_price', array( $this, 'get_price' ), 10 );
				remove_filter( 'woocommerce_product_variation_get_price', array( $this, 'get_price' ), 10 );
				$show_minimum_price = YITH_WC_Dynamic_Pricing()->get_option( 'show_minimum_price' );
				if ( ywdpd_is_true( $show_minimum_price ) ) {
					$discount = $this->get_minimum_price( $product );
				} else {
					$discount = $this->get_minimum_price( $product, 1 );
				}

				add_filter( 'woocommerce_product_get_price', array( $this, 'get_price' ), 10, 2 );
				add_filter( 'woocommerce_product_variation_get_price', array( $this, 'get_price' ), 10, 2 );

				$discount = wc_get_price_to_display( $product, array( 'price' => $discount ) );

				$discount_html = wc_price( $discount );

			}

			do_action( 'yith_ywdpd_get_price_and_discount_after' );

			$discount = empty( $discount ) ? 0 : $discount;

			if ( $discount >= 0 && $discount != $display_regular_price ) {

				if ( empty( $percentual_discount ) && 0 != $display_regular_price ) {
					$per_disc = 100 - ( $discount / $display_regular_price * 100 );

					if ( $per_disc > 0 ) {
						$percentual_discount = apply_filters( 'ywdpd_percentual_discount', '-' . number_format( $per_disc, 2, '.', '' ) . '%', $per_disc );
					}
				}

				$new_price = str_replace( '%original_price%', wc_price( $display_regular_price ), $new_price );
				$new_price = str_replace( '%discounted_price%', $discount_html, $new_price );
				$new_price = str_replace( '%percentual_discount%', $percentual_discount, $new_price );
				$new_price .= $product->get_price_suffix();

			} else {
				$show_minimum_price = YITH_WC_Dynamic_Pricing()->get_option( 'show_minimum_price' );
				if ( ywdpd_is_true( $show_minimum_price ) ) {
					$new_price = wc_price( $discount );
				} else {
					$new_price = apply_filters( 'ywdpd_maybe_should_be_converted', $price );
				}
			}

			add_filter( 'woocommerce_product_get_price', array( $this, 'get_price' ), 10, 2 );

			$this->has_get_price_html_filter[ $product_id ] = $new_price;

			return apply_filters( 'yith_ywdpd_single_bulk_discount', $new_price, $product );

		}

		/**
		 * Only the first quantity table can be applied to the product
		 *
		 * @param WC_Product $product Product.
		 *
		 * @return mixed
		 */
		public function get_table_rules( $product ) {

			if ( isset( $this->table_rules[ $product->get_id() ] ) ) {
				return $this->table_rules[ $product->get_id() ];
			}

			$valid_rules = $this->pricing_rules;

			$table_rules = array();
			if ( empty( $valid_rules ) || YITH_WC_Dynamic_Pricing_Helper()->is_in_exclusion_rule( array( 'product_id' => $product->get_id() ) ) ) {
				add_filter( 'woocommerce_product_get_price', array( $this, 'get_price' ), 10, 2 );
				$this->table_rules[ $product->get_id() ] = $table_rules;

				return false;
			}

			// build rules array.
			foreach ( $valid_rules as $rule ) {

				if ( ! ywdpd_is_true( $rule['active'] ) ||
				     'bulk' != $rule['discount_mode'] ||
				     ! YITH_WC_Dynamic_Pricing_Helper()->valid_product_to_apply_bulk( $rule, $product, false )
				) {
					continue;
				}

				$table_rules[]                           = $rule;
				$this->table_rules[ $product->get_id() ] = $table_rules;

				break;
			}

			return $table_rules;
		}

		/**
		 * Check if has markup rule.
		 *
		 * @param WC_Product|WC_Product_Variable $product Product.
		 *
		 * @return boolean
		 */
		public function has_markup_rule( $product ) {

			$table_rules = $this->get_table_rules( $product );

			$has_markup_rule = false;
			if ( $table_rules ) {
				foreach ( $table_rules as $rules ) {
					foreach ( $rules['rules'] as $rule ) {

						if ( $rule['type_discount'] && $rule['discount_amount'] < 0 ) {
							$has_markup_rule = true;
						}
					}
				}
			}

			return $has_markup_rule;
		}

		/**
		 * Get minimum price.
		 *
		 * @param WC_Product|WC_Product_Variable $product Product.
		 * @param string $min_quantity Minimum quantity.
		 *
		 * @return int|mixed
		 */
		public function get_minimum_price( $product, $min_quantity = '' ) {

			$table_rules   = $this->get_table_rules( $product );

			if( $product->is_type('variable')){
				$minimum_price = $product->get_variation_price('min');
			}else {
				$minimum_price = $product->get_price();
			}


			$discount_price     = $minimum_price;
			$min_quantity_check = 0;
			$last_check         = true;
			if ( $table_rules ) {
				foreach ( $table_rules as $rules ) {
					$main_rule = $rules;
					foreach ( $rules['rules'] as $rule ) {

						if ( $product->is_type( 'variable' ) ) {
							$prices = apply_filters( 'ywdpd_get_variable_prices', $product->get_variation_prices(), $product );
							$prices = isset( $prices['price'] ) ? $prices['price'] : array();

							if ( $prices ) {
								$min_price = current( $prices );
								$max_price = end( $prices );
								if ( $min_price == $max_price ) {
									// for products where only the variation is discounted.
									foreach ( $prices as $id => $p ) {
										if ( YITH_WC_Dynamic_Pricing_Helper()->valid_product_to_apply_bulk( $main_rule, wc_get_product( $id ) ) ) {
											$curr_discount_price = ywdpd_get_discounted_price_table( $p, $rule );

										} else {
											$curr_discount_price = $p;
										}
										$discount_price = $curr_discount_price < $discount_price ? $curr_discount_price : $discount_price;

										if ( $rule['type_discount'] && $rule['discount_amount'] < 0 ) {
											$discount_price = $curr_discount_price;
										}
									}
								} else {
									$min_key       = array_search( $min_price, $prices );
									$minimum_price = $min_price;
									if ( '' != $min_quantity && $rule['min_quantity'] != $min_quantity ) {
										continue;
									}

									if ( YITH_WC_Dynamic_Pricing_Helper()->valid_product_to_apply_bulk( $rules, wc_get_product( $min_key ) ) ) {
										$discount_min_price = ywdpd_get_discounted_price_table( $min_price, $rule );
									} else {
										$discount_min_price = $min_price;
									}

									$discount_price = $discount_min_price;
								}

								if ( $rule['type_discount'] && $rule['discount_amount'] < 0 ) {
									$minimum_price = $discount_price;
								}
							}
						} else {

							$price = $product->get_price();

							if ( YITH_WC_Dynamic_Pricing_Helper()->valid_product_to_apply_bulk( $rules, $product ) ) {
								$discount_price = ywdpd_get_discounted_price_table( $price, $rule );
							} else {
								$discount_price = $price;

							}

							if ( isset( $rule['discount_amount'] ) && $rule['discount_amount'] <= 0 && apply_filters( 'ywdpd_show_minimum_price_for_simple', true ) ) {
								$minimum_price = $discount_price < $minimum_price ? $minimum_price : $discount_price;
								$last_check    = true;
							}
						}

						if ( '' != $min_quantity && $rule['min_quantity'] == $min_quantity ) {
							$min_quantity_check = 1;
							break;
						}
					}
				}
			}

			if ( ! $last_check || ( '' != $min_quantity && ! $min_quantity_check ) ) {
				return $minimum_price;
			}

			$minimum_price = $minimum_price > $discount_price ? $discount_price : $minimum_price;

			return $minimum_price;
		}

		/**
		 * Get the maximum price.
		 *
		 * @param WC_Product|WC_Product_Variable $product Product.
		 * @param string $min_quantity Minimum quantity.
		 *
		 * @return int|mixed
		 */
		public function get_maximum_price( $product, $min_quantity = '' ) {

			$table_rules    = $this->get_table_rules( $product );
			$maximum_price  = $product->get_price();
			$discount_price = 0;
			if ( $product->get_type() == 'variable' ) {

				$prices        = $product->get_variation_prices();
				$prices        = isset( $prices['price'] ) ? $prices['price'] : array();
				$maximum_price = end( $prices );
			}

			if ( $table_rules ) {
				foreach ( $table_rules as $rules ) {
					foreach ( $rules['rules'] as $rule ) {
						$main_rule = $rules;
						if ( $product->is_type( 'variable' ) ) {
							$prices = apply_filters( 'ywdpd_get_variable_prices', $product->get_variation_prices(), $product );
							$prices = isset( $prices['price'] ) ? $prices['price'] : array();

							if ( $prices ) {
								$min_price = current( $prices );
								$max_price = end( $prices );
								if ( $min_price == $max_price ) {
									// for products where only the variation is discounted.
									foreach ( $prices as $id => $p ) {
										if ( YITH_WC_Dynamic_Pricing_Helper()->valid_product_to_apply_bulk( $main_rule, wc_get_product( $id ) ) ) {
											$curr_discount_price = ywdpd_get_discounted_price_table( $p, $rule );
										} else {
											$curr_discount_price = $p;
										}
										$discount_price = $curr_discount_price > $discount_price ? $curr_discount_price : $discount_price;
									}
								} else {
									$max_key       = array_search( $max_price, $prices );
									$maximum_price = $max_price;

									if ( '' != $min_quantity && $rule['min_quantity'] != $min_quantity ) {
										continue;
									}

									if ( YITH_WC_Dynamic_Pricing_Helper()->valid_product_to_apply_bulk( $rules, wc_get_product( $max_key ) ) ) {
										$discount_max_price = ywdpd_get_discounted_price_table( $max_price, $rule );
									} else {
										$discount_max_price = $max_price;
									}

									$discount_price = $discount_max_price > $discount_price ? $discount_max_price : $discount_price;
								}
							}
						} else {
							$discount_price = ywdpd_get_discounted_price_table( $maximum_price, $rule );
						}

						if ( '' != $min_quantity && $rule['min_quantity'] == $min_quantity ) {
							break;
						}
					}
				}
			}

			if ( $discount_price ) {
				$maximum_price = $discount_price;
			}

			return $maximum_price;
		}

		/**
		 * Get min variation price.
		 *
		 * @param WC_Product_Variable $product Product variation.
		 *
		 * @return string
		 * @since  1.1.3
		 */
		public function get_min_regular_variation_price( $product ) {

			$price = null;

			if ( $product->is_type( 'variable' ) ) {

				$prices_array = $product->get_variation_prices();

				if ( isset( $prices_array['regular_price'] ) ) {

					foreach ( $prices_array['regular_price'] as $single_price ) {

						if ( ! isset( $price ) ) {

							$price = $single_price;

						} elseif ( $price > 0 && $single_price < $price ) {

							$price = $single_price;

						}
					}
				}
			}

			return isset( $price ) ? $price : '';

		}

		/**
		 * Get max regular variation price.
		 *
		 * @param WC_Product_Variable $product Product variation.
		 *
		 * @return string
		 * @since  1.1.3
		 */
		public function get_max_regular_variation_price( $product ) {

			$price = null;

			if ( $product->is_type( 'variable' ) ) {

				$prices_array = $product->get_variation_prices();

				if ( isset( $prices_array['regular_price'] ) ) {

					foreach ( $prices_array['regular_price'] as $single_price ) {

						if ( ! isset( $price ) ) {

							$price = $single_price;

						} elseif ( $price > 0 && $single_price > $price ) {

							$price = $single_price;

						}
					}
				}
			}

			return isset( $price ) ? $price : '';

		}

		/**
		 * Get price modified.
		 *
		 * @param float $price Price.
		 * @param WC_Product $product Product.
		 *
		 * @return mixed
		 */
		public function get_price( $price, $product ) {

			global $woocommerce_loop;

			if ( ( ( is_cart() || is_checkout() ) && is_null( $woocommerce_loop ) ) || ! YITH_WC_Dynamic_Pricing()->check_discount( $product ) || ! apply_filters( 'ywdpd_apply_discount', true, $price, $product ) || empty( $price ) ) {
				return $price;
			}

			$product_id = $product->get_id();

			if ( array_key_exists( $product_id, $this->has_get_price_filter ) || apply_filters( 'ywdpd_get_price_exclusion', false, $price, $product ) || YITH_WC_Dynamic_Pricing_Helper()->is_in_exclusion_rule( array( 'product_id' => $product_id ) ) ) {
				return isset( $this->has_get_price_filter[ $product_id ] ) ? $this->has_get_price_filter[ $product_id ] : $price;
			}

			$discount = (string) YITH_WC_Dynamic_Pricing()->get_discount_price( $price, $product );

			$this->has_get_price_filter[ $product_id ] = $discount;

			return apply_filters( 'yith_ywdpd_get_price', $discount, $product );

		}

		/**
		 * Enqueue styles and scripts
		 *
		 * @access public
		 * @return void
		 * @since  1.0.0
		 */
		public function enqueue_styles_scripts() {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_register_script( 'yith_ywdpd_frontend', YITH_YWDPD_ASSETS_URL . '/js/ywdpd-frontend' . $suffix . '.js', array( 'jquery' ), YITH_YWDPD_VERSION, true );
			wp_enqueue_style( 'yith_ywdpd_frontend', YITH_YWDPD_ASSETS_URL . '/css/frontend.css', false, YITH_YWDPD_VERSION );

			if ( $this->check_pricing_rules_combination() ) {

				$script = "jQuery( document.body ).on( 'updated_cart_totals', function(){
						window.location.href = window.location.href;
					});";
				wp_add_inline_script( 'wc-cart', $script );
			}

			$show_minimum_price = YITH_WC_Dynamic_Pricing()->get_option( 'show_minimum_price' );
			$template           = YITH_WC_Dynamic_Pricing()->get_option( 'quantity_table_orientation' );
			$change_qty         = YITH_WC_Dynamic_Pricing()->get_option( 'update_price_on_qty', 'yes' );
			$default_qty        = YITH_WC_Dynamic_Pricing()->get_option( 'default_qty_selected', 'no' );
			$args               = array(
				'show_minimum_price'     => yith_plugin_fw_is_true( $show_minimum_price ) ? 'yes' : 'no',
				'template'               => $template,
				'is_change_qty_enabled'  => $change_qty,
				'is_default_qty_enabled' => $default_qty
			);

			wp_localize_script( 'yith_ywdpd_frontend', 'ywdpd_qty_args', $args );
			wp_enqueue_script( 'yith_ywdpd_frontend' );
		}

		/**
		 * Check if pricing rules has disabled the combination with coupons
		 *
		 * @access public
		 * @return bool
		 * @since  1.1.4
		 */
		public function check_pricing_rules_combination() {
			if ( ! WC()->cart ) {
				return false;
			}
			$cart_coupons = WC()->cart->applied_coupons;
			if ( ! empty( $cart_coupons ) && $this->pricing_rules ) {
				foreach ( $this->pricing_rules as $pricing_rule ) {
					$with_other_coupons = isset( $pricing_rule['disable_with_other_coupon'] ) && ywdpd_is_true( $pricing_rule['disable_with_other_coupon'] );
					if ( $with_other_coupons && ywdpd_check_cart_coupon() ) {
						return true;
					}
				}
			}

			return false;
		}

		/**
		 * Show table quantity in the single product if there's a pricing rule
		 *
		 * @param WC_Product|bool $product Product.
		 * @param bool $sh Boolean.
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 *
		 */
		public function show_table_quantity( $product = false, $sh = false ) {
			if ( ! $product ) {
				global $product;
			}

			if ( apply_filters( 'ywdpd_exclude_products_from_discount', false, $product ) ) {
				return;
			}

			$table_rules = $this->get_table_rules( $product );

			if ( $table_rules ) {
				echo ( $sh ) ? '<div class="ywdpd-table-discounts-wrapper-sh">' : '<div class="ywdpd-table-discounts-wrapper">';

				foreach ( $table_rules as $rule ) {
					$showtable = isset( $rule['show_table_price'] ) && ywdpd_is_true( $rule['show_table_price'] );
					if ( ! $showtable ) {
						continue;
					}
					$show_quantity_table_schedule = YITH_WC_Dynamic_Pricing()->get_option( 'show_quantity_table_schedule' );
					$args                         = array(
						'rules'          => $rule['rules'],
						'main_rule'      => $rule,
						'product'        => $product,
						'note'           => ywdpd_get_note( $rule['table_note'] ),
						'label_table'    => YITH_WC_Dynamic_Pricing()->get_option( 'show_quantity_table_label' ),
						'label_quantity' => YITH_WC_Dynamic_Pricing()->get_option( 'show_quantity_table_label_quantity' ),
						'label_price'    => YITH_WC_Dynamic_Pricing()->get_option( 'show_quantity_table_label_price' ),
						'until'          => ( ywdpd_is_true( $show_quantity_table_schedule ) && '' != $rule['schedule_to'] ) ? sprintf( __( 'Offer ends: %s', 'ywdpd' ), date_i18n( wc_date_format(), strtotime( $rule['schedule_to'] ) ) ) : '',
					);

					wc_get_template( 'yith_ywdpd_table_pricing.php', $args, '', YITH_YWDPD_TEMPLATE_PATH );

				}

				add_filter( 'woocommerce_product_get_price', array( $this, 'get_price' ), 10, 2 );
				echo '</div>';
			} else {
				echo '<div class="ywdpd-table-discounts-wrapper"></div>';
			}
		}

		/**
		 * Table quantity.
		 *
		 * @param WC_Product $product Product.
		 *
		 * @return string
		 */
		public function table_quantity( $product ) {
			ob_start();
			$this->show_table_quantity( $product );

			return ob_get_clean();
		}

		/**
		 * Table Quantity Shortcode
		 *
		 * @param array $atts Attributes.
		 * @param null $content Shortcode Content.
		 *
		 * @return mixed
		 * @internal param $product
		 */
		public function table_quantity_shortcode( $atts, $content = null ) {

			$args = shortcode_atts(
				array(
					'product' => false,
				),
				$atts
			);

			if ( ! $args['product'] ) {
				global $product;
				$the_product = $product;
			} else {
				$the_product = wc_get_product( $args['product'] );
			}

			if ( ! $the_product || apply_filters( 'ywdpd_exclude_products_from_discount', false, $the_product ) ) {
				return '';
			}

			ob_start();
			$this->show_table_quantity( $the_product, true );

			return ob_get_clean();
		}


		/**
		 * Product Note Shortcode
		 *
		 * @param array $atts Attributes.
		 * @param null $content Shortcode Content.
		 *
		 * @return mixed
		 * @internal param $product
		 */
		public function product_note_shortcode( $atts, $content = null ) {

			$args = shortcode_atts(
				array(
					'product' => false,
				),
				$atts
			);

			if ( ! $args['product'] ) {
				global $product;
				$the_product = $product;
			} else {
				$the_product = wc_get_product( $args['product'] );
			}

			if ( ! $the_product || apply_filters( 'ywdpd_exclude_products_from_discount', false, $the_product ) ) {
				return '';
			}

			ob_start();
			$this->show_note_on_products( $the_product );

			return ob_get_clean();
		}



		/**
		 * Add action for single product page to display table pricing
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function table_quantity_init() {
			// Table Pricing.
			$position                    = YITH_WC_Dynamic_Pricing()->get_option( 'show_quantity_table_place' );
			$priority_single_add_to_cart = has_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart' );
			$priority_single_excerpt     = has_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt' );

			$custom_hook = apply_filters( 'ywdpd_table_custom_hook', array() );

			if ( ! empty( $custom_hook ) && isset( $custom_hook['hook'] ) ) {
				$hook     = $custom_hook['hook'];
				$priority = isset( $custom_hook['priority'] ) ? $custom_hook['priority'] : 10;
				add_action( $hook, array( $this, 'show_table_quantity' ), $priority );

				return;
			}

			switch ( $position ) {
				case 'before_add_to_cart':
					if ( $priority_single_add_to_cart ) {
						add_action(
							'woocommerce_single_product_summary',
							array(
								$this,
								'show_table_quantity',
							),
							$priority_single_add_to_cart - 1
						);
					} else {
						add_action( 'woocommerce_single_product_summary', array( $this, 'show_table_quantity' ), 28 );
					}
					break;
				case 'after_add_to_cart':
					if ( $priority_single_add_to_cart ) {
						add_action(
							'woocommerce_single_product_summary',
							array(
								$this,
								'show_table_quantity',
							),
							$priority_single_add_to_cart + 1
						);
					} else {
						add_action( 'woocommerce_single_product_summary', array( $this, 'show_table_quantity' ), 32 );
					}
					break;
				case 'before_excerpt':
					if ( $priority_single_excerpt ) {
						add_action(
							'woocommerce_single_product_summary',
							array(
								$this,
								'show_table_quantity',
							),
							$priority_single_excerpt - 1
						);
					} else {
						add_action( 'woocommerce_single_product_summary', array( $this, 'show_table_quantity' ), 18 );
					}
					break;
				case 'after_excerpt':
					if ( $priority_single_excerpt ) {
						add_action(
							'woocommerce_single_product_summary',
							array(
								$this,
								'show_table_quantity',
							),
							$priority_single_excerpt + 1
						);
					} else {
						add_action( 'woocommerce_single_product_summary', array( $this, 'show_table_quantity' ), 22 );
					}
					break;
				case 'after_meta':
					$priority_after_meta = has_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta' );
					if ( $priority_after_meta ) {
						add_action(
							'woocommerce_single_product_summary',
							array(
								$this,
								'show_table_quantity',
							),
							$priority_after_meta + 1
						);
					} else {
						add_action( 'woocommerce_single_product_summary', array( $this, 'show_table_quantity' ), 42 );
					}
					break;
				default:
					break;
			}
		}

		/**
		 * Add action for single product page to display table pricing
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function note_on_products_init() {
			// Table Pricing.
			$position                    = YITH_WC_Dynamic_Pricing()->get_option( 'show_note_on_products_place' );
			$priority_single_add_to_cart = has_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart' );
			$priority_single_excerpt     = has_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt' );

			$custom_hook = apply_filters( 'ywdpd_note_custom_hook', array() );

			if ( ! empty( $custom_hook ) && isset( $custom_hook['hook'] ) ) {
				$hook     = $custom_hook['hook'];
				$priority = isset( $custom_hook['priority'] ) ? $custom_hook['priority'] : 10;
				add_action( $hook, array( $this, 'show_note_on_products' ), $priority );

				return;
			}

			switch ( $position ) {
				case 'before_add_to_cart':
					if ( $priority_single_add_to_cart ) {
						add_action(
							'woocommerce_single_product_summary',
							array(
								$this,
								'show_note_on_products',
							),
							$priority_single_add_to_cart - 1
						);
					} else {
						add_action( 'woocommerce_single_product_summary', array( $this, 'show_note_on_products' ), 28 );
					}
					break;
				case 'after_add_to_cart':
					if ( $priority_single_add_to_cart ) {
						add_action(
							'woocommerce_single_product_summary',
							array(
								$this,
								'show_note_on_products',
							),
							$priority_single_add_to_cart + 1
						);
					} else {
						add_action( 'woocommerce_single_product_summary', array( $this, 'show_note_on_products' ), 32 );
					}
					break;
				case 'before_excerpt':
					if ( $priority_single_excerpt ) {
						add_action(
							'woocommerce_single_product_summary',
							array(
								$this,
								'show_note_on_products',
							),
							$priority_single_excerpt - 1
						);
					} else {
						add_action( 'woocommerce_single_product_summary', array( $this, 'show_note_on_products' ), 18 );
					}
					break;
				case 'after_excerpt':
					if ( $priority_single_excerpt ) {
						add_action(
							'woocommerce_single_product_summary',
							array(
								$this,
								'show_note_on_products',
							),
							$priority_single_excerpt + 1
						);
					} else {
						add_action( 'woocommerce_single_product_summary', array( $this, 'show_note_on_products' ), 22 );
					}
					break;
				case 'after_meta':
					$priority_after_meta = has_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta' );
					if ( $priority_after_meta ) {
						add_action(
							'woocommerce_single_product_summary',
							array(
								$this,
								'show_note_on_products',
							),
							$priority_after_meta + 1
						);
					} else {
						add_action( 'woocommerce_single_product_summary', array( $this, 'show_note_on_products' ), 42 );
					}
					break;
				default:
					break;
			}
		}
	}
}

/**
 * Unique access to instance of YITH_WC_Dynamic_Pricing_Frontend class
 *
 * @return YITH_WC_Dynamic_Pricing_Frontend
 */
function YITH_WC_Dynamic_Pricing_Frontend() {
	return YITH_WC_Dynamic_Pricing_Frontend::get_instance();
}
