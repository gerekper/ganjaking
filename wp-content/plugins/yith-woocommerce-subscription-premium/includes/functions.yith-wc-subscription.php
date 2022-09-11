<?php
/**
 * Implements helper functions for YITH WooCommerce Subscription
 *
 * @since   1.0.0
 * @author  YITH
 * @package YITH WooCommerce Subscription
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! function_exists( 'ywsbs_get_subscription' ) ) {
	/**
	 * Return the subscription object
	 *
	 * @param int $subscription_id Subscription id.
	 *
	 * @return YWSBS_Subscription
	 * @since 1.0.0
	 */
	function ywsbs_get_subscription( $subscription_id ) {
		return new YWSBS_Subscription( $subscription_id );
	}
}

if ( ! function_exists( 'ywsbs_get_view_subscription_url' ) ) {
	/**
	 * Return the subscription detail page url
	 *
	 * @param int  $subscription_id Subscription id.
	 * @param bool $admin           Admin page.
	 *
	 * @return string
	 * @since 2.0.0
	 */
	function ywsbs_get_view_subscription_url( $subscription_id, $admin = false ) {
		if ( $admin ) {
			$view_subscription_url = admin_url( 'post.php?post=' . $subscription_id . '&action=edit' );
		} else {
			$view_subscription_url = wc_get_endpoint_url( 'view-subscription', $subscription_id, wc_get_page_permalink( 'myaccount' ) );
		}

		return apply_filters( 'ywsbs_get_subscription_url', $view_subscription_url, $subscription_id, $admin );
	}
}

if ( ! function_exists( 'ywsbs_get_change_status_link' ) ) {
	/**
	 * Return the a link for change the status of subscription
	 *
	 * @param int    $subscription_id Subscription id.
	 * @param string $status          Status.
	 *
	 * @return string
	 * @since 2.0.0
	 */
	function ywsbs_get_change_status_link( $subscription_id, $status ) {
		$args        = array(
			'subscription'  => $subscription_id,
			'change_status' => $status,
		);
		$action_link = add_query_arg( $args );
		$action_link = wp_nonce_url( $action_link, $subscription_id );

		return apply_filters( 'ywsbs_change_status_link', $action_link, $subscription_id, $status );
	}
}

if ( ! function_exists( 'ywsbs_get_overdue_time' ) ) {
	/**
	 * Return overdue time period
	 *
	 * @return integer|bool
	 * @since 2.0.0
	 */
	function ywsbs_get_overdue_time() {
		$overdue_time = false;
		$option       = (array) get_option( 'ywsbs_change_status_after_renew_order_creation' );
		if ( $option && isset( $option['status'] ) ) {
			if ( 'overdue' === $option['status'] && '' !== $option['length'] ) {
				$overdue_time = DAY_IN_SECONDS * intval( $option['length'] );
			}
		}

		return $overdue_time;
	}
}

if ( ! function_exists( 'ywsbs_get_suspension_time' ) ) {
	/**
	 * Return suspension time period
	 *
	 * @return integer|bool
	 * @since 2.0.0
	 */
	function ywsbs_get_suspension_time() {
		$suspension_time = false;
		$option          = (array) get_option( 'ywsbs_change_status_after_renew_order_creation' );
		if ( $option && isset( $option['status'] ) ) {
			if ( 'suspended' === $option['status'] && ! empty( $option['length'] ) ) {
				$suspension_time = DAY_IN_SECONDS * intval( $option['length'] );
			}
		}

		if ( ! $suspension_time ) {
			$option = (array) get_option( 'ywsbs_change_status_after_renew_order_creation_step_2' );
			if ( $option && isset( $option['status'] ) && 'suspended' === $option['status'] ) {
				$suspension_time = DAY_IN_SECONDS * intval( $option['length'] );
			}
		}

		return $suspension_time;
	}
}

if ( ! function_exists( 'yith_subscription_log' ) ) {
	/**
	 * Write LOG
	 *
	 * @param string $message Message to write inside the log.
	 * @param string $type    Type of message.
	 *
	 * @return void
	 */
	function yith_subscription_log( $message, $type = 'subscription_status' ) {

		if ( 'yes' === get_option( 'ywsbs_enable_log', false ) ) {
			$debug = wc_get_logger();
			$debug->add( 'ywsbs_' . $type, $message );
		}

	}
}

if ( ! function_exists( 'ywsbs_get_max_failed_attempts_by_gateway' ) ) {
	/**
	 * Return the max failed attempts for each compatible gateways.
	 *
	 * @param string $gateway_id The id of the gateway.
	 *
	 * @return int $max_failed_attempts
	 * @since 1.4.6
	 */
	function ywsbs_get_max_failed_attempts_by_gateway( $gateway_id ) {
		$list                = ywsbs_get_max_failed_attempts_list();
		$max_failed_attempts = isset( $list[ $gateway_id ] ) ? $list[ $gateway_id ] : 3;

		// APPLY_FILTER: ywsbs_max_failed_attempts_by_gateway : allow filtering the value of max attempts allowed by gateway.
		return apply_filters( 'ywsbs_max_failed_attempts_by_gateway', $max_failed_attempts, $gateway_id );
	}
}

if ( ! function_exists( 'ywsbs_get_max_failed_attempts_list' ) ) {
	/**
	 * Return the list of max failed attempts for each compatible gateways
	 *
	 * @return array
	 */
	function ywsbs_get_max_failed_attempts_list() {
		$arg = array(
			'paypal'      => 3,
			'yith-stripe' => 4,
		);

		// APPLY_FILTER: ywsbs_max_failed_attempts_list: filtering the max failed attempts list.
		return apply_filters( 'ywsbs_max_failed_attempts_list', $arg );
	}
}

if ( ! function_exists( 'ywsbs_support_scheduling' ) ) {
	/**
	 * Check if the gateway supports the scheduling.
	 *
	 * @param integer $gateway_id Gateway ID.
	 *
	 * @return bool
	 * @since 1.4.6
	 */
	function ywsbs_support_scheduling( $gateway_id ) {
		$support = false;
		if ( WC()->payment_gateways() ) {
			$gateways = WC()->payment_gateways()->get_available_payment_gateways();
			if ( isset( $gateways[ $gateway_id ] ) ) {
				$support = $gateways[ $gateway_id ]->supports( 'yith_subscriptions_scheduling' );
			}
		}

		return $support;
	}
}

if ( ! function_exists( 'ywsbs_get_num_of_days_between_attempts_by_gateway' ) ) {
	/**
	 * Return the number of days from a attempt and another one of a gateway
	 *
	 * @param string $gateway_id The id of the gateway.
	 *
	 * @return int
	 * @since 1.4.6
	 */
	function ywsbs_get_num_of_days_between_attempts_by_gateway( $gateway_id ) {
		$list                         = ywsbs_get_num_of_days_between_attempts();
		$num_of_days_between_attempts = isset( $list[ $gateway_id ] ) ? $list[ $gateway_id ] : 5;

		// APPLY_FILTER: ywsbs_num_of_days_between_attempts_by_gateway : allow filtering the value number of days from a attempt and another one of a gateway.
		return apply_filters( 'ywsbs_num_of_days_between_attempts_by_gateway', $num_of_days_between_attempts, $gateway_id );
	}
}

if ( ! function_exists( 'ywsbs_get_from_list' ) ) {
	/**
	 * Return the list of who can make actions on subscription
	 *
	 * @return array
	 * @since 1.0.0
	 */
	function ywsbs_get_from_list() {
		$options = array(
			'customer'      => esc_html__( 'customer', 'yith-woocommerce-subscription' ),
			'administrator' => esc_html__( 'administrator', 'yith-woocommerce-subscription' ),
			'gateway'       => esc_html__( 'gateway', 'yith-woocommerce-subscription' ),
			'rest-api'      => esc_html__( 'rest api', 'yith-woocommerce-subscription' ),
		);

		// APPLY_FILTER: ywsbs_from_list: the the list of who can make actions on subscription : it can be used by the gateways.
		return apply_filters( 'ywsbs_from_list', $options );
	}
}

if ( ! function_exists( 'ywsbs_get_status' ) ) {
	/**
	 * Return the list of status available.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	function ywsbs_get_status() {
		$options = array(
			'active'    => esc_html_x( 'active', 'Subscription status', 'yith-woocommerce-subscription' ),
			'paused'    => esc_html_x( 'paused', 'Subscription status', 'yith-woocommerce-subscription' ),
			'pending'   => esc_html_x( 'pending', 'Subscription status', 'yith-woocommerce-subscription' ),
			'overdue'   => esc_html_x( 'overdue', 'Subscription status', 'yith-woocommerce-subscription' ),
			'trial'     => esc_html_x( 'trial', 'Subscription status', 'yith-woocommerce-subscription' ),
			'cancelled' => esc_html_x( 'cancelled', 'Subscription status', 'yith-woocommerce-subscription' ),
			'expired'   => esc_html_x( 'expired', 'Subscription status', 'yith-woocommerce-subscription' ),
			'suspended' => esc_html_x( 'suspended', 'Subscription status', 'yith-woocommerce-subscription' ),
		);

		// APPLY_FILTER: ywsbs_status: the list of status of a subscription.
		return apply_filters( 'ywsbs_status', $options );
	}
}

if ( ! function_exists( 'ywsbs_get_status_label_counter' ) ) {
	/**
	 * Return the list of status for the label counter.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	function ywsbs_get_status_label_counter() {
		$options = array(
			'active'    => esc_html_x( 'Active', 'Subscription filter status', 'yith-woocommerce-subscription' ),
			'paused'    => esc_html_x( 'Paused', 'Subscription filter status', 'yith-woocommerce-subscription' ),
			'pending'   => esc_html_x( 'Pending', 'Subscription filter status', 'yith-woocommerce-subscription' ),
			'overdue'   => esc_html_x( 'Overdue', 'Subscription filter status', 'yith-woocommerce-subscription' ),
			'trial'     => esc_html_x( 'Trial', 'Subscription filter status', 'yith-woocommerce-subscription' ),
			'cancelled' => esc_html_x( 'Cancelled', 'Subscription filter status', 'yith-woocommerce-subscription' ),
			'expired'   => esc_html_x( 'Expired', 'Subscription filter status', 'yith-woocommerce-subscription' ),
			'suspended' => esc_html_x( 'Suspended', 'Subscription filter status', 'yith-woocommerce-subscription' ),
		);

		// APPLY_FILTER: ywsbs_status: the list of status of a subscription.
		return apply_filters( 'ywsbs_status_label_counter', $options );
	}
}


if ( ! function_exists( 'ywsbs_get_status_label' ) ) {
	/**
	 * Return the readable version ot status.
	 *
	 * @param string $status Status.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	function ywsbs_get_status_label( $status ) {
		$list = ywsbs_get_status();
		if ( isset( $list[ $status ] ) ) {
			$status = $list[ $status ];
		}

		return $status;
	}
}

if ( ! function_exists( 'ywsbs_get_status_colors' ) ) {
	/**
	 * Return the list of status available with colors.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	function ywsbs_get_status_colors() {

		$status_colors = array(
			'active'    => array(
				'color'            => '#ffffff',
				'background-color' => '#b2ac00',
			),
			'paused'    => array(
				'color'            => '#ffffff',
				'background-color' => '#34495e',
			),
			'pending'   => array(
				'color'            => '#ffffff',
				'background-color' => '#d38a0b',
			),
			'overdue'   => array(
				'color'            => '#ffffff',
				'background-color' => '#d35400',
			),
			'trial'     => array(
				'color'            => '#ffffff',
				'background-color' => '#8e44ad',
			),
			'cancelled' => array(
				'color'            => '#ffffff',
				'background-color' => '#c0392b',
			),
			'expired'   => array(
				'color'            => '#ffffff',
				'background-color' => '#bdc3c7',
			),
			'suspended' => array(
				'color'            => '#ffffff',
				'background-color' => '#e74c3c',
			),
		);

		foreach ( $status_colors as $status => $value ) {
			if ( get_option( 'ywsbs_' . $status . '_subscription_status_style' ) ) {
				$status_colors[ $status ] = get_option( 'ywsbs_' . $status . '_subscription_status_style' );
			}
		}

		// APPLY_FILTER: ywsbs_status_colors: the list of status of a subscription.
		return apply_filters( 'ywsbs_status_colors', $status_colors );
	}
}

if ( ! function_exists( 'ywsbs_get_days' ) ) {
	/**
	 * Return the days from timestamp
	 *
	 * @param int $timestamp Timestamp.
	 *
	 * @return int
	 * @since 1.0.0
	 */
	function ywsbs_get_days( $timestamp ) {
		$days = ceil( $timestamp / 86400 );

		return $days;
	}
}

if ( ! function_exists( 'ywsbs_add_date' ) ) {

	/**
	 * Add day, months or year to a date.
	 *
	 * @param int $given_date Date start.
	 * @param int $day        Day.
	 * @param int $mth        Month.
	 * @param int $yr         Year.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	function ywsbs_add_date( $given_date, $day = 0, $mth = 0, $yr = 0 ) {
		$new_date = $given_date;
		$new_date = strtotime( '+' . $day . ' days', $new_date );
		$new_date = strtotime( '+' . $mth . ' month', $new_date );
		$new_date = strtotime( '+' . $yr . ' year', $new_date );

		return $new_date;
	}
}


if ( ! function_exists( 'ywsbs_get_timestamp_from_option' ) ) {
	/**
	 * Add a date to a timestamp
	 *
	 * @param int    $time_from Start time.
	 * @param int    $qty       Quantity.
	 * @param string $time_opt  Time options.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	function ywsbs_get_timestamp_from_option( $time_from, $qty, $time_opt ) {

		$timestamp = 0;
		switch ( $time_opt ) {
			case 'days':
				$timestamp = ywsbs_add_date( $time_from, intval( $qty ) );
				break;
			case 'weeks':
				$timestamp = ywsbs_add_date( $time_from, intval( $qty ) * 7 );
				break;
			case 'months':
				$timestamp = ywsbs_add_date( $time_from, 0, intval( $qty ) );
				break;
			case 'years':
				$timestamp = ywsbs_add_date( $time_from, 0, 0, intval( $qty ) );
				break;
			default:
		}

		return $timestamp;
	}
}

if ( ! function_exists( 'ywsbs_reset_order_failed_attempts' ) ) {
	/**
	 * Reset the order failed attempts
	 *
	 * @param WC_Order $order WC_Order.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	function ywsbs_reset_order_failed_attempts( $order ) {
		if ( $order instanceof WC_Order ) {
			$order->update_meta_data( 'failed_attemps', '' );
			$order->update_meta_data( 'next_payment_attempt', '' );
			$order->save();
		}
	}
}

if ( ! function_exists( 'ywsbs_get_price_per_string' ) ) {
	/**
	 * Return the recurring period string.
	 *
	 * @param int    $price_per       Subscription recurring quantity.
	 * @param string $time_option     Subscription recurring.
	 * @param bool   $show_one_number Option to show or not the number 1 before the time period.
	 *
	 * @return int
	 * @since 1.0.0
	 */
	function ywsbs_get_price_per_string( $price_per, $time_option, $show_one_number = false ) {

		$price_html = ( ( 1 == $price_per && ! $show_one_number ) ? '' : $price_per ) . ' '; // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison

		switch ( $time_option ) {
			case 'days':
				$price_html .= esc_html( _n( 'day', 'days', $price_per, 'yith-woocommerce-subscription' ) );
				break;
			case 'weeks':
				$price_html .= esc_html( _n( 'week', 'weeks', $price_per, 'yith-woocommerce-subscription' ) );
				break;
			case 'months':
				$price_html .= esc_html( _n( 'month', 'months', $price_per, 'yith-woocommerce-subscription' ) );
				break;
			case 'years':
				$price_html .= esc_html( _n( 'year', 'years', $price_per, 'yith-woocommerce-subscription' ) );
				break;
			default:
		}

		return $price_html;
	}
}

if ( ! function_exists( 'ywsbs_check_valid_admin_page' ) ) {
	/**
	 * Return if the current pagenow is valid for a post_type, useful if you want add metabox, scripts inside the editor of a particular post type.
	 *
	 * @param string $post_type_name Post type.
	 *
	 * @return bool
	 */
	function ywsbs_check_valid_admin_page( $post_type_name ) {
		global $pagenow;

		$post = isset( $_REQUEST['post'] ) ? $_REQUEST['post'] : ( isset( $_REQUEST['post_ID'] ) ? $_REQUEST['post_ID'] : 0 ); // phpcs:ignore
		$post = get_post( $post );

		if ( ( $post && $post->post_type === $post_type_name ) || ( 'post-new.php' === $pagenow && isset( $_REQUEST['post_type'] ) && $post_type_name === $_REQUEST['post_type'] ) ) { // phpcs:ignore
			return true;
		}

		return false;
	}
}


if ( ! function_exists( 'ywsbs_delete_cancelled_pending_enabled' ) ) {
	/**
	 * Check if the tool for export and erase personal data are enabled.
	 *
	 * @return bool
	 * @since 2.0.0
	 */
	function ywsbs_delete_cancelled_pending_enabled() {
		$delete_pending_and_cancelled = ( 'yes' === get_option( 'ywsbs_delete_personal_info', 'no' ) );

		return apply_filters( 'ywsbs_delete_cancelled_pending_enabled', $delete_pending_and_cancelled );
	}
}

if ( ! function_exists( 'ywsbs_get_max_length_period' ) ) {
	/**
	 * Return the max length of period that can be accepted from paypal
	 *
	 * @return string
	 * @since    1.0.0
	 */
	function ywsbs_get_max_length_period() {

		$max_length = array(
			'days'   => 90,
			'weeks'  => 52,
			'months' => 24,
			'years'  => 5,
		);

		// APPLY_FILTER: ywsbs_get_max_length_period: the time limit options for PayPal can be filtered.
		return apply_filters( 'ywsbs_get_max_length_period', $max_length );
	}
}

if ( ! function_exists( 'ywsbs_get_time_options' ) ) {
	/**
	 * Return the list of time options to add in product editor panel.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	function ywsbs_get_time_options() {
		$options = array(
			'days'   => esc_html__( 'days', 'yith-woocommerce-subscription' ),
			'weeks'  => esc_html__( 'weeks', 'yith-woocommerce-subscription' ),
			'months' => esc_html__( 'months', 'yith-woocommerce-subscription' ),
			'years'  => esc_html__( 'years', 'yith-woocommerce-subscription' ),
		);

		// APPLY_FILTER: ywsbs_time_options : Filtering the time options in recurring period.
		return apply_filters( 'ywsbs_time_options', $options );
	}
}

if ( ! function_exists( 'ywsbs_get_time_options_sing_plur' ) ) {
	/**
	 * Return the label for singular or plurar period
	 *
	 * @param string $key     Time option.
	 * @param int    $counter Counter.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	function ywsbs_get_time_options_sing_plur( $key, $counter ) {
		$options = array(
			'days'   => _n( 'day', 'days', $counter, 'yith-woocommerce-subscription' ),
			'weeks'  => _n( 'week', 'weeks', $counter, 'yith-woocommerce-subscription' ),
			'months' => _n( 'month', 'months', $counter, 'yith-woocommerce-subscription' ),
			'years'  => _n( 'year', 'years', $counter, 'yith-woocommerce-subscription' ),
		);

		return $options[ $key ];
	}
}

if ( ! function_exists( 'ywsbs_validate_max_length' ) ) {
	/**
	 * Return the max length of period that can be accepted from PayPal.
	 *
	 * @param int    $max_length Max Length.
	 * @param string $time_opt   Time options.
	 *
	 * @return int
	 * @since  1.0.0
	 */
	function ywsbs_validate_max_length( $max_length, $time_opt ) {
		$max_lengths = ywsbs_get_max_length_period();
		$max_length  = ( $max_length > $max_lengths[ $time_opt ] ) ? $max_lengths[ $time_opt ] : $max_length;

		return $max_length;
	}
}

if ( ! function_exists( 'ywsbs_get_applied_coupons' ) ) {
	/**
	 * Return the applied coupons on cart.
	 *
	 * @param WC_Cart $cart Cart.
	 *
	 * @return array
	 */
	function ywsbs_get_applied_coupons( $cart ) {

		$coupons    = array();
		$coupons_id = $cart->get_applied_coupons();
		if ( $coupons_id ) {
			foreach ( $coupons_id as $coupon_code ) {
				$coupons[] = new WC_Coupon( $coupon_code );
			}
		}

		return $coupons;
	}
}

if ( ! function_exists( 'ywsbs_coupon_is_valid' ) ) {
	/**
	 * Check if a coupon is valid.
	 *
	 * @param WC_Coupon       $coupon  Coupon.
	 * @param WC_Cart         $object  Cart.
	 * @param bool|WC_Product $product Product.
	 *
	 * @return bool|WP_Error
	 * @throws Exception Return Error.
	 */
	function ywsbs_coupon_is_valid( $coupon, $object = array(), $product = false ) {

		$wc_discounts = new WC_Discounts( $object );
		$valid        = $wc_discounts->is_coupon_valid( $coupon );

		if ( $valid && $product ) {
			$valid = $coupon->is_valid_for_product( $product );
		}

		$valid = is_wp_error( $valid ) ? false : $valid;

		return $valid;
	}
}

if ( ! function_exists( 'ywsbs_get_activity_status' ) ) {
	/**
	 * Retrieve the status localized
	 *
	 * @param string $status Status.
	 *
	 * @return mixed
	 */
	function ywsbs_get_activity_status( $status ) {
		$list_status = array(
			'success' => esc_html__( 'success', 'yith-woocommerce-subscription' ),
			'info'    => esc_html__( 'info', 'yith-woocommerce-subscription' ),
		);

		return isset( $list_status[ $status ] ) ? $list_status[ $status ] : $status;
	}
}

if ( ! function_exists( 'ywsbs_enable_subscriptions_multiple' ) ) {
	/**
	 * Check if there are payment methods that support the multiple product subscription on cart.
	 *
	 * @return bool
	 * @since 1.4.6
	 */
	function ywsbs_enable_subscriptions_multiple() {

		$multiple_subscription_enabled = ( 'yes' === get_option( 'ywsbs_enable_subscriptions_multiple', 'no' ) );

		if ( $multiple_subscription_enabled && WC()->payment_gateways() ) {
			$gateways                      = ywsbs_get_gateways_multiple_subscriptions();
			$multiple_subscription_enabled = count( $gateways ) > 0;
		}

		return apply_filters( 'ywsbs_force_multiple_subscriptions', $multiple_subscription_enabled );
	}
}

if ( ! function_exists( 'ywsbs_get_gateways_multiple_subscriptions' ) ) {
	/**
	 * Get the payment methods that support the multiple product subscription on cart.
	 *
	 * @return array
	 * @since 1.4.6
	 */
	function ywsbs_get_gateways_multiple_subscriptions() {

		$supported_gateways    = array();
		$manual_renews_allowed = ( 'yes' === get_option( 'ywsbs_enable_manual_renews', 'yes' ) );
		if ( WC()->payment_gateways() ) {

			$gateways = WC()->payment_gateways()->payment_gateways;

			foreach ( $gateways as $gateway ) {

				if ( $manual_renews_allowed && ! $gateway->supports( 'yith_subscriptions' ) ) {
					$supported_gateways[] = $gateway;
				}

				if ( $gateway->supports( 'yith_subscriptions_multiple' ) ) {
					$supported_gateways[] = $gateway;
				}
			}
		}

		return apply_filters( 'ywsbs_get_gateways_multiple_subscriptions', $supported_gateways );
	}
}

if ( ! function_exists( 'ywsbs_check_renew_order_before_pay' ) ) {
	/**
	 * Check if the renew order is can be paid or not.
	 *
	 * Check if the number of max attempts is reached and if the status of renew order is correct.
	 *
	 * @param WC_Order $order The renew order object.
	 *
	 * @return boolean
	 * @since 1.4.6
	 */
	function ywsbs_check_renew_order_before_pay( $order ) {
		$is_valid = true;

		$failed_payments = (int) $order->get_meta( 'failed_attemps' );
		$gateway_id      = $order->get_payment_method();
		$max_attempts    = ywsbs_get_max_failed_attempts_by_gateway( $gateway_id );

		if ( ! ( 0 === $failed_payments || ( $failed_payments > 0 && $failed_payments < $max_attempts ) ) ) {
			yith_subscription_log( 'Cannot pay the order because failed_payments ( ' . $failed_payments . ') is < of max attempts ( ' . $max_attempts . ' )', 'subscription_payment' );
			$is_valid = false;
		}

		// check if the subscription can be paid.
		$subscriptions = $order->get_meta( 'subscriptions' );
		if ( $subscriptions ) {
			$subscription = ywsbs_get_subscription( $subscriptions[0] );
			if ( in_array( $subscription->get( 'status' ), array( 'cancelled', 'paused', 'expired' ), true ) ) {
				yith_subscription_log( 'Cannot pay the order because the subscription ' . $subscription->get_id() . ' is ' . $subscription->get( 'status' ), 'subscription_payment' );
				$is_valid = false;
			}

			if ( $is_valid ) {
				$status   = YWSBS_Subscription_Order()->get_renew_order_status( $subscription );
				$order_id = $order->get_id();

				if ( ! $order->has_status( $status ) ) {
					yith_subscription_log( sprintf( 'New payment request denied because the order #%1$d is on %2$s status', $order_id, $order->get_status() ), 'subscription_payment' );
					$is_valid = false;
				}
			}
		}

		// APPLY_FILTER: ywsbs_check_renew_order_before_pay : allow filtering the check the renew order before the payment.
		return apply_filters( 'ywsbs_check_renew_order_before_pay', $is_valid, $order );
	}
}


if ( ! function_exists( 'ywsbs_get_payment_gateway_by_subscription' ) ) {
	/**
	 * Get the gateway registered for the $subscription
	 *
	 * @param YWSBS_Subscription $subscription Subscription.
	 *
	 * @return WC_Payment_Gateway|bool
	 * @since 1.4.5
	 */
	function ywsbs_get_payment_gateway_by_subscription( $subscription ) {

		$payment_method = $subscription->get_payment_method();

		if ( empty( $payment_method ) ) {
			return false;
		}

		$payment_gateways = array();

		if ( WC()->payment_gateways() ) {
			foreach ( WC()->payment_gateways()->payment_gateways as $gateway ) {
				if ( 'yes' === $gateway->enabled ) {
					$payment_gateways[ $gateway->id ] = $gateway;
				}
			}
		}

		return isset( $payment_gateways[ $payment_method ] ) ? $payment_gateways[ $payment_method ] : false;
	}
}

if ( ! function_exists( 'ywsbs_get_resubscribe_subscription_url' ) ) {
	/**
	 * Get the url to resubscribe a subscription
	 *
	 * @param YWSBS_Subscription $subscription Subscription.
	 *
	 * @return string
	 * @since 2.0.0
	 */
	function ywsbs_get_resubscribe_subscription_url( $subscription ) {

		$resubscribe_url = add_query_arg(
			array(
				'ywsbs_resubscribe' => $subscription->get_id(),
				'_nonce'            => wp_create_nonce( 'ywsbs-resubscribe-' . $subscription->get_id() ),
			),
			wc_get_checkout_url()
		);

		return apply_filters( 'ywsbs_resubscribe_subscription_url', $resubscribe_url, $subscription );
	}
}


if ( ! function_exists( 'ywsbs_get_switch_to_subscription_url' ) ) {
	/**
	 * Get the url to switch a subscription to a new plan
	 *
	 * @param YWSBS_Subscription $subscription Subscription.
	 * @param int                $new_plan     The new plan to switch.
	 *
	 * @return string
	 * @since 2.0.0
	 */
	function ywsbs_get_switch_to_subscription_url( $subscription, $new_plan ) {

		$switch_url = add_query_arg(
			array(
				'subscription' => $subscription->get_id(),
				'plan'         => $new_plan,
				'_nonce'       => wp_create_nonce( 'ywsbs-switch-' . $subscription->get_id() ),
			),
			wc_get_checkout_url()
		);

		return apply_filters( 'ywsbs_switch_to_subscription_url', $switch_url, $subscription, $new_plan );
	}
}


if ( ! function_exists( 'ywsbs_get_order_fields_to_edit' ) ) {
	/**
	 * Return the list of fields that can be edited on a subscription.
	 *
	 * @param string $type Type of fields.
	 *
	 * @return array|void
	 */
	function ywsbs_get_order_fields_to_edit( $type ) {
		$fields = array();

		if ( 'billing' === $type ) {
			// APPLY_FILTER: ywsbs_admin_billing_fields : filtering the admin billing fields.
			$fields = apply_filters(
				'ywsbs_admin_billing_fields',
				array(
					'first_name' => array(
						'label' => esc_html__( 'First name', 'yith-woocommerce-subscription' ),
						'show'  => false,
					),
					'last_name'  => array(
						'label' => esc_html__( 'Last name', 'yith-woocommerce-subscription' ),
						'show'  => false,
					),
					'company'    => array(
						'label' => esc_html__( 'Company', 'yith-woocommerce-subscription' ),
						'show'  => false,
					),
					'address_1'  => array(
						'label' => esc_html__( 'Address line 1', 'yith-woocommerce-subscription' ),
						'show'  => false,
					),
					'address_2'  => array(
						'label' => esc_html__( 'Address line 2', 'yith-woocommerce-subscription' ),
						'show'  => false,
					),
					'city'       => array(
						'label' => esc_html__( 'City', 'yith-woocommerce-subscription' ),
						'show'  => false,
					),
					'postcode'   => array(
						'label' => esc_html__( 'Postcode / ZIP', 'yith-woocommerce-subscription' ),
						'show'  => false,
					),
					'country'    => array(
						'label'   => esc_html__( 'Country', 'yith-woocommerce-subscription' ),
						'show'    => false,
						'class'   => 'js_field-country select short',
						'type'    => 'select',
						'options' => array( '' => esc_html__( 'Select a country&hellip;', 'yith-woocommerce-subscription' ) ) + WC()->countries->get_allowed_countries(),
					),
					'state'      => array(
						'label' => esc_html__( 'State / County', 'yith-woocommerce-subscription' ),
						'class' => 'js_field-state select short',
						'show'  => false,
					),
					'email'      => array(
						'label' => esc_html__( 'Email address', 'yith-woocommerce-subscription' ),
					),
					'phone'      => array(
						'label' => esc_html__( 'Phone', 'yith-woocommerce-subscription' ),
					),
				)
			);
		} elseif ( 'shipping' === $type ) {
			// APPLY_FILTER: ywsbs_admin_shipping_fields : filtering the admin shipping fields.
			$fields = apply_filters(
				'ywsbs_admin_shipping_fields',
				array(
					'first_name' => array(
						'label' => esc_html__( 'First name', 'yith-woocommerce-subscription' ),
						'show'  => false,
					),
					'last_name'  => array(
						'label' => esc_html__( 'Last name', 'yith-woocommerce-subscription' ),
						'show'  => false,
					),
					'company'    => array(
						'label' => esc_html__( 'Company', 'yith-woocommerce-subscription' ),
						'show'  => false,
					),
					'address_1'  => array(
						'label' => esc_html__( 'Address line 1', 'yith-woocommerce-subscription' ),
						'show'  => false,
					),
					'address_2'  => array(
						'label' => esc_html__( 'Address line 2', 'yith-woocommerce-subscription' ),
						'show'  => false,
					),
					'city'       => array(
						'label' => esc_html__( 'City', 'yith-woocommerce-subscription' ),
						'show'  => false,
					),
					'postcode'   => array(
						'label' => esc_html__( 'Postcode / ZIP', 'yith-woocommerce-subscription' ),
						'show'  => false,
					),
					'country'    => array(
						'label'   => esc_html__( 'Country', 'yith-woocommerce-subscription' ),
						'show'    => false,
						'type'    => 'select',
						'class'   => 'js_field-country select short',
						'options' => array( '' => esc_html__( 'Select a country&hellip;', 'yith-woocommerce-subscription' ) ) + WC()->countries->get_shipping_countries(),
					),
					'state'      => array(
						'label' => esc_html__( 'State / County', 'yith-woocommerce-subscription' ),
						'class' => 'js_field-state select short',
						'show'  => false,
					),
				)
			);
		}

		return $fields;
	}
}

if ( ! function_exists( 'ywsbs_subscription_order_type' ) ) {
	/**
	 * Return the relation between the order and the subscription
	 *
	 * @param YWSBS_Subscription $subscription Subscription Object.
	 * @param WC_Order           $order        Order.
	 *
	 * @return string
	 */
	function ywsbs_subscription_order_type( $subscription, $order ) {

		$type                = '';
		$is_a_renew          = $order->get_meta( 'is_a_renew' );
		$order_subscriptions = $order->get_meta( 'subscriptions' );

		if ( (int) $subscription->get( 'order_id' ) === $order->get_id() ) {
			$type = esc_html__( 'Parent Order', 'yith-woocommerce-subscription' );
		}

		if ( $is_a_renew && in_array( $subscription->get_id(), $order_subscriptions ) ) { // phpcs:ignore
			$type = esc_html__( 'Renew Order', 'yith-woocommerce-subscription' );
		}

		if ( ( $order->get_meta( '_child_subscription' ) === $subscription->get_id() ) || ( $order->get_meta( '_parent_subscription' ) == $subscription->get_id() ) ) { // phpcs:ignore
			$type = esc_html__( 'Resubscribed Order', 'yith-woocommerce-subscription' );
		}

		return $type;
	}
}


if ( ! function_exists( 'ywsbs_get_num_of_days_between_attempts' ) ) {

	/**
	 * Return the list of max failed attempts for each compatible gateways
	 *
	 * @return array
	 */
	function ywsbs_get_num_of_days_between_attempts() {
		$arg = array(
			'paypal'      => 5,
			'yith-stripe' => 5,
		);

		// APPLY_FILTER: ywsbs_get_num_of_days_between_attempts: the number of days from a attempt and another one : it can be filtered by gateways.
		return apply_filters( 'ywsbs_get_num_of_days_between_attempts', $arg );
	}
}


if ( ! function_exists( 'ywsbs_register_failed_payment' ) ) {
	/**
	 * Register failed payment for a renew.
	 *
	 * Usually it is used by the gateway when a payment is failed.
	 *
	 * @param WC_Order $order         The renew order failed.
	 * @param string   $error_message The error message returned by gateway.
	 *
	 * @return mixed|void
	 * @since 1.4.6
	 */
	function ywsbs_register_failed_payment( $order, $error_message ) {
		$is_a_renew           = $order->get_meta( 'is_a_renew' );
		$order_id             = $order->get_id();
		$subscriptions        = $order->get_meta( 'subscriptions' );
		$payment_method       = $order->get_payment_method();
		$payment_method_title = $order->get_payment_method_title();

		// translators: $1 gateway name $2 error message.
		$order->add_order_note( sprintf( __( '%1$s Failed payment: %2$s', 'yith-woocommerce-subscription' ), $payment_method_title, $error_message ) );

		if ( 'yes' === $is_a_renew ) {
			$failed_attempts      = $order->get_meta( 'failed_attemps' );
			$failed_attempts      = empty( $failed_attempts ) ? 0 : $failed_attempts;
			$max_attempts         = ywsbs_get_max_failed_attempts_by_gateway( $payment_method );
			$gap_between_attempts = ywsbs_get_num_of_days_between_attempts_by_gateway( $payment_method );

			if ( $failed_attempts + 1 < $max_attempts ) {
				// update the post meta of the renew order.
				$next_payment_attempt_up = current_time( 'timestamp' ) + $gap_between_attempts * DAY_IN_SECONDS; //phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
				$order->update_meta_data( 'failed_attemps', $failed_attempts + 1 );
				$order->update_meta_data( 'next_payment_attempt', $next_payment_attempt_up );
				$order->save();

				// translators: $1: gateway name id $2: failed attempts number $3: order id.
				yith_subscription_log( sprintf( '%1$s - total failed attempts (%2$s) for order #%3$s', $payment_method_title, ( $failed_attempts + 1 ), $order_id ) );

				foreach ( $subscriptions as $subscription_id ) {
					$subscription = ywsbs_get_subscription( $subscription_id );
					$subscription->register_failed_attempt( $failed_attempts + 1, false, $next_payment_attempt_up, $order );

				}
			} else {
				// Max Failed Attempts reached.
				foreach ( $subscriptions as $subscription_id ) {
					$subscription = ywsbs_get_subscription( $subscription_id );
					$subscription->cancel( false );

					$parent = wc_get_order( $subscription->get_order_id() );
					if ( $parent instanceof WC_Order ) {
						$parent->add_order_note( __( 'Subscription cancelled for max failed attempts.', 'yith-woocommerce-subscription' ) );
					}

					$order->add_order_note( __( 'Subscription cancelled for max failed attempts.', 'yith-woocommerce-subscription' ) );
					// translators: $1: subscription id $2: order id.
					yith_subscription_log( sprintf( 'Subscription cancelled for max failed attempts. Subscription #%1$s. Order #%2$s', $subscription_id, $order_id ) );
				}
			}
		}
	}
}

if ( ! function_exists( 'ywsbs_scheduled_actions_enabled' ) ) {
	/**
	 * Check if the scheduled action are enabled.
	 *
	 * @return mixed|void
	 */
	function ywsbs_scheduled_actions_enabled() {
		$enabled = false;

		if ( version_compare( WC_VERSION, '4.0.0', '>=' ) ) {
			$enabled = true;
		}

		return apply_filters( 'ywsbs_scheduled_action_enabled', $enabled );
	}
}

if ( ! function_exists( 'ywsbs_get_price_time_option_paypal' ) ) {
	/**
	 * Return the symbol used by PayPal Standard Payment for time options.
	 *
	 * @param string $time_option Time option.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	function ywsbs_get_price_time_option_paypal( $time_option ) {
		$options = array(
			'days'   => 'D',
			'weeks'  => 'W',
			'months' => 'M',
			'years'  => 'Y',
		);

		return isset( $options[ $time_option ] ) ? $options[ $time_option ] : '';
	}
}


if ( ! function_exists( 'ywsbs_get_unused_subscription_days' ) ) {
	/**
	 * Return the number of days that the customer does not used.
	 *
	 * @param YWSBS_Subscription $subscription Subscription.
	 *
	 * @return int
	 * @since 2.0.0
	 */
	function ywsbs_get_unused_subscription_days( $subscription ) {
		$now             = time();
		$valid_date      = $subscription->get_confirmed_valid_date();
		$difference_days = 0;
		if ( $valid_date > $now ) {
			$difference_days = ceil( ( $valid_date - $now ) / DAY_IN_SECONDS );
			$period_in_days  = $subscription->get_total_period_in_days();
			$difference_days = ( $difference_days > $period_in_days ) ? $period_in_days : $difference_days;
		}

		return $difference_days;
	}
}


if ( ! function_exists( 'ywsbs_is_an_order_with_subscription' ) ) {
	/**
	 * Checks if in the order there's a subscription product
	 * returns false if is not an order with subscription or
	 * returns the type of subscription order ( parent|renew )
	 *
	 * @param WC_Order $order Order.
	 *
	 * @return string|bool
	 * @since 1.2.0
	 */
	function ywsbs_is_an_order_with_subscription( $order ) {

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		$order_subscription_type = false;
		$subscriptions           = $order->get_meta( 'subscriptions' );
		$is_renew                = $order->get_meta( 'is_renew' );

		if ( $subscriptions ) {
			$order_subscription_type = empty( $is_renew ) ? 'parent' : 'renew';
		}

		return $order_subscription_type;

	}
}

if ( ! function_exists( 'ywsbs_get_subscription_by_order' ) ) {
	/**
	 * Checks if in the order there's a subscription product
	 * returns false if is not an order with subscription or
	 * returns the type of subscription order ( parent|renew )
	 *
	 * @param int|WC_Order $order         Order.
	 * @param int|string   $order_item_id Order item.
	 *
	 * @return YWSBS_Subscription|bool
	 * @since 1.2.0
	 */
	function ywsbs_get_subscription_by_order( $order, $order_item_id = '' ) {

		$subscription = false;

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		$order_subscriptions = $order->get_meta( 'subscriptions' );
		if ( $order_subscriptions ) {
			if ( empty( $order_item_id ) ) {
				$subscription_id = array_shift( $order_subscriptions );
				$subscription    = ywsbs_get_subscription( $subscription_id );
			} else {
				foreach ( $order_subscriptions as $subscription_id ) {
					$subscription_to_check = ywsbs_get_subscription( $subscription_id );
					if ( $subscription_to_check->get_order_item_id() === (int) $order_item_id ) {
						$subscription = $subscription_to_check;
						break;
					}
				}
			}
		}

		return $subscription;
	}
}


/**
 * Week days
 */
if ( ! function_exists( 'ywsbs_get_week_day_string' ) ) {
	/**
	 * Get the week day string by string.
	 *
	 * @param int|string $index Index 0=Sunday - 6=Saturday.
	 *
	 * @return string
	 * @since 2.1
	 */
	function ywsbs_get_week_day_string( $index ) {
		$week_days = array( 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday' );

		return $week_days[ $index ];
	}
}

if ( ! function_exists( 'ywsbs_get_trial_period' ) ) {
	/**
	 * Get the period o
	 *
	 * @param WC_Product $product Product.
	 *
	 * @return string
	 * @since 2.3
	 */
	function ywsbs_get_trial_period( $product ) {
		$trial_per         = ywsbs_get_product_trial( $product );
		$trial_time_option = $product->get_meta( '_ywsbs_trial_time_option' );

		$period = ywsbs_get_date_time_period( $trial_per, $trial_time_option );

		return $period;
	}
}

if ( ! function_exists( 'ywsbs_get_date_time_period' ) ) {
	/**
	 * Get the period o
	 *
	 * @param int    $time_per    Time lenght.
	 * @param string $time_option Time option.
	 *
	 * @return string
	 * @since 2.3
	 */
	function ywsbs_get_date_time_period( $time_per, $time_option ) {

		if ( empty( $time_per ) || $time_per <= 0 ) {
			return false;
		}

		if ( 'weeks' === $time_option ) {
			$time_per    = (int) $time_per * 7;
			$time_option = 'days';
		}

		$time_per_format = array(
			'days'   => 'D',
			'months' => 'M',
			'years'  => 'Y',
		);

		$period = 'P' . $time_per . $time_per_format[ $time_option ];

		return $period;
	}
}

if ( ! function_exists( 'ywsbs_get_period_options' ) ) {
	/**
	 * Get the period
	 *
	 * @param string $period Can be weeks, months.
	 *
	 * @return string
	 * @since 2.2
	 */
	function ywsbs_get_period_options( $period ) {
		global $wp_locale;
		$result = array();

		switch ( $period ) {
			case 'day_weeks':
				for ( $day_index = 0; $day_index <= 6; $day_index ++ ) {
					$result[ $day_index ] = $wp_locale->get_weekday( $day_index );
				}
				break;
			case 'day_months':
				for ( $day_index = 1; $day_index <= 28; $day_index ++ ) {
					$result[ $day_index ] = $day_index;
				}
				$result['end'] = 'end';
				break;
			case 'months':
				for ( $month_index = 1; $month_index <= 12; $month_index ++ ) {
					$result[ $month_index ] = $wp_locale->get_month( $month_index );
				}
				break;
		}

		return $result;
	}
}


if ( ! function_exists( 'ywsbs_check_categories' ) ) {
	/**
	 * Check if the terms of the product are inside the list.
	 *
	 * @param WP_Product $product    Product to check.
	 * @param array      $categories Category list.
	 *
	 * @return bool.
	 * @since 2.2
	 */
	function ywsbs_check_categories( $product, $categories ) {
		$product_id                 = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
		$categories_of_item         = wc_get_product_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );
		$intersect_product_category = array_intersect( $categories_of_item, $categories );

		return ! empty( $intersect_product_category );
	}
}


if ( ! function_exists( 'ywsbs_get_formatted_date' ) ) {
	/**
	 * Return the date formatted.
	 *
	 * @param string $date       Date to format.
	 * @param string $empty_date String returned for empty date.
	 *
	 * @return string
	 * @since 2.2
	 */
	function ywsbs_get_formatted_date( $date, $empty_date = '' ) {

		if ( $date < 0 || empty( $date ) || '0000-00-00 00:00:00' === $date ) {
			return $empty_date;
		} else {
			$timestamp = strtotime( $date );

			return esc_html( date_i18n( wc_date_format(), $timestamp ) ); // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
		}
	}
}


if ( ! function_exists( 'ywsbs_get_subscription_number_by_subscription_id' ) ) {
	/**
	 * Return the subscription number from subscription id
	 *
	 * @param int $subscription_id Subscription id.
	 *
	 * @return string
	 * @since 2.2
	 */
	function ywsbs_get_subscription_number_by_subscription_id( $subscription_id ) {
		$subscription = ywsbs_get_subscription( $subscription_id );

		return ( $subscription ) ? $subscription->get_number() : '#' . $subscription_id;
	}
}

if ( ! function_exists( 'yith_ywsbs_is_wc_admin_enabled' ) ) {
	/**
	 * Is WC Admin plugin enabled?
	 *
	 * @return bool
	 */
	function yith_ywsbs_is_wc_admin_enabled() {
		return class_exists( 'Automattic\WooCommerce\Admin\Loader' ) && yith_ywsbs_check_wc_admin_min_version();
	}
}

if ( ! function_exists( 'yith_ywsbs_check_wc_admin_min_version' ) ) {
	/**
	 * Check min version for WC Admin
	 *
	 * @return bool
	 */
	function yith_ywsbs_check_wc_admin_min_version() {
		return defined( 'WC_ADMIN_VERSION_NUMBER' ) && version_compare( WC_ADMIN_VERSION_NUMBER, '0.24.0', '>=' );
	}
}

if ( ! function_exists( 'ywsbs_calculate_daily_amount' ) ) {
	/**
	 * Calculate the daily amount.
	 *
	 * @param int    $price_is_per      Num of days, months, weeks.
	 * @param string $price_time_option Days, months, weeks, years.
	 * @param float  $price             Total amount.
	 *
	 * @return float
	 */
	function ywsbs_calculate_daily_amount( $price_is_per, $price_time_option, $price ) {

		$divider      = ( $price_is_per * ywsbs_get_period_in_seconds( $price_time_option ) ) / DAY_IN_SECONDS;
		$daily_amount = $divider ? $price / (float) $divider : 0;

		return $daily_amount;
	}
}

if ( ! function_exists( 'ywsbs_get_period_in_seconds' ) ) {
	/**
	 * Return the period in seconds
	 *
	 * @param string $period Days, months, weeks, years.
	 *
	 * @return int
	 *
	 * @since 2.4.2
	 */
	function ywsbs_get_period_in_seconds( $period ) {

		$days_in_seconds = array(
			'days'   => DAY_IN_SECONDS,
			'weeks'  => WEEK_IN_SECONDS,
			'months' => MONTH_IN_SECONDS,
			'years'  => YEAR_IN_SECONDS,
		);

		return $days_in_seconds[ $period ];
	}
}


if ( ! function_exists( 'ywsbs_get_gmt_from_local_timestamp' ) ) {
	/**
	 * Returns the gmt date from local timestamp
	 *
	 * @param int    $timestamp Timestamp.
	 * @param string $format    Format of the date.
	 *
	 * @return string
	 */
	function ywsbs_get_gmt_from_local_timestamp( $timestamp, $format = 'Y-m-d H:i:s' ) {
		$local_time = gmdate( $format, $timestamp );
		$datetime   = date_create( $local_time, new DateTimeZone( wp_timezone_string() ) );

		return wp_date( $format, $datetime->getTimestamp(), new DateTimeZone( '+00:00' ) );
	}
}

if ( ! function_exists( 'wcs_get_subscriptions_for_order' ) ) {
	/**
	 * Short-circuit function for fixing an issue with WC Payments
	 *
	 * @return array
	 * @since  2.11.0
	 */
	function wcs_get_subscriptions_for_order() {
		return array();
	}
}
