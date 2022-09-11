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
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

// load base class
if ( ! class_exists( 'Hybrid_Providers_Google' ) ) {
	require_once( \Hybrid_Auth::$config['path_providers'] . 'Google.php' );
}


/**
 * Google provider for HybridAuth tailored for WP
 *
 * @since 2.0.0
 */
class SV_Hybrid_Providers_Google extends \Hybrid_Providers_Google {


	/**
	 * Initialize the provider.
	 *
	 * This method is largely the same as in the original provider with the
	 * exception of using the WP_OAuth2_Client instead of the built-in OAuth2Client class
	 *
	 * @since 2.0.0
	 */
	public function initialize() {

		if ( ! $this->config['keys']['id'] || ! $this->config['keys']['secret'] ) {
			throw new \Exception( "Your application id and secret are required in order to connect to {$this->providerId}.", 4 );
		}

		// override requested scope
		if ( isset( $this->config['scope'] ) && ! empty( $this->config['scope'] ) ) {
			$this->scope = $this->config['scope'];
		}

		// Override the redirect uri when it's set in the config parameters. This way we prevent
		// redirect uri mismatches when authenticating with Google.
		if ( isset( $this->config['redirect_uri'] ) && ! empty( $this->config['redirect_uri'] ) ) {
			$this->api->redirect_uri = $this->config['redirect_uri'];
		}

		// include OAuth2 client
		require_once( \Hybrid_Auth::$config['path_libraries'] . 'OAuth/OAuth2Client.php' );
		require_once( wc_social_login()->get_plugin_path() . '/src/hybridauth/class-wp-oauth2-client.php' );

		// create a new OAuth2 client instance
		$this->api = new \WP_OAuth2_Client( $this->config['keys']['id'], $this->config['keys']['secret'], $this->endpoint, $this->compressed );

		$this->api->authorize_url   = 'https://accounts.google.com/o/oauth2/auth';
		$this->api->token_url       = 'https://accounts.google.com/o/oauth2/token';
		$this->api->token_info_url  = 'https://www.googleapis.com/oauth2/v2/tokeninfo';

		// If we have an access token, set it
		if ( $this->token( 'access_token' ) ) {
			$this->api->access_token            = $this->token('access_token');
			$this->api->refresh_token           = $this->token('refresh_token');
			$this->api->access_token_expires_in = $this->token('expires_in');
			$this->api->access_token_expires_at = $this->token('expires_at');
		}

		$this->api->request_headers = array( 'Authorization' => 'OAuth ' . $this->api->access_token );
	}


	/**
	 * Get User profile from Google
	 *
	 * Overridden here because HybridAuth by default uses the Google Sign-In API
	 * for fetching user profile. However, this requires the app owner to
	 * explicitly enable the Google Sign-In API on their dev console, which is something
	 * we don't want to force upgrading users to do.
	 *
	 * {@inheritdoc}
	 */
	function getUserProfile() {

		// refresh tokens if needed
		$this->refreshToken();

		$response = $this->api->api( 'https://www.googleapis.com/oauth2/v1/userinfo' );

		if ( ! isset( $response->id ) || isset( $response->error ) ) {
			throw new \Exception( "User profile request failed! {$this->providerId} returned an invalid response:" . \Hybrid_Logger::dumpData( $response ), 6 );
		}

		$this->user->profile->identifier    = $response->id;
		$this->user->profile->firstName     = ( property_exists( $response, 'given_name' ) )     ? $response->given_name : '';
		$this->user->profile->lastName      = ( property_exists( $response, 'family_name' ) )    ? $response->family_name : '';
		$this->user->profile->displayName   = ( property_exists( $response, 'name' ) )           ? $response->name : '';
		$this->user->profile->photoURL      = ( property_exists( $response, 'picture' ) )        ? $response->picture : '';
		$this->user->profile->profileURL    = ( property_exists( $response, 'link' ) )           ? $response->link : '';
		$this->user->profile->gender        = ( property_exists( $response, 'gender' ) )         ? $response->gender : '';
		$this->user->profile->language      = ( property_exists( $response, 'locale' ) )         ? $response->locale : '';
		$this->user->profile->email         = ( property_exists( $response, 'email' ) )          ? $response->email : '';
		$this->user->profile->emailVerified = ( property_exists( $response, 'verified_email' ) ) ? $response->email : ''; // verified_email is a boolean

		return $this->user->profile;
	}


}
