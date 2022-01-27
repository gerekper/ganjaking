<?php
/**
 * Abstract Data.
 *
 * Handles generic data interaction which is implemented by different classes.
 *
 * @package WC_Instagram/Abstracts
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Data', false ) ) {
	include_once WC_ABSPATH . 'includes/abstracts/abstract-wc-data.php';
}

/**
 * Abstract WC_Instagram_Data class.
 */
abstract class WC_Instagram_Data extends WC_Data {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 * @since 4.0.0 Accepts an object or an ID as the first parameter.
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
	 * @since 4.0.0
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
	 * Gets multiple properties at once.
	 *
	 * @since 3.0.0
	 *
	 * @param array $props An array with the name of the properties.
	 * @return array
	 */
	public function get_props( $props ) {
		return array_combine( $props, array_map( array( $this, 'get_prop' ), $props ) );
	}

	/**
	 * Sets a boolean property.
	 *
	 * @since 4.0.0
	 *
	 * @param string $prop  Property name.
	 * @param mixed  $value Property value.
	 */
	protected function set_bool_prop( $prop, $value ) {
		$this->set_prop( $prop, wc_string_to_bool( $value ) );
	}

	/**
	 * Gets all data for this object excluding the specified properties.
	 *
	 * @since 4.0.0
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
	 * @since 4.0.0
	 */
	public function apply_changes() {
		$this->data    = array_replace( $this->data, $this->changes ); // @codingStandardsIgnoreLine
		$this->changes = array();
	}

	/**
	 * Call the getter method associated to the property key.
	 *
	 * @since 3.0.0
	 * @deprecated 4.0.0
	 *
	 * @param string $prop Property key.
	 * @return mixed
	 */
	protected function data_get( $prop ) {
		wc_deprecated_function( __FUNCTION__, '4.0.0', "get_{$prop}" );

		return $this->get_prop( $prop );
	}

	/**
	 * Call the setter method associated to the property key.
	 *
	 * @since 3.0.0
	 * @deprecated 4.0.0
	 *
	 * @param string $prop  Property key.
	 * @param mixed  $value Property value.
	 */
	protected function data_set( $prop, $value ) {
		wc_deprecated_function( __FUNCTION__, '4.0.0', "set_{$prop}" );

		$this->set_prop( $prop, $value );
	}
}
