<?php
/**
 * Processing of coupons
 *
 * @author      StoreApps
 * @since       3.3.0
 * @version     1.2.0
 * @package     WooCommerce Smart Coupons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Apply_Before_Tax' ) ) {

	/**
	 * Class for applying store credit before tax calculation
	 */
	class WC_SC_Apply_Before_Tax {

		/**
		 * Variable to hold instance of WC_SC_Apply_Before_Tax
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Store credit left after application on each cart item
		 *
		 * @var $sc_credit_left
		 */
		private $sc_credit_left = array();

		/**
		 * Remaining total to apply credit
		 *
		 * @var $remaining_total_to_apply_credit
		 */
		private $remaining_total_to_apply_credit = array();

		/**
		 * Constructor
		 */
		private function __construct() {
			add_action( 'woocommerce_order_before_calculate_totals', array( $this, 'order_calculate_discount_amount_before_tax' ), 10, 2 );

			add_action( 'woocommerce_order_after_calculate_totals', array( $this, 'order_set_discount_total' ), 10, 2 );
			add_action( 'woocommerce_checkout_create_order', array( $this, 'cart_set_discount_total' ), 10, 1 );

			add_action( 'wp_loaded', array( $this, 'cart_calculate_discount_amount' ), 20 );
			add_filter( 'woocommerce_coupon_get_discount_amount', array( $this, 'cart_return_discount_amount' ), 20, 5 );

			add_filter( 'woocommerce_coupon_custom_discounts_array', array( $this, 'store_credit_discounts_array' ), 10, 2 );

			add_filter( 'woocommerce_add_cart_item', array( $this, 'sc_mnm_compat' ), 20, 2 );

			add_action( 'woocommerce_after_calculate_totals', array( $this, 'cart_set_total_credit_used' ), 10, 1 );

			add_action( 'woocommerce_before_calculate_totals', array( $this, 'cart_reset_credit_left' ), 15 );
		}

		/**
		 * Get single instance of WC_SC_Apply_Before_Tax
		 *
		 * @return WC_SC_Apply_Before_Tax Singleton object of WC_SC_Apply_Before_Tax
		 */
		public static function get_instance() {
			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Handle call to functions which is not available in this class
		 *
		 * @param string $function_name Function to call.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return mixed Result of function call.
		 */
		public function __call( $function_name, $arguments = array() ) {

			global $woocommerce_smart_coupon;

			if ( ! is_callable( array( $woocommerce_smart_coupon, $function_name ) ) ) {
				return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( $woocommerce_smart_coupon, $function_name ), $arguments );
			} else {
				return call_user_func( array( $woocommerce_smart_coupon, $function_name ) );
			}
		}

		/**
		 * Function to apply store credit before tax calculation for orders that are manually created and updated from backend
		 *
		 * @param bool     $and_taxes Calc taxes if true.
		 * @param WC_Order $order Order object.
		 */
		public function order_calculate_discount_amount_before_tax( $and_taxes, $order ) {
			$order_actions = array( 'woocommerce_add_coupon_discount', 'woocommerce_calc_line_taxes', 'woocommerce_save_order_items' );

			if ( $order instanceof WC_Order && ! empty( $_POST['action'] ) && ( in_array( wp_unslash( $_POST['action'] ), $order_actions, true ) || ( ! empty( $_POST['post_type'] ) && 'shop_order' === wp_unslash( $_POST['post_type'] ) && 'editpost' === wp_unslash( $_POST['action'] ) ) ) ) { // phpcs:ignore
				if ( ! is_object( $order ) || ! is_callable( array( $order, 'get_id' ) ) ) {
					return;
				}
				$order_id = $order->get_id();
				if ( empty( $order_id ) ) {
					return;
				}
				$coupons     = $order->get_items( 'coupon' );
				$order_items = $order->get_items( 'line_item' );

				if ( empty( $order_items ) && empty( $coupons ) ) {
					return;
				}

				foreach ( $coupons as $item_id => $item ) {

					$coupon_code = ( is_object( $item ) && is_callable( array( $item, 'get_name' ) ) ) ? $item->get_name() : $item['name'];

					if ( empty( $coupon_code ) ) {
						continue;
					}

					$coupon        = new WC_Coupon( $coupon_code );
					$discount_type = $coupon->get_discount_type();

					if ( 'smart_coupon' === $discount_type ) {
						$sc_include_tax             = $this->is_store_credit_include_tax();
						$smart_coupons_contribution = get_post_meta( $order_id, 'smart_coupons_contribution', true );
						$smart_coupons_contribution = ( ! empty( $smart_coupons_contribution ) ) ? $smart_coupons_contribution : array();

						$discount_amount     = ( is_object( $item ) && is_callable( array( $item, 'get_discount' ) ) ) ? $item->get_discount() : wc_get_order_item_meta( $item_id, 'discount_amount', true );
						$discount_amount_tax = ( is_object( $item ) && is_callable( array( $item, 'get_discount_tax' ) ) ) ? $item->get_discount_tax() : wc_get_order_item_meta( $item_id, 'discount_amount_tax', true );

						if ( is_array( $smart_coupons_contribution ) && count( $smart_coupons_contribution ) > 0 && array_key_exists( $coupon_code, $smart_coupons_contribution ) ) {
							// If store credit discount is inclusive of tax then remove discount given tax from Smart Coupons' contribution.
							if ( 'yes' === $sc_include_tax && ! empty( $discount_amount_tax ) ) {
								$new_discount = $smart_coupons_contribution[ $coupon_code ] - $discount_amount_tax;
							} else {
								$new_discount = $smart_coupons_contribution[ $coupon_code ];
							}
							if ( is_object( $item ) && is_callable( array( $item, 'set_discount' ) ) ) {
								$item->set_discount( $new_discount );
							} else {
								$item['discount_amount'] = $new_discount;
							}
						} elseif ( ! empty( $discount_amount ) ) {
							if ( is_object( $item ) && is_callable( array( $item, 'set_discount' ) ) ) {
								$item->set_discount( $discount_amount );
							} else {
								$item['discount_amount'] = $discount_amount;
							}
							// If discount includes tax then Smart Coupons contribution is sum of discount on product price and discount on tax.
							if ( 'yes' === $sc_include_tax && ! empty( $discount_amount_tax ) ) {
								$smart_coupons_contribution[ $coupon_code ] = $discount_amount + $discount_amount_tax;
							} else {
								$smart_coupons_contribution[ $coupon_code ] = $discount_amount;
							}
						} else {
							$coupon_amount       = $coupon->get_amount();
							$coupon_product_ids  = $coupon->get_product_ids();
							$coupon_category_ids = $coupon->get_product_categories();

							$subtotal              = 0;
							$items_to_apply_credit = array();

							if ( count( $coupon_product_ids ) > 0 || count( $coupon_category_ids ) > 0 ) {
								foreach ( $order_items as $order_item_id => $order_item ) {

									$product_category_ids = wc_get_product_cat_ids( $order_item['product_id'] );

									if ( count( $coupon_product_ids ) > 0 && count( $coupon_category_ids ) > 0 ) {
										if ( ( in_array( $order_item['product_id'], $coupon_product_ids, true ) || in_array( $order_item['variation_id'], $coupon_product_ids, true ) ) && count( array_intersect( $product_category_ids, $coupon_category_ids ) ) > 0 ) {
											$items_to_apply_credit[] = $order_item_id;
										}
									} else {
										if ( in_array( $order_item['product_id'], $coupon_product_ids, true ) || in_array( $order_item['variation_id'], $coupon_product_ids, true ) || count( array_intersect( $product_category_ids, $coupon_category_ids ) ) > 0 ) {
											$items_to_apply_credit[] = $order_item_id;
										}
									}
								}
							} else {
								$items_to_apply_credit = array_keys( $order_items );
							}

							$subtotal = array_sum( array_map( array( $this, 'sc_get_order_subtotal' ), $items_to_apply_credit ) );

							if ( $subtotal <= 0 ) {
								continue;
							}

							$store_credit_used = 0;

							foreach ( $items_to_apply_credit as $order_item_id ) {
								$order_item         = $order_items[ $order_item_id ];
								$discounting_amount = $order_item->get_total();
								// If discount include tax then add item tax to discounting amount to allow discount calculation on tax also.
								if ( 'yes' === $sc_include_tax ) {
									$item_tax            = ( is_callable( array( $order, 'get_line_tax' ) ) ) ? $order->get_line_tax( $order_item ) : 0;
									$discounting_amount += $item_tax;
								}
								$quantity  = $order_item->get_quantity();
								$discount  = $this->sc_get_discounted_price( $discounting_amount, $quantity, $subtotal, $coupon_amount );
								$discount *= $quantity;
								$order_item->set_total( $discounting_amount - $discount );

								$store_credit_used += $discount;
							}

							if ( is_object( $item ) && is_callable( array( $item, 'set_discount' ) ) ) {
								$item->set_discount( $store_credit_used );
							} else {
								$item['discount_amount'] = $store_credit_used;
							}

							$smart_coupons_contribution[ $coupon_code ] = $store_credit_used;

							update_post_meta( $order_id, 'smart_coupons_contribution', $smart_coupons_contribution );
						}

						$order->sc_total_credit_used = $smart_coupons_contribution;
					}
				}
			}
		}

		/**
		 * Function to calculate subtotal of items in order which is necessary for applying store credit before tax calculation
		 *
		 * @param  int $order_item_id Item ID.
		 * @return float  $subtotal
		 */
		private function sc_get_order_subtotal( $order_item_id ) {
			$order_item = WC_Order_Factory::get_order_item( $order_item_id );
			$subtotal   = $order_item->get_total();

			$prices_include_tax = wc_prices_include_tax();
			// Get global setting for whether store credit discount is inclusive of tax or not.
			$sc_include_tax = get_option( 'woocommerce_smart_coupon_include_tax', 'no' );

			// If prices are inclusive of tax and discount amount is also inclusive of tax then add item tax in subtotal to handle discount calculation correctly.
			if ( true === $prices_include_tax && 'yes' === $sc_include_tax ) {
				$subtotal += $order_item->get_total_tax();
			}

			return $subtotal;
		}

		/**
		 * Function to update_discount_total for an order
		 *
		 * @param  WC_Order $order Order object.
		 * @param  float    $total_credit_used Total store credit used.
		 */
		public function update_discount_total( $order = '', $total_credit_used = 0 ) {
			if ( $order instanceof WC_Order ) {
				$discount_total = $order->get_discount_total();
				$sc_credit_used = min( $discount_total, $total_credit_used );
				$order->set_discount_total( $discount_total - $sc_credit_used );
			}
		}

		/**
		 * Function to set discount total for orders that are created manually
		 *
		 * @param bool     $and_taxes Calc taxes if true.
		 * @param WC_Order $order Order object.
		 */
		public function order_set_discount_total( $and_taxes, $order ) {
			if ( isset( $order->sc_total_credit_used ) && is_array( $order->sc_total_credit_used ) && count( $order->sc_total_credit_used ) > 0 ) {
				$total_credit_used = array_sum( $order->sc_total_credit_used );
				$this->update_discount_total( $order, $total_credit_used );

				$pending_statuses = $this->get_pending_statuses();

				if ( ! empty( $_POST['action'] ) && 'woocommerce_add_coupon_discount' === wp_unslash( $_POST['action'] ) && $order->has_status( $pending_statuses ) && did_action( 'sc_after_order_calculate_discount_amount' ) <= 0 ) { // phpcs:ignore
					do_action( 'sc_after_order_calculate_discount_amount', $order->get_id() );
				}
			}
		}

		/**
		 * Function to set discount total for a new order
		 *
		 * @param  WC_Order $order Order object.
		 */
		public function cart_set_discount_total( $order ) {
			if ( isset( WC()->cart->smart_coupon_credit_used ) && is_array( WC()->cart->smart_coupon_credit_used ) && count( WC()->cart->smart_coupon_credit_used ) > 0 ) {
				$total_credit_used = array_sum( WC()->cart->smart_coupon_credit_used );
				$this->update_discount_total( $order, $total_credit_used );
			}
		}

		/**
		 * Function to apply store credit before tax calculation for cart items
		 */
		public function cart_calculate_discount_amount() {
			$cart = ( isset( WC()->cart ) ) ? WC()->cart : '';

			if ( $cart instanceof WC_Cart ) {
				$cart_contents = WC()->cart->get_cart();
				$coupons       = $cart->get_coupons();

				if ( ! empty( $coupons ) ) {
					$items_to_apply_credit = array();

					foreach ( $coupons as $coupon_code => $coupon ) {
						$discount_type = $coupon->get_discount_type();

						if ( 'smart_coupon' === $discount_type ) {
							$coupon_product_ids  = $coupon->get_product_ids();
							$coupon_category_ids = $coupon->get_product_categories();

							if ( count( $coupon_product_ids ) > 0 || count( $coupon_category_ids ) > 0 ) {

								foreach ( $cart_contents as $cart_item_key => $cart_item ) {
									$product_category_ids = wc_get_product_cat_ids( $cart_item['product_id'] );

									if ( count( $coupon_product_ids ) > 0 && count( $coupon_category_ids ) > 0 ) {
										if ( ( in_array( $cart_item['product_id'], $coupon_product_ids, true ) || in_array( $cart_item['variation_id'], $coupon_product_ids, true ) ) && count( array_intersect( $product_category_ids, $coupon_category_ids ) ) > 0 ) {
											$items_to_apply_credit[ $coupon_code ][] = $cart_item_key;
										}
									} else {
										if ( in_array( $cart_item['product_id'], $coupon_product_ids, true ) || in_array( $cart_item['variation_id'], $coupon_product_ids, true ) || count( array_intersect( $product_category_ids, $coupon_category_ids ) ) > 0 ) {
											$items_to_apply_credit[ $coupon_code ][] = $cart_item_key;
										}
									}
								}
							} else {
								$items_to_apply_credit[ $coupon_code ] = array_keys( $cart_contents );
							}
						}
					}

					if ( ! empty( $items_to_apply_credit ) ) {
						WC()->cart->sc_items_to_apply_credit = $items_to_apply_credit;
					}
				}
			}

		}

		/**
		 * Get discount amount for a cart item.
		 *
		 * @param  float      $discount Amount this coupon has discounted.
		 * @param  float      $discounting_amount Amount the coupon is being applied to.
		 * @param  array|null $cart_item Cart item being discounted if applicable.
		 * @param  bool       $single True if discounting a single qty item, false if its the line.
		 * @param  WC_Coupon  $coupon Coupon object.
		 * @return float      $discount
		 */
		public function cart_return_discount_amount( $discount, $discounting_amount, $cart_item, $single, $coupon ) {
			$discount_type = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
			if ( 'smart_coupon' !== $discount_type ) {
				return $discount;
			}

			$coupon_code   = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_code' ) ) ) ? $coupon->get_code() : '';
			$coupon_amount = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_amount' ) ) ) ? $coupon->get_amount() : 0;
			$quantity      = $cart_item['quantity'];

			// Compatibility for WC version < 3.2.0.
			if ( ! isset( $cart_item['key'] ) ) {
				$product_id = ( ! empty( $cart_item['variation_id'] ) ) ? $cart_item['variation_id'] : $cart_item['product_id'];

				foreach ( WC()->cart->cart_contents as $key => $cart_data ) {
					$cart_data_product_id = ( ! empty( $cart_data['variation_id'] ) ) ? $cart_data['variation_id'] : $cart_data['product_id'];

					if ( $product_id === $cart_data_product_id ) {
						$cart_item['key'] = $key;
					}
				}
			}

			$prices_include_tax = ( 'incl' === get_option( 'woocommerce_tax_display_cart' ) ) ? true : false;
			if ( true === $prices_include_tax ) {
				$sc_include_tax = get_option( 'woocommerce_smart_coupon_include_tax', 'no' );
				if ( 'no' === $sc_include_tax ) {
					$discounting_amount = $cart_item['line_subtotal'] / $quantity;
				}
			}

			$items_to_apply_credit = isset( WC()->cart->sc_items_to_apply_credit ) ? WC()->cart->sc_items_to_apply_credit : array();

			if ( ! empty( $items_to_apply_credit ) && is_array( $items_to_apply_credit ) && array_key_exists( $coupon_code, $items_to_apply_credit ) && in_array( $cart_item['key'], $items_to_apply_credit[ $coupon_code ], true ) ) {

				$credit_left              = isset( $this->sc_credit_left[ $coupon_code ] ) ? $this->sc_credit_left[ $coupon_code ] : $coupon_amount;
				$total_discounting_amount = $discounting_amount * $quantity;
				if ( isset( $this->remaining_total_to_apply_credit[ $cart_item['key'] ] ) ) {
					$total_discounting_amount = wc_remove_number_precision( $this->remaining_total_to_apply_credit[ $cart_item['key'] ] );
				}
				$applied_discount = min( $total_discounting_amount, $credit_left );

				$this->sc_credit_left[ $coupon_code ] = ( $total_discounting_amount < $credit_left ) ? $credit_left - $total_discounting_amount : 0;

				if ( empty( $this->sc_credit_left[ $coupon_code ] ) && array_key_exists( $coupon_code, WC()->cart->sc_items_to_apply_credit ) ) {
					unset( WC()->cart->sc_items_to_apply_credit[ $coupon_code ] );
				}

				$discount = $applied_discount / $quantity;
			}

			return $discount;
		}

		/**
		 * Discount details for store credit
		 *
		 * @param  array     $discounts The discount details.
		 * @param  WC_Coupon $coupon    The coupon object.
		 * @return array
		 */
		public function store_credit_discounts_array( $discounts = array(), $coupon = null ) {
			$cart = ( isset( WC()->cart ) ) ? WC()->cart : '';
			if ( $cart instanceof WC_Cart ) {
				$cart_contents = ( is_object( WC()->cart ) && is_callable( array( WC()->cart, 'get_cart' ) ) ) ? WC()->cart->get_cart() : array();
				if ( ! empty( $cart_contents ) ) {
					$discount_type = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
					if ( 'smart_coupon' === $discount_type ) {
						$prices_include_tax = ( 'incl' === get_option( 'woocommerce_tax_display_cart' ) ) ? true : false;
						if ( true === $prices_include_tax ) {
							$sc_include_tax = get_option( 'woocommerce_smart_coupon_include_tax', 'no' );
							if ( 'no' === $sc_include_tax ) {
								if ( ! empty( $discounts ) ) {
									foreach ( $discounts as $item_key => $discount ) {
										$line_subtotal                 = wc_round_discount( wc_add_number_precision( $cart_contents[ $item_key ]['line_subtotal'] ), 0 );
										$line_subtotal                 = ( isset( $this->remaining_total_to_apply_credit[ $item_key ] ) ) ? min( $this->remaining_total_to_apply_credit[ $item_key ], $line_subtotal ) : $line_subtotal;
										$discount                      = min( $discount, $line_subtotal );
										$discounts       [ $item_key ] = $discount;
										$this->remaining_total_to_apply_credit[ $item_key ] = $line_subtotal - $discount;
									}
								}
							}
						}
					}
				}
			}
			return $discounts;
		}

		/**
		 * Set smart coupon credit used
		 */
		public function cart_set_total_credit_used() {

			$coupon_discount_totals     = ( is_callable( array( 'WC_Cart', 'get_coupon_discount_totals' ) ) ) ? WC()->cart->get_coupon_discount_totals() : WC()->cart->coupon_discount_amounts;
			$coupon_discount_tax_totals = ( is_callable( array( 'WC_Cart', 'get_coupon_discount_tax_totals' ) ) ) ? WC()->cart->get_coupon_discount_tax_totals() : WC()->cart->coupon_discount_tax_amounts;
			$sc_total_credit_used       = array();

			if ( ! empty( $coupon_discount_totals ) && is_array( $coupon_discount_totals ) && count( $coupon_discount_totals ) > 0 ) {
				foreach ( $coupon_discount_totals as $coupon_code => $total ) {
					$coupon        = new WC_Coupon( $coupon_code );
					$discount_type = $coupon->get_discount_type();

					if ( 'smart_coupon' === $discount_type ) {
						$sc_total_credit_used[ $coupon_code ] = $total;

						if ( ! empty( $coupon_discount_tax_totals[ $coupon_code ] ) ) {
							$sc_include_tax = $this->is_store_credit_include_tax();
							if ( 'yes' === $sc_include_tax ) {
								$sc_total_credit_used[ $coupon_code ] += $coupon_discount_tax_totals[ $coupon_code ];
							} else {
								$prices_include_tax = ( 'incl' === get_option( 'woocommerce_tax_display_cart' ) ) ? true : false;
								if ( true === $prices_include_tax ) {
									$apply_before_tax = get_option( 'woocommerce_smart_coupon_apply_before_tax', 'no' );
									if ( 'yes' === $apply_before_tax ) {
										$_sc_include_tax = get_option( 'woocommerce_smart_coupon_include_tax', 'no' );
										if ( 'no' === $_sc_include_tax ) {
											$sc_total_credit_used[ $coupon_code ] += $coupon_discount_tax_totals[ $coupon_code ];
										}
									}
								}
							}
						}
					}
				}
			}

			if ( ! empty( $sc_total_credit_used ) ) {
				WC()->cart->smart_coupon_credit_used = $sc_total_credit_used;
			}
		}

		/**
		 * Function to calulate discount amount for an item
		 *
		 * @param  float $discounting_amount Amount the coupon is being applied to.
		 * @param  int   $quantity Item quantity.
		 * @param  float $subtotal Cart/Order subtotal.
		 * @param  float $coupon_amount Coupon amount.
		 * @return float $discount
		 */
		public function sc_get_discounted_price( $discounting_amount = 0, $quantity = 1, $subtotal = 0, $coupon_amount = 0 ) {
			$discount           = 0;
			$discounting_amount = $discounting_amount / $quantity;
			$discount_percent   = ( $discounting_amount * $quantity ) / $subtotal;

			$discount = ( $coupon_amount * $discount_percent ) / $quantity;
			$discount = min( $discount, $discounting_amount );

			return $discount;
		}

		/**
		 * Function to add cart item key for MNM child items.
		 * This was need because MNM child items didn't had cart item key inside $cart_item_data array and the
		 * function WC_SC_Apply_Before_Tax::cart_return_discount_amount() uses cart item key to set discount amount.
		 *
		 * @param  array  $cart_item_data Cart item data.
		 * @param  string $cart_item_key Cart item key.
		 * @return float $cart_item_data
		 */
		public function sc_mnm_compat( $cart_item_data, $cart_item_key ) {
			if ( ! empty( $cart_item_data['mnm_container'] ) ) {
				$cart_item_data['key'] = $cart_item_key;
			}

			return $cart_item_data;
		}

		/**
		 * Reset credit left to the defaults.
		 */
		public function cart_reset_credit_left() {
			$this->sc_credit_left                  = array();
			$this->remaining_total_to_apply_credit = array();
		}
	}
}

WC_SC_Apply_Before_Tax::get_instance();
