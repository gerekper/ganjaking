<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WooThemes Updater API Class
 *
 * API class for the WooThemes Updater.
 *
 * @package WordPress
 * @subpackage WooThemes Updater
 * @category Core
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * private $token
 * private $api_url
 * private $errors
 *
 * - __construct()
 * - activate()
 * - deactivate()
 * - check()
 * - request()
 * - log_request_error()
 * - store_error_log()
 * - get_error_log()
 * - clear_error_log()
 */
class WooThemes_Updater_API {
	private $token;
	private $api_url;
	private $products_api_url;
	private $errors;
	private $version;

	public function __construct () {
		$this->version = '1.7.0';
		$this->token = 'woothemes-updater';
		$this->api_url = 'https://woocommerce.com/wc-api/product-key-api';
		$this->products_api_url = 'https://woocommerce.com/wc-api/woothemes-installer-api';
		$this->license_check_url = 'https://woocommerce.com/wc-api/license-status-check';
		$this->connect_url = 'https://woocommerce.com/my-account/connect-helper';
		$this->errors = array();
	} // End __construct()

	/**
	 * Get master key info if there is a token saved.
	 * @return bool | object
	 */
	public function get_master_key_info() {
		$token = get_option( 'woothemes_helper_master_key' );

		if ( ! $token ) {
			return false;
		}

		$info = $this->master_key_request( $token );
		return $info;
	}

	/**
	 * Get info about a master key from the Woo API, like the user it belongs to and products it has access to.
	 * @param $key
	 * @return bool
	 */
	public function master_key_request( $key ) {
		if ( false === ( $response = get_transient( 'wth_master_key_info' ) ) ) {
			$response = $this->request( 'master', array( 'master_key' => $key ) );
			set_transient( 'wth_master_key_info', $response, 1 * HOUR_IN_SECONDS );
		}
		return ! isset( $response->error ) ? $response : false;
	}

	/**
	 * Delete a master key remotely.
	 * @param $key
	 * @return array|bool
	 */
	public function delete_master_key_request( $key ) {
		$response = $this->request( 'master-delete', array( 'master_key' => $key ), 'post', 10 );
		return ! isset( $response->error ) ? $response : false;
	}

	/**
	 * Activate a given license key for this installation.
	 * @since    1.0.0
	 * @param   string $key 		 	The license key to be activated.
	 * @param   string $product_id	 	Product ID to be activated.
	 * @param	string $plugin_file
	 * @param	string $method			Method, default is manual - could also be 'master'
	 * @return boolean      			Whether or not the activation was successful.
	 */
	public function activate ( $key, $product_id, $plugin_file = '', $method = 'manual' ) {
		$response = false;

		//Ensure we have a correct product id.
		$product_id = trim( $product_id );
		if( ! is_numeric( $product_id ) ){
			$plugins = get_plugins();
			$plugin_name = isset( $plugins[ $plugin_file ]['Name'] ) ? $plugins[ $plugin_file ]['Name'] : $plugin_file;
			$error = '<strong>There seems to be incorrect data for the plugin ' . $plugin_name . '. Please contact <a href="https://support.woothemes.com" target="_blank">WooCommerce Support</a> with this message.</strong>';
			$this->log_request_error( $error );
			return false;
		}

		$request = $this->request( 'activation', array( 'licence_key' => $key, 'product_id' => $product_id, 'home_url' => esc_url( home_url( '/' ) ), 'method' => $method ) );

		if ( isset( $request->error ) ) {
			return 0;
		}

		return $request;
	} // End activate()

	/**
	 * Deactivate a given license key for this installation.
	 * @since    1.0.0
	 * @param   string $key  The license key to be deactivated.
	 * @return boolean      Whether or not the deactivation was successful.
	 */
	public function deactivate ( $key ) {
		$response = false;

		$request = $this->request( 'deactivation', array( 'licence_key' => $key, 'home_url' => esc_url( home_url( '/' ) ) ) );

		return ! isset( $request->error );
	} // End deactivate()

	/**
	 * Check if the license key is valid.
	 * @since    1.0.0
	 * @param   string $key The license key to be validated.
	 * @return boolean      Whether or not the license key is valid.
	 */
	public function check ( $key ) {
		$response = false;

		$request = $this->request( 'check', array( 'licence_key' => $key ) );

		return ! isset( $request->error );
	} // End check()

	/**
	 * Check if the API is up and reachable.
	 * @since    1.2.4
	 * @return boolean Whether or not the API is up and reachable.
	 */
	public function ping () {
		$response = false;

		$request = $this->request( 'ping' );

		return isset( $request->success );
	} // End ping()

	/**
	 * Check if a product license keys are actually active for the current website.
	 * @access   public
	 * @since    1.3.0
	 * @return   boolean Whether or not the given key is actually active for the current website.
	 */
	public function product_active_statuses_check( $product_details ) {
		$args = array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'headers' => array( 'user-agent' => 'WooThemesUpdater/' . $this->version ),
			'body' => json_encode( array( 'licenses' => $product_details ) ),
			'sslverify' => false
		);

		$response = wp_remote_post( $this->license_check_url, $args );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$payload = json_decode( wp_remote_retrieve_body( $response ) );
		if ( ! isset( $payload->payload ) ) {
			return false;
		}

		return $payload->payload;
	} // End product_active_statuses_check()

	/**
	 * Make a request to the WooCommerce API.
	 *
	 * @access private
	 * @since 1.0.0
	 * @param string $endpoint (must include / prefix)
	 * @param array $params
	 * @param string $method
	 * @param int $timeout
	 * @return array $data
	 */
	private function request ( $endpoint = 'check', $params = array(), $method = 'get', $timeout = 45 ) {
		$url = $this->api_url;

		if ( in_array( $endpoint, array( 'themeupdatecheck', 'pluginupdatecheck' ) ) ) {
			$url = $this->products_api_url;
		}

		$supported_methods = array( 'check', 'activation', 'deactivation', 'ping', 'pluginupdatecheck', 'themeupdatecheck', 'master', 'master-delete' );
		$supported_params = array( 'licence_key', 'file_id', 'product_id', 'home_url', 'license_hash', 'plugin_name', 'theme_name', 'version', 'master_key', 'method' );

		$defaults = array(
			'method' => strtoupper( $method ),
			'timeout' => $timeout,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array( 'user-agent' => 'WooThemesUpdater/' . $this->version ),
			'cookies' => array(),
			'ssl_verify' => false,
			'user-agent' => 'WooCommerce Updater; http://woocommerce.com'
	    );

		if ( 'GET' == strtoupper( $method ) ) {
			if ( 0 < count( $params ) ) {
				foreach ( $params as $k => $v ) {
					if ( in_array( $k, $supported_params ) ) {
						$url = add_query_arg( $k, $v, $url );
					}
				}
			}

			if ( in_array( $endpoint, $supported_methods ) ) {
				$url = add_query_arg( 'request', $endpoint, $url );
			}

			// Pass if this is a network install on all requests
			$url = add_query_arg( 'network', is_multisite() ? 1 : 0, $url );
		} else {
			if ( is_multisite() ) {
				$params['network'] = 1;
			} else {
				$params['network'] = 0;
			}

			if ( in_array( $endpoint, $supported_methods ) ) {
				$params['request'] = $endpoint;
			}


			// Add the 'body' parameter if using a POST method. Not required if using a GET method.
			$defaults['body'] = $params;
		}

		// Set up a filter on our default arguments. If any arguments are removed by the filter, replace them with the default value.
		$args = wp_parse_args( (array)apply_filters( 'woothemes_updater_request_args', $defaults, $endpoint, $params, $method ), $defaults );

		$response = wp_remote_get( $url, $args );

		if( is_wp_error( $response ) ) {
			$data = new StdClass;
			$data->error = __( 'WooCommerce Request Error', 'woothemes-updater' );
		} else {
			$data = $response['body'];
			$data = json_decode( $data );
		}

		// Store errors in a transient, to be cleared on each request.
		if ( isset( $data->error ) && ( '' != $data->error ) ) {
			$error = esc_html( $data->error );
			$error = '<strong>' . $error . '</strong>';
			if ( isset( $data->additional_info ) ) { $error .= '<br /><br />' . esc_html( $data->additional_info ); }
			$this->log_request_error( $error );
		}elseif ( empty( $data ) ) {
			$error = '<strong>' . __( 'There was an error making your request, please try again.', 'woothemes-updater' ) . '</strong>';
			$this->log_request_error( $error );
		}

		return $data;
	} // End request()

	/**
	 * Log an error from an API request.
	 *
	 * @access private
	 * @since 1.0.0
	 * @param string $error
	 */
	public function log_request_error ( $error ) {
		$this->errors[] = $error;
	} // End log_request_error()

	/**
	 * Store logged errors in a temporary transient, such that they survive a page load.
	 * @since  1.0.0
	 * @return  void
	 */
	public function store_error_log () {
		set_transient( $this->token . '-request-error', $this->errors );
	} // End store_error_log()

	/**
	 * Get the current error log.
	 * @since  1.0.0
	 * @return  void
	 */
	public function get_error_log () {
		return get_transient( $this->token . '-request-error' );
	} // End get_error_log()

	/**
	 * Clear the current error log.
	 * @since  1.0.0
	 * @return  void
	 */
	public function clear_error_log () {
		return delete_transient( $this->token . '-request-error' );
	} // End clear_error_log()
} // End Class
?>
