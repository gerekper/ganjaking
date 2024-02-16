<?php
/**
 * Class Redsys Push Notifications
 *
 * @package WooCommerce Redsys Gateway
 * @since 18.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @link https://redsys.joseconti.com
 * @link https://woo.com/products/redsys-gateway/
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2024 José Conti.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Redsys Push Notifications
 */
class Redsys_Push_Notifications {
	/**
	 * Construct
	 */
	public function __construct() {
		$this->log = new WC_Logger();
	}
	/**
	 * Identifier
	 *
	 * @param string $option Option name.
	 */
	public function get_option( $option ) {
		$option = get_option( $option );
		if ( $option ) {
			return $option;
		}
		return false;
	}
	/**
	 * Get access token
	 */
	public function is_active() {
		$is_active = $this->get_option( 'wc_settings_tab_redsys_sort_push_is_active' );
		if ( 'yes' === $is_active ) {
			return true;
		}
		return false;
	}
	/**
	 * Get access token
	 */
	public function get_access_token() {
		$access_token = $this->get_option( 'wc_settings_tab_redsys_sort_push_access_token' );
		return $access_token;
	}
	/**
	 * Get mobile app id
	 */
	public function get_mobile_app_id() {
		$mobile_app_id = $this->get_option( 'wc_settings_tab_redsys_sort_push_mobile_app_id' );
		return (int) $mobile_app_id;
	}
	/**
	 * Identifier
	 */
	public function identifier() {
		$identifier = $this->get_option( 'wc_settings_tab_redsys_sort_push_identifier' );
		return $identifier;
	}
	/**
	 * Call
	 *
	 * @param string $message message to send.
	 * @param string $recipient recipient to send mesafe (mobile number).
	 * @param string $identifier identifier.
	 */
	public function call( $message = false, $recipient = false, $identifier = false ) {

		if ( $message && $this->is_active() ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				$this->log->add( 'pushredsys', '/******************************************/' );
				$this->log->add( 'pushredsys', '  LLega la petición a la clase Call  ' );
				$this->log->add( 'pushredsys', '/******************************************/' );
			}

			$access_token  = $this->get_access_token();
			$mobile_app_id = $this->get_mobile_app_id();
			if ( ! $identifier ) {
				$identifier = $this->identifier();
			}
			$json     = '{"mobileAppId":' . $mobile_app_id . ',"text":"' . $message . '","recipients":[{"identifier":"' . $identifier . '"}]}';
			$url      = 'https://api.catapush.com/1/messages';
			$response = wp_remote_post(
				$url,
				array(
					'body'    => $json,
					'headers' => array(
						'Accept'        => 'application/json',
						'Authorization' => 'Bearer ' . $access_token,
						'Content-Type'  => 'application/json',
					),
				)
			);
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				$this->log->add( 'pushredsys', wp_remote_retrieve_body( $response ) );
			}
		}
	}
}
