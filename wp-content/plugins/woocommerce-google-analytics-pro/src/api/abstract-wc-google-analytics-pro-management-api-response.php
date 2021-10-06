<?php
/**
 * WooCommerce Google Analytics Pro
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Google Analytics Pro to newer
 * versions in the future. If you wish to customize WooCommerce Google Analytics Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-google-analytics-pro/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2015-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\API\Management_API;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_2 as Framework;

/**
 * Handles responses from the Google Analytics Management API.
 *
 * @link https://developers.google.com/analytics/devguides/config/mgmt/v3/mgmtReference/
 *
 * @since 1.7.0
 */
abstract class Response extends Framework\SV_WC_API_JSON_Response {


	/**
	 * Determines whether the response does not contain errors.
	 *
	 * @since 1.7.0
	 *
	 * @return bool
	 */
	public function is_ok() {

		return ! $this->has_errors();
	}


	/**
	 * Determines whether the response contains errors.
	 *
	 * @since 1.7.0
	 *
	 * @return bool
	 */
	public function has_errors() {

		return empty( $this->response_data ) || ! empty( $this->response_data->error );
	}


	/**
	 * Gets a response error code.
	 *
	 * @since 1.7.0
	 *
	 * @return int
	 */
	public function get_error_code() {

		return isset( $this->response_data->error->code ) ? (int) $this->response_data->error->code : 500;
	}


	/**
	 * Gets a response error message.
	 *
	 * @since 1.7.0
	 *
	 * @return string
	 */
	public function get_error_message() {

		return isset( $this->response_data->error->message ) ? $this->response_data->error->message : '';
	}


	/**
	 * Gets additional error response details.
	 *
	 * @sine 1.7.0
	 *
	 * @return \stdClass[] array of error objects
	 */
	public function get_error_details() {

		return isset( $this->response_data->error->errors ) ? (array) $this->response_data->error->errors : [];
	}


}
