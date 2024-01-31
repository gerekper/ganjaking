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
 * @copyright Copyright (c) 2015-2024, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\API\Admin_API;

use stdClass;

defined( 'ABSPATH' ) or exit;

/**
 * Handles responses from the Google Analytics Admin API Measurement Protocol Secrets routes.
 *
 * @link https://developers.google.com/analytics/devguides/config/admin/v1/rest/v1beta/properties.dataStreams.measurementProtocolSecrets
 *
 * @since 2.0.0
 */
class Measurement_Protocol_Secrets_Response extends Response {


	/**
	 * Returns a list of measurement protocol API secrets available to the Data Stream property specified in the request
	 *
	 * @link https://developers.google.com/analytics/devguides/config/admin/v1/rest/v1beta/properties.dataStreams.measurementProtocolSecrets/list
	 *
	 * @since 2.0.0
	 *
	 * @return stdClass[] array of objects representing measurement protocol secrets
	 */
	public function list_measurement_protocol_secrets(): array {

		$secrets = [];

		if ( isset( $this->response_data->measurementProtocolSecrets ) ) {
			$secrets = (array) $this->response_data->measurementProtocolSecrets;
		}

		return $secrets;
	}


	/**
	 * Returns a measurement protocol API secret.
	 *
	 * @link https://developers.google.com/analytics/devguides/config/admin/v1/rest/v1beta/properties.dataStreams.measurementProtocolSecrets/create
	 * @link https://developers.google.com/analytics/devguides/config/admin/v1/rest/v1beta/properties.dataStreams.measurementProtocolSecrets/get
	 * @link https://developers.google.com/analytics/devguides/config/admin/v1/rest/v1beta/properties.dataStreams.measurementProtocolSecrets/patch
	 *
	 * @since 2.0.0
	 *
	 * @return stdClass an object representing a measurement protocol secret
	 */
	public function get_measurement_protocol_secret(): stdClass {

		return $this->response_data;
	}


	/**
	 * Gets the string representation of this response with any and all sensitive elements masked or removed.
	 *
	 * @see SV_WC_API_Response::to_string_safe()
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function to_string_safe(): string {

		return preg_replace( '/("secretValue":\s*")(?:\"|[^"])*(")/i','$1***$2', parent::to_string_safe() );
	}


}
