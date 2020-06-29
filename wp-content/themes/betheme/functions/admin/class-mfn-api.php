<?php
if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}

class Mfn_API {

	public $version = '';

	protected $url = array(

		'changelog'	=> 'https://themes.muffingroup.com/betheme/documentation/changelog.html',

		'register' => 'https://api.muffingroup.com/register.php',

		'theme_version'	=> 'https://api.muffingroup.com/theme/version.php',
		'theme_download' => 'https://api.muffingroup.com/theme/download.php',

		'plugins_version'	=> 'https://api.muffingroup.com/plugins/version.php',
		'plugins_download' => 'https://api.muffingroup.com/plugins/download.php',

		'websites_download'	=> 'https://api.muffingroup.com/websites/download.php',

	);

	/**
	 * Constructor
	 */

	public function __construct(){

		$this->version = $this->get_update_version();

	}

	/**
	 * Return specified url
	 *
	 * @param string $key
	 * @return string
	 */

	protected function get_url( $key ){
		return $this->url[ $key ];
	}

	/**
	 * Remote post with error handling
	 *
	 * @param string $target
	 * @param array $args
	 */

	protected function remote_post( $target, $args = array() ){

		$response = wp_remote_post( $this->get_url( $target ), $args );

		if( is_wp_error( $response ) ){
			return $response;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		if( isset( $data['error'] ) ){
			return new WP_Error( 'invalid_response', $data['error'] );
		}

		return $data;
	}

	/**
	 * Remote get with error handling
	 *
	 * @param string $target
	 * @param array $args
	 */

	protected function remote_get( $target, $args = array() ){

		if( ! $args ){
			$args = array(
				'user-agent' => 'WordPress/'. get_bloginfo('version') .'; '. network_site_url(),
				'timeout' => 30,
			);
		}

		$response = wp_remote_get( $this->get_url( $target ), $args );

		// debug
		if( isset( $_GET['forcecheck'] ) && isset( $_GET['be-debug'] ) ){

			print_r( $response );
			exit;

		}

		if( is_wp_error( $response ) ){
			return $response;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		if( isset( $data['error'] ) ){
			return new WP_Error( 'invalid_response', $data['error'] );
		}

		return $data;
	}

	/**
	 * Get theme version
	 */

	function get_update_version(){

		$this->force_check_version();

		$version = get_site_transient( 'betheme_update' );

		if( ! $version ){
			$version = $this->refresh_update_version();
		}

		return $version;
	}

	/**
	 * Refresh theme version
	 * Remote get version
	 * Set transient
	 */

	function refresh_update_version(){

		if( ! $version = $this->remote_get_version() ){
			// set nagative value for transient which do not like 0 and false
			$version = -1;
		}

		// set transient
		set_site_transient( 'betheme_update', $version, HOUR_IN_SECONDS );

		// delete transient
		delete_site_transient( 'betheme_update_plugins' );

		return $version;
	}

	/**
	 * Remote get new theme version
	 */

	public function remote_get_version(){

		$response = $this->remote_get( 'theme_version' );

		if( is_wp_error( $response ) ){
			return false;
		}

		if( empty( $response['version'] ) ){
			return false;
		}

		return $response['version'];
	}

	/**
	 * Force connection check and redirect
	 */

	function force_check_version(){

		if( isset( $_GET['forcecheck'] ) ){

			$this->refresh_update_version();

			wp_redirect( wp_get_referer() );
			exit;
		}

	}

	/**
	 * Check if current host is localhost
	 */

	function is_localhost(){

		$whitelist = array( 'localhost', '127.0.0.1', '::1' );

		return in_array( $_SERVER['REMOTE_ADDR'], $whitelist ); // context is safe and necessary

	}

}
