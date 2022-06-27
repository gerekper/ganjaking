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

if ( ! class_exists( 'WC_Data', false ) ) {
	include_once WC_ABSPATH . 'includes/abstracts/abstract-wc-data.php';
}

/**
 * Abstract WC_OD_Data class.
 */
abstract class WC_OD_Data extends WC_Data implements ArrayAccess {

	/**
	 * Constructor.
	 *
	 * @since 1.6.0
	 *
	 * @throws Exception When the load of the object data fails.
	 *
	 * @param mixed $data Data object, ID, or an array with data.
	 */
	public function __construct( $data = 0 ) {
		parent::__construct();

		if ( is_numeric( $data ) && $data > 0 ) {
			$this->set_id( $data );
		} elseif ( $data instanceof self ) {
			$this->set_id( $data->get_id() );
		} elseif ( ! empty( $data->ID ) ) {
			$this->set_id( $data->ID );
		} elseif ( is_array( $data ) ) {
			$this->set_props( $data );
		} else {
			$this->set_object_read();
		}

		$this->read_object_from_database();
	}

	/**
	 * If the object has an ID, read using the data store.
	 *
	 * @since 2.0.0
	 *
	 * @throws Exception When the load of the object data fails.
	 */
	protected function read_object_from_database() {
		$this->data_store = WC_Data_Store::load( $this->object_type );

		if ( $this->get_id() > 0 ) {
			$this->data_store->read( $this );
		}
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
		return $this->data_get( $key );
	}

	/**
	 * Gets multiple properties at once.
	 *
	 * @since 1.6.0
	 *
	 * @param array $props An array with the property keys.
	 * @return array
	 */
	public function get_props( array $props ) {
		return array_combine( $props, array_map( array( $this, 'data_get' ), $props ) );
	}

	/**
	 * Gets all data for this object excluding the specified properties.
	 *
	 * @since 2.0.0
	 *
	 * @param array $exclude The properties to exclude from the list.
	 * @return array
	 */
	public function get_data_without( $exclude ) {
		if ( empty( $exclude ) ) {
			return $this->get_data();
		}

		$data = array_merge( array( 'id' => $this->get_id() ), $this->data );
		$data = array_diff_key( $data, array_flip( $exclude ) );

		// Don't read the object metadata if not necessary.
		if ( ! in_array( 'meta_data', $exclude, true ) ) {
			$data['meta_data'] = $this->get_meta_data();
		}

		return $data;
	}

	/**
	 * Merge changes with data and clear.
	 *
	 * Overrides WC_Data::apply_changes.
	 * `array_replace_recursive` does not work well with array properties
	 * because it merges the values instead of replacing them.
	 *
	 * @since 2.0.0
	 */
	public function apply_changes() {
		$this->data    = array_replace( $this->data, $this->changes ); // @codingStandardsIgnoreLine
		$this->changes = array();
	}

	/**
	 * Sets a boolean property.
	 *
	 * @since 2.0.0
	 *
	 * @param string $prop  Property name.
	 * @param mixed  $value Property value.
	 */
	protected function set_bool_prop( $prop, $value ) {
		$this->set_prop( $prop, wc_string_to_bool( $value ) );
	}

	/**
	 * Call the getter method associated to the property.
	 *
	 * @since 2.0.0
	 *
	 * @param string $prop Property key.
	 * @return mixed
	 */
	protected function data_get( $prop ) {
		if ( array_key_exists( $prop, $this->data ) ) {
			$getter = "get_$prop";

			if ( is_callable( array( $this, $getter ) ) ) {
				return $this->$getter();
			}
		}

		return null;
	}

	/**
	 * Call the setter method associated to the property.
	 *
	 * @since 2.0.0
	 *
	 * @param string $prop Property key.
	 * @param mixed  $value Property value.
	 */
	protected function data_set( $prop, $value ) {
		if ( array_key_exists( $prop, $this->data ) ) {
			$setter = "set_$prop";

			if ( is_callable( array( $this, $setter ) ) ) {
				$this->$setter( $value );
			}
		}
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
	| Array Access Methods (Deprecated)
	|--------------------------------------------------------------------------
	|
	| Backward compatibility with legacy arrays.
	|
	*/

	/**
	 * OffsetExists for ArrayAccess.
	 *
	 * @since 1.6.0
	 * @deprecated 2.0.0
	 *
	 * @param string $offset Offset.
	 * @return bool
	 */
	public function offsetExists( $offset ) {
		wc_deprecated_function( 'Array access', '2.0.0' );

		return array_key_exists( $offset, $this->data );
	}

	/**
	 * OffsetGet for ArrayAccess.
	 *
	 * @since 1.6.0
	 * @deprecated 2.0.0
	 *
	 * @param string $offset Offset.
	 * @return mixed
	 */
	public function offsetGet( $offset ) {
		wc_deprecated_function( 'Array access', '2.0.0', "get_{$offset}()" );

		return $this->data_get( $offset );
	}

	/**
	 * OffsetSet for ArrayAccess.
	 *
	 * @since 1.6.0
	 * @deprecated 2.0.0
	 *
	 * @param string $offset Offset.
	 * @param mixed  $value  Value.
	 */
	public function offsetSet( $offset, $value ) {
		wc_deprecated_function( 'Array access', '2.0.0', "set_{$offset}()" );

		$this->data_set( $offset, $value );
	}

	/**
	 * OffsetUnset for ArrayAccess.
	 *
	 * @since 1.6.0
	 * @deprecated 2.0.0
	 *
	 * @param string $offset Offset.
	 */
	public function offsetUnset( $offset ) {
		wc_deprecated_function( 'Array access', '2.0.0' );
	}
}
