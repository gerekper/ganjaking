<?php
/**
 * WooCommerce Intuit Payments
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Intuit Payments to newer
 * versions in the future. If you wish to customize WooCommerce Intuit Payments for your
 * needs please refer to http://docs.woothemes.com/document/intuit-qbms/
 *
 * @package   WC-Intuit-Payments/Gateway
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2019, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Intuit\Handlers;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_4_3 as Framework;

/**
 * Connection handler for OAuth v1.0 connections.
 *
 * @since 2.4.0
 */
class Connection_v1 extends Connection {


	/**
	 * Redirects to Intuit for authorization.
	 *
	 * @since 2.4.0
	 *
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function connect_redirect() {

		$response = $this->get_gateway()->get_api()->oauth_get_request_token( add_query_arg( [
			'wc-api' => $this->get_authorize_action_name(),
		], home_url() ) );

		if ( ! $response->get_token() ) {
			throw new Framework\SV_WC_Plugin_Exception( 'Could not get token.' );
		}

		if ( ! setcookie( 'wc_' . $this->get_gateway()->get_id() . '_oauth_token_secret', $response->get_token_secret(), 0, '/' ) ) {
			throw new Framework\SV_WC_Plugin_Exception( 'Could not set token secret cookie.' );
		}

		wp_redirect( add_query_arg( 'oauth_token', $response->get_token(), 'https://appcenter.intuit.com/Connect/Begin' ) );
		exit;
	}


	/**
	 * Handles the initial OAuth connection response.
	 *
	 * @internal
	 *
	 * @since 2.4.0
	 */
	public function authorize() {

		try {

			$token    = Framework\SV_WC_Helper::get_request( 'oauth_token' );
			$secret   = $_COOKIE[ 'wc_' . $this->get_gateway()->get_id() . '_oauth_token_secret' ];
			$verifier = Framework\SV_WC_Helper::get_request( 'oauth_verifier' );

			if ( ! $token ) {
				throw new Framework\SV_WC_API_Exception( 'Access token missing.' );
			}

			if ( ! $secret ) {
				throw new Framework\SV_WC_API_Exception( 'Could not find token secret cookie.' );
			}

			if ( ! $verifier ) {
				throw new Framework\SV_WC_API_Exception( 'Access verifier missing.' );
			}

			$response = $this->get_gateway()->get_api()->oauth_get_access_token( $token, $secret, $verifier );

			// store the encrypted tokens
			$this->set_access_token( $response->get_token() );
			$this->set_token_secret( $response->get_token_secret() );
			$this->set_access_token_expiry();

			$this->reset_cron_reconnect();

			$this->handle_oauth_connect_success();

		} catch ( Framework\SV_WC_Plugin_Exception $e ) {

			$this->handle_oauth_connect_error( $e );
		}
	}


	/**
	 * Resets the cron reconnect event.
	 *
	 * @since 2.4.0
	 */
	public function reset_cron_reconnect() {

		$expiry_date = time() + $this->get_token_max_age();

		wp_clear_scheduled_hook( 'wc_' . $this->get_gateway()->get_plugin()->get_id() . '_cron_reconnect', [ $this->get_gateway()->get_id() ] );

		wp_schedule_single_event( $expiry_date - $this->get_token_reconnect_window(), 'wc_' . $this->get_gateway()->get_plugin()->get_id() . '_cron_reconnect', [ $this->get_gateway()->get_id() ] );
	}


	/**
	 * Disconnects the gateway.
	 *
	 * @since 2.4.0
	 */
	public function disconnect() {

		try {

			$this->get_gateway()->get_api()->oauth_disconnect();

		} catch ( Framework\SV_WC_API_Exception $e ) {

			$this->get_gateway()->get_plugin()->log( 'Could not disconnect. ' . $e->getMessage(), $this->get_gateway()->get_id() );
		}

		wp_clear_scheduled_hook( 'wc_' . $this->get_gateway()->get_plugin()->get_id() . '_cron_reconnect', [ $this->get_gateway()->get_id() ] );

		$this->set_token_secret( '' );

		parent::disconnect();
	}


	/** Conditional methods *******************************************************************************************/


	/**
	 * Determines if the connection can be reconnected.
	 *
	 * @since 2.4.0
	 *
	 * @return bool
	 */
	public function can_reconnect() {

		return time() > ( $this->get_access_token_expiry() - $this->get_token_reconnect_window() );
	}


	/** Setter methods ************************************************************************************************/


	/**
	 * Sets the access token.
	 *
	 * @since 2.4.0
	 *
	 * @param string $token raw access token
	 * @return bool
	 */
	public function set_access_token( $token ) {

		$token = $this->get_gateway()->get_plugin()->encrypt_credential( $token );

		return parent::set_access_token( $token );
	}


	/**
	 * Sets the access token expiry timestamp.
	 *
	 * @since 2.4.0
	 *
	 * @param int|null $expiry expiry time, or null to calculate based on the maximum token age
	 * @return bool
	 */
	public function set_access_token_expiry( $expiry = null ) {

		if ( null === $expiry ) {
			$expiry = time() + $this->get_token_max_age();
		}

		return parent::set_access_token_expiry( $expiry );
	}


	/**
	 * Sets the access token secret.
	 *
	 * @since 2.4.0
	 *
	 * @param string $value raw token secret
	 * @return bool
	 */
	public function set_token_secret( $value ) {

		$value = $this->get_gateway()->get_plugin()->encrypt_credential( $value );

		return parent::set_token_secret( $value );
	}


	/** Getter methods ************************************************************************************************/


	/**
	 * Gets the OAuth flow "authorize" action name.
	 *
	 * Overridden so that we can pass it as the gateway ID instead of plugin ID. OAuth 2 requires the plugin ID and is
	 * configured in the developer app. OAuth 1 doesn't require the redirect URL be configured beforehand so it can be
	 * dynamic.
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	public function get_authorize_action_name() {

		return 'wc_' . $this->get_gateway()->get_id() . '_auth';
	}


	/**
	 * Gets the access token.
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	public function get_access_token() {

		$token = parent::get_access_token();

		return $this->get_gateway()->get_plugin()->decrypt_credential( $token );
	}


	/**
	 * Gets the token secret.
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	public function get_token_secret() {

		$secret = parent::get_token_secret();

		return $this->get_gateway()->get_plugin()->decrypt_credential( $secret );
	}


	/**
	 * Gets the max valid token age, in seconds.
	 *
	 * @since 2.4.0
	 *
	 * @return int
	 */
	protected function get_token_max_age() {

		/**
		 * Filters the oAuth token expiration age, in seconds.
		 *
		 * Currently, the Payments API specifies 180 days.
		 *
		 * @since 2.0.0
		 *
		 * @param int $age the oAuth token expiration age, in seconds. Default: 15552000
		 * @param \WC_Gateway_Inuit_Payments $gateway the gateway object
		 */
		return (int) apply_filters( 'wc_intuit_payments_oauth_token_max_age', 180 * DAY_IN_SECONDS, $this );
	}


	/**
	 * Gets the window in which tokens can be regenerated, in seconds.
	 *
	 * @since 2.4.0
	 *
	 * @return int
	 */
	protected function get_token_reconnect_window() {

		/**
		 * Filters the window in which tokens can be regenerated, in seconds.
		 *
		 * Currently, the Payments API specifies 30 days. We default to 29 as
		 * recommended to be safe.
		 *
		 * @since 2.0.0
		 *
		 * @param int $window the window in which tokens can be regenerated, in seconds. Default: 2505600
		 * @param \WC_Gateway_Inuit_Payments $gateway the gateway object
		 */
		return (int) apply_filters( 'wc_intuit_payments_oauth_token_reconnect_window', 29 * DAY_IN_SECONDS, $this );
	}


}
