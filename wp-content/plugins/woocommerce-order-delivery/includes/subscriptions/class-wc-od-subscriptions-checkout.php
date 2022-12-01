<?php
/**
 * Class to handle the subscriptions during checkout
 *
 * @package WC_OD/Subscriptions
 * @since   1.5.5
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Subscriptions_Checkout' ) ) {
	/**
	 * WC_OD_Subscriptions_Checkout class.
	 */
	class WC_OD_Subscriptions_Checkout {

		/**
		 * Constructor.
		 *
		 * @since 1.5.5
		 */
		public function __construct() {
			add_filter( 'wc_od_first_shipping_date_args', array( $this, 'first_shipping_date_args' ), 10, 2 );
			add_filter( 'wc_od_max_delivery_days', array( $this, 'max_delivery_days' ) );
			add_filter( 'woocommerce_checkout_get_value', array( $this, 'checkout_get_value' ), 20, 2 );
			add_filter( 'wc_od_enable_fees_for_cart', array( $this, 'enable_fees_for_cart' ), 10, 2 );
			add_filter( 'woocommerce_subscriptions_is_recurring_fee', array( $this, 'is_recurring_fee' ), 10, 3 );

			add_action( 'woocommerce_adjust_order_fees_for_setup_cart_for_subscription_renewal', array( $this, 'adjust_order_fees' ) );
			add_action( 'woocommerce_checkout_subscription_created', array( $this, 'subscription_created' ) );
			add_action( 'woocommerce_checkout_create_order', array( $this, 'create_order' ), 110 );
		}

		/**
		 * Filters the arguments used to calculate the first shipping date during checkout.
		 *
		 * @since 1.5.5
		 *
		 * @param array  $args    The arguments.
		 * @param string $context The context.
		 * @return array
		 */
		public function first_shipping_date_args( $args, $context ) {
			if ( 0 === strpos( $context, 'checkout' ) ) {
				$renewal = wcs_cart_contains_renewal();

				if ( $renewal ) {
					$subscription = wc_od_get_subscription( $renewal['subscription_renewal']['subscription_id'] );
					$next_payment = $subscription->get_time( 'next_payment', 'site' );

					if ( current_time( 'timestamp' ) < $next_payment ) {
						$args['start_date'] = $next_payment;
					}
				}
			}

			return $args;
		}

		/**
		 * Restricts the maximum delivery days value to the minimum subscription period.
		 *
		 * @since 1.5.5
		 *
		 * @param int $max_delivery_days The max delivery days value.
		 * @return int The maximum delivery days.
		 */
		public function max_delivery_days( $max_delivery_days ) {
			$renewal = wcs_cart_contains_renewal();

			if ( $renewal ) {
				$max_delivery_date = wc_od_get_subscription_max_delivery_date( $renewal['subscription_renewal']['subscription_id'] );
				$max_delivery_days = ( ( $max_delivery_date - wc_od_get_local_date() ) / DAY_IN_SECONDS );
			} elseif ( wc_string_to_bool( WC_OD()->settings()->get_setting( 'subscriptions_limit_to_billing_interval' ) ) ) {
				$period = wc_od_get_min_subscription_period_in_cart();

				if ( $period ) {
					$time = time();
					$diff = ( strtotime( "+ {$period['interval']} {$period['period']}", $time ) - $time );
					$days = abs( ( $diff / DAY_IN_SECONDS ) );

					if ( $days < $max_delivery_days ) {
						$max_delivery_days = $days;
					}
				}
			}

			return max( 0, $max_delivery_days );
		}

		/**
		 * Gets the value for a checkout field.
		 *
		 * Populates the value from the subscription data.
		 *
		 * @since 2.0.0
		 *
		 * @param mixed  $value The field value.
		 * @param string $input The input key.
		 * @return mixed
		 */
		public function checkout_get_value( $value, $input ) {
			// Not a delivery field, or it has already been initialized.
			if ( 0 !== strpos( $input, 'delivery_' ) || ! is_null( $value ) ) {
				return $value;
			}

			$cart_item = wcs_cart_contains_renewal();

			// Only for subscription renewals.
			if ( ! $cart_item || ! isset( $cart_item['subscription_renewal'] ) ) {
				return null;
			}

			$renewal = $cart_item['subscription_renewal'];

			if ( ! empty( $renewal['subscription_renewal_early'] ) ) {
				$object = wcs_get_subscription( $renewal['subscription_id'] );
			} else {
				$object = wc_get_order( $renewal['renewal_order_id'] );
			}

			if ( ! $object ) {
				return null;
			}

			$value = $object->get_meta( "_{$input}" );

			// Find the ID from the time frame data.
			if ( 'delivery_time_frame' === $input && is_array( $value ) && ! empty( $value ) ) {
				$delivery_date = WC()->checkout()->get_value( 'delivery_date' );
				$time_frames   = WC_OD()->checkout()->get_time_frames_for_date( $delivery_date );

				$search_params = array_intersect_key( $value, array_flip( array( 'time_from', 'time_to' ) ) );
				$time_frame_id = wc_od_search_time_frame( $time_frames, $search_params );
				$value         = ( false === $time_frame_id ? '' : str_replace( 'new:', 'time_frame:', $time_frame_id ) );
			}

			return $value;
		}

		/**
		 * Filters the registered delivery fields in the checkout form.
		 *
		 * @since 1.5.5
		 * @deprecated 2.0.0
		 *
		 * @param array $fields The checkout fields.
		 * @return array
		 */
		public function checkout_fields( $fields ) {
			wc_deprecated_function( __FUNCTION__, '2.0.0' );

			return $fields;
		}

		/**
		 * Whether to enable the delivery fees for the specified cart.
		 *
		 * @since 2.0.0
		 *
		 * @param bool    $enable_fees Whether to enable the fees.
		 * @param WC_Cart $cart        Cart object.
		 */
		public function enable_fees_for_cart( $enable_fees, $cart ) {
			// Recurring carts don't have delivery fees.
			return ( $enable_fees && ! property_exists( $cart, 'recurring_cart_key' ) );
		}

		/**
		 * Filters if it's a recurring fee.
		 *
		 * @since 2.0.0
		 *
		 * @param bool     $recurring Whether it's a recurring fee.
		 * @param stdClass $fee       Fee object.
		 * @param WC_Cart  $cart      Cart object.
		 * @return bool
		 */
		public function is_recurring_fee( $recurring, $fee, $cart ) {
			// Enable the delivery fees on manual renewals.
			if ( ! $recurring && ! property_exists( $cart, 'recurring_cart_key' ) && 0 === strpos( $fee->id, 'delivery_' ) ) {
				$recurring = true;
			}

			return $recurring;
		}

		/**
		 * Adjusts the Order fees before setting up the cart renewal.
		 *
		 * @since 2.0.0
		 *
		 * @param WC_Order $order Order object.
		 */
		public function adjust_order_fees( $order ) {
			$fees = $order->get_fees();

			// Remove the delivery fees.
			foreach ( $fees as $fee ) {
				if ( wc_string_to_bool( $fee->get_meta( '_delivery_fee' ) ) ) {
					$order->remove_item( $fee->get_id() );
				}
			}
		}

		/**
		 * Processes new subscriptions.
		 *
		 * @since 1.5.5
		 *
		 * @param WC_Subscription $subscription The subscription instance.
		 */
		public function subscription_created( $subscription ) {
			wc_od_setup_subscription_delivery_preferences( $subscription );
			wc_od_update_subscription_delivery_date( $subscription );
			wc_od_update_subscription_delivery_time_frame( $subscription );
		}

		/**
		 * Processes an order before save.
		 *
		 * @since 2.3.0
		 */
		public function create_order() {
			$cart_item = wcs_cart_contains_renewal();

			if ( ! $cart_item ) {
				return;
			}

			$checkout     = WC()->checkout();
			$subscription = wcs_get_subscription( $cart_item['subscription_renewal']['subscription_id'] );

			// Updates the subscription delivery details from the order renewal.
			$subscription->update_meta_data( '_delivery_date', $checkout->get_value( 'delivery_date' ) );
			$subscription->update_meta_data( '_delivery_time_frame', $checkout->get_value( 'delivery_time_frame' ) );
			$subscription->save_meta_data();
		}
	}
}

return new WC_OD_Subscriptions_Checkout();
