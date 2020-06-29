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
 * Export HTTP POST Class
 *
 * Simple wrapper for wp_remote_post() to POST exported data to remote URLs
 *
 * @since 1.0.3
 */
class WC_Customer_Order_XML_Export_Suite_Method_HTTP_POST implements WC_Customer_Order_XML_Export_Suite_Method {


	/** @var string MIME Content Type */
	private $content_type;

	/** @var string HTTP POST Url */
	private $http_post_url;


	/**
	 * Initialize the export method
	 *
	 * @since 2.0.0
	 * @param array $args {
	 *     An array of arguments.
	 *
	 *     @type string $content_type MIME Content-Type for the file
	 *     @type string $http_post_url URL to POST data to
	 * }
	 */
	 public function __construct( $args ) {

		$this->content_type  = $args['content_type'];
		$this->http_post_url = $args['http_post_url'];
	}


	/**
	 * Performs an HTTP POST to the specified URL with the exported data
	 *
	 * @since 1.0.3
	 *
	 * @param \WC_Customer_Order_XML_Export_Suite_Export|string $export the export object or a path to an export file
	 * @return bool whether the HTTP POST was successful or not
	 * @throws \SV_WC_Plugin_Exception WP HTTP error handling
	 */
	public function perform_action( $export ) {

		if ( ! $export ) {
			throw new SV_WC_Plugin_Exception( __( 'Unable to find export for transfer', 'woocommerce-customer-order-xml-export-suite' ) );
		}

		// export is a filename
		if ( is_string( $export ) && is_readable( $export ) ) {

			$contents = file_get_contents( $export );

		} else {

			$contents = $export->get_output();
		}

		/**
		 * Allow actors to modify HTTP POST args
		 *
		 * @since 1.0.3
		 * @param array $args
		 */
		$args = apply_filters( 'wc_customer_order_xml_export_suite_http_post_args', array(
			'timeout'     => 60,
			'redirection' => 0,
			'httpversion' => '1.0',
			'sslverify'   => true,
			'blocking'    => true,
			'headers'     => array(
				'accept'       => $this->content_type,
				'content-type' => $this->content_type,
			),
			'body'        => $contents,
			'cookies'     => array(),
			'user-agent'  => "WordPress " . $GLOBALS['wp_version'],
		) );

		$result = wp_safe_remote_post( $this->http_post_url, $args );

		// check for errors
		if ( is_wp_error( $result ) ) {

			throw new SV_WC_Plugin_Exception( $result->get_error_message() );
		}

		/**
		 * Allow actors to adjust whether the HTTP POST was a success or not
		 *
		 * By default a 200 (OK) or 201 (Created) status will indicate success.
		 *
		 * @since 2.0.0
		 * @param bool $success whether the request was successful or not
		 * @param array $result full wp_remote_post() result
		 */
		return apply_filters( 'wc_customer_order_xml_export_suite_http_post_success', in_array( $result['response']['code'], array( 200, 201 ) ), $result );
	}

}
