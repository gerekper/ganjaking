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
			add_filter( 'woocommerce_checkout_fields', array( $this, 'checkout_fields' ), 20 );

			add_action( 'woocommerce_checkout_subscription_created', array( $this, 'subscription_created' ) );
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
		 * Filters the registered delivery fields in the checkout form.
		 *
		 * @since 1.5.5
		 *
		 * @param array $fields The checkout fields.
		 * @return array
		 */
		public function checkout_fields( $fields ) {
			$renewal = wcs_cart_contains_renewal();

			// Only for subscription renewals with delivery fields.
			if ( ! $renewal || empty( $fields['delivery']['delivery_date'] ) ) {
				return $fields;
			}

			$is_early_renewal = ( ! empty( $renewal['subscription_renewal']['subscription_renewal_early'] ) );
			$order_id         = intval( $is_early_renewal ? $renewal['subscription_renewal']['subscription_id'] : $renewal['subscription_renewal']['renewal_order_id'] );

			$checkout      = WC()->checkout();
			$delivery_date = $checkout->get_value( 'delivery_date' );

			// The delivery date field can be empty. So, we only set the default value the first time.
			if ( is_null( $delivery_date ) ) {
				$delivery_date = wc_od_get_order_meta( $order_id, '_delivery_date' );

				if ( $delivery_date ) {
					$fields['delivery']['delivery_date']['default'] = wc_od_localize_date( $delivery_date );
				}
			} elseif ( ! empty( $fields['delivery']['delivery_time_frame'] ) && is_null( $checkout->get_value( 'delivery_time_frame' ) ) ) {
				// Set the default value for the delivery time frame field after updating the delivery date.
				$time_frame = wc_od_get_order_meta( $order_id, '_delivery_time_frame' );

				// Find the ID from the time frame data.
				if ( ! $is_early_renewal && $time_frame ) {
					$time_frames = wc_od_get_time_frames_for_date(
						$delivery_date,
						array(
							'shipping_method' => WC_OD()->checkout()->get_shipping_method(),
						),
						'checkout'
					);

					$search_params = array_intersect_key( $time_frame, array_flip( array( 'time_from', 'time_to' ) ) );
					$time_frame_id = wc_od_search_time_frame( $time_frames, $search_params );
					$time_frame    = ( false === $time_frame_id ? '' : 'time_frame:' . $time_frame_id );
				}

				// Set only if it's in the options list.
				if ( $time_frame && isset( $fields['delivery']['delivery_time_frame']['options'][ $time_frame ] ) ) {
					$fields['delivery']['delivery_time_frame']['default'] = $time_frame;
				}
			}

			return $fields;
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
	}
}

return new WC_OD_Subscriptions_Checkout();
