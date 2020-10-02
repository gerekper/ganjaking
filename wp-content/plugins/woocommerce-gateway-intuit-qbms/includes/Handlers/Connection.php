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
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Intuit\Handlers;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_8_1 as Framework;

class Connection {


	/** @var \WC_Gateway_Inuit_Payments payment gateway object */
	private $gateway;


	/**
	 * Connection constructor.
	 *
	 * @since 2.4.0
	 *
	 * @param \WC_Gateway_Inuit_Payments $gateway
	 */
	public function __construct( \WC_Gateway_Inuit_Payments $gateway ) {

		$this->gateway = $gateway;

		$this->add_hooks();
	}


	/**
	 * Adds the action and filter hooks.
	 *
	 * @since 2.4.0
	 */
	protected function add_hooks() {

		add_action( 'woocommerce_api_' . $this->get_connect_action_name(), [ $this, 'connect' ] );
		add_action( 'woocommerce_api_' . $this->get_authorize_action_name(), [ $this, 'authorize' ] );

		add_action( 'wp_ajax_' . $this->get_disconnect_action_name(), [ $this, 'handle_disconnect' ] );
	}


	/**
	 * Handles starting the connect process.
	 *
	 * @internal
	 *
	 * @since 2.4.0
	 */
	public function connect() {

		try {

			if ( ! current_user_can( 'manage_woocommerce') || ! wp_verify_nonce( $_GET['nonce'], $this->get_connect_action_name() ) ) {
				throw new Framework\SV_WC_Plugin_Exception( 'Invalid nonce' );
			}

			$this->connect_redirect();

		} catch ( Framework\SV_WC_Plugin_Exception $e ) {

			$this->handle_oauth_connect_error( $e );
		}
	}


	/**
	 * Redirects to Intuit for authorization.
	 *
	 * @since 2.4.0
	 */
	protected function connect_redirect() {

		wp_redirect( $this->get_intuit_url() );
		exit;
	}


	/**
	 * Handles the OAuth 2 response.
	 *
	 * @internal
	 *
	 * @since 2.4.0
	 */
	public function authorize() {

		list( $state, $gateway_id ) = explode( '.', Framework\SV_WC_Helper::get_requested_value( 'state' ) );

		// both gateways hook into this action, so make sure this is the gateway that started the OAuth flow
		if ( $gateway_id !== $this->get_gateway()->get_id() ) {
			return;
		}

		try {

			if ( ! hash_equals( $state, hash_hmac( 'sha256', md5( wp_salt(), true ), $this->get_gateway()->get_client_secret() ) ) ) {
				throw new Framework\SV_WC_API_Exception( 'Connection state is invalid.' );
			}

			if ( $error = Framework\SV_WC_Helper::get_requested_value( 'error' ) ) {

				switch ( $error ) {

					case 'access_denied':
						$message = 'The user did not authorize the request.';
					break;

					case 'invalid_scope':
						$message = 'An invalid scope string was sent in the request.';
					break;

					default:
						$message = "An unknown error occurred: {$error}";
				}

				throw new Framework\SV_WC_API_Exception( $message );
			}

			$code = Framework\SV_WC_Helper::get_requested_value( 'code' );

			if ( ! $code ) {
				throw new Framework\SV_WC_API_Exception( 'Authorization code is missing.' );
			}

			$response = $this->get_gateway()->get_api()->get_oauth_tokens( $code, $this->get_redirect_url() );

			$this->set_access_token( $response->get_access_token() );
			$this->set_refresh_token( $response->get_refresh_token() );
			$this->set_access_token_expiry( $response->get_access_token_expiry() );

			$this->handle_oauth_connect_success();

		} catch ( Framework\SV_WC_Plugin_Exception $e ) {

			$this->handle_oauth_connect_error( $e );
		}
	}


	/**
	 * Handles the auth window after successful auth.
	 *
	 * @since 2.4.0
	 */
	protected function handle_oauth_connect_success() {

		// clear any previous connection flags to avoid notices
		delete_option( 'wc_intuit_payments_connected' );

		echo '<script>if ( window.opener.onQuickBooksSuccessfulConnection ) { window.opener.onQuickBooksSuccessfulConnection(); } else { window.opener.location.reload(); }window.close();</script>';
		exit();
	}


	/**
	 * Handles OAuth errors.
	 *
	 * @since 2.4.0
	 *
	 * @param Framework\SV_WC_Plugin_Exception $e thrown exception
	 */
	protected function handle_oauth_connect_error( Framework\SV_WC_Plugin_Exception $e ) {

		$message = 'Could not authenticate. ' . $e->getMessage();

		$this->get_gateway()->get_plugin()->log( $message, $this->get_gateway()->get_id() );

		echo '<script>if ( window.opener.onQuickBooksConnectionFailed ) { window.opener.onQuickBooksConnectionFailed( "' . esc_js( $message ) . '" ); } else { alert( "' . esc_js( $message ) . '" ); }window.close();</script>';
		exit();
	}


	/**
	 * Handles disconnect via AJAX.
	 *
	 * @since 2.4.0
	 */
	public function handle_disconnect() {

		try {

			if ( ! current_user_can( 'manage_woocommerce' ) || ! wp_verify_nonce( Framework\SV_WC_Helper::get_posted_value( 'nonce' ), $this->get_disconnect_action_name() ) ) {
				throw new Framework\SV_WC_Plugin_Exception( 'Invalid nonce' );
			}

			$this->disconnect();

			wp_send_json_success();

		} catch ( Framework\SV_WC_Plugin_Exception $e ) {

			$this->get_gateway()->get_plugin()->log( 'Could not disconnect. ' . $e->getMessage(), $this->get_gateway()->get_id() );

			wp_send_json_error();
		}
	}


	/**
	 * Disconnects the plugin from Intuit.
	 *
	 * @since 2.4.0
	 */
	public function disconnect() {

		$this->set_access_token( '' );
		$this->set_refresh_token( '' );
		$this->set_access_token_expiry( '' );

		delete_option( 'wc_intuit_payments_connected' );
	}


	/** Conditional methods *******************************************************************************************/


	/**
	 * Determines if the connection can be reconnected.
	 *
	 * TODO: remove after 2021-01 or in v3.0.0 {DM 2020-01-14}
	 *
	 * @since 2.4.0
	 * @deprecated 2.6.2-dev.1
	 *
	 * @return bool
	 */
	public function can_reconnect() {

		wc_deprecated_function( __METHOD__, '2.6.2-dev.1' );

		return true;
	}


	/** Setter methods ************************************************************************************************/


	/**
	 * Sets the Intuit API access token.
	 *
	 * @since 2.4.0
	 *
	 * @param string $token token to set
	 * @return bool
	 */
	public function set_access_token( $token ) {

		return update_option( $this->get_token_option_name() . '_access_token', $token );
	}


	/**
	 * Gets the Intuit API refresh token.
	 *
	 * @since 2.4.0
	 *
	 * @param string $token token to set
	 * @return bool
	 */
	public function set_refresh_token( $token ) {

		return update_option( $this->get_token_option_name() . '_refresh_token', $token );
	}


	/**
	 * Sets the Intuit API token expiry.
	 *
	 * @since 2.4.0
	 *
	 * @param int $expiry expiry timestamp
	 * @return bool
	 */
	public function set_access_token_expiry( $expiry ) {

		return update_option( $this->get_token_option_name() . '_access_token_expiry', $expiry );
	}


	/**
	 * Sets the token secret.
	 *
	 * This is only used with OAuth v1.0.
	 *
	 * TODO: remove after 2021-01 or in v3.0.0 {DM 2020-01-14}
	 *
	 * @since 2.4.0
	 * @deprecated 2.6.2-dev.1
	 *
	 * @param $value
	 * @return bool
	 */
	public function set_token_secret( $value ) {

		wc_deprecated_function( __METHOD__, '2.6.2-dev.1' );

		return false;
	}


	/** Getter methods ************************************************************************************************/


	/**
	 * Gets the URL for starting the connect flow.
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	public function get_connect_url() {

		return add_query_arg( [
			'wc-api' => $this->get_connect_action_name(),
			'nonce'  => wp_create_nonce( $this->get_connect_action_name() ),
		], home_url() );
	}


	/**
	 * Gets the Intuit URL for connecting, with plugin args added.
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	protected function get_intuit_url() {

		return add_query_arg( array(
			'client_id'     => $this->get_gateway()->get_client_id(),
			'scope'         => 'com.intuit.quickbooks.payment',
			'redirect_uri'  => urlencode( $this->get_redirect_url() ),
			'response_type' => 'code',
			'state'         => hash_hmac( 'sha256', md5( wp_salt(), true ), $this->get_gateway()->get_client_secret() )  . '.' . $this->get_gateway()->get_id(),
		), 'https://appcenter.intuit.com/connect/oauth2' );
	}


	/**
	 * Gets the OAuth redirect URI.
	 *
	 * This tells Intuit where to redirect back to after auth, and must match
	 * the Intuit app's configured URI exactly.
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	public function get_redirect_url() {

		return add_query_arg( 'wc-api', $this->get_authorize_action_name(), home_url() );
	}


	/**
	 * Gets the "begin" action name.
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	protected function get_connect_action_name() {

		return 'wc_' . $this->get_gateway()->get_id() . '_connect';
	}


	/**
	 * Gets the "authorize" action name.
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	public function get_authorize_action_name() {

		return 'wc_intuit_payments_auth'; // this value can't change, as it's set in the merchant's account
	}


	/**
	 * Gets the "disconnect" action name.
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	public function get_disconnect_action_name() {

		return 'wc_' . $this->get_gateway()->get_id() . '_disconnect';
	}


	/**
	 * Gets the Intuit API access token.
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	public function get_access_token() {

		return get_option( $this->get_token_option_name() . '_access_token', '' );
	}


	/**
	 * Gets the Intuit API refresh token.
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	public function get_refresh_token() {

		return get_option( $this->get_token_option_name() . '_refresh_token', '' );
	}


	/**
	 * Gets the Intuit API refresh token.
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	public function get_access_token_expiry() {

		return get_option( $this->get_token_option_name() . '_access_token_expiry', '' );
	}


	/**
	 * Gets the stored access token secret.
	 *
	 * This is only used for OAuth v1.0.
	 *
	 * TODO: remove after 2021-01 or in v3.0.0 {DM 2020-01-14}
	 *
	 * @since 2.4.0
	 * @deprecated 2.6.2-dev.1
	 *
	 * @return string
	 */
	public function get_token_secret() {

		wc_deprecated_function( __METHOD__, '2.6.2-dev.1' );

		return '';
	}


	/**
	 * Gets the option name prefix for the OAuth 2.0 tokens.
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	public function get_token_option_name() {

		if ( $this->get_gateway()->inherit_settings() ) {
			$gateway_id = current( array_diff( $this->get_gateway()->get_plugin()->get_gateway_ids(), [ $this->get_gateway()->get_id() ] ) );
		} else {
			$gateway_id = $this->get_gateway()->get_id();
		}

		return "wc_{$gateway_id}";
	}


	/**
	 * Gets the gateway object.
	 *
	 * @since 2.4.0
	 *
	 * @return \WC_Gateway_Inuit_Payments
	 */
	protected function get_gateway() {

		return $this->gateway;
	}


}
