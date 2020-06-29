<?php
/**
 * WooCommerce Customer/Order XML Export Suite
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order XML Export Suite to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order XML Export Suite for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-customer-order-xml-export-suite/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2019, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Customer/Order XML Export Suite Methods
 *
 * Handles loading export methods and provides utility functions for
 * checking if auto export methods are configurered, etc.
 *
 * @since 2.0.0
 */
class WC_Customer_Order_XML_Export_Suite_Methods {


	/**
	 * Check if settings for chosen auto-export method are saved
	 *
	 * In 2.0.0 added $export_type param, moved here from WC_Customer_Order_XML_Export_Suite_Admin class
	 *
	 * @since 1.2.0
	 * @param string $method export method, either `ftp` or `http_post`
	 * @param string $export_type export type, either `orders` or `customers`
	 * @return bool
	 */
	public function method_settings_exist( $method, $export_type = 'orders' ) {

		// assume true
		$exist  = true;
		$prefix = 'wc_customer_order_xml_export_suite_' . $export_type;

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
	 * @since 2.0.0
	 * @param string $export_type One of `orders` or `customers`
	 * @param bool $check_if_configured Optional. Defaults to true
	 * @return string|null Export method or null if not configured
	 */
	public function get_auto_export_method( $export_type, $check_if_configured = true ) {

		$export_method = get_option( 'wc_customer_order_xml_export_suite_' . $export_type . '_auto_export_method' );

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
	 * In 2.0.0 added $export_type param, moved here from
	 * WC_Customer_Order_XML_Export_Suite_Handler class
	 *
	 * @since 1.1.0
	 * @param string $method the export method, `download`, `ftp`, `http_post`, or `email`
	 * @param string $export_type the export type, `orders` or `customers`
	 * @param string $completed_at optional - a string representation of the completion date
	 * @return \WC_Customer_Order_XML_Export_Suite_Method the export method
	 */
	public function get_export_method( $method, $export_type, $completed_at = '' ) {

		$path          = wc_customer_order_xml_export_suite()->get_plugin_path() . '/includes/export-methods';
		$option_prefix = 'wc_customer_order_xml_export_suite_' . $export_type . '_';

		require_once( $path. '/interface-wc-customer-order-xml-export-suite-method.php' );

		// get the export method specified
		switch ( $method ) {

			case 'ftp':
				// abstract FTP class
				require_once( $path . '/ftp/abstract-wc-customer-order-xml-export-suite-method-file-transfer.php' );

				$ftp_path = get_option( $option_prefix . 'ftp_path', '' );
				$ftp_path = $ftp_path ? trailingslashit( $ftp_path ) : '';

				$args = array(
					'ftp_server'       => get_option( $option_prefix . 'ftp_server' ),
					'ftp_username'     => get_option( $option_prefix . 'ftp_username' ),
					'ftp_password'     => get_option( $option_prefix . 'ftp_password', '' ),
					'ftp_port'         => get_option( $option_prefix . 'ftp_port' ),
					'ftp_path'         => $ftp_path,
					'ftp_security'     => get_option( $option_prefix . 'ftp_security' ),
					'ftp_passive_mode' => get_option( $option_prefix . 'ftp_passive_mode' ),
				);

				try {

					switch ( $args['ftp_security'] ) {

						// FTP over SSH
						case 'sftp' :
							require_once( $path . '/ftp/class-wc-customer-order-xml-export-suite-method-sftp.php' );
							return new WC_Customer_Order_XML_Export_Suite_Method_SFTP( $args );

						// FTP with Implicit SSL
						case 'ftp_ssl' :
							require_once( $path . '/ftp/class-wc-customer-order-xml-export-suite-method-ftp-implicit-ssl.php' );
							return new WC_Customer_Order_XML_Export_Suite_Method_FTP_Implicit_SSL( $args );

						// FTP with explicit SSL/TLS *or* regular FTP
						case 'ftps' :
						case 'none' :
							require_once( $path . '/ftp/class-wc-customer-order-xml-export-suite-method-ftp.php' );
							return new WC_Customer_Order_XML_Export_Suite_Method_FTP( $args );
					}

				} catch( SV_WC_Plugin_Exception $e ) {

					wc_customer_order_xml_export_suite()->log( $e->getMessage() );

					throw $e;
				}

				break;

			case 'http_post':
				require_once( $path . '/class-wc-customer-order-xml-export-suite-method-http-post.php' );

				$args = array(
					'content_type'  => 'application/xml',
					'http_post_url' => get_option( $option_prefix . 'http_post_url' ),
				);

				return new WC_Customer_Order_XML_Export_Suite_Method_HTTP_POST( $args );

			case 'email':
				require_once( $path . '/class-wc-customer-order-xml-export-suite-method-email.php' );

				/**
				 * Allow actors to change the email subject used for automated exports.
				 *
				 * In 2.0.0 moved here from WC_Customer_Order_XML_Export_Suite_Method_Email class
				 *
				 * @since 1.2.0
				 * @param string the subject as set in the plugin settings
				 */
				$subject = apply_filters( 'wc_customer_order_xml_export_suite_email_subject', get_option( $option_prefix . 'email_subject' ) );

				// create email message based on export type
				switch ( $export_type ) {

					case 'orders':
						/* translators: Placeholders: %s - date */
						$message = esc_html__( 'Order Export for %s', 'woocommerce-customer-order-xml-export-suite' );
					break;

					case 'customers':
						/* translators: Placeholders: %s - date */
						$message = esc_html__( 'Customer Export for %s', 'woocommerce-customer-order-xml-export-suite' );
					break;

					case 'coupons':
						/* translators: Placeholders: %s - date */
						$message = esc_html__( 'Coupon Export for %s', 'woocommerce-customer-order-xml-export-suite' );
					break;

					default:
						/* translators: Placeholders: %s - date */
						$message = esc_html__( 'Export for %s', 'woocommerce-customer-order-xml-export-suite' );
					break;
				}

				$timestamp = '' !== $completed_at ? strtotime( $completed_at ) : current_time( 'timestamp' );
				$message   = sprintf( $message, date_i18n( wc_date_format(), $timestamp ) );

				$args = array(
					'email_recipients' => get_option( $option_prefix . 'email_recipients' ),
					'email_subject'    => $subject,
					'email_message'    => $message,
					'email_id'         => 'wc_customer_order_xml_export_suite',
				);

				return new WC_Customer_Order_XML_Export_Suite_Method_Email( $args );

			default:

				/**
				 * Get Export Method
				 *
				 * Triggered when getting the export method. This is designed for
				 * custom methods to hook in and load their class so it can be
				 * returned and used.
				 *
				 * @since 2.0.0
				 * @param \WC_Customer_Order_XML_Export_Suite_Methods $this, export methods instance
				 */
				do_action( 'wc_customer_order_xml_export_suite_get_export_method', $this );

				$class_name = sprintf( 'WC_Customer_Order_XML_Export_Suite_Custom_Method_%s', ucwords( strtolower( $method ) ) );

				return class_exists( $class_name ) ? new $class_name() : null;
		}
	}



	/**
	 * Get export methods with labels
	 *
	 * @since 2.0.0
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
		 * @since 2.0.0
		 * @param array
		 */
		return apply_filters( 'wc_customer_order_xml_export_suite_methods', array(
			'local'     => __( 'locally', 'woocommerce-customer-order-xml-export-suite' ),
			'ftp'       => __( 'via FTP', 'woocommerce-customer-order-xml-export-suite' ),
			'http_post' => __( 'via HTTP POST', 'woocommerce-customer-order-xml-export-suite' ),
			'email'     => __( 'via Email', 'woocommerce-customer-order-xml-export-suite' ),
		) );
	}


	/**
	 * Get a label for an export method
	 *
	 * @since 2.0.0
	 * @param string $method Export method, such as `ftp`, `http_post` or `email`
	 * @return string
	 */
	public function get_export_method_label( $method ) {

		$methods = $this->get_export_method_labels();

		/* translators: Placeholders: %s - export method name, example: "via Email" */
		return ! empty( $methods[ $method ] ) ? $methods[ $method ] : sprintf( __( 'via %s', 'woocommerce-customer-order-xml-export-suite' ), $method );
	}


}
