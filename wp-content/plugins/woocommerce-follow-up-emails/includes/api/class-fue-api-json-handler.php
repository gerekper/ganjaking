<?php
/**
 * FUE API JSON Handler
 *
 * Handles parsing JSON request bodies and generating JSON responses
 *
 * @author      WooThemes
 * @category    API
 * @package     WooCommerce/API
 * @since       4.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class FUE_API_JSON_Handler implements FUE_API_Handler {

	/**
	 * Get the content type for the response
	 *
	 * @since 4.1
	 * @return string
	 */
	public function get_content_type() {

		return sprintf( '%s; charset=%s', isset( $_GET['_jsonp'] ) ? 'application/javascript' : 'application/json', get_option( 'blog_charset' ) );
	}

	/**
	 * Parse the raw request body entity
	 *
	 * @since 4.1
	 * @param string $body the raw request body
	 * @return array|mixed
	 */
	public function parse_body( $body ) {

		return json_decode( $body, true );
	}

	/**
	 * Generate a JSON response given an array of data
	 *
	 * @since 4.1
	 * @param array $data the response data
	 * @return string
	 */
	public function generate_response( $data ) {

		if ( isset( $_GET['_jsonp'] ) ) {

			// JSONP enabled by default
			if ( ! apply_filters( 'fue_api_jsonp_enabled', true ) ) {

				Follow_Up_Emails::instance()->api->server->send_status( 400 );

				$data = array( array( 'code' => 'fue_api_jsonp_disabled', 'message' => __( 'JSONP support is disabled on this site', 'follow_up_emails' ) ) );
			}

			// Check for invalid characters (only alphanumeric allowed)
			if ( preg_match( '/\W/', $_GET['_jsonp'] ) ) {

				Follow_Up_Emails::instance()->api->server->send_status( 400 );

				$data = array( array( 'code' => 'fue_api_jsonp_callback_invalid', __( 'The JSONP callback function is invalid', 'follow_up_emails' ) ) );
			}

			// see http://miki.it/blog/2014/7/8/abusing-jsonp-with-rosetta-flash/
			Follow_Up_Emails::instance()->api->server->header( 'X-Content-Type-Options', 'nosniff' );

			// Prepend '/**/' to mitigate possible JSONP Flash attacks
			return '/**/' . $_GET['_jsonp'] . '(' . wp_json_encode( $data ) . ')';
		}

		return wp_json_encode( $data );
	}

}
