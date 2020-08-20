<?php
/**
 * Extra Product Options License class
 *
 * @package Extra Product Options/Classes
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_UPDATE_Licenser {


	/**
	 * The single instance of the class
	 *
	 * @since 1.0
	 */
	protected static $_instance = NULL;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_tm_activate_license', array( $this, 'activate' ) );
		add_action( 'wp_ajax_tm_deactivate_license', array( $this, 'deactivate' ) );
	}

	/**
	 * Get api url
	 *
	 * @since 1.0
	 */
	public static function api_url( $array ) {
		$array1 = array(
			'https://themecomplete.com/api/activation/',
		);

		return implode( '', array_merge( $array1, $array ) );
	}

	/**
	 * Get posted variable
	 *
	 * @since 1.0
	 */
	private function get_ajax_var( $param, $default = NULL ) {
		return isset( $_POST[ $param ] ) ? $_POST[ $param ] : $default;
	}

	/**
	 * Get saved license
	 *
	 * @since 1.0
	 */
	public function get_license() {
		return get_option( 'tm_license_activation_key' );
	}

	/**
	 * Check license
	 *
	 * @since 1.0
	 */
	public function check_license() {
		$a1 = $this->get_license();
		$a2 = get_option( 'tm_epo_envato_username' );
		$a3 = get_option( 'tm_epo_envato_apikey' );
		$a4 = get_option( 'tm_epo_envato_purchasecode' );

		return (
			! empty( $a1 ) &&
			! empty( $a2 ) &&
			! empty( $a3 ) &&
			! empty( $a4 )
		);
	}

	/**
	 * Activate license
	 *
	 * @since 1.0
	 */
	public function activate() {
		$this->request( 'activation' );
	}

	/**
	 * Deactivate license
	 *
	 * @since 1.0
	 */
	public function deactivate() {
		$this->request( 'deactivation' );
	}

	/**
	 * perform a request
	 *
	 * @since 1.0
	 */
	public function request( $action = '' ) {
		check_ajax_referer( 'settings-nonce', 'security' );
		$params             = array();
		$params['username'] = $this->get_ajax_var( 'username' );
		$params['key']      = $this->get_ajax_var( 'key' );
		$params['api_key']  = $this->get_ajax_var( 'api_key' );
		$params['url']      = get_site_url();
		$params['plugin']   = THEMECOMPLETE_EPO_PLUGIN_ID;
		$params['license']  = $this->get_license();
		$params['action']   = $action;
		$string             = 'activation.php?';
		$message_wrap       = '<div class="%s"><p>%s</p></div>';

		$request_url = self::api_url( array( $string, http_build_query( $params, '', '&' ) ) );
		$response    = wp_remote_get( $request_url, array( 'timeout' => 300 ) );

		if ( is_wp_error( $response ) ) {
			wp_send_json( array( 'result' => 'wp_error', 'message' => sprintf( $message_wrap, 'error', esc_html__( 'Error connecting to server!', 'woocommerce-tm-extra-product-options' ) ) ) );
			die();
		}

		$result = json_decode( $response['body'] );

		if ( ! is_object( $result ) ) {
			wp_send_json( array( 'result' => 'server_error', 'message' => sprintf( $message_wrap, 'error', esc_html__( 'Error getting data from the server!', 'woocommerce-tm-extra-product-options' ) ) ) );
			die();
		}

		if ( (boolean) $result->result === TRUE && $result->key && $result->code && $result->code == "200" ) {
			$license = $result->key;
			if ( $action == 'activation' ) {
				update_option( 'tm_epo_envato_username', $params['username'] );
				update_option( 'tm_epo_envato_apikey', $params['api_key'] );
				update_option( 'tm_epo_envato_purchasecode', $params['key'] );

				update_option( 'tm_license_activation_key', $license );

				delete_site_transient( 'update_plugins' );

				wp_send_json( array( 'result' => '4', 'message' => sprintf( $message_wrap, 'activated', esc_html__( 'License activated!', 'woocommerce-tm-extra-product-options' ) ) ) );
			} elseif ( $action == 'deactivation' ) {
				delete_option( 'tm_license_activation_key' );
				delete_site_transient( 'update_plugins' );
				wp_send_json( array( 'result' => '4', 'message' => sprintf( $message_wrap, 'deactivated', esc_html__( 'License deactivated!', 'woocommerce-tm-extra-product-options' ) ) ) );
			}
			die();
		}

		if ( (boolean) $result->result === FALSE ) {
			$message = esc_html__( 'Invalid data!', 'woocommerce-tm-extra-product-options' );
			$status  = 'error';
			$rs      = $result->code;
			if ( ! empty( $rs ) ) {
				switch ( $result->code ) {
					case "1":
						$message = esc_html__( 'Invalid action.', 'woocommerce-tm-extra-product-options' );
						break;
					case "2":
						$message = esc_html__( 'Please fill all fields before trying to activate.', 'woocommerce-tm-extra-product-options' );
						break;
					case "3":
						$message = esc_html__( 'Trying to activate from outside the plugin interface is not allowed!', 'woocommerce-tm-extra-product-options' );
						break;
					case "4":
						$message = esc_html__( 'Error connecting to Envato API. Please try again later.', 'woocommerce-tm-extra-product-options' );
						break;
					case "5":
						$message = esc_html__( 'Trying to activate with an invalid purchase code for your token!', 'woocommerce-tm-extra-product-options' );
						break;
					case "50":
						$message = esc_html__( 'Envato rate limit exceeded! Please try again later.', 'woocommerce-tm-extra-product-options' );
						break;
					case "6":
						$message = esc_html__( 'That username is not valid for this item purchase code. Please make sure you entered the correct username (case sensitive).', 'woocommerce-tm-extra-product-options' );
						break;
					case "7":
						$message = esc_html__( 'Trying to activate from an invalid domain!', 'woocommerce-tm-extra-product-options' );
						break;
					case "8":
						$message = esc_html__( 'Trying to activate from an invalid IP address!', 'woocommerce-tm-extra-product-options' );
						break;
					case "9":
						$message = esc_html__( 'The purchase code is already activated!', 'woocommerce-tm-extra-product-options' );//by another username
						break;
					case "10":
						$message = esc_html__( 'The purchase code is already activated on another domain!', 'woocommerce-tm-extra-product-options' );
						break;
					case "11":
						$message = esc_html__( 'You have already activated that purchase code on another domain!', 'woocommerce-tm-extra-product-options' );
						break;
					case "12":
						$message = esc_html__( 'The purchase code is already activated! Please buy a valid license!', 'woocommerce-tm-extra-product-options' );
						break;
					case "13":
						$status  = 'updated';
						$message = esc_html__( 'You have already activated your purchase code!', 'woocommerce-tm-extra-product-options' );
						break;
					case "14":
						$message = esc_html__( 'Deactivated, but the purchase code was not activated for some reason!', 'woocommerce-tm-extra-product-options' );
						delete_option( 'tm_license_activation_key' );
						delete_site_transient( 'update_plugins' );
						wp_send_json( array( 'result' => '4', 'message' => sprintf( $message_wrap, 'updated', esc_html__( 'License deactivated!', 'woocommerce-tm-extra-product-options' ) ) ) );
						die();
						break;
					case "15":
						$status  = 'updated';
						$message = esc_html__( 'Cannot deactivate. Purchase code is not valid for your saved license key!', 'woocommerce-tm-extra-product-options' );
						break;
				}
			}
			wp_send_json( array( 'result' => '-2', 'code' => $result->code, 'message' => sprintf( $message_wrap, $status, $message ) ) );
			die();
		}
		wp_send_json( array( 'result' => '-3', 'message' => sprintf( $message_wrap, 'error', esc_html__( 'Could not complete request!', 'woocommerce-tm-extra-product-options' ) ) ) );
		die();
	}

}

