<?php

namespace Smush\Core\Api;

use WP_Error;

class Request_Multiple {
	public function do_requests( $requests, $options ) {
		$on_complete = ! empty( $options['complete'] )
			? $options['complete']
			: '__return_false';
		self::request_multiple( $requests, array_merge(
			$options,
			array(
				'complete' => function ( $response, $key ) use ( &$requests, $on_complete ) {
					// Convert to a response that looks like standard WP HTTP API responses
					$response = $this->multi_to_singular_response( $response );

					// Call the actual on complete callback
					call_user_func( $on_complete, $response, $key );
				},
			)
		) );
	}

	private function multi_to_singular_response( $multi_response ) {
		if ( is_a( $multi_response, self::get_requests_exception_class_name() ) ) {
			return new WP_Error(
				$multi_response->getType(),
				$multi_response->getMessage()
			);
		} else {
			return array(
				'body'     => $multi_response->body,
				'response' => array( 'code' => $multi_response->status_code ),
			);
		}
	}

	/** \Requests lib are deprecated on WP 6.2.0 */

    private static function get_wp_requests_class_name() {
        return class_exists('\WpOrg\Requests\Requests') ? '\WpOrg\Requests\Requests' : '\Requests';
    }

    private static function request_multiple( $requests, $options = array() ) {
        $wp_requests_class_name = self::get_wp_requests_class_name();
        return $wp_requests_class_name::request_multiple( $requests, $options );
    }

    private static function get_requests_exception_class_name() {
        return class_exists('\WpOrg\Requests\Exception') ? '\WpOrg\Requests\Exception' : '\Requests_Exception';
    }
}