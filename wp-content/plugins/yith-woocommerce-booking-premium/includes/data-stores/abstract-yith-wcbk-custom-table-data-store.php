<?php
/**
 * Class YITH_WCBK_Custom_Table_Data_Store
 * Data store for object handled by custom tables.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'ABSPATH' ) || exit();

/**
 * Class YITH_WCBK_Custom_Table_Data_Store
 *
 * @since 5.0.0
 */
abstract class YITH_WCBK_Custom_Table_Data_Store implements WC_Object_Data_Store_Interface {

	/**
	 * Gets a list of props and meta keys that need updated based on change state
	 *
	 * @param WC_Data $object        The WP_Data object.
	 * @param array   $keys_to_props A mapping of keys => prop names.
	 *
	 * @return array                        A mapping of keys => prop names, filtered by ones that should be updated.
	 */
	protected function get_props_to_update( $object, $keys_to_props ) {
		$props_to_update = array();
		$changed_props   = $object->get_changes();

		foreach ( $keys_to_props as $meta_key => $prop ) {
			if ( array_key_exists( $prop, $changed_props ) ) {
				$props_to_update[ $meta_key ] = $prop;
			}
		}

		return $props_to_update;
	}

	/**
	 * Returns an array of meta for an object.
	 *
	 * @param WC_Data $data Data object.
	 *
	 * @return array
	 */
	public function read_meta( &$data ) {
		// Do nothing.
		return array();
	}

	/**
	 * Deletes meta based on meta ID.
	 *
	 * @param WC_Data $data Data object.
	 * @param object  $meta Meta object (containing at least ->id).
	 *
	 * @return array
	 */
	public function delete_meta( &$data, $meta ) {
		// Do nothing.
		return array();
	}

	/**
	 * Add new piece of meta.
	 *
	 * @param WC_Data $data Data object.
	 * @param object  $meta Meta object (containing ->key and ->value).
	 *
	 * @return int meta ID
	 */
	public function add_meta( &$data, $meta ) {
		// Do nothing.
		return 0;
	}

	/**
	 * Update meta.
	 *
	 * @param WC_Data $data Data object.
	 * @param object  $meta Meta object (containing ->id, ->key and ->value).
	 */
	public function update_meta( &$data, $meta ) {
		// Do nothing.
	}
}
