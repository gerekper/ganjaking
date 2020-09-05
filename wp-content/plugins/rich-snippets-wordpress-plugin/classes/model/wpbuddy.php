<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class WP-Buddy.
 *
 * Performs API calls to wp-buddy.com
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
final class WPBuddy_Model {


	/**
	 * Generates a hash value from a value.
	 *
	 * @param string $sth
	 *
	 * @return string
	 * @since 2.0.0
	 *
	 */
	public static function hash( $sth ): string {

		$possible_hash_algos = hash_algos();

		# Search for an algorithm that produces short output
		if ( version_compare( PHP_VERSION, '7.2.0', '<' ) ) {
			$hash_algos_to_use = array(
				'crc32',
				'adler32',
				'crc32b',
				'fnv132',
				'fnv1a32',
				'fnv164',
				'joaat',
				'fnv164',
				'fnv1a64',
				'md5',
			);
		} else {
			$hash_algos_to_use = array(
				'haval128,4',
				'md4',
				'tiger128,4',
				'tiger128,3',
				'haval128,3',
				'md2',
				'ripemd128',
				'haval128,5',
				'haval160,5',
				'sha1',
				'tiger160,3',
				'tiger160,4',
				'ripemd160',
				'haval160,3',
				'haval192,4',
				'tiger192,3',
				'haval192,5',
				'tiger192,4',
				'tiger192,3',
				'sha224',
				'haval224,5',
				'haval224,5',
				'haval224,3',
				'sha512/224',
				'sha3-224',
				'haval224,4',
				'haval254,4',
				'haval256,3',
				'snefru256',
				'gost-crypto',
				'gost',
				'snefru',
				'ripemd256',
				'sha3-256',
				'sha512/256',
				'sha256',
				'haval256,5',
			);
		}

		$algo = 'sha256';

		# search for the first algo available
		foreach ( $hash_algos_to_use as $hash_algo_to_use ) {
			if ( false !== $k = array_search( $hash_algo_to_use, $possible_hash_algos ) ) {
				$algo = $hash_algo_to_use;
				break;
			}
		}

		return (string) hash_hmac( $algo, $sth, wp_salt( 'wpbuddy' ) );
	}


	/**
	 * Gets a cache key for a given URL.
	 *
	 * @param string $url
	 *
	 * @return string
	 * @since 2.0.0
	 *
	 */
	public static function get_cache_key( string $url ): string {

		/**
		 * Transients get saved using _transient_timeout_ prefix. But the database only can have
		 * 191 characters. So we only have 150 characters left for the hash value.
		 */
		return substr( sprintf( 'wpb_rs/r_cache/%s', self::hash( $url ) ), 0, 150 );
	}


	/**
	 * Returns the WP-Buddy API request URL.
	 *
	 * @return string
	 * @since 2.4.2
	 *
	 */
	public static function get_request_url() {
		return ( defined( 'WPB_RS_REMOTE' )
			? untrailingslashit( WPB_RS_REMOTE )
			: 'https://rich-snippets.io/wp-json'
		);
	}

	/**
	 * Send a request to wp-buddy.com API.
	 *
	 * @param string $url
	 * @param array $args
	 * @param bool $assoc If data should be returned as an associative array (true) or as an object (false)
	 * @param bool $skip_cache
	 *
	 * @return mixed|\WP_Error
	 * @since ?
	 *
	 */
	public static function request( $url, $args = array(), $assoc = false, $skip_cache = false ) {

		$cache_key   = self::get_cache_key( $url );
		$cache_value = get_transient( $cache_key );
		if ( false !== $cache_value && ! rich_snippets()->debug() && ! $skip_cache ) {
			return $cache_value;
		}

		$args = wp_parse_args( $args, array( 'timeout' => 15 ) );

		$url = self::get_request_url() . $url;

		add_filter( 'http_request_args', array( 'wpbuddy\rich_snippets\WPBuddy_Model', 'request_args' ) );

		$response = wp_remote_request( $url, $args );

		remove_filter( 'http_request_args', array( 'wpbuddy\rich_snippets\WPBuddy_Model', 'request_args' ) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );

		if ( isset( $response['file'] ) && is_file( $response['file'] ) ) {
			return $response['file'];
		}

		$json = json_decode( $body, $assoc );

		if ( is_null( $json ) ) {
			return new \WP_Error(
				'wpbuddy/rich_snippets/model/api/request',
				__( 'Could not JSON-decode the response from the wp-buddy.com API.', 'rich-snippets-schema' ),
				$body
			);
		}

		if ( is_wp_error( $response ) ) {

			$error = self::get_error_message( $json, $response );

			return new \WP_Error(
				'wpbuddy/rich_snippets/model/api/request',
				sprintf(
					__( 'Could not send a request to the wp-buddy.com API. Got error: %s', 'rich-snippets-schema' ),
					$error
				),
				$response
			);
		}

		if ( "2" !== substr( wp_remote_retrieve_response_code( $response ), 0, 1 ) ) {

			$error = self::get_error_message( $json, $response );

			return new \WP_Error(
				'wpbuddy/rich_snippets/model/api/request',
				sprintf(
					__( 'Could not send a request to the wp-buddy.com API. Got response code: %s and the following error message: <strong>%s</strong>', 'rich-snippets-schema' ),
					wp_remote_retrieve_response_code( $response ),
					$error
				),
				$response
			);
		}

		set_transient( $cache_key, $json, HOUR_IN_SECONDS );

		return $json;

	}


	/**
	 * Fetches any error messages from the response.
	 *
	 * @param \stdClass|array $json
	 * @param \WP_Error $response
	 *
	 * @return string
	 * @since 2.0.0
	 *
	 */
	public static function get_error_message( $json, $response ) {

		$error = is_object( $json ) && isset( $json->message ) ? $json->message : '';

		if ( ! empty( $error ) ) {
			return $error;
		}

		$error = is_array( $json ) && isset( $json['message'] ) ? $json['message'] : '';

		if ( ! empty( $error ) ) {
			return $error;
		}

		if ( is_wp_error( $response ) ) {
			return $response->get_error_message();
		}

		return __( 'An unknown error occurred during an API request.', 'rich-snippets-schema' );
	}


	/**
	 * Modifies the request args to the WP-Buddy API.
	 *
	 * @param array $args
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public static function request_args( $args ) {

		global $wp_version;

		$blog_url = get_bloginfo( 'url' );

		$args['user-agent'] = 'WordPress/' . $wp_version . '; ' . $blog_url;

		if ( rich_snippets()->debug() ) {

			$args['cookies'][] = new \Requests_Cookie(
				'XDEBUG_SESSION',
				'XDEBUG_ECLIPSE',
				[
					'expires' => time() + YEAR_IN_SECONDS,
					'domain'  => sprintf( '.%s', parse_url( WPB_RS_REMOTE, PHP_URL_HOST ) )
				]
			);
		}

		if ( ! isset( $args['headers'] ) ) {
			$args['headers'] = array();
		}

		if ( ! is_array( $args['headers'] ) ) {
			$args['headers'] = array( $args['headers'] );
		}

		$pc = '';
		if ( rich_snippets() instanceof \wpbuddy\rich_snippets\pro\Rich_Snippets_Plugin_Pro ) {
			$pc = get_option( 'wpb_rs/purchase_code', '' );
		}

		$args['headers']['Authorization'] = sprintf( 'Purchase_Code %s', ! empty( $pc ) ? esc_attr( $pc ) : 'FREE' );

		return $args;
	}
}
