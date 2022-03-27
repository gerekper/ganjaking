<?php

namespace ACP\Editing;

use InvalidArgumentException;

class View {

	/**
	 * @var array
	 */
	protected $args;

	public function __construct( $type ) {
		$this->set( 'type', (string) $type );
	}

	protected function set( $key, $value ) {
		if ( ! $this->validate( $value ) ) {
			throw new InvalidArgumentException( 'Invalid value.' );
		}

		if ( is_array( $value ) ) {
			$value = array_replace( (array) $this->get_arg( $key ), $value );
		}

		$this->args[ $key ] = $value;

		return $this;
	}

	public function get_arg( $key ) {
		return isset( $this->args[ $key ] )
			? $this->args[ $key ]
			: null;
	}

	private function validate( $value ) {
		return is_array( $value ) || is_scalar( $value );
	}

	/**
	 * @param bool $enable
	 */
	public function set_clear_button( $enable ) {
		$this->set( 'clear_button', (bool) $enable );

		return $this;
	}

	/**
	 * @param bool $required
	 *
	 * @return $this
	 */
	public function set_required( $required ) {
		$this->set( 'required', (bool) $required );

		return $this;
	}

	/**
	 * @param bool $enable
	 *
	 * @return $this
	 */
	public function set_revisioning( $enable ) {
		$this->set( 'disable_revisioning', ! $enable );

		return $this;
	}

	/**
	 * @param string $selector
	 *
	 * @return $this
	 */
	public function set_js_selector( $selector ) {
		$this->set( 'js', [
			'selector' => (string) $selector,
		] );

		return $this;
	}

	/**
	 * @return array
	 */
	public function get_args() {
		return $this->args;
	}

}