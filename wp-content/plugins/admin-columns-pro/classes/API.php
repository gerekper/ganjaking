<?php

namespace ACP;

use ACP\API\Request;
use ACP\API\Response;
use WP_Error;

class API implements RequestDispatcher {

	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @var string
	 */
	protected $proxy;

	/**
	 * @var bool
	 */
	protected $use_proxy = true;

	/**
	 * @var array
	 */
	private $meta = [];

	/**
	 * @return string
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * @param string $url
	 *
	 * @return $this
	 */
	public function set_url( $url ) {
		$this->url = $url;

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_proxy() {
		return $this->proxy;
	}

	/**
	 * @param string $proxy
	 *
	 * @return $this
	 */
	public function set_proxy( $proxy ) {
		$this->proxy = $proxy;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function is_use_proxy() {
		return $this->use_proxy;
	}

	/**
	 * @param bool $use_proxy
	 *
	 * @return $this
	 */
	public function set_use_proxy( $use_proxy ) {
		$this->use_proxy = $use_proxy;

		return $this;
	}

	/**
	 * @param array $meta
	 *
	 * @return $this
	 */
	public function set_request_meta( array $meta ) {
		$this->meta = $meta;

		return $this;
	}

	/**
	 * Get the URL to call
	 * @return string
	 */
	private function get_request_url() {
		if ( $this->use_proxy ) {
			return $this->proxy;
		}

		return $this->url;
	}

	private function check_response_for_error( $data ) {
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$code = (int) $data['response']['code'];

		if ( $code >= 200 && $code < 400 ) {
			return $data;
		}

		return new WP_Error( $data['response']['code'], __( 'The Admin Columns Server was unreachable.', 'codepress-admin-columns' ) );
	}

	public function dispatch( Request $request, array $args = [] ) {
		$body = $request->get_body();

		if ( ! isset( $body['meta'] ) || ! is_array( $body['meta'] ) ) {
			$body['meta'] = [];
		}

		$body['meta'] = array_merge( $body['meta'], $this->meta );

		$request->set_body( $body );

		$data = wp_remote_post( $this->get_request_url(), $request->get_args() );

		$response = new Response();

		$data = $this->check_response_for_error( $data );

		if ( is_wp_error( $data ) ) {
			return $response->with_error( $data );
		}

		$body = wp_remote_retrieve_body( $data );

		// retry with proxy disabled
		if ( ! $body && $this->use_proxy ) {
			$this->use_proxy = false;

			return $this->dispatch( $request );
		}

		$decoded = json_decode( $body, true );

		if ( empty( $decoded ) ) {
			return $response->with_error( new WP_Error( 'invalid_response', 'Invalid response.' ) );
		}

		$response = $response->with_body( (object) json_decode( $body, true ) );

		if ( $response->get( 'error' ) ) {
			$response = $response->with_error( new WP_Error( $response->get( 'code' ), $response->get( 'message' ) ) );
		}

		return $response;
	}

}