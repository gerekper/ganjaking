<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements helper functions for YITH WooCommerce Subscription
 *
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

global $yith_ywsbs_db_version;
$yith_ywsbs_db_version = '1.0.0';

if ( ! function_exists( 'yith_ywsbs_db_install' ) ) {

	/**
	 * Install the table yith_ywsbs_activities_log.
	 *
	 * @return void
	 * @since 1.0.0
	 */

	function yith_ywsbs_db_install() {
		global $wpdb;
		global $yith_ywsbs_db_version;

		$installed_ver = get_option( 'yith_ywsbs_db_version' );

		if ( $installed_ver != $yith_ywsbs_db_version ) {

			$table_name = $wpdb->prefix . 'yith_ywsbs_activities_log';

			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`activity` varchar(255) NOT NULL,
		`status` varchar(255) NOT NULL,
		`subscription` int(11) NOT NULL,
		`order` int(11) NOT NULL,
		`description` varchar(255) NOT NULL,
		`timestamp_date` datetime NOT NULL,
		PRIMARY KEY (id)
		) $charset_collate;";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );

			add_option( 'yith_ywsbs_db_version', $yith_ywsbs_db_version );
		}
	}
}

if ( ! function_exists( 'yith_ywsbs_update_db_check' ) ) {

	/**
	 * Check if the function yith_ywsbs_db_install must be installed or updated.
	 *
	 * @return void
	 * @since 1.0.0
	 */

	function yith_ywsbs_update_db_check() {
		global $yith_ywsbs_db_version;

		if ( get_site_option( 'yith_ywsbs_db_version' ) != $yith_ywsbs_db_version ) {
			yith_ywsbs_db_install();
		}
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
			'days'   => __( 'days', 'yith-woocommerce-subscription' ),
			'weeks'  => __( 'weeks', 'yith-woocommerce-subscription' ),
			'months' => __( 'months', 'yith-woocommerce-subscription' ),
			'years'  => __( 'years', 'yith-woocommerce-subscription' ),
		);

		// APPLY_FILTER: ywsbs_time_options : Filtering the time options in recurring period
		return apply_filters( 'ywsbs_time_options', $options );
	}
}

if ( ! function_exists( 'ywsbs_get_price_time_option_paypal' ) ) {

	/**
	 * Return the symbol used by PayPal Standard Payment for time options.
	 *
	 * @param  string $time_option
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

if ( ! function_exists( 'ywsbs_add_date' ) ) {

	/**
	 * Add day, months or year to a date.
	 *
	 * @param int $given_date
	 * @param int $day
	 * @param int $mth
	 * @param int $yr
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

if ( ! function_exists( 'ywsbs_time' ) ) {

	/**
	 * Return the timestamp of the day + 1 second
	 *
	 * @return int
	 * @since 1.0.0
	 */

	function ywsbs_time() {
		$timestamp = mktime( 0, 0, 1 );

		return $timestamp;
	}
}

if ( ! function_exists( 'ywsbs_get_days' ) ) {

	/**
	 * Return the days from timestamp
	 *
	 * @param $timestamp int
	 *
	 * @return int
	 * @since 1.0.0
	 */

	function ywsbs_get_days( $timestamp ) {
		$days = ceil( $timestamp / 86400 );

		return $days;
	}
}

if ( ! function_exists( 'ywsbs_date_to_time' ) ) {

	/**
	 * Return the date to time.
	 *
	 * @param $date_string
	 *
	 * @return int|string
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywsbs_date_to_time( $date_string ) {

		if ( 0 == $date_string ) {
			return 0;
		}

		$date_obj = new DateTime( $date_string, new DateTimeZone( 'UTC' ) );

		return $date_obj->format( 'U' );
	}
}

if ( ! function_exists( 'ywsbs_get_timestamp_from_option' ) ) {

	/**
	 * Add a date to a timestamp
	 *
	 * @param int    $time_from
	 * @param int    $qty
	 * @param string $time_opt
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

if ( ! function_exists( 'ywsbs_get_max_length_period' ) ) {

	/**
	 * Return the max length of period that can be accepted from paypal
	 *
	 * @return string
	 * @internal param int $time_from
	 * @internal param int $qty
	 * @since    1.0.0
	 */

	function ywsbs_get_max_length_period() {

		$max_length = array(
			'days'   => 90,
			'weeks'  => 52,
			'months' => 24,
			'years'  => 5,
		);

		// APPLY_FILTER: ywsbs_get_max_length_period: the time limit options for PayPal can be filtered
		return apply_filters( 'ywsbs_get_max_length_period', $max_length );

	}
}

if ( ! function_exists( 'ywsbs_validate_max_length' ) ) {

	/**
	 * Return the max length of period that can be accepted from PayPal.
	 *
	 * @param int    $max_length
	 * @param string $time_opt
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

if ( ! function_exists( 'ywsbs_get_status' ) ) {

	/**
	 * Return the list of status available.
	 *
	 * @return array
	 * @since 1.0.0
	 */

	function ywsbs_get_status() {
		$options = array(
			'active'    => __( 'active', 'yith-woocommerce-subscription' ),
			'paused'    => __( 'paused', 'yith-woocommerce-subscription' ),
			'pending'   => __( 'pending', 'yith-woocommerce-subscription' ),
			'overdue'   => __( 'overdue', 'yith-woocommerce-subscription' ),
			'trial'     => __( 'trial', 'yith-woocommerce-subscription' ),
			'cancelled' => __( 'cancelled', 'yith-woocommerce-subscription' ),
			'expired'   => __( 'expired', 'yith-woocommerce-subscription' ),
			'suspended' => __( 'suspended', 'yith-woocommerce-subscription' ),
		);

		// APPLY_FILTER: ywsbs_status: the list of status of a subscription
		return apply_filters( 'ywsbs_status', $options );
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
			'customer'      => __( 'customer', 'yith-woocommerce-subscription' ),
			'administrator' => __( 'administrator', 'yith-woocommerce-subscription' ),
			'gateway'       => __( 'gateway', 'yith-woocommerce-subscription' ),
		);

		// APPLY_FILTER: ywsbs_from_list: the the list of who can make actions on subscription : it can be used by the gateways
		return apply_filters( 'ywsbs_from_list', $options );
	}
}

if ( ! function_exists( 'ywsbs_get_subscription' ) ) {

	/**
	 * Return the subscription object
	 *
	 * @param int $subscription_id
	 *
	 * @return YWSBS_Subscription
	 * @since 1.0.0
	 */

	function ywsbs_get_subscription( $subscription_id ) {
		return new YWSBS_Subscription( $subscription_id );
	}
}

if ( ! function_exists( 'ywsbs_get_product_signup' ) ) {

	/**
	 * Return the sign up fee of a product.
	 *
	 * @param WC_Product $product
	 * @param string     $context If this string is different from 'view' the sign up won't be filtered.
	 *
	 * @return float
	 * @since 1.4.6
	 */

	function ywsbs_get_product_signup( $product, $context = 'view' ) {

		if ( ! $product ) {
			return false;
		} elseif ( ! $product instanceof WC_Product ) {
			$product = wc_get_product( $product->ID );
		}

		$sign_up = $product ? $product->get_meta( '_ywsbs_fee' ) : false;
		if ( 'view' === $context ) {
			// APPLY_FILTER: ywsbs_product_signup: the sig up of a product can be be filtered : the product object is passed as argument
			$sign_up = apply_filters( 'ywsbs_product_signup', $sign_up, $product );
		}

		return $sign_up;
	}
}

if ( ! function_exists( 'ywsbs_get_attribute_name' ) ) {

	/**
	 * Return the name of a variation product with the price
	 *
	 * @param array $variation
	 *
	 * @return string
	 * @since 1.0.0
	 */

	function ywsbs_get_attribute_name( $variation ) {
		$var          = wc_get_product( $variation['variation_id'] );
		$label_string = '';
		if ( ! empty( $variation['attributes'] ) ) {
			foreach ( $variation['attributes'] as $key => $value ) {
				$label_string .= $value . ' ';
			}

			$label_string .= $var->get_price_html();
		}

		return $label_string;
	}
}

if ( ! function_exists( 'yith_ywsbs_get_product_meta' ) ) {

	/**
	 * Return the product meta of a variation product.
	 *
	 * @param   YWSBS_Subscription $subscription
	 * @param   array              $attributes
	 * @param   bool               $echo
	 *
	 * @return string
	 * @since 1.0.0
	 */

	function yith_ywsbs_get_product_meta( $subscription, $attributes = array(), $echo = true ) {

		$item_data = array();

		if ( ! empty( $subscription->variation_id ) ) {
			$variation = wc_get_product( $subscription->variation_id );

			if ( empty( $attributes ) ) {
				$attributes = $variation->get_attributes();
			}

			foreach ( $attributes as $name => $value ) {
				if ( '' === $value ) {
					continue;
				}

				$taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $name ) ) );

				// If this is a term slug, get the term's nice name
				if ( taxonomy_exists( $taxonomy ) ) {
					$term = get_term_by( 'slug', $value, $taxonomy );
					if ( ! is_wp_error( $term ) && $term && $term->name ) {
						$value = $term->name;
					}
					$label = wc_attribute_label( $taxonomy );

				} else {
					if ( strpos( $name, 'attribute_' ) !== false ) {
						$custom_att = str_replace( 'attribute_', '', $name );
						if ( $custom_att != '' ) {
							$label = wc_attribute_label( $custom_att );
						} else {
							$label = apply_filters( 'woocommerce_attribute_label', wc_attribute_label( $name ), $name );
						}
					}
				}

				$item_data[] = array(
					'key'   => $label,
					'value' => $value,
				);
			}
		}

		// APPLY_FILTER: ywsbs_item_data: the meta data of a variation product can be filtered : YWSBS_Subscription is passed as argument
		$item_data = apply_filters( 'ywsbs_item_data', $item_data, $subscription );
		$out       = '';
		// Output flat or in list format
		if ( sizeof( $item_data ) > 0 ) {
			foreach ( $item_data as $data ) {
				if ( $echo ) {
					echo esc_html( $data['key'] ) . ': ' . wp_kses_post( $data['value'] ) . "\n";
				} else {
					$out .= ' - ' . esc_html( $data['key'] ) . ': ' . wp_kses_post( $data['value'] ) . ' ';
				}
			}
		}

		return $out;

	}
}

if ( ! function_exists( 'ywsbs_get_price_per_string' ) ) {

	/**
	 * Return the recurring period string.
	 *
	 * @param int    $price_per
	 * @param string $time_option
	 * @param bool   $show_one_number
	 * @return int
	 * @since 1.0.0
	 */

	function ywsbs_get_price_per_string( $price_per, $time_option, $show_one_number = false ) {
		$price_html = ( ( $price_per == 1 && ! $show_one_number ) ? '' : $price_per ) . ' ';

		switch ( $time_option ) {
			case 'days':
				$price_html .= _n( 'day', 'days', $price_per, 'yith-woocommerce-subscription' );
				break;
			case 'weeks':
				$price_html .= _n( 'week', 'weeks', $price_per, 'yith-woocommerce-subscription' );
				break;
			case 'months':
				$price_html .= _n( 'month', 'months', $price_per, 'yith-woocommerce-subscription' );
				break;
			case 'years':
				$price_html .= _n( 'year', 'years', $price_per, 'yith-woocommerce-subscription' );
				break;
			default:
		}

		return $price_html;
	}
}

if ( ! function_exists( 'ywsbs_get_gateways_list' ) ) {

	/**
	 * Return the list of gateways compatible with plugin
	 *
	 * @return array
	 */

	function ywsbs_get_gateways_list() {
		return apply_filters( 'ywsbs_get_gateways_list', array( 'paypal', 'yith-stripe' ) );
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

		// APPLY_FILTER: ywsbs_max_failed_attempts_list: filtering the max failed attempts list
		return apply_filters( 'ywsbs_max_failed_attempts_list', $arg );
	}
}

if ( ! function_exists( 'ywsbs_get_max_failed_attempts_by_gateway' ) ) {

	/**
	 * Return the max failed attempts for each compatible gateways.
	 *
	 * @param  string $gateway_id The id of the gateway
	 *
	 * @return int $max_failed_attempts
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 * @since 1.4.6
	 */

	function ywsbs_get_max_failed_attempts_by_gateway( $gateway_id ) {
		$list                = ywsbs_get_max_failed_attempts_list();
		$max_failed_attempts = isset( $list[ $gateway_id ] ) ? $list[ $gateway_id ] : 3;

		// APPLY_FILTER: ywsbs_max_failed_attempts_by_gateway : allow filtering the value of max attempts allowed by gateway.
		return apply_filters( 'ywsbs_max_failed_attempts_by_gateway', $max_failed_attempts, $gateway_id );
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

		// APPLY_FILTER: ywsbs_get_num_of_days_between_attempts: the number of days from a attempt and another one : it can be filtered by gateways
		return apply_filters( 'ywsbs_get_num_of_days_between_attempts', $arg );
	}
}

if ( ! function_exists( 'ywsbs_get_num_of_days_between_attempts_by_gateway' ) ) {

	/**
	 * Return the number of days from a attempt and another one of a gateway
	 *
	 * @param  string $gateway_id The id of the gateway
	 *
	 * @return int $num_of_days_between_attempts
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 * @since 1.4.6
	 */

	function ywsbs_get_num_of_days_between_attempts_by_gateway( $gateway_id ) {
		$list                         = ywsbs_get_num_of_days_between_attempts();
		$num_of_days_between_attempts = isset( $list[ $gateway_id ] ) ? $list[ $gateway_id ] : 5;

		// APPLY_FILTER: ywsbs_num_of_days_between_attempts_by_gateway : allow filtering the value number of days from a attempt and another one of a gateway
		return apply_filters( 'ywsbs_num_of_days_between_attempts_by_gateway', $num_of_days_between_attempts, $gateway_id );
	}
}

if ( ! function_exists( 'ywsbs_subscription_status_product_check' ) ) {

	/**
	 * Return the status of the subscription of a product for a specific customer and order.
	 *
	 * @param WC_Product $product
	 * @param int        $user_id
	 * @param int        $order_id
	 *
	 * @return string | bool
	 */

	function ywsbs_subscription_status_product_check( $product, $user_id, $order_id ) {

		$status = false;

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		$subscriptions_users = YWSBS_Subscription_Helper()->get_subscriptions_by_user( $user_id );

		if ( ! empty( $subscriptions_users ) ) {

			foreach ( $subscriptions_users as $subscription_post ) {
				$subscription = ywsbs_get_subscription( $subscription_post->ID );

				if ( $subscription->get( 'order_id' ) == $order_id && $subscription->get( 'product_id' ) == $product->get_id() ) {
					$status = $subscription->get( 'status' );
					break;
				}
			}
		}
		// APPLY_FILTER: ywsbs_subscription_status_product_check : the status of the subscription of a product for a specific customer and order : $product, $user_id, $order_id are the arguments
		return apply_filters( 'ywsbs_subscription_status_product_check', $status, $product, $user_id, $order_id );
	}
}

if ( ! function_exists( 'ywsbs_coupon_is_valid' ) ) {

	/**
	 * Check if a coupon is valid.
	 *
	 * @param $coupon WC_Coupon
	 * @param array            $object
	 *
	 * @return bool|WP_Error
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 * @throws Exception
	 */
	function ywsbs_coupon_is_valid( $coupon, $object = array() ) {
		if ( version_compare( WC()->version, '3.2.0', '>=' ) ) {
			$wc_discounts = new WC_Discounts( $object );
			$valid        = $wc_discounts->is_coupon_valid( $coupon );
			$valid        = is_wp_error( $valid ) ? false : $valid;
		} else {
			$valid = $coupon->is_valid();
		}

		return $valid;
	}
}

if ( ! function_exists( 'ywsbs_get_applied_coupons' ) ) {

	/**
	 * Return the applied coupons on cart.
	 *
	 * @param $cart WC_Cart
	 *
	 * @return array
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywsbs_get_applied_coupons( $cart ) {
		if ( version_compare( WC()->version, '3.2.0', '>=' ) ) {
			$coupons    = array();
			$coupons_id = $cart->get_applied_coupons();
			if ( $coupons_id ) {
				foreach ( $coupons_id as $coupon_code ) {
					$coupons[] = new WC_Coupon( $coupon_code );
				}
			}
		} else {
			$coupons = $cart->coupon;
		}

		return $coupons;
	}
}

if ( ! function_exists( 'ywsbs_is_an_order_with_subscription' ) ) {
	/**
	 * Checks if in the order there's a subscription product
	 * returns false if is not an order with subscription or
	 * returns the type of subscription order ( parent|renew )
	 *
	 * @param WC_Order $order
	 *
	 * @return string|bool
	 * @since 1.2.0
	 */
	function ywsbs_is_an_order_with_subscription( $order ) {

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		$order_subscription_type = false;
		$subscriptions           = yit_get_prop( $order, 'subscriptions' );
		$is_renew                = yit_get_prop( $order, 'is_renew' );

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
	 * @param int|WC_Order $order
	 * @param int|string   $order_item_id
	 *
	 * @return YWSBS_Subscription|bool
	 * @since 1.2.0
	 */
	function ywsbs_get_subscription_by_order( $order, $order_item_id = '' ) {

		$subscription = false;

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		$order_subscriptions = (array) yit_get_prop( $order, 'subscriptions' );

		if ( $order_subscriptions ) {
			if ( empty( $order_item_id ) ) {
				$subscription_id = array_shift( $order_subscriptions );
				$subscription    = ywsbs_get_subscription( $subscription_id );
			} else {
				foreach ( $order_subscriptions as $subscription_id ) {
					$subscription_to_check = ywsbs_get_subscription( $subscription_id );
					if ( $subscription_to_check->order_item_id == $order_item_id ) {
						$subscription = $subscription_to_check;
						break;
					}
				}
			}
		}

		return $subscription;
	}
}

if ( ! function_exists( 'ywsbs_get_payment_gateway_by_subscription' ) ) {
	/**
	 * Get the gateway registered for the $subscription
	 *
	 * @param $subscription YWSBS_Subscription
	 *
	 * @return WC_Payment_Gateway|bool
	 * @since 1.4.5
	 */
	function ywsbs_get_payment_gateway_by_subscription( $subscription ) {

		$payment_method = $subscription->get_payment_method();

		if ( empty( $payment_method ) ) {
			return false;
		}

		if ( WC()->payment_gateways() ) {
			$payment_gateways = WC()->payment_gateways()->payment_gateways();
		} else {
			$payment_gateways = array();
		}

		return isset( $payment_gateways[ $payment_method ] ) ? $payment_gateways[ $payment_method ] : false;
	}
}

if ( ! function_exists( 'yith_check_privacy_enabled' ) ) {

	/**
	 * Check if the tool for export and erase personal data are enabled.
	 *
	 * @param bool $wc tell if WooCommerce privacy is needed
	 * @return bool
	 * @since 1.0.0
	 */
	function yith_check_privacy_enabled( $wc = false ) {
		global $wp_version;
		$enabled = $wc ? version_compare( WC()->version, '3.4.0', '>=' ) && version_compare( $wp_version, '4.9.5', '>' ) : version_compare( $wp_version, '4.9.5', '>' );
		// APPLY_FILTER: yith_check_privacy_enabled: check if the privacy is enabled on site
		return apply_filters( 'yith_check_privacy_enabled', $enabled, $wc );
	}
}

if ( ! function_exists( 'ywsbs_check_valid_admin_page' ) ) {
	/**
	 * Return if the current pagenow is valid for a post_type, useful if you want add metabox, scripts inside the editor of a particular post type.
	 *
	 * @param $post_type_name
	 *
	 * @return bool
	 * @author Emanuela Castorina
	 */
	function ywsbs_check_valid_admin_page( $post_type_name ) {
		global $pagenow;
		$post = isset( $_REQUEST['post'] ) ? $_REQUEST['post'] : ( isset( $_REQUEST['post_ID'] ) ? $_REQUEST['post_ID'] : 0 );
		$post = get_post( $post );

		if ( ( $post && $post->post_type == $post_type_name ) || ( $pagenow == 'post-new.php' && isset( $_REQUEST['post_type'] ) && $_REQUEST['post_type'] == $post_type_name ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'ywsbs_get_order_fields_to_edit' ) ) {
	/**
	 * Return the list of fields that can be edited on a subscription.
	 *
	 * @param $type
	 *
	 * @return array|mixed|void
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywsbs_get_order_fields_to_edit( $type ) {
		$fields = array();

		if ( 'billing' == $type ) {
			// APPLY_FILTER: ywsbs_admin_billing_fields : filtering the admin billing fields
			$fields = apply_filters(
				'ywsbs_admin_billing_fields',
				array(
					'first_name' => array(
						'label' => __( 'First name', 'woocommerce' ),
						'show'  => false,
					),
					'last_name'  => array(
						'label' => __( 'Last name', 'woocommerce' ),
						'show'  => false,
					),
					'company'    => array(
						'label' => __( 'Company', 'woocommerce' ),
						'show'  => false,
					),
					'address_1'  => array(
						'label' => __( 'Address line 1', 'woocommerce' ),
						'show'  => false,
					),
					'address_2'  => array(
						'label' => __( 'Address line 2', 'woocommerce' ),
						'show'  => false,
					),
					'city'       => array(
						'label' => __( 'City', 'woocommerce' ),
						'show'  => false,
					),
					'postcode'   => array(
						'label' => __( 'Postcode / ZIP', 'woocommerce' ),
						'show'  => false,
					),
					'country'    => array(
						'label'   => __( 'Country', 'woocommerce' ),
						'show'    => false,
						'class'   => 'js_field-country select short',
						'type'    => 'select',
						'options' => array( '' => __( 'Select a country&hellip;', 'woocommerce' ) ) + WC()->countries->get_allowed_countries(),
					),
					'state'      => array(
						'label' => __( 'State / County', 'woocommerce' ),
						'class' => 'js_field-state select short',
						'show'  => false,
					),
					'email'      => array(
						'label' => __( 'Email address', 'woocommerce' ),
					),
					'phone'      => array(
						'label' => __( 'Phone', 'woocommerce' ),
					),
				)
			);
		} elseif ( 'shipping' == $type ) {
			// APPLY_FILTER: ywsbs_admin_shipping_fields : filtering the admin shipping fields
			$fields = apply_filters(
				'ywsbs_admin_shipping_fields',
				array(
					'first_name' => array(
						'label' => __( 'First name', 'woocommerce' ),
						'show'  => false,
					),
					'last_name'  => array(
						'label' => __( 'Last name', 'woocommerce' ),
						'show'  => false,
					),
					'company'    => array(
						'label' => __( 'Company', 'woocommerce' ),
						'show'  => false,
					),
					'address_1'  => array(
						'label' => __( 'Address line 1', 'woocommerce' ),
						'show'  => false,
					),
					'address_2'  => array(
						'label' => __( 'Address line 2', 'woocommerce' ),
						'show'  => false,
					),
					'city'       => array(
						'label' => __( 'City', 'woocommerce' ),
						'show'  => false,
					),
					'postcode'   => array(
						'label' => __( 'Postcode / ZIP', 'woocommerce' ),
						'show'  => false,
					),
					'country'    => array(
						'label'   => __( 'Country', 'woocommerce' ),
						'show'    => false,
						'type'    => 'select',
						'class'   => 'js_field-country select short',
						'options' => array( '' => __( 'Select a country&hellip;', 'woocommerce' ) ) + WC()->countries->get_shipping_countries(),
					),
					'state'      => array(
						'label' => __( 'State / County', 'woocommerce' ),
						'class' => 'js_field-state select short',
						'show'  => false,
					),
				)
			);
		}

		return $fields;
	}
}

if ( ! function_exists( 'ywsbs_enable_scheduling' ) ) {
	/**
	 * Check if there are payment methods that support the scheduling.
	 *
	 * @return bool
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 * @since 1.4.6
	 */
	function ywsbs_enable_scheduling() {
		if ( WC()->payment_gateways() ) {
			$gateways = WC()->payment_gateways()->get_available_payment_gateways();
			if ( $gateways ) {
				foreach ( $gateways as $gateway ) {
					if ( $gateway->supports( 'yith_subscriptions_scheduling' ) ) {
						return true;
					}
				}
			}
		}
		return false;
	}
}


if ( ! function_exists( 'ywsbs_support_scheduling' ) ) {
	/**
	 * Check if the gateway supports the scheduling.
	 *
	 * @return bool
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
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

if ( ! function_exists( 'ywsbs_enable_subscriptions_multiple' ) ) {
	/**
	 * Check if there are payment methods that support the multiple product subscription on cart.
	 *
	 * @return bool
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 * @since 1.4.6
	 */
	function ywsbs_enable_subscriptions_multiple() {
		if ( WC()->payment_gateways() ) {

			$gateways = WC()->payment_gateways()->get_available_payment_gateways();

			foreach ( $gateways as $gateway ) {
				if ( $gateway->supports( 'yith_subscriptions_multiple' ) ) {

					return true;
				}
			}
		}

		return apply_filters( 'ywsbs_force_multiple_subscriptions', false );
	}
}

if ( ! function_exists( 'ywsbs_support_subscriptions_multiple' ) ) {
	/**
	 * Check if the gateway supports the multiple product subscription on cart.
	 *
	 * @return bool
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 * @since 1.4.6
	 */
	function ywsbs_support_subscriptions_multiple( $gateway_id ) {
		$support = false;
		if ( WC()->payment_gateways() ) {
			$gateways = WC()->payment_gateways()->get_available_payment_gateways();
			if ( isset( $gateways[ $gateway_id ] ) ) {
				$support = $gateways[ $gateway_id ]->supports( 'yith_subscriptions_multiple' );
			}
		}

		return $support;
	}
}

if ( ! function_exists( 'ywsbs_check_renew_order_before_pay' ) ) {
	/**
	 * Check if the renew order is can be paid or not.
	 *
	 * Check if the number of max attempts is reached and if the status of renew order is correct.
	 *
	 * @param WC_Order $order The renew order object
	 *
	 * @return boolean
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 * @since 1.4.6
	 */
	function ywsbs_check_renew_order_before_pay( $order ) {
		$is_valid = true;

		$failed_payments = $order->get_meta( 'failed_attemps' );
		$gateway_id      = $order->get_payment_method();
		$max_attempts    = ywsbs_get_max_failed_attempts_by_gateway( $gateway_id );

		if ( ! ( $failed_payments == 0 || ( $failed_payments > 0 && $failed_payments < $max_attempts ) ) ) {
			yith_subscription_log( 'Cannot pay the order because failed_payments ( ' . $failed_payments . ') is < of max attempts ( ' . $max_attempts . ' )', 'subscription_payment' );
			$is_valid = false;
		}

		// check if the subscription can be paid
		$subscriptions = $order->get_meta( 'subscriptions' );
		if ( $subscriptions ) {
			$subscription = ywsbs_get_subscription( $subscriptions[0] );
			if ( in_array( $subscription->status, array( 'cancelled', 'paused', 'expired' ) ) ) {
				yith_subscription_log( 'Cannot pay the order because the subscription ' . $subscription->id . ' is ' . $subscription->status, 'subscription_payment' );

				$is_valid = false;
			}
		}

		if ( $is_valid ) {
			$status   = YWSBS_Subscription_Order()->get_renew_order_status();
			$order_id = $order->get_id();

			if ( ! $order->has_status( $status ) ) {
				YITH_WC_Subscription()->log( sprintf( __( 'New payment request denied because the order #%1$d is on %2$s status', 'yith-woocommerce-subscription' ), $order_id, $order->get_status() ) );
				yith_subscription_log( sprintf( __( 'New payment request denied because the order #%1$d is on %2$s status', 'yith-woocommerce-subscription' ), $order_id, $order->get_status() ), 'subscription_payment' );

				$is_valid = false;
			}
		}

		// APPLY_FILTER: ywsbs_check_renew_order_before_pay : allow filtering the check the renew order before the payment
		return apply_filters( 'ywsbs_check_renew_order_before_pay', $is_valid, $order );
	}
}

if ( ! function_exists( 'ywsbs_register_failed_payment' ) ) {
	/**
	 * Register failed payment for a renew.
	 *
	 * Usually it is used by the gateway when a payment is failed.
	 *
	 * @param WC_Order $order The renew order failed
	 * @param string   $error_message The error message returned by gateway
	 *
	 * @return mixed|void
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 * @since 1.4.6
	 */
	function ywsbs_register_failed_payment( $order, $error_message ) {
		$is_a_renew           = $order->get_meta( 'is_a_renew' );
		$order_id             = $order->get_id();
		$subscriptions        = $order->get_meta( 'subscriptions' );
		$payment_method       = $order->get_payment_method();
		$payment_method_title = $order->get_payment_method_title();

		$order->add_order_note( sprintf( __( '%1$s Failed payment: %2$s', 'yith-woocommerce-subscription' ), $payment_method_title, $error_message ) );

		if ( $is_a_renew == 'yes' ) {

			$failed_attempts      = $order->get_meta( 'failed_attemps' );
			$failed_attempts      = empty( $failed_attempts ) ? 0 : $failed_attempts;
			$max_attempts         = ywsbs_get_max_failed_attempts_by_gateway( $payment_method );
			$gap_between_attempts = ywsbs_get_num_of_days_between_attempts_by_gateway( $payment_method );

			if ( $failed_attempts + 1 < $max_attempts ) {
				// update the post meta of the renew order
				$next_payment_attempt_up = current_time( 'timestamp' ) + $gap_between_attempts * DAY_IN_SECONDS;
				$order->update_meta_data( 'failed_attemps', $failed_attempts + 1 );
				$order->update_meta_data( 'next_payment_attempt', $next_payment_attempt_up );
				$order->save();

				YITH_WC_Subscription()->log( sprintf( __( '%1$s - total failed attempts (%2$s) for order #%3$s', 'yith-woocommerce-subscription' ), $payment_method_title, ( $failed_attempts + 1 ), $order_id ) );

				foreach ( $subscriptions as $subscription_id ) {
					$subscription = ywsbs_get_subscription( $subscription_id );
					$subscription->register_failed_attempt( $failed_attempts + 1, false, $next_payment_attempt_up );
				}
			} else {
				// Max Failed Attempts reached
				foreach ( $subscriptions as $subscription_id ) {
					$subscription = ywsbs_get_subscription( $subscription_id );
					$subscription->cancel( false );
					$parent = wc_get_order( $subscription->get( 'order_id' ) );
					if ( $parent instanceof WC_Order ) {
						$parent->add_order_note( __( 'Subscription cancelled for max failed attempts.', 'yith-woocommerce-subscription' ) );
					}
					$order->add_order_note( __( 'Subscription cancelled for max failed attempts.', 'yith-woocommerce-subscription' ) );
					YITH_WC_Subscription()->log( sprintf( __( 'Subscription cancelled for max failed attempts. Subscription #%1$s. Order #%2$s', 'yith-woocommerce-subscription' ), $subscription_id, $order_id ) );
				}
			}
		}
	}
}

if ( ! function_exists( 'ywsbs_get_activity_status' ) ) {
	/**
	 * Retrieve the status localized
	 *
	 * @param $status
	 *
	 * @return mixed
	 */
	function ywsbs_get_activity_status( $status ) {
		$list_status = array(
			'success' => __( 'success', 'yith-woocommerce-subscription' ),
			'info'    => __( 'info', 'yith-woocommerce-subscription' ),
		);
		return isset( $list_status[ $status ] ) ? $list_status[ $status ] : $status;
	}
}

// ** DEPRECATED FUNCTIONS */
if ( ! function_exists( 'ywsbs_get_max_failed_attemps_list' ) ) {
	/**
	 * @deprecated Use instead ywsbs_get_max_failed_attempts_list
	 * @since 1.4.6
	 */
	function ywsbs_get_max_failed_attemps_list() {
		return apply_filters( 'ywsbs_max_failed_attemps_list', ywsbs_get_max_failed_attempts_list() );
	}
}

if ( ! function_exists( 'ywsbs_get_paypal_limit_options' ) ) {
	/**
	 * Return the list of time options with the max value that PayPal accepts.
	 *
	 * @deprecated Use instead ywsbs_get_max_length_period
	 * @since 1.4.6
	 */
	function ywsbs_get_paypal_limit_options() {
		return apply_filters( 'ywsbs_paypal_limit_options', ywsbs_get_max_length_period() );
	}
}

if ( ! function_exists( 'ywsbs_get_num_of_days_between_attemps' ) ) {

	/**
	 * Return the list of max failed attemps for each compatible gateways
	 *
	 * @return array
	 * @deprecated Use instead ywsbs_get_num_of_days_between_attempts
	 * @since 1.4.6
	 */
	function ywsbs_get_num_of_days_between_attemps() {
		return apply_filters( 'ywsbs_get_num_of_days_between_attemps', ywsbs_get_num_of_days_between_attempts() );
	}
}

if ( ! function_exists( 'yith_ywsbs_locate_template' ) ) {

	/**
	 * Locate the templates and return the path of the file found
	 *
	 * @param string $path
	 * @param array  $var
	 * @deprecated
	 * @return string
	 * @since 1.0.0
	 */

	function yith_ywsbs_locate_template( $path, $var = null ) {

		global $woocommerce;

		if ( function_exists( 'WC' ) ) {
			$woocommerce_base = WC()->template_path();
		} elseif ( defined( 'WC_TEMPLATE_PATH' ) ) {
			$woocommerce_base = WC_TEMPLATE_PATH;
		} else {
			$woocommerce_base = $woocommerce->plugin_path() . '/templates/';
		}

		$template_woocommerce_path = $woocommerce_base . $path;
		$template_path             = '/' . $path;
		$plugin_path               = YITH_YWSBS_DIR . 'templates/' . $path;

		$located = locate_template(
			array(
				$template_woocommerce_path, // Search in <theme>/woocommerce/
				$template_path,             // Search in <theme>/
				$plugin_path,                // Search in <plugin>/templates/
			)
		);

		if ( ! $located && file_exists( $plugin_path ) ) {
			return apply_filters( 'yith_ywsbs_locate_template', $plugin_path, $path );
		}

		return apply_filters( 'yith_ywsbs_locate_template', $located, $path );
	}
}

if ( ! function_exists( 'yith_subscription_log' ) ) {

	function yith_subscription_log( $message, $type = 'subscription_status' ) {

		$debug_enabled = get_option( 'ywsbs_enable_log', false );

		if ( 'yes' === $debug_enabled ) {

			$debug = wc_get_logger();

			$debug->add( 'ywsbs_' . $type, $message );

		}

	}
}
