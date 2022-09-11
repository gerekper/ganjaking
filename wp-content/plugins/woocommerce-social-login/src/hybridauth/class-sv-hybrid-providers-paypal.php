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
if ( ! class_exists( 'Hybrid_Providers_PayPal' ) ) {
	// TODO since v2.10.0 HybridAuth moved to use the official PayPal SDK library, which requires a refactoring of our implementation {FN 2018-08-08}
	/* @see https://github.com/hybridauth/hybridauth/pull/857 */
	/* @link https://hybridauth.github.io/hybridauth/userguide/IDProvider_info_PayPal.html */
	require_once( wc_social_login()->get_plugin_path() . '/src/hybridauth/class-sv-hybrid-providers-paypal-legacy.php' );
	// TODO when updating to use the PayPal SDK the following may be used instead {FN 2018-08-08}
	// require_once( \Hybrid_Auth::$config['path_providers'] . 'Paypal.php' );
}


/**
 * PayPal provider for HybridAuth tailored for WP
 *
 * @since 2.0.0
 * @link https://developer.paypal.com/docs/api/auth-headers/
 * @link https://www.sitepoint.com/implement-user-log-paypal/
 */
class SV_Hybrid_Providers_PayPal extends \Hybrid_Providers_PayPal {


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

		// Set environment
		if ( isset( $this->config['sandbox'] ) ) {
			$this->sandbox = $this->config['sandbox'];
		}

		// include OAuth2 client
		require_once( \Hybrid_Auth::$config['path_libraries'] . 'OAuth/OAuth2Client.php' );
		require_once( wc_social_login()->get_plugin_path() . '/src/hybridauth/class-wp-oauth2-client.php' );
		// TODO remove the following line when updating to use the PayPal SDK {FN 2018-08-08}
		require_once( wc_social_login()->get_plugin_path() . '/src/hybridauth/class-sv-hybrid-providers-paypal-legacy-oauth2client.php' );

		// create a new OAuth2 client instance
		$this->api = new \WP_OAuth2_Client( $this->config['keys']['id'], $this->config['keys']['secret'], $this->endpoint, $this->compressed );

		if ( $this->sandbox ) {

			$this->api->authorize_url  = "https://www.sandbox.paypal.com/signin/authorize";
			$this->api->token_url      = "https://api.sandbox.paypal.com/v1/oauth2/token";
			$this->api->token_info_url = "https://api.sandbox.paypal.com/v1/identity/openidconnect/tokenservice";

		} else {

			$this->api->authorize_url  = "https://www.paypal.com/signin/authorize";
			$this->api->token_url      = "https://api.paypal.com/v1/oauth2/token";
			$this->api->token_info_url = "https://api.paypal.com/v1/identity/openidconnect/tokenservice";
		}

		// paypal requires the client id & secrett in the auth header
		$this->api->request_headers = array(
			'Accept'          => 'application/json',
			'Accept-Language' => 'en_US',
			'Authorization'   => 'Basic ' . base64_encode( $this->config['keys']['id'] . ':' . $this->config['keys']['secret'] ),
		);

		// If we have an access token, set it
		if ( $this->token( 'access_token' ) ) {
			$this->api->access_token            = $this->token('access_token');
			$this->api->refresh_token           = $this->token('refresh_token');
			$this->api->access_token_expires_in = $this->token('expires_in');
			$this->api->access_token_expires_at = $this->token('expires_at');
		}
	}


	/**
	 * Load the user profile from the IDp API client
	 *
	 * Overwritten here because the original method is probably outdated, as it fails on
	 * a valid response.
	 *
	 * @since 2.0.0
	 * @return array
	 * @throws Exception
	 */
	public function getUserProfile() {

		// refresh tokens if needed
		$this->refreshToken();

		// ask PayPal api for user info
		$response = $this->api->api( 'https://api' . ( $this->sandbox ? '.sandbox' : '' ) . '.paypal.com/v1/identity/openidconnect/userinfo/?schema=openid' );

		if ( ! isset( $response->user_id ) || isset( $response->message ) ) {
			throw new \Exception( "User profile request failed! {$this->providerId} returned an invalid response.", 6 );
		}

		$this->user->profile->identifier    = ( property_exists( $response, 'user_id' ) )        ? $response->user_id : '';
		$this->user->profile->firstName     = ( property_exists( $response, 'given_name' ) )     ? $response->given_name : '';
		$this->user->profile->lastName      = ( property_exists( $response, 'family_name' ) )    ? $response->family_name : '';
		$this->user->profile->displayName   = ( property_exists( $response, 'name' ) )           ? $response->name : '';
		$this->user->profile->photoURL      = ( property_exists( $response, 'picture' ) )        ? $response->picture : '';
		$this->user->profile->gender        = ( property_exists( $response, 'gender' ) )         ? $response->gender : '';
		$this->user->profile->email         = ( property_exists( $response, 'email' ) )          ? $response->email : '';
		$this->user->profile->emailVerified = ( property_exists( $response, 'email_verified' ) ) ? $response->email_verified : '';
		$this->user->profile->language      = ( property_exists( $response, 'locale' ) )         ? $response->locale : '';
		$this->user->profile->phone         = ( property_exists( $response, 'phone_number' ) )   ? $response->phone_number : '';

		if ( property_exists( $response, 'address' ) ) {

			$address = $response->address;

			$this->user->profile->address   = ( property_exists( $address, 'street_address' ) ) ? $address->street_address : '';
			$this->user->profile->city      = ( property_exists( $address, 'locality' ) )       ? $address->locality : '';
			$this->user->profile->zip       = ( property_exists( $address, 'postal_code' ) )    ? $address->postal_code : '';
			$this->user->profile->country   = ( property_exists( $address, 'country' ) )        ? $address->country : '';
			$this->user->profile->region    = ( property_exists( $address, 'region' ) )         ? $address->region : '';
		}

		if ( property_exists( $response, 'birthdate' ) ) {

			if ( false === strpos( $response->birthdate, '-' ) ) {
				if ( '0000' !== $response->birthdate ) {

					$this->user->profile->birthYear = (int) $response->birthdate;
				}
			} else {

				list( $birthday_year, $birthday_month, $birthday_day ) = explode( '-', $response->birthdate );

				$this->user->profile->birthDay   = (int) $birthday_day;
				$this->user->profile->birthMonth = (int) $birthday_month;

				if ( '0000' !== $birthday_year ) {
						$this->user->profile->birthYear  = (int) $birthday_year;
				}
			}
		}

		return $this->user->profile;
	}


}
