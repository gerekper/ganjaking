<?php

namespace WCML\Rest\Wrapper;


class Handler {

	/**
	 * @param array            $args
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return array
	 */
	public function query( $args, $request ) {
		return $args;
	}

	/**
	 * @param \WP_REST_Response $response The response object.
	 * @param object            $object Object data.
	 * @param \WP_REST_Request  $request Request object.
	 *
	 * @return \WP_REST_Response
	 */
	public function prepare( $response, $object, $request ) {
		return $response;
	}

	/**
	 * @param object           $object Inserted object.
	 * @param \WP_REST_Request $request Request object.
	 * @param boolean          $creating True when creating object, false when updating.
	 */
	public function insert( $object, $request, $creating ) {

	}

}