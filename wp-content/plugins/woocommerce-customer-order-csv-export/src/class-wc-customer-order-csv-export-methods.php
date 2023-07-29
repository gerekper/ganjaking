<?php
/**
 * WooCommerce Customer/Order/Coupon Export
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order/Coupon Export to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order/Coupon Export for your
 * needs please refer to http://docs.woocommerce.com/document/ordercustomer-csv-exporter/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_6 as Framework;

/**
 * Customer/Order CSV Export Methods
 *
 * Handles loading export methods and provides utility functions for
 * checking if auto export methods are configured, etc.
 *
 * @since 4.0.0
 */
class WC_Customer_Order_CSV_Export_Methods {


	/**
	 * Check if settings for chosen auto-export method are saved
	 *
	 * In 4.0.0 added $export_type param, moved here from WC_Customer_Order_CSV_Export_Admin class
	 *
	 * @since 3.1.0
	 * @deprecated 5.0.0
	 *
	 * @param string $method export method, either `ftp` or `http_post`
	 * @param string $export_type export type, either `orders` or `customers`
	 * @return bool
	 */
	public function method_settings_exist( $method, $export_type = WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS ) {

		wc_deprecated_function( __METHOD__, '5.0.0' );

		// assume true
		$exist  = true;
		$prefix = 'wc_customer_order_csv_export_' . $export_type;

		if ( 'ftp' === $method ) {
			$exist = get_option( $prefix . '_ftp_server' ) && get_option( $prefix . '_ftp_username' ) && get_option( $prefix . '_ftp_password' );
		} elseif ( 'http_post' === $method ) {
			$exist = get_option( $prefix . '_http_post_url' );
		}

		// since email defaults to the admin email, we don't require any
		// explicitly set email address

		return $exist;
	}


	/**
	 * Get the auto-export method and its label
	 *
	 * @since 4.0.0
	 * @deprecated 5.0.0
	 *
	 * @param string $export_type One of `orders` or `customers`
	 * @param bool $check_if_configured Optional. Defaults to true
	 * @return string|null Export method or null if not configured
	 */
	public function get_auto_export_method( $export_type, $check_if_configured = true ) {

		wc_deprecated_function( __METHOD__, '5.0.0' );

		$export_method = get_option( 'wc_customer_order_csv_export_' . $export_type . '_auto_export_method' );

		if ( ! $export_method || 'disabled' === $export_method ) {
			return null;
		}

		if ( $check_if_configured && ! $this->method_settings_exist( $export_method, $export_type ) ) {
			return null;
		}

		return $export_method;
	}


	/**
	 * Returns the export method object
	 *
	 * In 5.0.0 added $args param
	 *
	 * In 4.0.0 added $export_type param, moved here from
	 * WC_Customer_Order_CSV_Export_Handler class
	 *
	 * @since 3.0.0
	 *
	 * @param string $method the export method, `download`, `ftp`, `http_post`, or `email`
	 * @param string $export_type the export type, `orders` or `customers`
	 * @param string $completed_at optional - a string representation of the completion date
	 * @param string $output_type output type, either `csv` or `xml`
	 * @param array $args settings for the export method
	 * @return \WC_Customer_Order_CSV_Export_Method the export method
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function get_export_method( $method, $export_type, $completed_at = '', $output_type = WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV, $args = [] ) {

		// get the export method specified
		switch ( $method ) {

			case 'ftp':

				switch ( $args['ftp_security'] ) {

					// FTP over SSH
					case 'sftp' :
						return new WC_Customer_Order_CSV_Export_Method_SFTP( $args );

					// FTP with Implicit SSL
					case 'ftp_ssl' :
						return new WC_Customer_Order_CSV_Export_Method_FTP_Implicit_SSL( $args );

					// FTP with explicit SSL/TLS *or* regular FTP
					case 'ftps' :
					case 'none' :
						return new WC_Customer_Order_CSV_Export_Method_FTP( $args );
				}
				break;

			case 'http_post':

				$args['content_type'] = WC_Customer_Order_CSV_Export::OUTPUT_TYPE_CSV === $output_type ? 'text/csv' : 'application/xml';

				return new WC_Customer_Order_CSV_Export_Method_HTTP_POST( $args );

			case 'email':

				/**
				 * Filters the email subject used for automated exports to the given output type.
				 *
				 * @since 5.0.0
				 *
				 * @param string $subject subject text as set in the plugin settings
				 */
				$subject = apply_filters( "wc_customer_order_export_{$output_type}_email_subject", $args['email_subject'] );

				/**
				 * Filters the email subject used for automated exports.
				 *
				 * In 4.0.0 moved here from WC_Customer_Order_CSV_Export_Method_Email class
				 *
				 * @since 5.0.0
				 *
				 * @param string $subject subject text as set in the plugin settings
				 */
				$subject = apply_filters( 'wc_customer_order_export_email_subject', $subject );

				// create email message based on export type
				switch ( $export_type ) {

					case WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS:
						/* translators: Placeholders: %s - date */
						$message = esc_html__( 'Order Export for %s', 'woocommerce-customer-order-csv-export' );
					break;

					case WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS:
						/* translators: Placeholders: %s - date */
						$message = esc_html__( 'Customer Export for %s', 'woocommerce-customer-order-csv-export' );
					break;

					default:
						/* translators: Placeholders: %s - date */
						$message = esc_html__( 'Export for %s', 'woocommerce-customer-order-csv-export' );
					break;
				}

				$timestamp = ! empty( $args['completed_at'] ) ? strtotime( $args['completed_at'] ) : current_time( 'timestamp' );
				$message   = sprintf( $message, date_i18n( wc_date_format(), $timestamp ) );

				$args = array_merge( $args, [
					'email_subject'    => $subject,
					'email_message'    => $message,
					'email_id'         => 'wc_customer_order_csv_export',
				] );

				return new WC_Customer_Order_CSV_Export_Method_Email( $args );

			default:

				/**
				 * Fires when getting the export method for the given output type.
				 *
				 * This is designed for custom methods to hook in and load their class so it can be
				 * returned and used.
				 *
				 * @since 5.0.0
				 *
				 * @param \WC_Customer_Order_CSV_Export_Methods $handler export methods instance
				 */
				do_action( "wc_customer_order_export_get_{$output_type}_export_method", $this );

				/**
				 * Fires when getting the export method.
				 *
				 * This is designed for custom methods to hook in and load their class so it can be
				 * returned and used.
				 *
				 * In 4.0.0 moved here from WC_Customer_Order_CSV_Export_Handler class
				 *
				 * @since 5.0.0
				 *
				 * @param \WC_Customer_Order_CSV_Export_Methods $handler export methods instance
				 */
				do_action( 'wc_customer_order_export_get_export_method', $this );

				$class_name = sprintf( 'WC_Customer_Order_CSV_Export_Custom_Method_%s', ucwords( strtolower( $method ) ) );

				return class_exists( $class_name ) ? new $class_name() : null;
		}
	}


	/**
	 * Gets the export method class name for the given method type.
	 *
	 * @since 5.0.0
	 *
	 * @param string $method_type the export method type
	 * @param array $method_settings settings for the export method
	 * @return string|false
	 */
	public function get_export_method_class( $method_type, array $method_settings = [] ) {

		switch ( $method_type ) {

			case 'ftp':

				$ftp_security = ! empty( $method_settings['ftp_security'] ) ? $method_settings['ftp_security'] : null;

				switch ( $ftp_security ) {

					// FTP over SSH
					case 'sftp' :
						$class_name = 'WC_Customer_Order_CSV_Export_Method_SFTP';
					break;

					// FTP with Implicit SSL
					case 'ftp_ssl' :
						$class_name = 'WC_Customer_Order_CSV_Export_Method_FTP_Implicit_SSL';
					break;

					// FTP with explicit SSL/TLS *or* regular FTP
					case 'ftps' :
					case 'none' :
					default:
						$class_name = 'WC_Customer_Order_CSV_Export_Method_FTP';
				}

			break;

			case 'http_post':
				$class_name = 'WC_Customer_Order_CSV_Export_Method_HTTP_POST';
			break;

			case 'email':
				$class_name = 'WC_Customer_Order_CSV_Export_Method_Email';
			break;

			default:
				// TODO: should we fire wc_customer_order_export_get_export_method and wc_customer_order_export_get_{$output_type}_export_method here? {WV 2109-10-22}
				$class_name = sprintf( 'WC_Customer_Order_CSV_Export_Custom_Method_%s', ucwords( strtolower( $method_type ) ) );
		}

		/**
		 * Fires when getting the class name for the given export method type.
		 *
		 * @since 5.0.0
		 *
		 * @param string $class_name the class name for the export method
		 * @param string $method_type the export method type
		 */
		$class_name = apply_filters( 'wc_customer_order_coupon_export_export_method_class_name', $class_name, $method_type );

		return class_exists( $class_name ) && is_subclass_of( $class_name, WC_Customer_Order_CSV_Export_Method::class ) ? $class_name : false;
	}


	/**
	 * Get export methods with labels
	 *
	 * @since 4.0.0
	 * @return array keyed off of method id => label
	 */
	public function get_export_method_labels() {

		/**
		 * Allow actors to change the available export methods
		 *
		 * This only affects the options available in export settings
		 * and various dropdown menus. Actual support for custom
		 * export methods need to be added by providing a custom export method class.
		 *
		 * @since 4.0.0
		 * @param array
		 */
		return apply_filters( 'wc_customer_order_export_methods', [
			'local'     => __( 'Locally', 'woocommerce-customer-order-xml-export-suite' ),
			'ftp'       => __( 'FTP', 'woocommerce-customer-order-csv-export' ),
			'http_post' => __( 'HTTP POST', 'woocommerce-customer-order-csv-export' ),
			'email'     => __( 'Email', 'woocommerce-customer-order-csv-export' ),
		] );
	}


	/**
	 * Get a label for an export method
	 *
	 * @since 4.0.0
	 * @param string $method Export method, such as `ftp`, `http_post` or `email`
	 * @return string
	 */
	public function get_export_method_label( $method ) {

		$methods = $this->get_export_method_labels();

		if ( ! empty( $methods[ $method ] ) ) {
			$method = $methods[ $method ];
		}

		/* translators: Placeholders: %s - export method name, example: "via Email" */
		return sprintf( __( 'via %s', 'woocommerce-customer-order-csv-export' ), $method );
	}


}
