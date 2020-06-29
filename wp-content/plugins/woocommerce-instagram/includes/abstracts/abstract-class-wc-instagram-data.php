<?php
/**
 * Abstract Data.
 *
 * Handles generic data interaction which is implemented by different classes.
 *
 * @package WC_Instagram/Classes
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Abstract WC_Instagram_Data class.
 */
abstract class WC_Instagram_Data {

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
	 * @since 3.0.0
	 *
	 * @param array $data The object data.
	 */
	public function __construct( array $data = array() ) {
		$this->data = array_merge( $this->data, $data );
	}

	/**
	 * Magic __call method
	 *
	 * @since 3.0.0
	 *
	 * @param string $method     Method.
	 * @param mixed  $parameters Parameters.
	 * @return mixed
	 */
	public function __call( $method, $parameters ) {
		$prefix = substr( $method, 0, 4 );
		$prop   = substr( $method, 4 );

		if ( in_array( $prefix, array( 'get_', 'set_' ), true ) && $this->has_prop( $prop ) ) {
			array_unshift( $parameters, $prop );

			return call_user_func_array( array( $this, "{$prefix}prop" ), $parameters );
		}

		return null;
	}

	/**
	 * Gets all data for this object.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Gets if the specified property exists.
	 *
	 * @since 3.0.0
	 *
	 * @param string $prop The property name.
	 * @return bool
	 */
	public function has_prop( $prop ) {
		return ( array_key_exists( $prop, $this->data ) );
	}

	/**
	 * Gets the value for the specified property.
	 *
	 * @since 3.0.0
	 *
	 * @param string $prop The property name.
	 * @return mixed The property value. Null on failure.
	 */
	protected function get_prop( $prop ) {
		return ( $this->has_prop( $prop ) ? $this->data[ $prop ] : null );
	}

	/**
	 * Gets multiple properties at once.
	 *
	 * @since 3.0.0
	 *
	 * @param array $props An array with the name of the properties.
	 * @return array
	 */
	public function get_props( $props ) {
		return array_combine( $props, array_map( array( $this, 'data_get' ), $props ) );
	}

	/**
	 * Sets the value of the specified property.
	 *
	 * @since 3.0.0
	 *
	 * @param string $prop  The property name.
	 * @param mixed  $value The property value.
	 */
	protected function set_prop( $prop, $value ) {
		if ( $this->has_prop( $prop ) ) {
			$this->data[ $prop ] = $value;
		}
	}

	/**
	 * Sets multiple properties at once.
	 *
	 * @since 3.0.0
	 *
	 * @param array $props Key value pairs to set.
	 */
	public function set_props( $props ) {
		array_map( array( $this, 'data_set' ), array_keys( $props ), array_values( $props ) );
	}

	/**
	 * Call the getter method associated to the data key.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key Data key.
	 * @return mixed
	 */
	protected function data_get( $key ) {
		$value = null;

		if ( $this->has_prop( $key ) ) {
			$getter = "get_{$key}";

			if ( is_callable( array( $this, $getter ) ) ) {
				$value = $this->$getter();
			}
		}

		return $value;
	}

	/**
	 * Call the setter method associated to the data key.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key   Data key.
	 * @param mixed  $value Data value.
	 */
	protected function data_set( $key, $value ) {
		if ( ! $this->has_prop( $key ) ) {
			return;
		}

		$setter = "set_$key";

		if ( is_callable( array( $this, $setter ) ) ) {
			$this->$setter( $value );
		}
	}
}
