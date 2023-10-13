<?php

namespace WCML\Rest\Wrapper;

class Composite extends Handler {

	/** @var Handler[] $restHandlers */
	private $restHandlers;

	public function __construct( array $restHandlers ) {
		$this->restHandlers = $restHandlers;
	}

	/**
	 * @param array           $args
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return array
	 */
	public function query( $args, $request ) {
		foreach ( $this->restHandlers as $restHandler ) {
			$args = $restHandler->query( $args, $request );
		}

		return $args;
	}

	/**
	 * Appends the language and translation information to the get_product response
	 *
	 * @param \WP_REST_Response $response
	 * @param object $object
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function prepare( $response, $object, $request ) {
		foreach ( $this->restHandlers as $restHandler ) {
			$response = $restHandler->prepare( $response, $object, $request );
		}

		return $response;
	}

	/**
	 * Sets the product information according to the provided language
	 *
	 * @param object $object
	 * @param \WP_REST_Request $request
	 * @param bool $creating
	 */
	public function insert( $object, $request, $creating ) {
		foreach ( $this->restHandlers as $restHandler ) {
			$restHandler->insert( $object, $request, $creating );
		}
	}

}