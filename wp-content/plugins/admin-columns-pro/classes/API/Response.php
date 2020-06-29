<?php

namespace ACP\API;

use WP_Error;

class Response {

	/**
	 * @var object
	 */
	private $body;

	/**
	 * @var WP_Error
	 */
	private $error;

	public function get_body() {
		return $this->body;
	}

	public function get_error() {
		return $this->error;
	}

	/**
	 * @return bool
	 */
	public function has_error() {
		return $this->error instanceof WP_Error;
	}

	/**
	 * @param object $body
	 *
	 * @return Response
	 */
	public function with_body( $body ) {
		$self = clone $this;
		$self->body = $body;

		return $self;
	}

	public function with_error( WP_Error $error ) {
		$self = clone $this;
		$self->error = $error;

		return $self;
	}

	/**
	 * Access properties from the body
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function get( $key ) {
		if ( ! isset( $this->body->$key ) ) {
			return null;
		}

		return $this->body->$key;
	}

}