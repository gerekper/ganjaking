<?php
/**
 * MailChimp for WooCommerce Memberships
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
 * Do not edit or add to this file if you wish to upgrade MailChimp for WooCommerce Memberships to newer
 * versions in the future. If you wish to customize MailChimp for WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships-mailchimp/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2017-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\MailChimp\API;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * MailChimp API request handler to query resources.
 *
 * @since 1.0.0
 */
class Request extends Framework\SV_WC_API_JSON_Request {


	/**
	 * Build the request.
	 *
	 * @since 1.0.0
	 *
	 * @param string $method API method to use
	 * @param string $path REST path
	 */
	public function __construct( $method = 'GET', $path = '/' ) {

		$this->method = $method;
		$this->path   = $path;
	}


	/**
	 * Sets the request params.
	 *
	 * Useful for GET requests.
	 *
	 * @since 1.0.0
	 *
	 * @param array $params params to set
	 */
	public function set_params( array $params ) {

		$this->params = $params;
	}


	/**
	 * Sets the request data.
	 *
	 * Useful for POST/PUT requests.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data data to set
	 */
	public function set_data( array $data ) {

		$this->data = $data;
	}


	/**
	 * Gets request parameters.
	 *
	 * @since 1.0.7
	 *
	 * @return array
	 */
	public function get_params() {

		/**
		 * Filters the MailChimp API request parameters.
		 *
		 * @since 1.0.7
		 *
		 * @param array $params associative array of request parameters
		 */
		return (array) apply_filters( 'wc_memberships_mailchimp_api_request_params', $this->params );
	}


}
