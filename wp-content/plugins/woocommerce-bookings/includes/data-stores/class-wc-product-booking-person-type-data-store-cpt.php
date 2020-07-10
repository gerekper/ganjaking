<?php

/**
 * WC Bookable Product Person Type Data Store: Stored in CPT.
 */
class WC_Product_Booking_Person_Type_Data_Store_CPT extends WC_Data_Store_WP {

	/**
	 * Create person type.
	 *
	 * @param WC_Product_Booking_Person_Type $person_type
	 */
	public function create( &$person_type ) {
		if ( ! $person_type->get_name() ) {
			/* translators: 1: person type sort order */
			$person_type->set_name( sprintf( __( 'Person Type #%d', 'woocommerce-bookings' ), $person_type->get_sort_order() ) );
		}

		$id = wp_insert_post( array(
			'post_title'   => $person_type->get_name(),
			'post_excerpt' => $person_type->get_description(),
			'menu_order'   => $person_type->get_sort_order(),
			'post_parent'  => $person_type->get_parent_id(),
			'post_content' => '',
			'post_status'  => 'publish',
			'post_author'  => get_current_user_id(),
			'post_type'    => 'bookable_person',
		), true );

		if ( $id && ! is_wp_error( $id ) ) {
			$person_type->set_id( $id );
			update_post_meta( $id, 'cost', $person_type->get_cost() );
			update_post_meta( $id, 'block_cost', $person_type->get_block_cost() );
			update_post_meta( $id, 'min', $person_type->get_min() );
			update_post_meta( $id, 'max', $person_type->get_max() );
			$person_type->apply_changes();
		}
	}

	/**
	 * Method to read a person type from the database.
	 *
	 * @param WC_Product_Booking_Person_Type
	 */
	public function read( &$person_type ) {
		$person_type_post = get_post( $person_type->get_id() );

		if ( ! $person_type_post ) {
			throw new Exception( __( 'Invalid booking person type.', 'woocommerce-bookings' ) );
		}

		$person_type->set_defaults();
		$cost       = get_post_meta( $person_type->get_id(), 'cost', true );
		$cost       = empty( $cost ) ? 0 : $cost;
		$block_cost = get_post_meta( $person_type->get_id(), 'block_cost', true );
		$block_cost = empty( $block_cost ) ? 0 : $block_cost;

		$person_type->set_props( array(
			'name'        => $person_type_post->post_title,
			'description' => $person_type_post->post_excerpt,
			'sort_order'  => $person_type_post->menu_order,
			'parent_id'   => $person_type_post->post_parent,
			'cost'        => $cost,
			'block_cost'  => $block_cost,
			'min'         => get_post_meta( $person_type->get_id(), 'min', true ),
			'max'         => get_post_meta( $person_type->get_id(), 'max', true ),
		) );

		$person_type->set_object_read( true );
	}

	/**
	 * Method to update a person type in the database.
	 *
	 * @param WC_Product_Booking_Person_Type $person_type
	 */
	public function update( &$person_type ) {
		if ( ! $person_type->get_name() ) {
			/* translators: 1: person type sort order */
			$person_type->set_name( sprintf( __( 'Person Type #%d', 'woocommerce-bookings' ), $person_type->get_sort_order() ) );
		}

		wp_update_post( array(
			'ID'           => $person_type->get_id(),
			'post_title'   => $person_type->get_name( 'edit' ),
			'post_excerpt' => $person_type->get_description( 'edit' ),
			'menu_order'   => $person_type->get_sort_order( 'edit' ),
			'post_parent'  => $person_type->get_parent_id( 'edit' ),
		) );

		update_post_meta( $person_type->get_id(), 'cost', $person_type->get_cost( 'edit' ) );
		update_post_meta( $person_type->get_id(), 'block_cost', $person_type->get_block_cost( 'edit' ) );
		update_post_meta( $person_type->get_id(), 'min', $person_type->get_min( 'edit' ) );
		update_post_meta( $person_type->get_id(), 'max', $person_type->get_max( 'edit' ) );

		$person_type->apply_changes();
	}

	/**
	 * Delete a person type.
	 *
	 * @param WC_Product_Booking_Person_Type $person_type
	 * @param array $args Array of args to pass to the delete method.
	 */
	public function delete( &$person_type, $args = array() ) {
		wp_delete_post( $person_type->get_id(), true );
		$person_type->set_id( 0 );
	}
}
