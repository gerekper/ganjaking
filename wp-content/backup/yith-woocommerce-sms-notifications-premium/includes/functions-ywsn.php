<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * GDPR RELATED FUNCTIONS
 */
if ( ! function_exists( 'ywsn_remove_numbers_from_log' ) ) {

	/**
	 * Remove Phone numbers from plugin's logs
	 *
	 * @param   $anon_value string
	 * @param   $prop       string
	 * @param   $value      string
	 * @param   $data_type  string
	 * @param   $order      WC_Order
	 *
	 * @return  string
	 * @since   1.4.0
	 *
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywsn_remove_numbers_from_log( $anon_value, $prop, $value, $data_type, $order ) {

		if ( 'billing_phone' === $prop ) {

			$messages = new YWSN_Messages( array( 'object' => $order ) );
			$phone    = $messages->get_formatted_number( $value, $order->get_billing_country() );

			if ( defined( 'WC_LOG_HANDLER' ) && 'WC_Log_Handler_DB' === WC_LOG_HANDLER ) {

				global $wpdb;

				$wpdb->query( "UPDATE {$wpdb->prefix}woocommerce_log SET message = replace(message, $phone, '************')" );//phpcs:ignore

			} else {
				$logs = WC_Admin_Status::scan_log_files();

				foreach ( $logs as $log ) {
					if ( strpos( $log, 'ywsn-' ) !== false ) {
						$current_log   = file_get_contents( WC_LOG_DIR . $log );
						$file_contents = str_replace( $phone, '************', $current_log );
						file_put_contents( WC_LOG_DIR . $log, $file_contents );
					}
				}
			}
		}

		return $anon_value;

	}

	add_filter( 'woocommerce_privacy_remove_order_personal_data_prop_value', 'ywsn_remove_numbers_from_log', 10, 5 );

}

/**
 * URL SHORTENING RELATED FUNCTIONS
 */
if ( ! function_exists( 'ywsn_url_shortening' ) ) {

	/**
	 * Replace URLs with shorten URLs via callback
	 *
	 * @param   $text string
	 *
	 * @return  string
	 * @since   1.4.0
	 *
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywsn_url_shortening( $text ) {

		$pattern = "/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/";

		$text = preg_replace_callback( $pattern, 'ywsn_get_shorten_url', $text );

		return $text;

	}
}

if ( ! function_exists( 'ywsn_get_shorten_url' ) ) {

	/**
	 * Callback for shortening regex
	 *
	 * @param   $text array
	 *
	 * @return  string
	 * @since   1.4.0
	 *
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywsn_get_shorten_url( $text ) {

		$url     = reset( $text );
		$service = get_option( 'ywsn_url_shortening' );

		switch ( $service ) {

			case 'bitly':
				$short_url = ywsn_bitly_url_shortening( $url );
				break;

			default:
				$short_url = apply_filters( 'ywsn_custom_shortening_' . $service, $url );

		}

		return $short_url;

	}
}

if ( ! function_exists( 'ywsn_bitly_url_shortening' ) ) {

	/**
	 * Shortens a URL via Bitly Shortener
	 *
	 * @param   $url string
	 *
	 * @return  string
	 * @since   1.0.0
	 *
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywsn_bitly_url_shortening( $url ) {

		$args = array(
			'access_token' => get_option( 'ywsn_bitly_access_token' ),
			'longUrl'      => esc_url( $url ),
			'format'       => 'json',
		);

		$response = wp_remote_get( add_query_arg( $args, 'https://api-ssl.bitly.com/v3/shorten' ) );
		$json     = json_decode( wp_remote_retrieve_body( $response ) );

		if ( isset( $json->status_code ) && 200 === (int) $json->status_code && isset( $json->data->url ) ) {

			$url = $json->data->url;

		}

		return $url;

	}
}

/**
 * MESSAGES RELATED FUNCTIONS
 */
if ( ! function_exists( 'ywsn_get_admin_numbers' ) ) {

	/**
	 * Get admin numbers
	 *
	 * @param   $order WC_Order
	 *
	 * @return  array
	 * @since   1.0.3
	 *
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywsn_get_admin_numbers( $order ) {

		if ( wp_get_post_parent_id( $order->get_id() ) !== 0 ) {

			$numbers = apply_filters( 'ywsn_admin_phone_numbers', array(), $order );

		} else {

			$phone_numbers = trim( get_option( 'ywsn_admin_phone' ) );
			$numbers       = ( '' === $phone_numbers ) ? array() : explode( ',', $phone_numbers );

		}

		return $numbers;

	}
}

if ( ! function_exists( 'ywsn_replace_placeholders' ) ) {

	/**
	 * Replace placeholders
	 *
	 * @param   $message  string
	 * @param   $object   WC_Order|YITH_WCBK_Booking|boolean
	 *
	 * @return  string
	 * @since   1.0.0
	 *
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywsn_replace_placeholders( $message, $object = false ) {

		if ( $object instanceof YITH_WCBK_Booking ) {
			$order = $object->get_order();
		} else {
			$order = $object;
		}

		$is_test = ! $order;

		$placeholders = array(
			'{site_title}'          => get_bloginfo( 'name' ),
			'{order_id}'            => ( $is_test ? _x( 'OrderID', 'test placeholder for Order ID', 'yith-woocommerce-sms-notifications' ) : $order->get_order_number() ),
			'{order_total}'         => ( $is_test ? _x( 'Total', 'test placeholder for Order Total', 'yith-woocommerce-sms-notifications' ) : $order->get_total() ),
			'{order_status}'        => ( $is_test ? _x( 'Status', 'test placeholder for Order Status', 'yith-woocommerce-sms-notifications' ) : wc_get_order_status_name( $order->get_status() ) ),
			'{billing_name}'        => ( $is_test ? _x( 'Billing name', 'test placeholder for Billing Name', 'yith-woocommerce-sms-notifications' ) : sprintf( '%s %s', $order->get_billing_first_name(), $order->get_billing_last_name() ) ),
			'{billing_first_name}'  => ( $is_test ? _x( 'Billing first name', 'test placeholder for Billing First Name', 'yith-woocommerce-sms-notifications' ) : $order->get_billing_first_name() ),
			'{billing_last_name}'   => ( $is_test ? _x( 'Billing last name', 'test placeholder for Billing Last Name', 'yith-woocommerce-sms-notifications' ) : $order->get_billing_last_name() ),
			'{shipping_name}'       => ( $is_test ? _x( 'Shipping name', 'test placeholder for Shipping Name', 'yith-woocommerce-sms-notifications' ) : sprintf( '%s %s', $order->get_shipping_first_name(), $order->get_shipping_last_name() ) ),
			'{shipping_first_name}' => ( $is_test ? _x( 'Shipping first name', 'test placeholder for Shipping Name', 'yith-woocommerce-sms-notifications' ) : $order->get_shipping_first_name() ),
			'{shipping_last_name}'  => ( $is_test ? _x( 'Shipping last name', 'test placeholder for Shipping Name', 'yith-woocommerce-sms-notifications' ) : $order->get_shipping_last_name() ),
			'{shipping_method}'     => ( $is_test ? _x( 'Shipping method', 'test placeholder for Shipping Method', 'yith-woocommerce-sms-notifications' ) : $order->get_shipping_method() ),
			'{additional_notes}'    => ( $is_test ? _x( 'Additional Notes', 'test placeholder for Additional Notes', 'yith-woocommerce-sms-notifications' ) : $order->get_customer_note() ),
			'{order_date}'          => ( $is_test ? _x( 'Order Date', 'test placeholder for Order Date', 'yith-woocommerce-sms-notifications' ) : wc_format_datetime( $order->get_date_created() ) ),
		);

		if ( $object instanceof YITH_WCBK_Booking ) {
			$placeholders['{booking_id}']      = ( $is_test ? _x( 'Booking ID', 'test placeholder for Booking ID', 'yith-woocommerce-sms-notifications' ) : $object->get_id() );
			$placeholders['{booking_status}']  = ( $is_test ? _x( 'Booking Status', 'test placeholder for Booking Status', 'yith-woocommerce-sms-notifications' ) : yith_wcbk_get_booking_status_name( $object->get_status() ) );
			$placeholders['{booking_details}'] = ( $is_test ? _x( 'Booking Details URL', 'test placeholder for Booking Details URL', 'yith-woocommerce-sms-notifications' ) : $object->get_view_booking_url() );
		}

		$placeholders = apply_filters( 'ywsn_sms_placeholders', $placeholders, $order );

		return str_replace( array_keys( $placeholders ), $placeholders, $message );

	}
}

/**
 * GENERAL PURPOSE FUNCTIONS
 */
if ( ! function_exists( 'ywsn_placeholder_reference' ) ) {

	/**
	 * Get Placeholders reference
	 *
	 * @return  array
	 * @since   1.0.8
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywsn_placeholder_reference() {

		$placeholders = array(
			'{site_title}'          => esc_html__( 'Website name', 'yith-woocommerce-sms-notifications' ),
			'{order_id}'            => esc_html__( 'Order number', 'yith-woocommerce-sms-notifications' ),
			'{order_total}'         => esc_html__( 'Order total', 'yith-woocommerce-sms-notifications' ),
			'{order_status}'        => esc_html__( 'Order status', 'yith-woocommerce-sms-notifications' ),
			'{billing_name}'        => esc_html__( 'Billing name', 'yith-woocommerce-sms-notifications' ),
			'{billing_first_name}'  => esc_html__( 'Billing first name', 'yith-woocommerce-sms-notifications' ),
			'{billing_last_name}'   => esc_html__( 'Billing last name', 'yith-woocommerce-sms-notifications' ),
			'{shipping_name}'       => esc_html__( 'Shipping name', 'yith-woocommerce-sms-notifications' ),
			'{shipping_first_name}' => esc_html__( 'Shipping first name', 'yith-woocommerce-sms-notifications' ),
			'{shipping_last_name}'  => esc_html__( 'Shipping last name', 'yith-woocommerce-sms-notifications' ),
			'{shipping_method}'     => esc_html__( 'Shipping method', 'yith-woocommerce-sms-notifications' ),
			'{additional_notes}'    => esc_html__( 'Additional Notes', 'yith-woocommerce-sms-notifications' ),
			'{order_date}'          => esc_html__( 'Order Date', 'yith-woocommerce-sms-notifications' ),
		);

		if ( function_exists( 'YITH_YWOT' ) ) {

			$placeholders['{tracking_number}'] = esc_html__( 'Tracking Number', 'yith-woocommerce-sms-notifications' );
			$placeholders['{carrier_name}']    = esc_html__( 'Carrier name', 'yith-woocommerce-sms-notifications' );
			$placeholders['{shipping_date}']   = esc_html__( 'Shipping date', 'yith-woocommerce-sms-notifications' );
			$placeholders['{tracking_url}']    = esc_html__( 'Tracking url', 'yith-woocommerce-sms-notifications' );

		}

		if ( ywsn_is_booking_active() ) {
			$placeholders['{booking_id}']      = esc_html__( 'Booking ID', 'yith-woocommerce-sms-notifications' );
			$placeholders['{booking_status}']  = esc_html__( 'Booking Status', 'yith-woocommerce-sms-notifications' );
			$placeholders['{booking_details}'] = esc_html__( 'Booking Details URL', 'yith-woocommerce-sms-notifications' );
		}

		return $placeholders;

	}
}

if ( ! function_exists( 'ywsn_is_multivendor_active' ) ) {

	/**
	 * Check if YITH WooCommerce Multi Vendor is active
	 *
	 * @return  boolean
	 * @since   1.0.3
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywsn_is_multivendor_active() {

		return defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM;

	}
}

if ( ! function_exists( 'ywsn_is_booking_active' ) ) {

	/**
	 * Check if YITH WooCommerce Multi Vendor is active
	 *
	 * @return  boolean
	 * @since   1.4.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywsn_is_booking_active() {

		return defined( 'YITH_WCBK_VERSION' ) && version_compare( YITH_WCBK_VERSION, '2.1.9', '>=' );

	}
}
