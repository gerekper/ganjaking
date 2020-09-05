<?php

namespace wpbuddy\rich_snippets\pro;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Dictionary, suitable for HTTP headers
 *
 * @since 2.0.0
 */
class Dictionary implements \ArrayAccess, \IteratorAggregate, \Countable {

	/**
	 * Creates a case insensitive dictionary.
	 *
	 * @param array $data Dictionary/map to convert to case-insensitive
	 */
	public function __construct( array $data = array() ) {

		foreach ( $data as $k => $v ) {
			$this->offsetSet( $k, $v );
		}
	}

	/**
	 * Check if the given item exists
	 *
	 * @param string $key Item key
	 *
	 * @return boolean Does the item exist?
	 */
	public function offsetExists( $key ) {

		return isset( $this->{$key} );
	}

	/**
	 * Get the value for the item
	 *
	 * @param string $key Item key
	 *
	 * @return mixed Item value
	 */
	public function offsetGet( $key ) {

		if ( ! isset( $this->{$key} ) ) {
			return null;
		}

		return $this->{$key};
	}

	/**
	 * Set the given item
	 *
	 * @param string $key   Item name
	 * @param string $value Item value
	 */
	public function offsetSet( $key, $value ) {

		$vars = get_class_vars( get_class( $this ) );

		if ( ! isset( $vars[ $key ] ) ) {
			return;
		}

		$this->{$key} = $value;
	}

	/**
	 * Unset the given header
	 *
	 * @param string $key
	 */
	public function offsetUnset( $key ) {

		unset( $this->{$key} );
	}

	/**
	 * Get an iterator for the data
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator() {

		return new \ArrayIterator( get_object_vars( $this ) );
	}

	/**
	 * Get the headers as an array
	 *
	 * @return array Header data
	 */
	public function getAll() {

		return get_object_vars( $this );
	}


	/**
	 * Counts the elements.
	 *
	 * @return int
	 */
	public function count() {

		return count( get_object_vars( $this ) );
	}
}
