<?php

namespace WBCR\Factory_Adverts_102;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Factory request class.
 *
 * Performs a server request, retrieves banner data and stores it in the cache.
 *
 * @author        Alexander Vitkalov <nechin.va@gmail.com>
 * @since         1.0.0 Added
 * @package       factory-adverts
 * @copyright (c) 2019 Webcraftic Ltd
 */
class Rest_Request {

	/**
	 * Rest route path.
	 *
	 * Define rest route path for rest request.
	 *
	 * @since 1.0.0
	 * @var string
	 * @uses  do_rest_request()
	 */
	const FACTORY_ADVERTS_REST_ROUTE = '/adverds/v1/advt';

	/**
	 * Plugin name.
	 *
	 * Set on class initialization from parameter plugin_name.
	 * Used as query parameter in rest request.
	 *
	 * @since 1.0.0 Added
	 *
	 * @var string
	 */
	private $plugin_name = '';

	/**
	 * Adverts position.
	 *
	 * Position for advert (dashboard_widget, right_sidebar, notice, businnes_suggetion, support)
	 *
	 * Set on class initialization.
	 * Used as query parameter in rest request.
	 *
	 * @since 1.0.0 Added
	 *
	 * @var string
	 */
	private $ad_position = '';

	/**
	 * Request constructor.
	 *
	 * Variable initialization.
	 *
	 * @since 1.0.0 Added
	 *
	 * @param string $plugin_name   Plugin name from parameter plugin_name
	 * @param string $position      Position for advert
	 */
	public function __construct( $plugin_name, $position ) {
		$this->plugin_name = $plugin_name;
		$this->ad_position = $position;
	}

	/**
	 * Get key for cached data.
	 *
	 * Used for store and get cached data for current plugin and position.
	 *
	 * @since 1.0.0 Added
	 *
	 * @return string
	 */
	private function get_key() {
		return md5( self::FACTORY_ADVERTS_REST_ROUTE . $this->plugin_name . $this->ad_position );
	}

	/**
	 * Get data from cache.
	 *
	 * If data in the cache, not empty and not expired, then get data from cache. Or get data from server.
	 *
	 * @since 1.0.0 Added
	 *
	 * @return mixed array(
	 *  'plugin'  => 'wbcr_insert_php',
	 *  'content' => '<p></p>',
	 *  'expires' => 1563542199,
	 * );
	 */
	private function get_cache() {

		$cached_data = defined( 'FACTORY_ADVERTS_DEBUG' ) && FACTORY_ADVERTS_DEBUG ? false : get_option( $this->get_key() );

		if ( empty( $cached_data ) || ! isset( $cached_data['expires'] ) || ! isset( $cached_data['content'] ) || empty( $cached_data['expires'] ) || $cached_data['expires'] <= current_time( 'timestamp' ) ) {
			$data = $this->do_rest_request();

			if ( ! empty( $data ) && isset( $data['content'] ) && isset( $data['expires'] ) ) {
				update_option( $this->get_key(), $data );
			}
		} else {
			$data = $cached_data;
		}

		return $data;
	}

	/**
	 * Get adverts content.
	 *
	 * @since 1.0.0 Added
	 *
	 * @return string
	 */
	public function get_content() {
		$content = '';

		$data = $this->get_cache();

		if ( $data && isset( $data['content'] ) ) {
			$content = $data['content'];
		}

		return $content;
	}

	/**
	 * Performs rest api request.
	 *
	 * If defined WBCR_ADINSERTER_REST_URL, then data requested from the remote server.
	 * Otherwise data will be requested from the same server.
	 * Defined in boot.php
	 *
	 * In some case on the server (Apache) in the .htaccess must be set
	 * RewriteRule ^wp-json/(.*)[?](.*) /?rest_route=/$1&$2 [L]
	 *
	 * @since 1.0.0 Added
	 *
	 * @return mixed array(
	 *  'plugin'  => 'wbcr_insert_php',
	 *  'content' => '<p></p>',
	 *  'expires' => 1563542199,
	 * );
	 */
	private function do_rest_request() {
		$empty_data = [
			'plugin'  => $this->plugin_name,
			'content' => '',
			'expires' => current_time( 'timestamp' ) + 60 * 60,
		];

		$url = site_url();
		if ( defined( 'WBCR_ADINSERTER_REST_URL' ) && '' != WBCR_ADINSERTER_REST_URL ) {
			$url = WBCR_ADINSERTER_REST_URL;
		}

		// Remote rest request
		$url = rtrim( $url, '/' ) . '/wp-json' . self::FACTORY_ADVERTS_REST_ROUTE;
		$url = add_query_arg( 'plugin', $this->plugin_name, $url );
		$url = add_query_arg( 'position', $this->ad_position, $url );

		$response = wp_remote_get( $url );

		$code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );
		$data = (array) json_decode( $body );

		return 200 == $code && $data && isset( $data['expires'] ) ? $data : $empty_data;
	}

}
