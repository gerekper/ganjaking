<?php
/**
 * Extends the WP data store.
 *
 * @package WC_Extension/Abstracts
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Data_Store_WP', false ) ) {
	include_once WC_ABSPATH . 'includes/data-stores/class-wc-data-store-wp.php';
}

/**
 * Class WC_OD_Data_Store.
 */
abstract class WC_OD_Data_Store extends WC_Data_Store_WP {

	/**
	 * Maps the metadata with the object properties.
	 *
	 * @since 2.0.0
	 *
	 * @var array An array of pairs [meta_key => property_key].
	 */
	protected $meta_key_to_props = array();

	/**
	 * Maps the type of each meta key for sanitizing the values in the BD.
	 *
	 * Include only the meta keys that need sanitization (dates, booleans, etc.).
	 *
	 * @var array
	 */
	protected $meta_key_types = array();

	/**
	 * Gets the table structure for the different meta types.
	 *
	 * @since 2.0.0
	 *
	 * @return array Array elements: table, object_id_field, meta_id_field.
	 */
	protected function get_db_info() {
		global $wpdb;

		$meta_table = $this->meta_type . 'meta';

		if ( ! property_exists( $wpdb, $meta_table ) ) {
			return parent::get_db_info();
		}

		return array(
			'table'           => $wpdb->{$meta_table},
			'object_id_field' => ( ! empty( $this->object_id_field_for_meta ) ? $this->object_id_field_for_meta : $this->meta_type . '_id' ),
			'meta_id_field'   => ( 'user' === $this->meta_type ? 'umeta_id' : 'meta_id' ),
		);
	}

	/**
	 * Reads the object's metadata.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_Data $object The WP_Data object.
	 * @return array
	 */
	protected function read_meta_data( $object ) {
		$object_id = $object->get_id();
		$props     = array();

		foreach ( $this->internal_meta_keys as $meta_key ) {
			if ( ! isset( $this->meta_key_to_props[ $meta_key ] ) ) {
				continue;
			}

			$prop_key = $this->meta_key_to_props[ $meta_key ];

			$props[ $prop_key ] = get_metadata( $this->meta_type, $object_id, $meta_key, true );
		}

		return $props;
	}

	/**
	 * Updates the object's metadata.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_Data $object The WP_Data object.
	 */
	protected function save_meta_data( $object ) {
		$props_to_update = $this->get_props_to_update( $object, $this->meta_key_to_props, $this->meta_type );

		foreach ( $props_to_update as $meta_key => $prop ) {
			$value = $object->{"get_$prop"}( 'edit' );
			$value = $this->sanitize_meta( $meta_key, $value );

			$this->update_or_delete_meta( $object, $meta_key, $value );
		}
	}

	/**
	 * Updates metadata in, or deletes it from, the database.
	 *
	 * Avoids storing meta when it's either an empty string or empty array.
	 * Other empty values such as numeric 0 and null should still be stored.
	 * Data-stores can force meta to exist using the `must_exist_meta_keys` property.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_Data $object     The WP_Data object.
	 * @param string  $meta_key   Meta key to update.
	 * @param mixed   $meta_value Value to save.
	 * @return bool
	 */
	protected function update_or_delete_meta( $object, $meta_key, $meta_value ) {
		if ( in_array( $meta_value, array( array(), '' ), true ) && ! in_array( $meta_key, $this->must_exist_meta_keys, true ) ) {
			$updated = delete_metadata( $this->meta_type, $object->get_id(), $meta_key );
		} else {
			$updated = update_metadata( $this->meta_type, $object->get_id(), $meta_key, $meta_value );
		}

		return (bool) $updated;
	}

	/**
	 * Sanitizes an object meta to store its value in the DB.
	 *
	 * @since 2.0.0
	 *
	 * @param string $meta_key The meta key.
	 * @param mixed  $value    The value to sanitize.
	 * @return mixed
	 */
	protected function sanitize_meta( $meta_key, $value ) {
		$meta_key_type = ( isset( $this->meta_key_types[ $meta_key ] ) ? $this->meta_key_types[ $meta_key ] : '' );

		switch ( $meta_key_type ) {
			case 'date':
				$value = ( $value ? $value->getTimestamp() : null );
				break;
			case 'bool':
				$value = wc_bool_to_string( $value );
				break;
		}

		return $value;
	}
}
