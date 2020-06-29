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
 * Admin User Memberships class.
 *
 * @since 1.0.0
 */
class Response extends Framework\SV_WC_API_JSON_Response {


	/**
	 * Determines if the response contains an error.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function has_api_error() {

		// note: some responses such as the one for a list subscriber include a 'status' field, but that's for the subscription status
		return isset( $this->response_data->status ) && is_numeric( $this->response_data->status ) && 200 !== (int) $this->response_data->status;
	}


	/**
	 * Gets the error status code, if any.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_error_status() {

		return (int) $this->response_data->status;
	}


	/**
	 * Gets the error title, if any.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_error_title() {

		return $this->response_data->title;
	}


	/**
	 * Gets the error detail, if any.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_error_detail() {

		return $this->response_data->detail;
	}


}
