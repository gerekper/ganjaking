<?php
/**
 * WooCommerce Social Login
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Social Login to newer
 * versions in the future. If you wish to customize WooCommerce Social Login for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-social-login/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Social_Login;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * Social Login REST API handler.
 *
 * @since 2.6.0
 *
 * @method \WC_Social_Login get_plugin()
 */
class REST_API extends Framework\REST_API {


	/**
	 * Returns the data to add to the WooCommerce REST API System Status response.
	 *
	 * @since 2.6.0
	 *
	 * @return array associative array of response data
	 */
	protected function get_system_status_data() {

		$data      = parent::get_system_status_data();
		$providers = wc_social_login()->get_providers();

		if ( ! empty( $providers ) ) {

			$data['providers'] = array();

			foreach ( $providers as $provider ) {

				$data['providers'][] = array(
					'id'                         => $provider->get_id(),
					'name'                       => $provider->get_title(),
					'is_enabled'                 => $provider->is_enabled(),
					'is_configured'              => $provider->is_configured(),
					'is_redirect_uri_configured' => $provider->is_redirect_uri_configured(),
					'is_available'               => $provider->is_available(),
				);
			}
		}

		return $data;
	}


}
