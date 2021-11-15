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
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

// load base class
if ( ! class_exists( 'Hybrid_Providers_Yahoo' ) ) {
	require_once( \Hybrid_Auth::$config['path_providers'] . 'Yahoo.php' );
}


/**
 * Yahoo provider for HybridAuth tailored for WP
 *
 * @since 2.0.0
 */
class SV_Hybrid_Providers_Yahoo extends \Hybrid_Providers_Yahoo {


	/**
	 * Initialize the provider.
	 *
	 * This method is largely the same as in the original provider with the
	 * exception of using the WP_OAuth2_Client instead of the built-in OAuth2Client class
	 *
	 * @since 2.0.0
	 */
	public function initialize() {

		parent::initialize();

		// include OAuth2 client
		require_once( \Hybrid_Auth::$config['path_libraries'] . 'OAuth/OAuth2Client.php' );
		require_once( wc_social_login()->get_plugin_path() . '/src/hybridauth/class-wp-oauth2-client.php' );

		// create a new OAuth2 client instance
		$api = new \WP_OAuth2_Client( $this->config['keys']['id'], $this->config['keys']['secret'], $this->endpoint, $this->compressed );

		// list of api props to copy over from the original client
		$props = array( 'access_token', 'refresh_token', 'access_token_expires_in', 'access_token_expires_at', 'curl_proxy', 'api_base_url', 'authorize_url', 'token_url', 'curl_header' );

		foreach ( $props as $prop ) {
			if ( isset( $this->api->{$prop} ) ) {
				$api->{$prop} = $this->api->{$prop};
			}
		}

		// override the api client with our own
		$this->api = $api;
	}

}
