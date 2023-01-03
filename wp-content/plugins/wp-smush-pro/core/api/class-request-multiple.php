<?php

namespace Smush\Core\Api;

use Requests;
use Requests_Exception;
use WP_Error;

class Request_Multiple {
	public function do_requests( $requests, $options ) {
		$on_complete = ! empty( $options['complete'] )
			? $options['complete']
			: '__return_false';

		Requests::request_multiple( $requests, array_merge(
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
		if ( is_a( $multi_response, Requests_Exception::class ) ) {
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
}
