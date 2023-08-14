<?php

namespace SearchWP\Support;

/**
 * The SearchWP classes container.
 *
 * @since 4.2.9
 */
class Container {

	/**
	 * Classes instances container.
	 *
	 * @since 4.2.9
	 *
	 * @var array
	 */
	private $instances;

	/**
	 * Register a class to the container.
	 *
	 * @since 4.2.9
	 *
	 * @param string $class_name Class to register.
	 *
	 * @return mixed|\stdClass
	 */
	public function register( $class_name ) {

		if ( ! class_exists( $class_name ) ) {
			return new \stdClass();
		}

		$this->instances[ $class_name ] = new $class_name();

		return $this->instances[ $class_name ];
	}

	/**
	 * Get a class from the container.
	 *
	 * @since 4.2.9
	 *
	 * @param string $class_name Class to get.
	 *
	 * @return mixed|\stdClass
	 */
	public function get( $class_name ) {

		return $this->has( $class_name ) ? $this->instances[ $class_name ] : new \stdClass();
	}

	/**
	 * Check if a class is in the container.
	 *
	 * @since 4.2.9
	 *
	 * @param string $class_name Class to check.
	 *
	 * @return bool
	 */
	public function has( $class_name ) {

		return array_key_exists( $class_name, $this->instances );
	}
}
