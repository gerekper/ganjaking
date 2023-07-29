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
 * Export HTTP POST Class
 *
 * Simple wrapper for wp_remote_post() to POST exported data to remote URLs
 *
 * @since 3.0.0
 */
class WC_Customer_Order_CSV_Export_Method_HTTP_POST implements WC_Customer_Order_CSV_Export_Method {


	/** @var string MIME Content Type */
	private $content_type;

	/** @var string HTTP POST Url */
	private $http_post_url;


	/**
	 * Initialize the export method
	 *
	 * @since 4.0.0
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
	 * @since 3.0.0
	 *
	 * @param \WC_Customer_Order_CSV_Export_Export|string $export the export object or a path to an export file
	 * @return bool whether the HTTP POST was successful or not
	 * @throws Framework\SV_WC_Plugin_Exception WP HTTP error handling
	 */
	public function perform_action( $export ) {

		if ( ! $export ) {
			throw new Framework\SV_WC_Plugin_Exception( __( 'Unable to find export for transfer', 'woocommerce-customer-order-csv-export' ) );
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
		 * @since 3.0.0
		 * @param array $args
		 */
		$args = apply_filters( 'wc_customer_order_export_http_post_args', [
			'timeout'     => 60,
			'redirection' => 0,
			'httpversion' => '1.0',
			'sslverify'   => true,
			'blocking'    => true,
			'headers'     => [
				'accept'       => $this->content_type,
				'content-type' => $this->content_type,
			],
			'body'        => $contents,
			'cookies'     => [],
			'user-agent'  => "WordPress " . $GLOBALS['wp_version'],
		] );

		$result = wp_safe_remote_post( $this->http_post_url, $args );

		// check for errors
		if ( is_wp_error( $result ) ) {

			throw new Framework\SV_WC_Plugin_Exception( $result->get_error_message() );
		}

		/**
		 * Allow actors to adjust whether the HTTP POST was a success or not
		 *
		 * By default a 200 (OK) or 201 (Created) status will indicate success.
		 *
		 * @since 4.0.0
		 * @param bool $success whether the request was successful or not
		 * @param array $result full wp_remote_post() result
		 */
		return apply_filters( 'wc_customer_order_export_http_post_success', in_array( $result['response']['code'], [ 200, 201 ] ), $result );
	}

}
