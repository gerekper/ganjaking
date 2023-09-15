<?php
/**
 * S2S OAuth for zoom
 * @version 4.4.5
 */

namespace evozoomoauth;

class ZOOM_S2SOAuth{

	public static $instance = null;

	public static function get_instance() {
		return is_null( self::$instance ) ? self::$instance = new self() : self::$instance;
	}

	public function run_access_token_process(){

		EVO()->cal->set_cur('evcal_1');

		$account_id = EVO()->cal->get_prop('_evo_zoom_oauth_id');
		$client_id = EVO()->cal->get_prop('_evo_zoom_oauth_cid');
		$client_secret = EVO()->cal->get_prop('_evo_zoom_oauth_csecret');

		$result = $this->generate_access_token( $account_id, $client_id, $client_secret );

		if( ! is_wp_error( $result )){			
			EVO()->cal->set_prop('_evo_zoom_oauth_data', $result);
		}
	}

	private function generate_access_token( $account_id, $client_id, $client_secret){
		if ( empty( $account_id ) ) {
			return new \WP_Error( 'Account ID', 'Account ID is missing' );
		} elseif ( empty( $client_id ) ) {
			return new \WP_Error( 'Client ID', 'Client ID is missing' );
		} elseif ( empty( $client_secret ) ) {
			return new \WP_Error( 'Client Secret', 'Client Secret is missing' );
		}

		$base64Encoded = base64_encode( $client_id . ':' . $client_secret );
		$result        = new \WP_Error( 0, 'Something went wrong' );

		$args = [
			'method'  => 'POST',
			'headers' => [
				'Authorization' => "Basic $base64Encoded",
			],
			'body'    => [
				'grant_type' => 'account_credentials',
				'account_id' => $account_id,
			],
		];

		$request_url      = "https://zoom.us/oauth/token";
		$response         = wp_remote_post( $request_url, $args );
		$responseCode     = wp_remote_retrieve_response_code( $response );
		$response_message = wp_remote_retrieve_response_message( $response );
		if ( $responseCode == 200 && strtolower( $response_message ) == 'ok' ) {
			$responseBody          = wp_remote_retrieve_body( $response );
			$decoded_response_body = json_decode( $responseBody );
			if ( isset( $decoded_response_body->access_token ) && ! empty( $decoded_response_body->access_token ) ) {
				$result = $decoded_response_body;
			} elseif ( isset( $decoded_response_body->errorCode ) && ! empty( $decoded_response_body->errorCode ) ) {
				$result = new \WP_Error( $decoded_response_body->errorCode, $decoded_response_body->errorMessage );
			}
		} else {
			$result = new \WP_Error( $responseCode, $response_message );
		}

		return $result;
	}
}