<?php

namespace ACP\API;

class Request {

	/**
	 * @var array
	 */
	protected $args;

	/**
	 * @var string
	 */
	protected $format;

	/**
	 * @param array $body
	 */
	public function __construct( array $body = [] ) {
		$this->set_body( $body )
		     ->set_format( 'json' )
		     ->set_arg( 'timeout', 15 );
	}

	/**
	 * @return array
	 */
	public function get_body() {
		return $this->args['body'];
	}

	/**
	 * @param array $value
	 *
	 * @return $this
	 */
	public function set_body( array $value ) {
		$this->args['body'] = $value;

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_format() {
		return $this->format;
	}

	/**
	 * @param string $format
	 *
	 * @return $this
	 */
	public function set_format( $format ) {
		$this->format = $format;

		return $this;
	}

	public function get_args() {
		return $this->args;
	}

	/**
	 * @param array $args
	 *
	 * @return $this
	 */
	public function set_args( array $args ) {
		$this->args = $args;

		return $this;
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return $this
	 */
	public function set_arg( $key, $value ) {
		switch ( $key ) {
			case 'body':
				$this->set_body( $value );

				break;
			default:
				$this->args[ $key ] = $value;
		}

		return $this;
	}

}