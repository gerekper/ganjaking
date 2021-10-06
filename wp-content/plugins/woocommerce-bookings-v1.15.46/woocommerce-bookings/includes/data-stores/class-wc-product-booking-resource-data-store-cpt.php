<?php

/**
 * WC Bookable Product Resource Data Store: Stored in CPT.
 */
class WC_Product_Booking_Resource_Data_Store_CPT extends WC_Data_Store_WP {

	/**
	 * Flush transients for all products related to a specific resource.
	 *
	 * @param WC_Product_Booking_Resource $resource
	 */
	public function flush_resource_transients( $resource ) {
		global $wpdb;

		$product_ids = wp_parse_id_list( $wpdb->get_col( $wpdb->prepare( "
			SELECT product_id
			FROM {$wpdb->prefix}wc_booking_relationships AS relationships
			WHERE relationships.resource_id = %d
			ORDER BY sort_order ASC
		", $resource->get_id() ) ) );

		foreach ( $product_ids as $product_id ) {
			WC_Bookings_Cache::delete_booking_slots_transient( $product_id );
		}
	}

	/**
	 * Create resource.
	 *
	 * @param WC_Product_Booking_Resource $resource
	 */
	public function create( &$resource ) {
		$id = wp_insert_post( array(
			'post_title'   => $resource->get_name(),
			'menu_order'   => $resource->get_sort_order(),
			'post_content' => '',
			'post_status'  => 'publish',
			'post_author'  => get_current_user_id(),
			'post_type'    => 'bookable_resource',
		), true );

		if ( $id && ! is_wp_error( $id ) ) {
			$resource->set_id( $id );
			update_post_meta( $id, 'qty', $resource->get_qty() );
			update_post_meta( $id, '_wc_booking_availability', $resource->get_availability() );
			$resource->apply_changes();
		}
		$this->flush_resource_transients( $resource );
	}

	/**
	 * Method to read a resource from the database.
	 *
	 * @param WC_Product_Booking_Resource
	 */
	public function read( &$resource ) {
		$resource_post        = get_post( $resource->get_id() );
		$resource_availabilty = get_post_meta( $resource->get_id(), '_wc_booking_availability', true );
		$resource->set_defaults();
		$resource->set_props( array(
			'name'         => $resource_post->post_title,
			'qty'          => get_post_meta( $resource->get_id(), 'qty', true ),
			'sort_order'   => $resource_post->menu_order,
			'availability' => $resource_availabilty ? $resource_availabilty : array(),
		) );
		$resource->set_object_read( true );
	}

	/**
	 * Method to update a resource in the database.
	 *
	 * @param WC_Product_Booking_Resource $resource
	 */
	public function update( &$resource ) {
		wp_update_post( array(
			'ID'         => $resource->get_id(),
			'post_title' => $resource->get_name( 'edit' ),
			'menu_order' => $resource->get_sort_order( 'edit' ),
		) );

		update_post_meta( $resource->get_id(), 'qty', $resource->get_qty( 'edit' ) );
		update_post_meta( $resource->get_id(), '_wc_booking_availability', $resource->get_availability( 'edit' ) );

		$resource->apply_changes();
		$this->flush_resource_transients( $resource );
	}

	/**
	 * Delete a resource.
	 *
	 * @param WC_Product_Booking_Resource $resource
	 * @param array $args Array of args to pass to the delete method.
	 */
	public function delete( &$resource, $args = array() ) {
		$this->flush_resource_transients( $resource );
		wp_delete_post( $resource->get_id(), true );
		$resource->set_id( 0 );
	}

	/**
	 * Get all booking product resources.
	 *
	 * @return array
	 */
	public static function get_bookable_product_resource_ids() {
		$ids = get_posts( apply_filters( 'get_booking_resources_args', array(
			'post_status'      => 'publish',
			'post_type'        => 'bookable_resource',
			'posts_per_page'   => -1,
			'orderby'          => 'menu_order',
			'order'            => 'asc',
			'suppress_filters' => true,
			'fields'           => 'ids',
		) ) );
		return wp_parse_id_list( $ids );
	}
}
