<?php
/**
 * Extends the WP data store.
 *
 * @package WC_Instagram/Abstracts
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Data_Store_WP', false ) ) {
	include_once WC_ABSPATH . 'includes/data-stores/class-wc-data-store-wp.php';
}

/**
 * Class WC_Instagram_Data_Store.
 */
abstract class WC_Instagram_Data_Store extends WC_Data_Store_WP {

	/**
	 * Maps the metadata with the object properties.
	 *
	 * @since 4.0.0
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
	 * Helper method that reads all the post meta.
	 *
	 * @since 4.0.0
	 * @since 4.6.0 Renamed parameter `$object` to `$data_object`.
	 *
	 * @param WC_Data $data_object The WP_Data object.
	 */
	protected function read_post_meta( $data_object ) {
		$object_id = $data_object->get_id();
		$props     = array();

		foreach ( $this->internal_meta_keys as $meta_key ) {
			if ( ! isset( $this->meta_key_to_props[ $meta_key ] ) ) {
				continue;
			}

			$prop_key = $this->meta_key_to_props[ $meta_key ];

			$props[ $prop_key ] = get_post_meta( $object_id, $meta_key, true );
		}

		$data_object->set_props( $props );
	}

	/**
	 * Helper method that updates all the post meta.
	 *
	 * @since 4.0.0
	 * @since 4.6.0 Renamed parameter `$object` to `$data_object`.
	 *
	 * @param WC_Data $data_object The WP_Data object.
	 */
	protected function update_post_meta( $data_object ) {
		$props_to_update = $this->get_props_to_update( $data_object, $this->meta_key_to_props, $this->meta_type );

		foreach ( $props_to_update as $meta_key => $prop ) {
			$value = $data_object->{"get_$prop"}( 'edit' );
			$value = $this->sanitize_post_meta( $meta_key, $value );

			$this->update_or_delete_post_meta( $data_object, $meta_key, $value );
		}
	}

	/**
	 * Sanitizes a post meta to store its value in the DB.
	 *
	 * @since 4.0.0
	 *
	 * @param string $meta_key The meta key.
	 * @param mixed  $value    The value to sanitize.
	 * @return mixed
	 */
	protected function sanitize_post_meta( $meta_key, $value ) {
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
