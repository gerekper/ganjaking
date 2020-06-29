<?php

namespace ACP\Search\Middleware;

abstract class Mapping {

	const RESPONSE = 'response';
	const REQUEST = 'request';

	/**
	 * @var string
	 */
	protected $direction;

	/**
	 * @var array
	 */
	protected $properties;

	/**
	 * @param string|null $direction
	 */
	public function __construct( $direction = null ) {
		if ( null === $direction || $direction != self::REQUEST ) {
			$direction = self::RESPONSE;
		}

		$this->direction = $direction;
		$this->properties = $this->apply_direction(
			$this->get_properties()
		);
	}

	/**
	 * @param array $array
	 *
	 * @return array
	 */
	protected function apply_direction( array $array ) {
		if ( $this->direction == self::REQUEST ) {
			$array = array_flip( $array );
		}

		return $array;
	}

	/**
	 * Return array of properties with the response side first
	 * @return array
	 */
	protected abstract function get_properties();

	/**
	 * Get a property
	 *
	 * @param string $key
	 *
	 * @return false|string
	 */
	public function __get( $key ) {
		if ( ! isset( $this->properties[ $key ] ) ) {
			return false;
		}

		return $this->properties[ $key ];
	}

}