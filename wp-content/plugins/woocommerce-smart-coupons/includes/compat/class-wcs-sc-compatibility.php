<?php
/**
 * Compatibility file for WooCommerce Subscriptions
 *
 * @author      StoreApps
 * @since       3.3.0
 * @version     1.5.1
 * @package     WooCommerce Smart Coupons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WCS_SC_Compatibility' ) ) {

	/**
	 * Class for handling compatibility with WooCommerce Subscriptions
	 */
	class WCS_SC_Compatibility {

		/**
		 * Variable to hold instance of WCS_SC_Compatibility
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {

			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			if ( is_plugin_active( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ) ) {
				add_action( 'wp_loaded', array( $this, 'sc_wcs_renewal_filters' ), 20 );
				add_filter( 'woocommerce_subscriptions_validate_coupon_type', array( $this, 'smart_coupon_as_valid_subscription_coupon_type' ), 10, 3 );
				add_filter( 'wc_smart_coupons_settings', array( $this, 'smart_coupons_settings' ) );
				add_filter( 'wcs_bypass_coupon_removal', array( $this, 'bypass_removal_of_coupon_having_coupon_actions' ), 10, 4 );
				add_filter( 'woocommerce_subscriptions_calculated_total', array( $this, 'modify_recurring_cart' ) );
				add_action( 'wp_loaded', array( $this, 'hooks_for_wcs_230' ) );
				add_filter( 'wc_sc_endpoint_account_settings_after_key', array( $this, 'endpoint_account_settings_after_key' ), 10, 2 );
				add_filter( 'wc_sc_coupon_type', array( $this, 'valid_display_type' ), 11, 3 );
				add_filter( 'wc_sc_coupon_amount', array( $this, 'valid_display_amount' ), 11, 2 );
				add_filter( 'wc_sc_coupon_design_thumbnail_src_set', array( $this, 'coupon_design_thumbnail_src_set' ), 10, 2 );
				add_filter( 'wc_sc_percent_discount_types', array( $this, 'percent_discount_types' ), 10, 2 );
				add_filter( 'wc_sc_is_auto_apply', array( $this, 'is_auto_apply' ), 10, 2 );
			}

		}

		/**
		 * Get single instance of WCS_SC_Compatibility
		 *
		 * @return WCS_SC_Compatibility Singleton object of WCS_SC_Compatibility
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
		 * Function to manage appropriate filter for applying Smart Coupons feature in renewal order
		 */
		public function sc_wcs_renewal_filters() {
			if ( self::is_wcs_gte( '2.0.0' ) ) {
				add_filter( 'wcs_get_subscription', array( $this, 'sc_wcs_modify_subscription' ) );
				add_filter( 'wcs_renewal_order_meta', array( $this, 'sc_wcs_renewal_order_meta' ), 10, 3 );
				add_filter( 'wcs_new_order_created', array( $this, 'sc_wcs_modify_renewal_order_meta' ), 10, 2 );
				add_filter( 'wcs_renewal_order_items', array( $this, 'sc_wcs_modify_renewal_order' ), 10, 3 );
				add_filter( 'wcs_renewal_order_items', array( $this, 'sc_wcs_renewal_order_items' ), 10, 3 );
				add_filter( 'wcs_renewal_order_created', array( $this, 'sc_wcs_renewal_complete_payment' ), 10, 2 );
				add_action( 'woocommerce_update_order', array( $this, 'smart_coupons_contribution' ), 8 );
				add_filter( 'is_show_gift_certificate_receiver_detail_form', array( $this, 'is_show_gift_certificate_receiver_detail_form' ), 10, 2 );
			} else {
				add_filter( 'woocommerce_subscriptions_renewal_order_items', array( $this, 'sc_modify_renewal_order' ), 10, 5 );
				add_filter( 'woocommerce_subscriptions_renewal_order_items', array( $this, 'sc_subscriptions_renewal_order_items' ), 10, 5 );
				add_action( 'woocommerce_subscriptions_renewal_order_created', array( $this, 'sc_renewal_complete_payment' ), 10, 4 );
			}
		}

		/**
		 * Function to manage payment method for renewal orders based on availability of store credit (WCS 2.0+)
		 *
		 * @param WC_Subscription $subscription Subscription object.
		 * @return WC_Subscription $subscription
		 */
		public function sc_wcs_modify_subscription( $subscription = null ) {

			if ( did_action( 'woocommerce_scheduled_subscription_payment' ) < 1 ) {
				return $subscription;
			}

			if ( ! empty( $subscription ) && $subscription instanceof WC_Subscription ) {

				$pay_from_credit_of_original_order = get_option( 'pay_from_smart_coupon_of_original_order', 'yes' );

				if ( 'yes' !== $pay_from_credit_of_original_order ) {
					return $subscription;
				}

				if ( $this->is_wc_gte_30() ) {
					$subscription_parent_order = $subscription->get_parent();
					$original_order_id         = ( is_object( $subscription_parent_order ) && is_callable( array( $subscription_parent_order, 'get_id' ) ) ) ? $subscription_parent_order->get_id() : 0;
				} else {
					$original_order_id = ( ! empty( $subscription->order->id ) ) ? $subscription->order->id : 0;
				}

				if ( empty( $original_order_id ) ) {
					return $subscription;
				}

				$renewal_total                 = $subscription->get_total();
				$original_order                = wc_get_order( $original_order_id );
				$coupon_used_in_original_order = $this->get_coupon_codes( $original_order );

				if ( $this->is_wc_gte_30() ) {
					$order_payment_method = $original_order->get_payment_method();
				} else {
					$order_payment_method = ( ! empty( $original_order->payment_method ) ) ? $original_order->payment_method : 0;
				}

				if ( count( $coupon_used_in_original_order ) > 0 ) {
					foreach ( $coupon_used_in_original_order as $coupon_code ) {
						$coupon = new WC_Coupon( $coupon_code );
						if ( $this->is_wc_gte_30() ) {
							$coupon_amount = $coupon->get_amount();
							$discount_type = $coupon->get_discount_type();
						} else {
							$coupon_amount = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
							$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
						}
						if ( ! empty( $discount_type ) && 'smart_coupon' === $discount_type && ! empty( $coupon_amount ) ) {
							if ( $coupon_amount >= $renewal_total ) {
								$subscription->set_payment_method( '' );
							} else {
								$payment_gateways = WC()->payment_gateways->get_available_payment_gateways();
								if ( ! empty( $payment_gateways[ $order_payment_method ] ) ) {
									$payment_method = $payment_gateways[ $order_payment_method ];
									$subscription->set_payment_method( $payment_method );
								}
							}
						}
					}
				}
			}

			return $subscription;
		}

		/**
		 * Function to add meta which is necessary for coupon processing, in order
		 *
		 * @param   array           $meta Order meta.
		 * @param   WC_Order        $to_order Order to copy meta to.
		 * @param   WC_Subscription $from_order Order to copy meta from.
		 * @return  array $meta
		 */
		public function sc_wcs_renewal_order_meta( $meta, $to_order, $from_order ) {

			if ( $this->is_wc_gte_30() ) {
				$order    = $from_order->get_parent();
				$order_id = ( is_object( $order ) && is_callable( array( $order, 'get_id' ) ) ) ? $order->get_id() : 0;
			} else {
				$order    = $from_order->order;
				$order_id = ( ! empty( $order->id ) ) ? $order->id : 0;
			}

			if ( empty( $order_id ) ) {
				return $meta;
			}

			$meta_exists = array(
				'coupon_sent'                => false,
				'gift_receiver_email'        => false,
				'gift_receiver_message'      => false,
				'sc_called_credit_details'   => false,
				'smart_coupons_contribution' => false,
			);

			foreach ( $meta as $index => $data ) {
				if ( $this->is_wcs_gte( '2.2.0' ) ) {
					if ( ! empty( $data['meta_key'] ) ) {
						$prefixed_key   = wcs_maybe_prefix_key( $data['meta_key'] );
						$unprefixed_key = ( $data['meta_key'] === $prefixed_key ) ? substr( $data['meta_key'], 1 ) : $data['meta_key'];
						if ( array_key_exists( $unprefixed_key, $meta_exists ) ) {
							unset( $meta[ $index ] );
						}
					}
				} else {
					if ( ! empty( $data['meta_key'] ) && array_key_exists( $data['meta_key'], $meta_exists ) ) {
						$meta_exists[ $data['meta_key'] ] = true; // phpcs:ignore
					}
				}
			}

			foreach ( $meta_exists as $key => $value ) {
				if ( $value ) {
					continue;
				}
				$meta_value = get_post_meta( $order_id, $key, true );

				if ( empty( $meta_value ) ) {
					continue;
				}

				if ( $this->is_wcs_gte( '2.2.0' ) ) {
					$renewal_order_id = ( is_object( $to_order ) && is_callable( array( $to_order, 'get_id' ) ) ) ? $to_order->get_id() : 0;
					if ( 'coupon_sent' === $key ) {
						// update_post_meta( $renewal_order_id, $key, 'no' );
						// No need to update meta 'coupon_sent' as it's being updated by function sc_modify_renewal_order in this class.
						continue;
					} elseif ( 'smart_coupons_contribution' === $key ) {
						update_post_meta( $renewal_order_id, $key, array() );
					} else {
						update_post_meta( $renewal_order_id, $key, $meta_value );
					}
				} else {
					if ( ! isset( $meta ) || ! is_array( $meta ) ) {
						$meta = array();
					}
					$meta[] = array(
						'meta_key'   => $key, // phpcs:ignore
						'meta_value' => $meta_value, // phpcs:ignore
					);
				}
			}

			return $meta;
		}

		/**
		 * Function to modify renewal order meta
		 *
		 * @param WC_Order        $renewal_order Order created on subscription renewal.
		 * @param WC_Subscription $subscription Subscription we're basing the order off of.
		 * @return WC_Order $renewal_order
		 */
		public function sc_wcs_modify_renewal_order_meta( $renewal_order = null, $subscription = null ) {
			global $wpdb;

			if ( $this->is_wc_gte_30() ) {
				$renewal_order_id = ( is_object( $renewal_order ) && is_callable( array( $renewal_order, 'get_id' ) ) ) ? $renewal_order->get_id() : 0;
			} else {
				$renewal_order_id = ( ! empty( $renewal_order->id ) ) ? $renewal_order->id : 0;
			}

			if ( empty( $renewal_order_id ) ) {
				return $renewal_order;
			}

			$sc_called_credit_details = get_post_meta( $renewal_order_id, 'sc_called_credit_details', true );
			if ( empty( $sc_called_credit_details ) ) {
				return $renewal_order;
			}

			$old_order_item_ids = ( ! empty( $sc_called_credit_details ) ) ? array_keys( $sc_called_credit_details ) : array();

			if ( ! empty( $old_order_item_ids ) ) {

				$old_order_item_ids = array_map( 'absint', $old_order_item_ids );

				$meta_keys   = array( '_variation_id', '_product_id' );
				$how_many    = count( $old_order_item_ids );
				$placeholder = array_fill( 0, $how_many, '%d' );

				$meta_keys = esc_sql( $meta_keys );

				// @codingStandardsIgnoreStart.
				$query_to_fetch_product_ids = $wpdb->prepare(
					"SELECT woim.order_item_id,
					(CASE
						WHEN woim.meta_key = %s AND woim.meta_value > 0 THEN woim.meta_value
						WHEN woim.meta_key = %s AND woim.meta_value > 0 THEN woim.meta_value
					END) AS product_id
					FROM {$wpdb->prefix}woocommerce_order_itemmeta AS woim
					WHERE woim.order_item_id IN ( " . implode( ',', $placeholder ) . " )
						AND woim.meta_key IN ( %s, %s )
					GROUP BY woim.order_item_id",
					array_merge( $meta_keys, $old_order_item_ids, array_reverse( $meta_keys ) )
				);
				// @codingStandardsIgnoreEnd.

				$product_ids_results = $wpdb->get_results( $query_to_fetch_product_ids, 'ARRAY_A' ); // phpcs:ignore

				if ( ! is_wp_error( $product_ids_results ) && ! empty( $product_ids_results ) ) {
					$product_to_old_item = array();
					foreach ( $product_ids_results as $result ) {
						$product_to_old_item[ $result['product_id'] ] = $result['order_item_id'];
					}

					$found_product_ids = ( ! empty( $product_to_old_item ) ) ? $product_to_old_item : array();

					$query_to_fetch_new_order_item_ids = $wpdb->prepare(
						"SELECT woim.order_item_id,
																			(CASE
																				WHEN woim.meta_value > 0 THEN woim.meta_value
																			END) AS product_id
																			FROM {$wpdb->prefix}woocommerce_order_items AS woi
																				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS woim
																					ON (woim.order_item_id = woi.order_item_id AND woim.meta_key IN ( %s, %s ))
																			WHERE woi.order_id = %d
																				AND woim.order_item_id IS NOT NULL
																			GROUP BY woim.order_item_id",
						'_product_id',
						'_variation_id',
						$renewal_order_id
					);

					$new_order_item_ids_result = $wpdb->get_results( $query_to_fetch_new_order_item_ids, 'ARRAY_A' ); // phpcs:ignore

					if ( ! is_wp_error( $new_order_item_ids_result ) && ! empty( $new_order_item_ids_result ) ) {
						$product_to_new_item = array();
						foreach ( $new_order_item_ids_result as $result ) {
							$product_to_new_item[ $result['product_id'] ] = $result['order_item_id'];
						}
					}
				}
			}
			foreach ( $sc_called_credit_details as $item_id => $credit_amount ) {
				$product_id = array_search( $item_id, $product_to_old_item, true );
				if ( false !== $product_id ) {
					$sc_called_credit_details[ $product_to_new_item[ $product_id ] ] = $credit_amount;
					unset( $sc_called_credit_details[ $product_to_old_item[ $product_id ] ] );
				}
			}

			update_post_meta( $renewal_order_id, 'sc_called_credit_details', $sc_called_credit_details );
			return $renewal_order;
		}

		/**
		 * New function to handle auto generation of coupon from renewal orders (WCS 2.0+)
		 *
		 * @param array           $order_items Order items.
		 * @param WC_Order        $renewal_order Order created on subscription renewal.
		 * @param WC_Subscription $subscription Subscription we're basing the order off of.
		 * @return array $order_items
		 */
		public function sc_wcs_modify_renewal_order( $order_items = null, $renewal_order = null, $subscription = null ) {

			if ( $this->is_wc_gte_30() ) {
				$subscription_parent_order = $subscription->get_parent();
				$subscription_order_id     = ( is_object( $subscription_parent_order ) && is_callable( array( $subscription_parent_order, 'get_id' ) ) ) ? $subscription_parent_order->get_id() : 0;
				$renewal_order_id          = ( is_callable( array( $renewal_order, 'get_id' ) ) ) ? $renewal_order->get_id() : 0;
			} else {
				$subscription_order_id = ( ! empty( $subscription->order->id ) ) ? $subscription->order->id : 0;
				$renewal_order_id      = ( ! empty( $renewal_order->id ) ) ? $renewal_order->id : 0;
			}

			$order_items = $this->sc_modify_renewal_order( $order_items, $subscription_order_id, $renewal_order_id );
			return $order_items;
		}

		/**
		 * New function to modify order_items of renewal order (WCS 2.0+)
		 *
		 * @param array           $order_items Order items.
		 * @param WC_Order        $renewal_order Order created on subscription renewal.
		 * @param WC_Subscription $subscription Subscription we're basing the order off of.
		 * @return array $order_items
		 */
		public function sc_wcs_renewal_order_items( $order_items = null, $renewal_order = null, $subscription = null ) {

			if ( $this->is_wc_gte_30() ) {
				$subscription_parent_order = $subscription->get_parent();
				$subscription_order_id     = ( is_object( $subscription_parent_order ) && is_callable( array( $subscription_parent_order, 'get_id' ) ) ) ? $subscription_parent_order->get_id() : 0;
				$renewal_order_id          = ( is_callable( array( $renewal_order, 'get_id' ) ) ) ? $renewal_order->get_id() : 0;
			} else {
				$subscription_order_id = ( ! empty( $subscription->order->id ) ) ? $subscription->order->id : 0;
				$renewal_order_id      = ( ! empty( $renewal_order->id ) ) ? $renewal_order->id : 0;
			}

			$order_items = $this->sc_subscriptions_renewal_order_items( $order_items, $subscription_order_id, $renewal_order_id, 0, 'child' );
			return $order_items;
		}

		/**
		 * New function to mark payment complete for renewal order (WCS 2.0+)
		 *
		 * @param WC_Order        $renewal_order Order object.
		 * @param WC_Subscription $subscription Subscription we're basing the order off of.
		 * @return WC_Order $renewal_order
		 */
		public function sc_wcs_renewal_complete_payment( $renewal_order = null, $subscription = null ) {
			$this->sc_renewal_complete_payment( $renewal_order );
			return $renewal_order;
		}

		/**
		 * Function to save Smart Coupon's contribution in discount
		 *
		 * @param int $order_id Order ID.
		 */
		public function smart_coupons_contribution( $order_id = 0 ) {

			if ( self::is_wcs_gte( '2.0.0' ) ) {
				$is_renewal_order = wcs_order_contains_renewal( $order_id );
			} else {
				$is_renewal_order = WC_Subscriptions_Renewal_Order::is_renewal( $order_id );
			}

			if ( ! $is_renewal_order ) {
				return;
			}

			$applied_coupons = ( is_object( WC()->cart ) && isset( WC()->cart->applied_coupons ) ) ? WC()->cart->applied_coupons : array();

			if ( ! empty( $applied_coupons ) ) {

				foreach ( $applied_coupons as $code ) {

					$smart_coupon = new WC_Coupon( $code );

					if ( $this->is_wc_gte_30() ) {
						$discount_type = $smart_coupon->get_discount_type();
					} else {
						$discount_type = ( ! empty( $smart_coupon->discount_type ) ) ? $smart_coupon->discount_type : '';
					}

					if ( 'smart_coupon' === $discount_type ) {

						$smart_coupon_credit_used = get_post_meta( $order_id, 'smart_coupons_contribution', true );

						$cart_smart_coupon_credit_used = WC()->cart->smart_coupon_credit_used;

						$update = false;

						if ( ! empty( $smart_coupon_credit_used ) ) {
							if ( ! empty( $cart_smart_coupon_credit_used ) ) {
								foreach ( $cart_smart_coupon_credit_used as $code => $amount ) {
									$smart_coupon_credit_used[ $code ] = $amount;
									$update                            = true;
								}
							}
						} else {
							$smart_coupon_credit_used = $cart_smart_coupon_credit_used;
							$update                   = true;
						}

						if ( $update ) {
							update_post_meta( $order_id, 'smart_coupons_contribution', $smart_coupon_credit_used );
						}
					}
				}
			}
		}

		/**
		 * Get first order of a subscription to which the supplied order belongs
		 *
		 * @param  integer $order_id The order id.
		 * @return integer
		 */
		public function get_first_order_id( $order_id = 0 ) {
			if ( self::is_wcs_gte( '2.0.0' ) && ! empty( $order_id ) ) {
				$subscriptions     = wcs_get_subscriptions_for_order( $order_id );
				$related_order_ids = ( is_object( $subscriptions ) && is_callable( array( $subscriptions, 'get_related_order_ids' ) ) ) ? $subscriptions->get_related_order_ids() : array();
				$related_order_ids = array_filter( $related_order_ids );
				if ( ! empty( $related_order_ids ) ) {
					sort( $related_order_ids, SORT_NUMERIC );
					reset( $related_order_ids );
					return current( $related_order_ids );
				}
			}
			return 0;
		}

		/**
		 * Set 'coupon_sent' as 'no' for renewal order to allow auto generation of coupons (if applicable)
		 *
		 * @param array  $order_items Associative array of order items.
		 * @param int    $original_order_id Post ID of the order being used to purchased the subscription being renewed.
		 * @param int    $renewal_order_id Post ID of the order created for renewing the subscription.
		 * @param int    $product_id ID of the product being renewed.
		 * @param string $new_order_role The role the renewal order is taking, one of 'parent' or 'child'.
		 * @return array $order_items
		 */
		public function sc_modify_renewal_order( $order_items = null, $original_order_id = 0, $renewal_order_id = 0, $product_id = 0, $new_order_role = null ) {

			if ( empty( $original_order_id ) && empty( $renewal_order_id ) ) {
				return $order_items;
			}

			if ( empty( $original_order_id ) ) {
				$original_order_id = $this->get_first_order_id( $renewal_order_id );
				if ( empty( $original_order_id ) ) {
					return $order_items;
				}
			}

			if ( self::is_wcs_gte( '2.0.0' ) ) {
				$is_subscription_order = wcs_order_contains_subscription( $original_order_id );
			} else {
				$is_subscription_order = WC_Subscriptions_Order::order_contains_subscription( $original_order_id );
			}
			if ( $is_subscription_order ) {
				$return = false;
			} else {
				$return = true;
			}
			if ( $return ) {
				return $order_items;
			}

			$is_recursive = false;
			if ( ! empty( $order_items ) ) {
				foreach ( $order_items as $order_item ) {
					$send_coupons_on_renewals = 'no';
					if ( ! empty( $order_item['variation_id'] ) ) {
						$coupon_titles = get_post_meta( $order_item['variation_id'], '_coupon_title', true );
						if ( empty( $coupon_titles ) ) {
							$send_coupons_on_renewals = get_post_meta( $order_item['product_id'], 'send_coupons_on_renewals', true );
						} else {
							$send_coupons_on_renewals = get_post_meta( $order_item['variation_id'], 'send_coupons_on_renewals', true );
						}
					} elseif ( ! empty( $order_item['product_id'] ) ) {
						$send_coupons_on_renewals = get_post_meta( $order_item['product_id'], 'send_coupons_on_renewals', true );
					} else {
						continue;
					}
					if ( 'yes' === $send_coupons_on_renewals ) {
						$is_recursive = true;
						break;  // if in any order item recursive is enabled, it will set coupon_sent as 'no'.
					}
				}
			}
			$stop_recursive_coupon_generation = get_option( 'stop_recursive_coupon_generation', 'no' );
			if ( ( empty( $stop_recursive_coupon_generation ) || 'no' === $stop_recursive_coupon_generation ) && $is_recursive ) {
				update_post_meta( $renewal_order_id, 'coupon_sent', 'no' );
			} else {
				update_post_meta( $renewal_order_id, 'coupon_sent', 'yes' );
			}

			return $order_items;
		}

		/**
		 * Get order item subtotal
		 *
		 * @param  WC_Order_Item_Product $order_item The order item.
		 * @return mixed
		 */
		public function sc_get_order_item_subtotal( $order_item = null ) {
			if ( is_object( $order_item ) && is_callable( array( $order_item, 'get_total' ) ) ) {
				return $order_item->get_total();
			}
			return floatval( 0 );
		}

		/**
		 * Function to modify order_items of renewal order
		 *
		 * @param array  $order_items Associative array of order items.
		 * @param int    $original_order_id Post ID of the order being used to purchased the subscription being renewed.
		 * @param int    $renewal_order_id Post ID of the order created for renewing the subscription.
		 * @param int    $product_id ID of the product being renewed.
		 * @param string $new_order_role The role the renewal order is taking, one of 'parent' or 'child'.
		 * @return array $order_items
		 */
		public function sc_subscriptions_renewal_order_items( $order_items = null, $original_order_id = 0, $renewal_order_id = 0, $product_id = 0, $new_order_role = null ) {

			if ( empty( $original_order_id ) && empty( $renewal_order_id ) ) {
				return $order_items;
			}

			if ( empty( $original_order_id ) ) {
				$original_order_id = $this->get_first_order_id( $renewal_order_id );
				if ( empty( $original_order_id ) ) {
					return $order_items;
				}
			}

			if ( self::is_wcs_gte( '2.0.0' ) ) {
				$is_subscription_order = wcs_order_contains_subscription( $original_order_id );
			} else {
				$is_subscription_order = WC_Subscriptions_Order::order_contains_subscription( $original_order_id );
			}
			if ( $is_subscription_order ) {
				$return = false;
			} else {
				$return = true;
			}
			if ( $return ) {
				return $order_items;
			}

			$pay_from_credit_of_original_order = get_option( 'pay_from_smart_coupon_of_original_order', 'yes' );

			if ( 'child' !== $new_order_role ) {
				return $order_items;
			}
			if ( empty( $renewal_order_id ) || empty( $original_order_id ) ) {
				return $order_items;
			}

			$original_order = wc_get_order( $original_order_id );
			$renewal_order  = wc_get_order( $renewal_order_id );

			$subscriptions = wcs_get_subscriptions_for_order( $original_order, array( 'order_type' => 'parent' ) );
			reset( $subscriptions );
			$subscription = current( $subscriptions );

			$coupon_used_in_original_order = $this->get_coupon_codes( $original_order );
			$coupon_used_in_renewal_order  = $this->get_coupon_codes( $renewal_order );

			if ( $this->is_wc_gte_30() ) {
				$renewal_order_billing_email = ( is_callable( array( $renewal_order, 'get_billing_email' ) ) ) ? $renewal_order->get_billing_email() : '';
			} else {
				$renewal_order_billing_email = ( ! empty( $renewal_order->billing_email ) ) ? $renewal_order->billing_email : '';
			}

			$all_coupons = array_merge( $coupon_used_in_original_order, $coupon_used_in_renewal_order );
			$all_coupons = array_unique( $all_coupons );

			if ( count( $all_coupons ) > 0 ) {
				$apply_before_tax = get_option( 'woocommerce_smart_coupon_apply_before_tax', 'no' );

				$smart_coupons_contribution = array();
				foreach ( $all_coupons as $coupon_code ) {
					$coupon = new WC_Coupon( $coupon_code );
					if ( $this->is_wc_gte_30() ) {
						$coupon_amount       = $coupon->get_amount();
						$discount_type       = $coupon->get_discount_type();
						$coupon_product_ids  = $coupon->get_product_ids();
						$coupon_category_ids = $coupon->get_product_categories();
					} else {
						$coupon_amount       = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
						$discount_type       = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
						$coupon_product_ids  = ( ! empty( $coupon->product_ids ) ) ? $coupon->product_ids : array();
						$coupon_category_ids = ( ! empty( $coupon->product_categories ) ) ? $coupon->product_categories : array();
					}

					if ( ! empty( $discount_type ) && 'smart_coupon' === $discount_type && ! empty( $coupon_amount ) ) {
						if ( 'yes' !== $pay_from_credit_of_original_order && in_array( $coupon_code, $coupon_used_in_original_order, true ) ) {
							continue;
						}
						if ( $this->is_wc_gte_30() && 'yes' === $apply_before_tax ) {
							$renewal_order_items = ( is_object( $subscription ) && is_callable( array( $subscription, 'get_items' ) ) ) ? $subscription->get_items( 'line_item' ) : array();
							if ( empty( $renewal_order_items ) ) {
								break;
							}
							$subtotal              = 0;
							$items_to_apply_credit = array();
							if ( count( $coupon_product_ids ) > 0 || count( $coupon_category_ids ) > 0 ) {
								foreach ( $renewal_order_items as $renewal_order_item_id => $renewal_order_item ) {
									$renewal_product_id   = ( is_object( $renewal_order_item ) && is_callable( array( $renewal_order_item, 'get_product_id' ) ) ) ? $renewal_order_item->get_product_id() : $renewal_order_item['product_id'];
									$renewal_variation_id = ( is_object( $renewal_order_item ) && is_callable( array( $renewal_order_item, 'get_variation_id' ) ) ) ? $renewal_order_item->get_variation_id() : $renewal_order_item['variation_id'];
									$product_category_ids = wc_get_product_cat_ids( $renewal_product_id );
									if ( count( $coupon_product_ids ) > 0 && count( $coupon_category_ids ) > 0 ) {
										if ( ( in_array( $renewal_product_id, $coupon_product_ids, true ) || in_array( $renewal_variation_id, $coupon_product_ids, true ) ) && count( array_intersect( $product_category_ids, $coupon_category_ids ) ) > 0 ) {
											$items_to_apply_credit[ $renewal_order_item_id ] = $renewal_order_item;
										}
									} else {
										if ( in_array( $renewal_product_id, $coupon_product_ids, true ) || in_array( $renewal_variation_id, $coupon_product_ids, true ) || count( array_intersect( $product_category_ids, $coupon_category_ids ) ) > 0 ) {
											$items_to_apply_credit[ $renewal_order_item_id ] = $renewal_order_item;
										}
									}
								}
							} else {
								$items_to_apply_credit = $renewal_order_items;
							}
							if ( empty( $items_to_apply_credit ) ) {
								continue;
							}
							$subtotal = array_sum( array_map( array( $this, 'sc_get_order_item_subtotal' ), $items_to_apply_credit ) );
							if ( $subtotal <= 0 ) {
								continue;
							}
							if ( ! class_exists( 'WC_SC_Apply_Before_Tax' ) ) {
								include_once '../class-wc-sc-apply-before-tax.php';
							}
							$sc_apply_before_tax = WC_SC_Apply_Before_Tax::get_instance();
							foreach ( $renewal_order_items as $renewal_order_item_id => $renewal_order_item ) {
								if ( array_key_exists( $renewal_order_item_id, $items_to_apply_credit ) ) {
									$discounting_amount = $renewal_order_item->get_total();
									$quantity           = $renewal_order_item->get_quantity();
									$discount           = $sc_apply_before_tax->sc_get_discounted_price( $discounting_amount, $quantity, $subtotal, $coupon_amount );
									$discount          *= $quantity;
									$renewal_order_items[ $renewal_order_item_id ]->set_total( $discounting_amount - $discount );
								}
							}
							$renewal_order_total = $subtotal;
							$order_items         = $renewal_order_items;
						} else {
							$renewal_order_total = $renewal_order->get_total();
						}
						$discount = min( $renewal_order_total, $coupon_amount );
						if ( $discount > 0 ) {
							$new_order_total = $renewal_order_total - $discount;
							update_post_meta( $renewal_order_id, '_order_total', $new_order_total );
							update_post_meta( $renewal_order_id, '_order_discount', $discount );
							if ( $new_order_total <= floatval( 0 ) ) {
								update_post_meta( $renewal_order_id, '_renewal_paid_by_smart_coupon', 'yes' );
							}
							if ( $this->is_wc_gte_30() ) {
								$item = new WC_Order_Item_Coupon();
								$item->set_props(
									array(
										'code'     => $coupon_code,
										'discount' => $discount,
										'order_id' => ( is_object( $renewal_order ) && is_callable( array( $renewal_order, 'get_id' ) ) ) ? $renewal_order->get_id() : 0,
									)
								);
								$item->save();
								$renewal_order->add_item( $item );
							} else {
								$renewal_order->add_coupon( $coupon_code, $discount );
							}
							$smart_coupons_contribution[ $coupon_code ] = $discount;
						}
					}
				}
				if ( ! empty( $smart_coupons_contribution ) ) {
					update_post_meta( $renewal_order_id, 'smart_coupons_contribution', $smart_coupons_contribution );
					$renewal_order->sc_total_credit_used = $smart_coupons_contribution;
				}
			}

			return $order_items;
		}

		/**
		 * Function to trigger complete payment for renewal if it's paid by Smart Coupons
		 *
		 * @param WC_Order $renewal_order Order created on subscription renewal.
		 * @param WC_Order $original_order Order being used to purchased the subscription.
		 * @param int      $product_id ID of the product being renewed.
		 * @param string   $new_order_role The role the renewal order is taking, one of 'parent' or 'child'.
		 */
		public function sc_renewal_complete_payment( $renewal_order = null, $original_order = null, $product_id = 0, $new_order_role = null ) {
			global $store_credit_label;

			if ( $this->is_wc_gte_30() ) {
				$renewal_order_id = ( is_object( $renewal_order ) && is_callable( array( $renewal_order, 'get_id' ) ) ) ? $renewal_order->get_id() : 0;
			} else {
				$renewal_order_id = ( ! empty( $renewal_order->id ) ) ? $renewal_order->id : 0;
			}

			if ( empty( $renewal_order_id ) ) {
				return;
			}
			if ( self::is_wcs_gte( '2.0.0' ) ) {
				$is_renewal_order = wcs_order_contains_renewal( $renewal_order_id );
			} else {
				$is_renewal_order = WC_Subscriptions_Renewal_Order::is_renewal( $renewal_order_id );
			}
			if ( $is_renewal_order ) {
				$return = false;
			} else {
				$return = true;
			}
			if ( $return ) {
				return;
			}

			$order_needs_processing = false;

			if ( count( $renewal_order->get_items() ) > 0 ) {
				foreach ( $renewal_order->get_items() as $item ) {
					$_product = ( is_object( $item ) && is_callable( array( $item, 'get_product' ) ) ) ? $item->get_product() : $renewal_order->get_product_from_item( $item );

					if ( $_product instanceof WC_Product ) {
						$virtual_downloadable_item = $_product->is_downloadable() && $_product->is_virtual();

						if ( apply_filters( 'woocommerce_order_item_needs_processing', ! $virtual_downloadable_item, $_product, $renewal_order_id ) ) {
							$order_needs_processing = true;
							break;
						}
					} else {
						$order_needs_processing = true;
						break;
					}
				}
			}

			$is_renewal_paid_by_smart_coupon = get_post_meta( $renewal_order_id, '_renewal_paid_by_smart_coupon', true );
			if ( ! empty( $is_renewal_paid_by_smart_coupon ) && 'yes' === $is_renewal_paid_by_smart_coupon ) {

				/* translators: %s: singular name for store credit */
				$order_paid_txt = ! empty( $store_credit_label['singular'] ) ? sprintf( __( 'Order paid by %s', 'woocommerce-smart-coupons' ), strtolower( $store_credit_label['singular'] ) ) : __( 'Order paid by store credit.', 'woocommerce-smart-coupons' );
				$renewal_order->update_status( apply_filters( 'woocommerce_payment_complete_order_status', $order_needs_processing ? 'processing' : 'completed', $renewal_order_id ), $order_paid_txt );
			}
		}

		/**
		 * Get valid_subscription_coupon array and add smart_coupon type
		 *
		 * @param bool      $is_validate_for_subscription Validate coupon or not.
		 * @param WC_Coupon $coupon Coupon object.
		 * @param bool      $valid Coupon Validity.
		 * @return bool $is_validate_for_subscription whether to validate coupon for subscription or not.
		 */
		public function smart_coupon_as_valid_subscription_coupon_type( $is_validate_for_subscription, $coupon, $valid ) {

			if ( $this->is_wc_gte_30() ) {
				$discount_type = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : 0;
			} else {
				$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
			}

			if ( ! empty( $discount_type ) && 'smart_coupon' === $discount_type ) {
				$is_validate_for_subscription = false;
			}

			return $is_validate_for_subscription;
		}

		/**
		 * Function to show gift certificate received details form based on product type
		 *
		 * @param  boolean $is_show Whether to show or not.
		 * @param  array   $args    Additional arguments.
		 * @return boolean          [description]
		 */
		public function is_show_gift_certificate_receiver_detail_form( $is_show = false, $args = array() ) {

			if ( wcs_cart_contains_renewal() ) {
				return false;
			}

			return $is_show;
		}

		/**
		 * Function to add subscription specific settings
		 *
		 * @param  array $settings Existing settings.
		 * @return array  $settings
		 */
		public function smart_coupons_settings( $settings = array() ) {
			global $store_credit_label;

			$singular = ( ! empty( $store_credit_label['singular'] ) ) ? $store_credit_label['singular'] : __( 'store credit', 'woocommerce-smart-coupons' );

			$wc_subscriptions_options = array(
				array(
					'name'          => __( 'Recurring subscriptions', 'woocommerce-smart-coupons' ),
					/* translators: %s: Label for store credit */
					'desc'          => sprintf( __( 'Use %s applied in first subscription order for subsequent renewals until credit reaches zero', 'woocommerce-smart-coupons' ), strtolower( $singular ) ),
					'id'            => 'pay_from_smart_coupon_of_original_order',
					'type'          => 'checkbox',
					'default'       => 'no',
					'checkboxgroup' => 'start',
					'autoload'      => false,
				),
				array(
					'desc'          => __( 'Renewal orders should not generate coupons even when they include a product that issues coupons', 'woocommerce-smart-coupons' ),
					'id'            => 'stop_recursive_coupon_generation',
					'type'          => 'checkbox',
					'default'       => 'no',
					'checkboxgroup' => 'end',
					'autoload'      => false,
				),
			);

			array_splice( $settings, ( count( $settings ) - 17 ), 0, $wc_subscriptions_options );

			return $settings;

		}

		/**
		 * Whether to bypass coupon removal from recurring item
		 *
		 * @param  boolean   $bypass           Bypass or not.
		 * @param  WC_Coupon $coupon           The coupon object.
		 * @param  string    $coupon_type      The discount type.
		 * @param  string    $calculation_type The calculation type of subscription.
		 * @return boolean   $bypass           Bypass or not
		 */
		public function bypass_removal_of_coupon_having_coupon_actions( $bypass = false, $coupon = null, $coupon_type = '', $calculation_type = '' ) {

			if ( $this->is_wc_gte_30() ) {
				$coupon_code = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_code' ) ) ) ? $coupon->get_code() : '';
			} else {
				$coupon_code = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
			}

			if ( ! class_exists( 'WC_SC_Coupon_Actions' ) ) {
				include_once 'class-wc-sc-coupon-actions.php';
			}

			$wc_sc_coupon_actions = WC_SC_Coupon_Actions::get_instance();

			$coupon_actions = $wc_sc_coupon_actions->get_coupon_actions( $coupon_code );

			if ( false === $bypass && ! empty( $coupon_actions ) ) {
				$coupon        = new WC_Coupon( $coupon_code );
				$discount_type = $coupon->get_discount_type();
				if ( 'smart_coupon' === $discount_type ) {
					return true;
				}
			}

			return $bypass;
		}

		/**
		 * Modify recurring cart
		 * Specifically, remove coupons having coupon actions from recurring carts
		 *
		 * @param  mixed $total The total.
		 * @return mixed $total
		 */
		public function modify_recurring_cart( $total ) {

			$recurring_carts = WC()->cart->recurring_carts;

			if ( ! empty( $recurring_carts ) ) {

				if ( ! class_exists( 'WC_SC_Coupon_Actions' ) ) {
					include_once 'class-wc-sc-coupon-actions.php';
				}

				$wc_sc_coupon_actions = WC_SC_Coupon_Actions::get_instance();

				foreach ( WC()->cart->recurring_carts as $cart_item_key => $cart ) {
					if ( ! empty( $cart->applied_coupons ) ) {
						foreach ( $cart->applied_coupons as $index => $coupon_code ) {
							$coupon_actions = $wc_sc_coupon_actions->get_coupon_actions( $coupon_code );
							if ( ! empty( $coupon_actions ) ) {
								$coupon        = new WC_Coupon( $coupon_code );
								$discount_type = $coupon->get_discount_type();
								if ( 'smart_coupon' === $discount_type ) {
									unset( WC()->cart->recurring_carts[ $cart_item_key ]->applied_coupons[ $index ] );
								}
							}
						}
					}
				}
			}

			return $total;

		}

		/**
		 * Hooks for WCS 2.3.0+
		 */
		public function hooks_for_wcs_230() {
			if ( $this->is_wcs_gte( '2.3.0' ) ) {
				add_filter( 'wc_smart_coupons_export_headers', array( $this, 'export_headers' ) );
				add_filter( 'smart_coupons_parser_postmeta_defaults', array( $this, 'postmeta_defaults' ) );
				add_filter( 'sc_generate_coupon_meta', array( $this, 'generate_coupon_meta' ), 10, 2 );
				add_action( 'wc_sc_new_coupon_generated', array( $this, 'copy_subscriptions_coupon_meta' ) );
			}
		}

		/**
		 * Add subscriptions coupon meta in export headers
		 *
		 * @param  array $headers Existing headers.
		 * @return array
		 */
		public function export_headers( $headers = array() ) {

			$action_headers = array(
				'_wcs_number_payments' => __( 'Active for x payments', 'woocommerce-smart-coupons' ),
			);

			return array_merge( $headers, $action_headers );

		}

		/**
		 * Post meta defaults for subscriptions coupon meta
		 *
		 * @param  array $defaults Existing postmeta defaults.
		 * @return array
		 */
		public function postmeta_defaults( $defaults = array() ) {

			$actions_defaults = array(
				'_wcs_number_payments' => '',
			);

			return array_merge( $defaults, $actions_defaults );
		}

		/**
		 * Add subscriptions coupons meta with value in coupon meta
		 *
		 * @param  array $data The row data.
		 * @param  array $post The POST values.
		 * @return array Modified data
		 */
		public function generate_coupon_meta( $data = array(), $post = array() ) {

			if ( isset( $post['wcs_number_payments'] ) ) {
				$data['_wcs_number_payments'] = trim( $post['wcs_number_payments'] );
			}

			return $data;
		}

		/**
		 * Function to copy subscription coupon meta in newly generated coupon
		 *
		 * @param  array $args The arguments.
		 */
		public function copy_subscriptions_coupon_meta( $args = array() ) {

			$new_coupon_id = ( ! empty( $args['new_coupon_id'] ) ) ? absint( $args['new_coupon_id'] ) : 0;
			$coupon        = ( ! empty( $args['ref_coupon'] ) ) ? $args['ref_coupon'] : false;

			if ( empty( $new_coupon_id ) || empty( $coupon ) ) {
				return;
			}

			$wcs_number_payments = '';
			if ( $this->is_wc_gte_30() ) {
				$wcs_number_payments = $coupon->get_meta( '_wcs_number_payments' );
			} else {
				$old_coupon_id       = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
				$wcs_number_payments = get_post_meta( $old_coupon_id, '_wcs_number_payments', true );
			}
			update_post_meta( $new_coupon_id, '_wcs_number_payments', $wcs_number_payments );

		}

		/**
		 * Get the reference key after which the setting will be inserted
		 *
		 * @param  string $after_key The key after which the setting to be inserted.
		 * @param  array  $args      Additional arguments.
		 * @return string
		 */
		public function endpoint_account_settings_after_key( $after_key = '', $args = array() ) {
			return 'woocommerce_myaccount_subscription_payment_method_endpoint';
		}

		/**
		 * Function to change coupon display name for Subscription coupons
		 *
		 * @param string    $coupon_type The type of the coupon.
		 * @param WC_Coupon $coupon Coupon object.
		 * @param array     $all_discount_types List of available discount types.
		 * @return string
		 */
		public function valid_display_type( $coupon_type, $coupon, $all_discount_types ) {
			if ( 'Recurring Product % Discount' === $coupon_type ) {
				$coupon_type = 'Recurring Product Discount';
			} elseif ( 'Sign Up Fee % Discount' === $coupon_type ) {
				$coupon_type = 'Sign Up Fee Discount';
			}
			return $coupon_type;
		}

		/**
		 * Function to change coupon amount display for Subscription coupons
		 *
		 * @param float     $coupon_amount Coupon amount.
		 * @param WC_Coupon $coupon Coupon object.
		 * @return float
		 */
		public function valid_display_amount( $coupon_amount, $coupon ) {
			$coupon_discount_type = $coupon->get_discount_type();
			if ( 'recurring_percent' === $coupon_discount_type || 'sign_up_fee_percent' === $coupon_discount_type ) {
				$coupon_amount = $coupon_amount . '%';
			} elseif ( 'recurring_fee' === $coupon_discount_type || 'sign_up_fee' === $coupon_discount_type ) {
				$coupon_amount = wc_price( $coupon_amount );
			}
			return $coupon_amount;
		}

		/**
		 * Coupon design thumbnail src set for subscription coupons
		 *
		 * @param array $src_set Existing src set.
		 * @param array $args Additional arguments.
		 * @return array
		 */
		public function coupon_design_thumbnail_src_set( $src_set = array(), $args = array() ) {
			$coupon = ( ! empty( $args['coupon_object'] ) ) ? $args['coupon_object'] : null;
			if ( $this->is_wc_gte_30() ) {
				$discount_type = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
			} else {
				$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
			}
			if ( ! empty( $discount_type ) ) {
				switch ( $discount_type ) {
					case 'sign_up_fee':
					case 'sign_up_fee_percent':
						$src_set = array(
							'subs-discount-voucher.svg',
						);
						break;

					case 'recurring_fee':
					case 'recurring_percent':
						$src_set = array(
							'subs-calendar-discount.svg',
						);
						break;
				}
			}
			return $src_set;
		}

		/**
		 * Get percent discount types fromm subscriptions
		 *
		 * @param array $discount_types Existing discount tyeps.
		 * @param array $args Additional arguments.
		 * @return array
		 */
		public function percent_discount_types( $discount_types = array(), $args = array() ) {
			$subs_percent_discount_types = array(
				'sign_up_fee_percent',
				'recurring_percent',
			);
			$discount_types              = array_merge( $discount_types, $subs_percent_discount_types );
			return $discount_types;
		}

		/**
		 * Function to check if a coupon can be auto applied or not
		 *
		 * @param boolean $is_auto_apply Is auto apply.
		 * @param array   $args Additional arguments.
		 * @return boolean
		 */
		public function is_auto_apply( $is_auto_apply = true, $args = array() ) {
			$cart_total                 = ( ! empty( $args['cart_total'] ) ) ? floatval( $args['cart_total'] ) : 0;
			$cart_contains_subscription = self::is_cart_contains_subscription();
			if ( false === $is_auto_apply && empty( $cart_total ) && true === $cart_contains_subscription ) {
				$is_auto_apply = true;
			}
			return $is_auto_apply;
		}

		/**
		 * Function to check if cart contains subscription
		 *
		 * @return bool whether cart contains subscription or not
		 */
		public static function is_cart_contains_subscription() {
			if ( class_exists( 'WC_Subscriptions_Cart' ) && WC_Subscriptions_Cart::cart_contains_subscription() ) {
				return true;
			}
			return false;
		}

		/**
		 * Function to check WooCommerce Subscription version
		 *
		 * @param string $version Subscription version.
		 * @return bool whether passed version is greater than or equal to current version of WooCommerce Subscription
		 */
		public static function is_wcs_gte( $version = null ) {
			if ( null === $version ) {
				return false;
			}
			if ( ! class_exists( 'WC_Subscriptions' ) || empty( WC_Subscriptions::$version ) ) {
				return false;
			}
			return version_compare( WC_Subscriptions::$version, $version, '>=' );
		}

	}

}

WCS_SC_Compatibility::get_instance();
