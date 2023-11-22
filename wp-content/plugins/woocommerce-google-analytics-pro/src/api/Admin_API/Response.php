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
 * @copyright Copyright (c) 2015-2023, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\API\Admin_API;

use SkyVerge\WooCommerce\PluginFramework\v5_11_12 as Framework;
use stdClass;

defined( 'ABSPATH' ) or exit;

/**
 * Handles responses from the Google Analytics Admin API.
 *
 * @link https://developers.google.com/analytics/devguides/config/admin/v1/rest
 *
 * @since 2.0.0
 */
abstract class Response extends Framework\SV_WC_API_JSON_Response {


	/**
	 * Determines whether the response does not contain errors.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_ok(): bool {

		return ! $this->has_errors();
	}


	/**
	 * Determines whether the response contains errors.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function has_errors(): bool {

		return empty( $this->response_data ) || ! empty( $this->response_data->error );
	}


	/**
	 * Gets a response error code.
	 *
	 * @since 2.0.0
	 *
	 * @return int
	 */
	public function get_error_code(): int {

		return isset( $this->response_data->error->code ) ? (int) $this->response_data->error->code : 500;
	}


	/**
	 * Gets a response error message.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_error_message(): string {

		return $this->response_data->error->message ?? '';
	}


	/**
	 * Gets additional error response details.
	 *
	 * @sine 2.0.0
	 *
	 * @return stdClass[] array of error objects
	 */
	public function get_error_details(): array {

		return isset( $this->response_data->error->errors ) ? (array) $this->response_data->error->errors : [];
	}


	/**
	 * Gets the next page token, if available
	 *
	 * @sine 2.0.4
	 *
	 * @return string|null
	 */
	public function get_next_page_token() : ?string {

		return $this->response_data->nextPageToken ?? null;
	}


}
