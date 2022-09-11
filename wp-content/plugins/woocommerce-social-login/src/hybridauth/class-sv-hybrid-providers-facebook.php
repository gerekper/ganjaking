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


/**
 * Facebook provider for HybridAuth tailored for WP
 *
 * @since 2.0.0
 */
class SV_Hybrid_Providers_Facebook extends \Hybrid_Provider_Model_OAuth2 {


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

		// include OAuth2 client
		require_once( \Hybrid_Auth::$config['path_libraries'] . 'OAuth/OAuth2Client.php' );
		require_once( wc_social_login()->get_plugin_path() . '/src/hybridauth/class-wp-oauth2-client.php' );

		// create a new OAuth2 client instance
		$this->api = new \WP_OAuth2_Client( $this->config['keys']['id'], $this->config['keys']['secret'], $this->endpoint, $this->compressed );

		$this->api->api_base_url    = 'https://graph.facebook.com/v2.12/';
		$this->api->authorize_url   = 'https://www.facebook.com/v2.12/dialog/oauth';
		$this->api->token_url       = 'https://graph.facebook.com/oauth/access_token';

		// If we have an access token, set it
		if ( $this->token( 'access_token' ) ) {
			$this->api->access_token            = $this->token('access_token');
			$this->api->refresh_token           = $this->token('refresh_token');
			$this->api->access_token_expires_in = $this->token('expires_in');
			$this->api->access_token_expires_at = $this->token('expires_at');
		}

		if ( empty( $this->config['fields'] ) ) {
			$this->config['fields'] = array(
				'id',
				'name',
				'first_name',
				'last_name',
				'link',
				'website',
				'gender',
				'locale',
				'about',
				'email',
				'hometown',
				'location',
				'birthday',
			);
		}
	}


	/**
	 * Load the user profile from the IDp API client
	 *
	 * @since 2.0.0
	 * @return array profile
	 * @throws Exception
	 */
	public function getUserProfile() {

		$data = $this->api->get( 'me?fields=' . implode( ',', $this->config['fields'] ) , array( 'app_id' => $this->api->client_id, 'app_secret' => $this->api->client_secret ) );

		// if the provider identifier is not received, we assume the auth has failed
		if ( ! isset( $data->id ) ) {
			throw new \Exception( "User profile request failed! {$this->providerId} api returned an invalid response: " . \Hybrid_Logger::dumpData( $data ), 6 );
		}

		# store the user profile.
		$this->user->profile->identifier    = ( property_exists ( $data, 'id' ) )         ? $data->id : '';
		$this->user->profile->username      = ( property_exists ( $data, 'username' ) )   ? $data->username : '';
		$this->user->profile->displayName   = ( property_exists ( $data, 'name' ) )       ? $data->name : '';
		$this->user->profile->firstName     = ( property_exists ( $data, 'first_name' ) ) ? $data->first_name : '';
		$this->user->profile->lastName      = ( property_exists ( $data, 'last_name' ) )  ? $data->last_name : '';
		$this->user->profile->profileURL    = ( property_exists ( $data, 'link' ) )       ? $data->link : '';
		$this->user->profile->webSiteURL    = ( property_exists ( $data, 'website' ) )    ? $data->website : '';
		$this->user->profile->gender        = ( property_exists ( $data, 'gender' ) )     ? $data->gender : '';
		$this->user->profile->language      = ( property_exists ( $data, 'locale' ) )     ? $data->locale : '';
		$this->user->profile->description   = ( property_exists ( $data, 'about' ) )      ? $data->about : '';
		$this->user->profile->email         = ( property_exists ( $data, 'email' ) )      ? $data->email : '';
		$this->user->profile->emailVerified = ( property_exists ( $data, 'email' ) )      ? $data->email : '';
		$this->user->profile->region        = ( property_exists ( $data, 'hometown') && property_exists( $data->hometown, 'name' ) ) ? $data->hometown->name : '';
		$this->user->profile->photoURL      = 'https://graph.facebook.com/' . $this->user->profile->identifier . '/picture?width=150&height=150';

		if ( ! empty( $this->user->profile->region ) ) {

			$region_parts = explode( ',', $this->user->profile->region );

			if ( count( $region_parts ) > 1 ) {
				$this->user->profile->city    = trim( $region_parts[0] );
				$this->user->profile->country = trim( $region_parts[1] );
			}
		}

		if ( property_exists ( $data, 'birthday' ) ) {

			list( $birthday_month, $birthday_day, $birthday_year ) = explode( '/', $data->birthday );

			$this->user->profile->birthDay   = (int) $birthday_day;
			$this->user->profile->birthMonth = (int) $birthday_month;
			$this->user->profile->birthYear  = (int) $birthday_year;
		}

		return $this->user->profile;
	}


}
