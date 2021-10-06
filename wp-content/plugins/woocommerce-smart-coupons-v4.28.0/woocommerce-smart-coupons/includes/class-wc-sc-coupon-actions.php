<?php
/**
 * Handle coupon actions
 *
 * @author      StoreApps
 * @since       3.5.0
 * @version     1.2.0
 *
 * @package     woocommerce-smart-coupons/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Coupon_Actions' ) ) {

	/**
	 * Class for handling processes of coupons
	 */
	class WC_SC_Coupon_Actions {

		/**
		 * Variable to hold instance of WC_SC_Coupon_Actions
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		private function __construct() {

			add_filter( 'woocommerce_add_cart_item', array( $this, 'modify_cart_item_data_in_add_to_cart' ), 15, 2 );
			add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'modify_cart_item_in_session' ), 15, 3 );
			add_filter( 'woocommerce_cart_item_quantity', array( $this, 'modify_cart_item_quantity' ), 5, 3 );
			add_filter( 'woocommerce_cart_item_price', array( $this, 'modify_cart_item_price' ), 10, 3 );
			add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'modify_cart_item_price' ), 10, 3 );
			add_filter( 'woocommerce_order_formatted_line_subtotal', array( $this, 'modify_cart_item_price' ), 10, 3 );
			add_filter( 'woocommerce_coupon_get_items_to_validate', array( $this, 'remove_products_from_validation' ), 10, 2 );

			add_action( 'woocommerce_applied_coupon', array( $this, 'coupon_action' ) );
			add_action( 'woocommerce_removed_coupon', array( $this, 'remove_product_from_cart' ) );
			add_action( 'woocommerce_check_cart_items', array( $this, 'review_cart_items' ) );
			add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'add_product_source_in_order_item_meta' ), 10, 4 );

			add_filter( 'wc_smart_coupons_export_headers', array( $this, 'export_headers' ) );
			add_filter( 'wc_sc_export_coupon_meta_data', array( $this, 'export_coupon_meta_data' ), 10, 2 );
			add_filter( 'smart_coupons_parser_postmeta_defaults', array( $this, 'postmeta_defaults' ) );
			add_filter( 'sc_generate_coupon_meta', array( $this, 'generate_coupon_meta' ), 10, 2 );
			add_filter( 'is_protected_meta', array( $this, 'make_action_meta_protected' ), 10, 3 );

			add_action( 'wc_sc_new_coupon_generated', array( $this, 'copy_coupon_action_meta' ) );

			add_filter( 'show_zero_amount_coupon', array( $this, 'show_coupon_with_actions' ), 10, 2 );
			add_filter( 'wc_sc_is_auto_generate', array( $this, 'auto_generate_coupon_with_actions' ), 10, 2 );
			add_filter( 'wc_sc_validate_coupon_amount', array( $this, 'validate_coupon_amount' ), 10, 2 );

			add_filter( 'wc_sc_hold_applied_coupons', array( $this, 'maybe_run_coupon_actions' ), 10, 2 );

		}

		/**
		 * Get single instance of WC_SC_Coupon_Actions
		 *
		 * @return WC_SC_Coupon_Actions Singleton object of WC_SC_Coupon_Actions
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
		 * @param string $function_name The function name.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return result of function call
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
		 * Get coupon actions
		 *
		 * @param  string $coupon_code The coupon code.
		 * @return array  Coupon actions
		 */
		public function get_coupon_actions( $coupon_code = '' ) {

			if ( empty( $coupon_code ) ) {
				return array();
			}

			$coupon_code = wc_format_coupon_code( $coupon_code );
			$coupon      = new WC_Coupon( $coupon_code );

			if ( ! is_wp_error( $coupon ) ) {

				if ( $this->is_wc_gte_30() ) {
					$actions = $coupon->get_meta( 'wc_sc_add_product_details' );
				} else {
					$coupon_id = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
					$actions   = get_post_meta( $coupon_id, 'wc_sc_add_product_details', true );
				}

				return apply_filters( 'wc_sc_coupon_actions', $actions, array( 'coupon_code' => $coupon_code ) );

			}

			return array();

		}

		/**
		 * Modify cart item data
		 *
		 * @param  array   $cart_item_data The cart item data.
		 * @param  integer $product_id     The product id.
		 * @param  integer $variation_id   The variation id.
		 * @param  integer $quantity       The quantity of product.
		 * @return array   $cart_item_data
		 */
		public function modify_cart_item_data( $cart_item_data = array(), $product_id = 0, $variation_id = 0, $quantity = 0 ) {

			if ( empty( $cart_item_data ) || empty( $product_id ) || empty( $quantity ) ) {
				return $cart_item_data;
			}

			if ( ! empty( $cart_item_data['wc_sc_product_source'] ) ) {
				$coupon_code    = $cart_item_data['wc_sc_product_source'];
				$coupon_actions = $this->get_coupon_actions( $coupon_code );
				if ( ! empty( $coupon_actions ) ) {
					foreach ( $coupon_actions as $product_data ) {
						if ( ! empty( $product_data['product_id'] ) && in_array( absint( $product_data['product_id'] ), array_map( 'absint', array( $product_id, $variation_id ) ), true ) ) {
							$discount_amount = ( '' !== $product_data['discount_amount'] ) ? $product_data['discount_amount'] : '';
							if ( '' !== $discount_amount ) {
								if ( ! empty( $variation_id ) ) {
									$product = wc_get_product( $variation_id );
								} else {
									$product = wc_get_product( $product_id );
								}
								$product_price = $product->get_price();
								$regular_price = $product->get_regular_price();
								$discount_type = ( ! empty( $product_data['discount_type'] ) ) ? $product_data['discount_type'] : 'percent';
								switch ( $discount_type ) {
									case 'flat':
										$discount = $discount_amount;
										break;

									case 'percent':
										$discount = ( $product_price * $discount_amount ) / 100;
										break;
								}
								$discount         = wc_cart_round_discount( min( $product_price, $discount ), 2 );
								$discounted_price = $product_price - $discount;
								$cart_item_data['data']->set_price( $discounted_price );
								$cart_item_data['data']->set_regular_price( $regular_price );
								$cart_item_data['data']->set_sale_price( $discounted_price );
							}
							break;
						}
					}
				}
			}

			return $cart_item_data;
		}

		/**
		 * Modify cart item in WC_Cart::add_to_cart()
		 *
		 * @param array  $cart_item_data The cart item data as passed by filter 'woocommerce_add_cart_item'.
		 * @param string $cart_item_key The cart item key.
		 * @return array $cart_item_data
		 */
		public function modify_cart_item_data_in_add_to_cart( $cart_item_data = array(), $cart_item_key = '' ) {
			if ( ! empty( $cart_item_data['wc_sc_product_source'] ) ) {
				$cart_item_data = $this->modify_cart_item_data( $cart_item_data, $cart_item_data['product_id'], $cart_item_data['variation_id'], $cart_item_data['quantity'] );
			}

			return $cart_item_data;
		}

		/**
		 * Modify cart item in session
		 *
		 * @param  array  $session_data The session data.
		 * @param  array  $values       The cart item.
		 * @param  string $key          The cart item key.
		 * @return array  $session_data
		 */
		public function modify_cart_item_in_session( $session_data = array(), $values = array(), $key = '' ) {

			if ( ! empty( $values['wc_sc_product_source'] ) ) {
				$session_data['wc_sc_product_source'] = $values['wc_sc_product_source'];
				$qty                                  = ( ! empty( $session_data['quantity'] ) ) ? absint( $session_data['quantity'] ) : ( ( ! empty( $values['quantity'] ) ) ? absint( $values['quantity'] ) : 1 );
				$session_data                         = $this->modify_cart_item_data( $session_data, $session_data['product_id'], $session_data['variation_id'], $qty );
			}

			return $session_data;
		}

		/**
		 * Modify cart item quantity
		 *
		 * @param  string $product_quantity The product quantity.
		 * @param  string $cart_item_key    The cart item key.
		 * @param  array  $cart_item        The cart item.
		 * @return string $product_quantity
		 */
		public function modify_cart_item_quantity( $product_quantity = '', $cart_item_key = '', $cart_item = array() ) {

			if ( ! empty( $cart_item['wc_sc_product_source'] ) ) {
				$product_quantity = sprintf( '%s <input type="hidden" name="cart[%s][qty]" value="%s" />', $cart_item['quantity'], $cart_item_key, $cart_item['quantity'] );
			}

			return $product_quantity;
		}

		/**
		 * Modify cart item price
		 *
		 * @param  string $product_price The product price.
		 * @param  array  $cart_item     The cart item.
		 * @param  string $cart_item_key The cart item key.
		 * @return string $product_price
		 */
		public function modify_cart_item_price( $product_price = '', $cart_item = array(), $cart_item_key = '' ) {

			if ( ( is_array( $cart_item ) && isset( $cart_item['wc_sc_product_source'] ) ) || ( is_object( $cart_item ) && is_callable( array( $cart_item, 'get_meta' ) ) && $cart_item->get_meta( '_wc_sc_product_source' ) ) ) {
				if ( wc_price( 0 ) === $product_price ) {
					$product_price = apply_filters(
						'wc_sc_price_zero_text',
						$product_price,
						array(
							'cart_item'     => $cart_item,
							'cart_item_key' => $cart_item_key,
						)
					);
				}
			}

			return $product_price;
		}

		/**
		 * Remove products added by the coupon from validation
		 *
		 * Since the filter 'woocommerce_coupon_get_items_to_validate' is added in WooCommerce 3.4.0, this function will work only in WC 3.4.0+
		 * Otherwise, the products added by coupon might get double discounts applied
		 *
		 * @param  array        $items     The cart/order items.
		 * @param  WC_Discounts $discounts The discounts object.
		 * @return mixed        $items
		 */
		public function remove_products_from_validation( $items = array(), $discounts = null ) {

			if ( ! empty( $items ) ) {
				foreach ( $items as $index => $item ) {
					$coupon_code = '';
					if ( is_array( $item->object ) && isset( $item->object['wc_sc_product_source'] ) ) {
						$coupon_code = $item->object['wc_sc_product_source'];
					} elseif ( is_a( $item->object, 'WC_Order_Item' ) && is_callable( array( $item->object, 'get_meta' ) ) && $item->object->get_meta( '_wc_sc_product_source' ) ) {
						$coupon_code = $item->object->get_meta( '_wc_sc_product_source' );
					}
					if ( ! empty( $coupon_code ) ) {
						$item_product_id = ( is_a( $item->product, 'WC_Product' ) && is_callable( array( $item->product, 'get_id' ) ) ) ? $item->product->get_id() : 0;
						$coupon_actions  = $this->get_coupon_actions( $coupon_code );
						if ( ! empty( $coupon_actions ) ) {
							foreach ( $coupon_actions as $product_data ) {
								if ( ! empty( $product_data['product_id'] ) && absint( $product_data['product_id'] ) === absint( $item_product_id ) ) {
									$discount_amount = ( '' !== $product_data['discount_amount'] ) ? $product_data['discount_amount'] : '';
									if ( '' !== $discount_amount ) {
										unset( $items[ $index ] );
									}
								}
							}
						}
					}
				}
			}

			return $items;

		}

		/**
		 * Apply coupons actions
		 *
		 * @param string $coupon_code The coupon code.
		 */
		public function coupon_action( $coupon_code = '' ) {

			if ( empty( $coupon_code ) ) {
				return;
			}

			$coupon_actions = $this->get_coupon_actions( $coupon_code );

			if ( ! empty( $coupon_actions ) ) {
				$product_names = array();
				foreach ( $coupon_actions as $coupon_action ) {
					if ( empty( $coupon_action['product_id'] ) ) {
						continue;
					}

					$id = absint( $coupon_action['product_id'] );

					$product = wc_get_product( $id );

					$product_data = $this->get_product_data( $product );

					$product_id   = ( ! empty( $product_data['product_id'] ) ) ? absint( $product_data['product_id'] ) : 0;
					$variation_id = ( ! empty( $product_data['variation_id'] ) ) ? absint( $product_data['variation_id'] ) : 0;
					$variation    = array();

					if ( ! empty( $variation_id ) ) {
						$variation = $product->get_variation_attributes();
					}

					$quantity = absint( $coupon_action['quantity'] );

					$cart_item_data = array(
						'wc_sc_product_source' => $coupon_code,
					);

					$cart_item_key = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation, $cart_item_data );

					if ( ! empty( $cart_item_key ) ) {
						if ( $this->is_wc_gte_30() ) {
							$product_names[] = ( is_object( $product ) && is_callable( array( $product, 'get_name' ) ) ) ? $product->get_name() : '';
						} else {
							$product_names[] = ( is_object( $product ) && is_callable( array( $product, 'get_title' ) ) ) ? $product->get_title() : '';
						}
					}
				}

				if ( ! empty( $product_names ) ) {
					/* translators: 1. Product title */
					wc_add_notice( sprintf( __( '%s has been added to your cart!', 'woocommerce-smart-coupons' ), implode( ', ', $product_names ) ) );
				}
			}

		}

		/**
		 * Remove products from cart if the coupon, which added the product, is removed
		 *
		 * @param string $coupon_code The coupon code.
		 */
		public function remove_product_from_cart( $coupon_code = '' ) {

			if ( is_admin() ) {
				return;
			}

			if ( ! empty( $coupon_code ) ) {
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
					if ( isset( $cart_item['wc_sc_product_source'] ) && $cart_item['wc_sc_product_source'] === $coupon_code ) {
						// Action 'woocommerce_before_calculate_totals' is hooked by WooCommerce Subscription while removing coupons in local WooCommerce Cart variable in which we don't need to remove added cart item.
						if ( ! doing_action( 'woocommerce_before_calculate_totals' ) ) {
							WC()->cart->set_quantity( $cart_item_key, 0 );
						}
					}
				}
			}

		}

		/**
		 * Review cart items
		 */
		public function review_cart_items() {

			$applied_coupons = (array) WC()->cart->applied_coupons;

			$products = array();
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				if ( ! empty( $cart_item['wc_sc_product_source'] ) && ! in_array( $cart_item['wc_sc_product_source'], $applied_coupons, true ) ) {
					WC()->cart->set_quantity( $cart_item_key, 0 );
					$coupon_code = $cart_item['wc_sc_product_source'];
					if ( empty( $products[ $coupon_code ] ) || ! is_array( $products[ $coupon_code ] ) ) {
						$products[ $coupon_code ] = array();
					}
					$products[ $coupon_code ][] = ( is_object( $cart_item['data'] ) && is_callable( array( $cart_item['data'], 'get_name' ) ) ) ? $cart_item['data']->get_name() : '';
					$products[ $coupon_code ]   = array_filter( $products[ $coupon_code ] );
				}
			}

			if ( ! empty( $products ) ) {
				foreach ( $products as $coupon_code => $product_names ) {
					/* translators: 1. Product/s 2. Product names 3. is/are 4. Coupons code */
					wc_add_notice( sprintf( __( '%1$s %2$s %3$s removed because coupon %4$s is removed.', 'woocommerce-smart-coupons' ), _n( 'Product', 'Products', count( $products[ $coupon_code ] ), 'woocommerce-smart-coupons' ), '<strong>' . implode( ', ', $products[ $coupon_code ] ) . '</strong>', _n( 'is', 'are', count( $products[ $coupon_code ] ), 'woocommerce-smart-coupons' ), '<code>' . $coupon_code . '</code>' ), 'error' );
				}
			}

		}

		/**
		 * Add product source in order item meta
		 *
		 * @param mixed    $item          The item.
		 * @param string   $cart_item_key The cart item key.
		 * @param array    $values        The cart item.
		 * @param WC_Order $order         The order.
		 */
		public function add_product_source_in_order_item_meta( $item = null, $cart_item_key = '', $values = array(), $order = null ) {

			if ( isset( $values['wc_sc_product_source'] ) ) {
				$item->add_meta_data( '_wc_sc_product_source', $values['wc_sc_product_source'], true );
			}

		}

		/**
		 * Get product data
		 *
		 * @param  mixed $product The product object.
		 * @return array
		 */
		public function get_product_data( $product = null ) {

			if ( empty( $product ) ) {
				return array();
			}

			if ( $this->is_wc_gte_30() ) {
				$product_id = ( is_object( $product ) && is_callable( array( $product, 'get_id' ) ) ) ? $product->get_id() : 0;
			} else {
				$product_id = ( ! empty( $product->id ) ) ? $product->id : 0;
			}

			$product_type = ( is_object( $product ) && is_callable( array( $product, 'get_type' ) ) ) ? $product->get_type() : '';

			if ( 'variation' === $product_type ) {
				$variation_id = $product_id;
				if ( $this->is_wc_gte_30() ) {
					$parent_id      = ( is_object( $product ) && is_callable( array( $product, 'get_parent_id' ) ) ) ? $product->get_parent_id() : 0;
					$variation_data = wc_get_product_variation_attributes( $variation_id );
				} else {
					$parent_id      = ( is_object( $product ) && is_callable( array( $product, 'get_parent' ) ) ) ? $product->get_parent() : 0;
					$variation_data = ( ! empty( $product->variation_data ) ) ? $product->variation_data : array();
				}
				$product_id = $parent_id;
			} else {
				$variation_id   = 0;
				$variation_data = array();
			}

			$product_data = array(
				'product_id'     => $product_id,
				'variation_id'   => $variation_id,
				'variation_data' => $variation_data,
			);

			return apply_filters( 'wc_sc_product_data', $product_data, array( 'product_obj' => $product ) );

		}

		/**
		 * Add action's meta in export headers
		 *
		 * @param  array $headers Existing headers.
		 * @return array
		 */
		public function export_headers( $headers = array() ) {

			$action_headers = array(
				'wc_sc_add_product_details' => __( 'Add product details', 'woocommerce-smart-coupons' ),
			);

			return array_merge( $headers, $action_headers );

		}

		/**
		 * Function to handle coupon meta data during export of existing coupons
		 *
		 * @param  mixed $meta_value The meta value.
		 * @param  array $args       Additional arguments.
		 * @return string Processed meta value
		 */
		public function export_coupon_meta_data( $meta_value = '', $args = array() ) {

			$index       = ( ! empty( $args['index'] ) ) ? $args['index'] : -1;
			$meta_keys   = ( ! empty( $args['meta_keys'] ) ) ? $args['meta_keys'] : array();
			$meta_values = ( ! empty( $args['meta_values'] ) ) ? $args['meta_values'] : array();

			if ( $index >= 0 && ! empty( $meta_keys[ $index ] ) && 'wc_sc_add_product_details' === $meta_keys[ $index ] ) {

				if ( ! empty( $meta_value ) && is_array( $meta_value ) ) {
					$product_details = array();
					foreach ( $meta_value as $value ) {
						$product_details[] = implode( ',', $value );
					}
					$meta_value = implode( '|', $product_details );
				}
			}

			return $meta_value;

		}

		/**
		 * Post meta defaults for action's meta
		 *
		 * @param  array $defaults Existing postmeta defaults.
		 * @return array
		 */
		public function postmeta_defaults( $defaults = array() ) {

			$actions_defaults = array(
				'wc_sc_add_product_details' => '',
			);

			return array_merge( $defaults, $actions_defaults );
		}

		/**
		 * Add action's meta with value in coupon meta
		 *
		 * @param  array $data The row data.
		 * @param  array $post The POST values.
		 * @return array Modified data
		 */
		public function generate_coupon_meta( $data = array(), $post = array() ) {

			if ( isset( $post['wc_sc_add_product_ids'] ) ) {
				if ( $this->is_wc_gte_30() ) {
					$product_ids = wc_clean( wp_unslash( $post['wc_sc_add_product_ids'] ) ); // phpcs:ignore
				} else {
					$product_ids = array_filter( array_map( 'trim', explode( ',', sanitize_text_field( wp_unslash( $post['wc_sc_add_product_ids'] ) ) ) ) ); // phpcs:ignore
				}
				$add_product_details = array();
				if ( ! empty( $product_ids ) ) {
					$quantity        = ( isset( $post['wc_sc_add_product_qty'] ) ) ? wc_clean( wp_unslash( $post['wc_sc_add_product_qty'] ) ) : 1;
					$discount_amount = ( isset( $post['wc_sc_product_discount_amount'] ) ) ? wc_clean( wp_unslash( $post['wc_sc_product_discount_amount'] ) ) : '';
					$discount_type   = ( isset( $post['wc_sc_product_discount_type'] ) ) ? wc_clean( wp_unslash( $post['wc_sc_product_discount_type'] ) ) : '';
					foreach ( $product_ids as $id ) {
						$product_data                    = array();
						$product_data['product_id']      = $id;
						$product_data['quantity']        = $quantity;
						$product_data['discount_amount'] = $discount_amount;
						$product_data['discount_type']   = $discount_type;
						$add_product_details[]           = implode( ',', $product_data );
					}
				}
				$data['wc_sc_add_product_details'] = implode( '|', $add_product_details );
			}

			return $data;
		}

		/**
		 * Make meta data of SC actions, protected
		 *
		 * @param bool   $protected Is protected.
		 * @param string $meta_key The meta key.
		 * @param string $meta_type The meta type.
		 * @return bool $protected
		 */
		public function make_action_meta_protected( $protected, $meta_key, $meta_type ) {
			$sc_meta = array(
				'wc_sc_add_product_details',
			);
			if ( in_array( $meta_key, $sc_meta, true ) ) {
				return true;
			}
			return $protected;
		}

		/**
		 * Function to copy coupon action meta in newly generated coupon
		 *
		 * @param  array $args The arguments.
		 */
		public function copy_coupon_action_meta( $args = array() ) {

			$new_coupon_id = ( ! empty( $args['new_coupon_id'] ) ) ? absint( $args['new_coupon_id'] ) : 0;
			$coupon        = ( ! empty( $args['ref_coupon'] ) ) ? $args['ref_coupon'] : false;

			if ( empty( $new_coupon_id ) || empty( $coupon ) ) {
				return;
			}

			$add_product_details = array();
			if ( $this->is_wc_gte_30() ) {
				$add_product_details = $coupon->get_meta( 'wc_sc_add_product_details' );
			} else {
				$old_coupon_id       = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
				$add_product_details = get_post_meta( $old_coupon_id, 'wc_sc_add_product_details', true );
			}
			update_post_meta( $new_coupon_id, 'wc_sc_add_product_details', $add_product_details );

		}

		/**
		 * Function to validate whether to show the coupon or not
		 *
		 * @param  boolean $is_show Show or not.
		 * @param  array   $args    Additional arguments.
		 * @return boolean
		 */
		public function show_coupon_with_actions( $is_show = false, $args = array() ) {

			$coupon = ( ! empty( $args['coupon'] ) ) ? $args['coupon'] : null;

			if ( empty( $coupon ) ) {
				return $is_show;
			}

			if ( $this->is_wc_gte_30() ) {
				$coupon_code = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_code' ) ) ) ? $coupon->get_code() : '';
			} else {
				$coupon_code = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
			}

			$coupon_actions = $this->get_coupon_actions( $coupon_code );

			if ( ! empty( $coupon_actions ) ) {
				return true;
			}

			return $is_show;

		}

		/**
		 * Allow auto-generate of coupon with coupon action
		 *
		 * @param  boolean $is_auto_generate Whether to auto-generate or not.
		 * @param  array   $args             Additional parameters.
		 * @return boolean $is_auto_generate
		 */
		public function auto_generate_coupon_with_actions( $is_auto_generate = false, $args = array() ) {

			$coupon    = ( ! empty( $args['coupon_obj'] ) && $args['coupon_obj'] instanceof WC_Coupon ) ? $args['coupon_obj'] : false;
			$coupon_id = ( ! empty( $args['coupon_id'] ) ) ? $args['coupon_id'] : false;

			if ( ! empty( $coupon ) && ! empty( $coupon_id ) ) {
				if ( $this->is_wc_gte_30() ) {
					$coupon_code = $coupon->get_code();
				} else {
					$coupon_code = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
				}
				if ( ! empty( $coupon_code ) ) {
					$actions        = get_post_meta( $coupon_id, 'wc_sc_add_product_details', true );
					$coupon_actions = apply_filters( 'wc_sc_coupon_actions', $actions, array( 'coupon_code' => $coupon_code ) );
					if ( ! empty( $coupon_actions ) ) {
						return true;
					}
				}
			}

			return $is_auto_generate;
		}

		/**
		 * Validate coupon having actions but without an amount
		 *
		 * @param  boolean $is_valid_coupon_amount Whether the amount is validate or not.
		 * @param  array   $args                   Additional parameters.
		 * @return boolean
		 */
		public function validate_coupon_amount( $is_valid_coupon_amount = true, $args = array() ) {

			if ( ! $is_valid_coupon_amount ) {
				$coupon_amount = ( ! empty( $args['coupon_amount'] ) ) ? $args['coupon_amount'] : 0;
				$discount_type = ( ! empty( $args['discount_type'] ) ) ? $args['discount_type'] : '';
				$coupon_code   = ( ! empty( $args['coupon_code'] ) ) ? $args['coupon_code'] : '';

				$coupon_actions = ( ! empty( $coupon_code ) ) ? $this->get_coupon_actions( $coupon_code ) : array();

				if ( 'smart_coupon' === $discount_type && $coupon_amount <= 0 && ! empty( $coupon_actions ) ) {
					return true;
				}
			}

			return $is_valid_coupon_amount;
		}

		/**
		 * Handle coupon actions when the cart is empty
		 *
		 * @param  boolean $is_hold Whether to hold the coupon in cookie.
		 * @param  array   $args    Additional arguments.
		 * @return boolean
		 */
		public function maybe_run_coupon_actions( $is_hold = true, $args = array() ) {
			$cart = ( is_object( WC() ) && isset( WC()->cart ) ) ? WC()->cart : null;
			if ( empty( $cart ) || WC()->cart->is_empty() ) {
				$coupons_data = ( ! empty( $args['coupons_data'] ) ) ? $args['coupons_data'] : array();
				if ( ! empty( $coupons_data ) ) {
					foreach ( $coupons_data as $coupon_data ) {
						$coupon_code = ( ! empty( $coupon_data['coupon-code'] ) ) ? $coupon_data['coupon-code'] : '';
						if ( ! empty( $coupon_code ) ) {
							$coupon_actions  = $this->get_coupon_actions( $coupon_code );
							$coupon_products = ( ! empty( $coupon_actions ) ) ? wp_list_pluck( $coupon_actions, 'product_id' ) : array();
							if ( ! empty( $coupon_products ) ) {
								if ( ! WC()->cart->has_discount( $coupon_code ) ) {
									WC()->cart->add_discount( trim( $coupon_code ) );
									$is_hold = false;
								}
							}
						}
					}
				}
			}
			return $is_hold;
		}

	}

}

WC_SC_Coupon_Actions::get_instance();
