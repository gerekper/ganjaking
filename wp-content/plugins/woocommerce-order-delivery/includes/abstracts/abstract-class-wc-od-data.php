<?php
/**
 * Abstract Data.
 *
 * Handles generic data interaction which is implemented by different classes.
 *
 * @package WC_OD/Abstracts
 * @since   1.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Abstract WC_OD_Data class.
 */
abstract class WC_OD_Data implements ArrayAccess {

	/**
	 * Object data.
	 *
	 * Name value pairs (name + default value).
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * Constructor.
	 *
	 * @since 1.6.0
	 *
	 * @param array $data The object data.
	 */
	public function __construct( array $data = array() ) {
		$this->data = array_merge( $this->data, $data );
	}

	/**
	 * Magic get method.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $key Key to get.
	 * @return mixed
	 */
	public function __get( $key ) {
		return $this->offsetGet( $key );
	}

	/**
	 * Gets all data for this object.
	 *
	 * @since 1.6.0
	 *
	 * @return array
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Gets a prop for a getter method.
	 *
	 * @since 1.6.0
	 *
	 * @param string $prop Name of prop to get.
	 * @return mixed
	 */
	protected function get_prop( $prop ) {
		return ( array_key_exists( $prop, $this->data ) ? $this->data[ $prop ] : null );
	}

	/**
	 * Gets multiple properties at once.
	 *
	 * @since 1.6.0
	 *
	 * @param array $keys An array with the object properties.
	 * @return array
	 */
	public function get_props( array $keys ) {
		return array_combine(
			$keys,
			array_map( array( $this, 'offsetGet' ), $keys )
		);
	}

	/**
	 * Sets a prop for a setter method.
	 *
	 * @since 1.6.0
	 *
	 * @param string $prop Name of prop to set.
	 * @param mixed  $value Value of the prop.
	 */
	protected function set_prop( $prop, $value ) {
		if ( array_key_exists( $prop, $this->data ) ) {
			$this->data[ $prop ] = $value;
		}
	}

	/**
	 * Sets a collection of props in one go.
	 *
	 * @since 1.6.0
	 *
	 * @param array $props Key value pairs to set. Key is the prop and should map to a setter function name.
	 */
	public function set_props( $props ) {
		array_map( array( $this, 'offsetSet' ), array_keys( $props ), array_values( $props ) );
	}

	/**
	 * Converts the object into a plain PHP array.
	 *
	 * @since 1.6.0
	 *
	 * @return array
	 */
	public function to_array() {
		$data = $this->data;

		foreach ( $data as $key => $value ) {
			if ( $value instanceof self || $value instanceof WC_OD_Collection ) {
				$data[ $key ] = $value->to_array();
			}
		}

		return $data;
	}

	/**
	 * Gets the object as JSON.
	 *
	 * @since 1.6.0
	 *
	 * @see wp_json_encode()
	 *
	 * @param int $options Optional. Options to be passed to json_encode(). Default 0.
	 * @return string|false The JSON encoded string, or false if it cannot be encoded.
	 */
	public function to_json( $options = 0 ) {
		return wp_json_encode( $this->to_array(), $options );
	}

	/**
	 * Converts the object to its string representation.
	 *
	 * @since 1.6.0
	 *
	 * @return string Data in JSON format.
	 */
	public function __toString() {
		return $this->to_json();
	}

	/*
	|--------------------------------------------------------------------------
	| Array Access Methods
	|--------------------------------------------------------------------------
	|
	| For backwards compatibility with legacy arrays.
	|
	*/

	/**
	 * OffsetExists for ArrayAccess.
	 *
	 * @since 1.6.0
	 *
	 * @param string $offset Offset.
	 * @return bool
	 */
	public function offsetExists( $offset ) {
		return array_key_exists( $offset, $this->data );
	}

	/**
	 * OffsetGet for ArrayAccess.
	 *
	 * @since 1.6.0
	 *
	 * @param string $offset Offset.
	 * @return mixed
	 */
	public function offsetGet( $offset ) {
		if ( array_key_exists( $offset, $this->data ) ) {
			$getter = "get_$offset";

			if ( is_callable( array( $this, $getter ) ) ) {
				return $this->$getter();
			}
		}

		return null;
	}

	/**
	 * OffsetSet for ArrayAccess.
	 *
	 * @since 1.6.0
	 *
	 * @param string $offset Offset.
	 * @param mixed  $value  Value.
	 */
	public function offsetSet( $offset, $value ) {
		if ( ! array_key_exists( $offset, $this->data ) ) {
			return;
		}

		$setter = "set_$offset";

		if ( is_callable( array( $this, $setter ) ) ) {
			$this->$setter( $value );
		}
	}

	/**
	 * OffsetUnset for ArrayAccess.
	 *
	 * @since 1.6.0
	 *
	 * @param string $offset Offset.
	 */
	public function offsetUnset( $offset ) {
		if ( array_key_exists( $offset, $this->data ) ) {
			unset( $this->data[ $offset ] );
		}
	}
}
