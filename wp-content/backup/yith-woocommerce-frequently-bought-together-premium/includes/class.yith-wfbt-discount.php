<?php
/**
 * Frontend Discount class
 *
 * @author  YITH
 * @package YITH WooCommerce Frequently Bought Together Premium
 * @version 1.3.0
 */

if ( ! defined( 'YITH_WFBT' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WFBT_Discount' ) ) {
	/**
	 * Frontend class.
	 * The class manage the discount feature.
	 *
	 * @since 1.3.0
	 */
	class YITH_WFBT_Discount {

		/**
		 * The coupon code
		 *
		 * @since 1.4.0
		 * @var string
		 */
		protected $coupon_code = '';

		/**
		 * Discounts data
		 *
		 * @since  1.3.0
		 * @author Francesco Licandro
		 * @var array
		 */
		protected $data_session = array();

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.3.0
		 */
		public function __construct() {

			add_action( 'wp_loaded', array( $this, 'load_session' ), 0 );
			add_action( 'yith_wfbt_group_added_to_cart', array( $this, 'save_data' ), 10, 2 );
			// filter coupon data
			add_filter( 'woocommerce_get_shop_coupon_data', array( $this, 'filter_coupon_data' ), 10, 2 );
			// add coupon
			add_action( 'yith_wfbt_group_added_to_cart', array( $this, 'add_coupon' ), 99 );
			// handle calculate totals
			add_filter( 'woocommerce_after_calculate_totals', array( $this, 'check_cart' ), 99, 1 );
			// filter coupon message
			add_filter( 'woocommerce_coupon_message', array( $this, 'remove_coupon_messages' ), 10, 3 );
			add_filter( 'woocommerce_coupon_error', array( $this, 'remove_coupon_messages' ), 10, 3 );
			// filter coupon html
			add_filter( 'woocommerce_cart_totals_coupon_html', array( $this, 'totals_coupon_html' ), 10, 3 );
		}

		/**
		 * Load session
		 *
		 * @since  1.3.0
		 * @author Francesco Licandro
		 */
		public function load_session() {

			if ( is_null( WC()->session ) ) {
				return;
			}

			if ( empty( $this->data_session ) ) {
				$this->data_session = WC()->session->get( 'yith_wfbt_data', array() );
			}
		}

		/**
		 * Get data session
		 *
		 * @since  1.3.0
		 * @author Francesco Licandro
		 * @return array
		 */
		public function get_data_session() {
			return $this->data_session;
		}

		/**
		 * Set data session
		 *
		 * @since  1.3.0
		 * @author Francesco Licandro
		 * @param mixed $value
		 */
		public function set_data_session( $value = null ) {
			if ( ! is_null( WC()->session ) ) {
				is_null( $value ) && $value = $this->data_session;
				WC()->session->set( 'yith_wfbt_data', $value );
			}
		}

		/**
		 * Save single group data on session
		 *
		 * @since  1.3.0
		 * @author Francesco Licandro
		 * @param array   $added
		 * @param integer $main_product
		 * @return void
		 */
		public function save_data( $added, $main_product ) {

			if ( in_array( $main_product, $added ) && ( $amount = $this->get_discount_amount( $main_product, $added ) ) ) {
				$this->data_session[] = array(
					'main'     => $main_product,
					'products' => $added,
					'discount' => $amount,
				);
			}

			$this->set_data_session();
		}

		/**
		 * Check if a discount is valid for a product group
		 *
		 * @since  1.3.0
		 * @author Francesco Licandro
		 * @param integer|\WC_Product product
		 * @param array    $added
		 * @param array    $data
		 * @param \WC_Cart $cart
		 * @param float    $subtotal A fixed subtotal to use in calculate discount
		 * @return boolean
		 */
		public function is_discount_valid( $product, $added, $data = array(), $cart = null, $subtotal = 0.00 ) {

			( $product instanceof WC_Product ) || $product = wc_get_product( intval( $product ) );

			empty( $data ) && $data = $this->get_product_data( $product );

			if( empty( $data ) ) {
				return false;
			}

			$discount_perc = isset( $data['discount_percentage'] ) ? intval( $data['discount_percentage'] ) : 0;

			// check for type
			if ( ! in_array( $data['discount_type'], array( 'percentage', 'fixed' ) ) ) {
				return false;
			} elseif ( intval( $data['discount_min_products'] ) < 2 || intval( $data['discount_min_products'] ) > count( $added ) ) {
				return false;
			} elseif ( $data['discount_type'] == 'fixed' && ! floatval( $data['discount_fixed'] ) ) {
				return false;
			} elseif ( $data['discount_type'] == 'percentage' && ( ! $discount_perc || $discount_perc > 100 || $discount_perc < 0 ) ) {
				return false;
			} elseif ( $data['discount_min_spend'] ) {

				if ( ! $subtotal ) {
					is_null( $cart ) && $cart = WC()->cart;
					$cart_contents = $cart->get_cart_contents();

					foreach ( $added as $item_key => $product_id ) {
						if ( ! isset( $cart_contents[ $item_key ] ) ) {
							continue;
						}

						$item_subtotal = $cart_contents[ $item_key ]['line_subtotal'];
						wc_prices_include_tax() && $item_subtotal += $cart_contents[ $item_key ]['line_subtotal_tax'];

						$subtotal += $item_subtotal / ( $cart_contents[ $item_key ]['quantity'] );
					}
				}

				return $subtotal >= floatval( $data['discount_min_spend'] );
			}

			return true;
		}

		/**
		 * Get discount amount
		 *
		 * @since  1.3.0
		 * @author Francesco Licandro
		 * @param integer|\WC_Product product
		 * @param array    $added
		 * @param \WC_Cart $cart
		 * @param float    $subtotal A fixed subtotal to use in calculate discount
		 * @return mixed
		 */
		public function get_discount_amount( $product, $added, $cart = null, $subtotal = 0.00 ) {

			( $product instanceof WC_Product ) || $product = wc_get_product( intval( $product ) );

			$data     = $this->get_product_data( $product );
			$discount = 0;

			if ( $this->is_discount_valid( $product, $added, $data, $cart, $subtotal ) ) {

				if ( ! $subtotal ) {
					is_null( $cart ) && $cart = WC()->cart;
					$cart_contents = $cart->get_cart_contents();

					foreach ( $added as $item_key => $product_id ) {
						if ( ! isset( $cart_contents[ $item_key ] ) ) {
							continue;
						}

						$item_subtotal = $cart_contents[ $item_key ]['line_subtotal'];
						wc_prices_include_tax() && $item_subtotal += $cart_contents[ $item_key ]['line_subtotal_tax'];

						$subtotal += $item_subtotal / ( $cart_contents[ $item_key ]['quantity'] );
					}
				}

				if ( $subtotal ) {
					if ( $data['discount_type'] == 'fixed' ) {
						$f        = floatval( $data['discount_fixed'] );
						$discount = ( $subtotal > $f ) ? $f : $subtotal;
					} else {
						$discount = ( $subtotal * ( intval( $data['discount_percentage'] ) / 100 ) );
					}
				}
			}

			return $discount ? wc_format_decimal( $discount, wc_get_price_decimals() ) : 0;
		}

		/**
		 * Filter coupon data for discount
		 *
		 * @since  1.3.0
		 * @author Francesco Licandro
		 * @param mixed  $data
		 * @param string $code
		 * @return mixed
		 */
		public function filter_coupon_data( $data, $code ) {

			$myCode = $this->get_coupon_code();

			if ( $code != $myCode ) {
				return $data;
			}

			$amount = 0;
			foreach ( $this->data_session as $value ) {
				$amount += $value['discount'];
			}

			$amount && $data = array(
				'code'           => $myCode,
				'amount'         => $amount,
				'discount_type'  => 'fixed_cart',
				'individual_use' => apply_filters( 'yith-wfbt-coupon-individual-use', true ),
				'usage_limit'    => 1,
			);

			return $data;
		}

		/**
		 * Add coupon with discount to cart
		 *
		 * @since  1.3.0
		 * @author Francesco Licandr
		 */
		public function add_coupon() {
			if ( apply_filters( 'yith_wcfbt_add_coupon', true, $this ) && ! empty( $this->data_session ) ) {
				$myCode = $this->get_coupon_code();
				( $myCode && ! WC()->cart->has_discount( $myCode ) ) && WC()->cart->add_discount( $myCode );
			}
		}

		/**
		 * Check cart content on cart update/remove item
		 *
		 * @since  1.3.0
		 * @author Francesco Licandro
		 * @param \WC_Cart $cart
		 * @return void
		 */
		public function check_cart( $cart = null ) {

			if ( empty( $this->data_session ) ) {
				return;
			}

			is_null( $cart ) && $cart = WC()->cart;

			if ( $cart->is_empty() ) {
				$this->set_data_session( array() ); // empty session
			};

			$items            = $cart->get_cart_contents();
			$products_in_cart = $this->get_products_in_cart( $items );

			foreach ( $this->data_session as $key => $data ) {
				$current = array_intersect( $data['products'], $products_in_cart );

				if ( in_array( $data['main'], $current ) && ( $amount = $this->get_discount_amount( $data['main'], $current, $cart ) ) ) {
					$this->data_session[ $key ]['discount'] = $amount; // set new amount
					foreach ( $current as $c ) {
						$k = array_search( $c, $products_in_cart );
						unset( $products_in_cart[ $k ] );
					}  // remove items
					continue;
				}

				// if you are here, the discount is no more valid. Remove it!
				unset( $this->data_session[ $key ] );
			}

			$this->set_data_session();
		}

		/**
		 * Get an array of products in cart
		 *
		 * @since  1.3.0
		 * @author Francesco Licandro
		 * @param array $items
		 * @return array
		 */
		public function get_products_in_cart( $items ) {
			$r = array();
			foreach ( $items as $item_key => $item ) {
				$qty = $item['quantity'];
				do {
					$r[] = $item['variation_id'] ? $item['variation_id'] : $item['product_id'];
				} while ( --$qty > 0 );
			}

			return $r;
		}

		/**
		 * Get product data
		 *
		 * @since  1.3.0
		 * @author Francesco Licandro
		 * @param \WC_Product $product
		 * @return array
		 */
		public function get_product_data( $product ) {
			if ( $product instanceof WC_Product && $product->is_type( 'variation' ) ) {
				$product = wc_get_product( $product->get_parent_id() );
			}

			return yith_wfbt_get_meta( $product );
		}

		/**
		 * Remove WC coupon messages
		 *
		 * @since  1.4.0
		 * @author Francesco Licandro
		 * @param string    $msg
		 * @param integer   $msg_code Message code.
		 * @param WC_Coupon $coupon
		 * @return string
		 */
		public function remove_coupon_messages( $msg, $msg_code, $coupon ) {
			if ( isset( $coupon ) && $coupon->get_code() === $this->get_coupon_code() ) {
				return '';
			}

			return $msg;
		}

		/**
		 * Get discount code
		 *
		 * @since  1.4.0
		 * @author Francesco Licandro
		 * @return string
		 */
		public function get_coupon_code() {
			if ( empty( $this->coupon_code ) ) {
				$this->coupon_code = yith_wfbt_discount_code_validation( get_option( 'yith-wfbt-discount-name', 'frequently-bought-discount' ) );
				// discount code cannot be empty, if it is, set as default
				empty( $this->coupon_code ) && $this->coupon_code = 'frequently-bought-discount';
			}
			return $this->coupon_code;
		}

		/**
		 * Customized template for plugin coupon code
		 *
		 * @since  1.4.0
		 * @author Francesco Licandro
		 * @param string    $coupon_html
		 * @param WC_Coupon $coupon
		 * @param string    $discount_amount_html
		 * @return string
		 */
		public function totals_coupon_html( $coupon_html, $coupon, $discount_amount_html ) {
			if ( $coupon->get_code() === $this->get_coupon_code() ) {
				return $discount_amount_html;
			}

			return $coupon_html;
		}

	}
}