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
	 * Metadata which should exist in the DB, even if empty.
	 *
	 * This property was added in WC 3.6.
	 *
	 * @var array
	 */
	protected $must_exist_meta_keys = array();

	/**
	 * Helper method that reads all the post meta.
	 *
	 * @since 4.0.0
	 *
	 * @param WC_Data $object The WP_Data object.
	 */
	protected function read_post_meta( $object ) {
		$object_id = $object->get_id();
		$props     = array();

		foreach ( $this->internal_meta_keys as $meta_key ) {
			if ( ! isset( $this->meta_key_to_props[ $meta_key ] ) ) {
				continue;
			}

			$prop_key = $this->meta_key_to_props[ $meta_key ];

			$props[ $prop_key ] = get_post_meta( $object_id, $meta_key, true );
		}

		$object->set_props( $props );
	}

	/**
	 * Helper method that updates all the post meta.
	 *
	 * @since 4.0.0
	 *
	 * @param WC_Data $object The WP_Data object.
	 */
	protected function update_post_meta( $object ) {
		$props_to_update = $this->get_props_to_update( $object, $this->meta_key_to_props, $this->meta_type );

		foreach ( $props_to_update as $meta_key => $prop ) {
			$value = $object->{"get_$prop"}( 'edit' );
			$value = $this->sanitize_post_meta( $meta_key, $value );

			$this->update_or_delete_post_meta( $object, $meta_key, $value );
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

	/**
	 * Update metadata in, or delete it from, the database.
	 *
	 * This method was added in WC 3.6.
	 *
	 * @param WC_Data $object     The WP_Data object (WC_Coupon for coupons, etc.).
	 * @param string  $meta_key   Meta key to update.
	 * @param mixed   $meta_value Value to save.
	 * @return bool
	 */
	protected function update_or_delete_post_meta( $object, $meta_key, $meta_value ) {
		if ( in_array( $meta_value, array( array(), '' ), true ) && ! in_array( $meta_key, $this->must_exist_meta_keys, true ) ) {
			$updated = delete_post_meta( $object->get_id(), $meta_key );
		} else {
			$updated = update_post_meta( $object->get_id(), $meta_key, $meta_value );
		}

		return (bool) $updated;
	}
}
