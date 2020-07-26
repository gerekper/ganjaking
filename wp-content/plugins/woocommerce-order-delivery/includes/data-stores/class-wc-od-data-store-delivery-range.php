<?php
/**
 * Data Store: Delivery Range
 *
 * @package WC_OD/Data_Stores
 * @since   1.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_OD_Data_Store_Delivery_Range.
 */
class WC_OD_Data_Store_Delivery_Range {

	/**
	 * Method to create a new delivery range in the database.
	 *
	 * @since 1.7.0
	 *
	 * @param WC_OD_Delivery_Range $delivery_range Delivery range object.
	 */
	public function create( &$delivery_range ) {
		// The method 'get_data()' doesn't fetch the data with the applied changes.
		$delivery_range->apply_changes();

		$data = $this->get_data( $delivery_range );

		/**
		 * Filters the delivery range data before creating it.
		 *
		 * @since 1.7.0
		 *
		 * @param array $data An array with the delivery range data.
		 */
		$data = apply_filters( 'wc_od_new_delivery_range_data', $data );

		$ranges = get_option( 'wc_od_delivery_ranges', array() );

		// Indices start at 1 to reserve the 0 for the default delivery range.
		if ( empty( $ranges ) ) {
			$ranges[1] = $data;
		} else {
			$ranges[] = $data;
		}

		update_option( 'wc_od_delivery_ranges', $ranges );

		$range_ids = array_keys( $ranges );
		$range_id  = end( $range_ids );

		if ( ! $range_id ) {
			return;
		}

		$delivery_range->set_id( $range_id );
		$delivery_range->apply_changes();

		/**
		 * Fires after creating a delivery range.
		 *
		 * @since 1.7.0
		 *
		 * @param WC_OD_Delivery_Range $delivery_range Delivery range object.
		 */
		do_action( 'wc_od_create_delivery_range', $delivery_range );
	}

	/**
	 * Method to read an delivery range.
	 *
	 * @since 1.7.0
	 *
	 * @throws Exception If invalid delivery range.
	 *
	 * @param WC_OD_Delivery_Range $delivery_range Delivery range object.
	 */
	public function read( &$delivery_range ) {
		$delivery_range->set_defaults();

		$range_id = $delivery_range->get_id();

		if ( 0 === $range_id ) {
			$range = get_option( 'wc_od_delivery_range', array() );
			$range = wp_parse_args(
				$range,
				array(
					'min' => 1,
					'max' => 10,
				)
			);

			$data = array(
				'title' => _x( 'Default', 'default delivery range title', 'woocommerce-order-delivery' ),
				'from'  => $range['min'],
				'to'    => $range['max'],
			);
		} else {
			$ranges = get_option( 'wc_od_delivery_ranges', array() );
			$data   = ( isset( $ranges[ $range_id ] ) ? $ranges[ $range_id ] : array() );
		}

		if ( empty( $data ) ) {
			throw new Exception( __( 'Invalid delivery range.', 'woocommerce-order-delivery' ) );
		}

		$delivery_range->set_props( $data );
		$delivery_range->set_object_read( true );

		/**
		 * Fires after loading the delivery range data.
		 *
		 * @since 1.7.0
		 *
		 * @param WC_OD_Delivery_Range $delivery_range Delivery range object.
		 */
		do_action( 'wc_od_delivery_range_loaded', $delivery_range );
	}

	/**
	 * Updates a delivery range in the database.
	 *
	 * @since 1.7.0
	 *
	 * @param WC_OD_Delivery_Range $delivery_range Delivery range object.
	 */
	public function update( &$delivery_range ) {
		$delivery_range->apply_changes();

		$range_id = $delivery_range->get_id();

		if ( 0 === $range_id ) {
			$data = array(
				'min' => $delivery_range->get_from(),
				'max' => $delivery_range->get_to(),
			);

			update_option( 'wc_od_delivery_range', $data );
		} elseif ( $range_id ) {
			$ranges = get_option( 'wc_od_delivery_ranges', array() );

			$ranges[ $range_id ] = $this->get_data( $delivery_range );

			update_option( 'wc_od_delivery_ranges', $ranges );
		}

		/**
		 * Fires after updating the delivery range.
		 *
		 * @since 1.7.0
		 *
		 * @param WC_OD_Delivery_Range $delivery_range Delivery range object.
		 */
		do_action( 'wc_od_update_delivery_range', $delivery_range );
	}

	/**
	 * Deletes a delivery range from the database.
	 *
	 * @since 1.7.0
	 *
	 * @param WC_OD_Delivery_Range $delivery_range Delivery range object.
	 * @param array                $args           Arguments passed to the delete method.
	 * @return bool
	 */
	public function delete( &$delivery_range, $args = array() ) {
		$id = $delivery_range->get_id();

		// Don't delete the default delivery range.
		if ( ! $id ) {
			return false;
		}

		/**
		 * Fires before deleting a delivery range.
		 *
		 * @since 1.7.0
		 *
		 * @param WC_OD_Delivery_Range $delivery_range Delivery range object.
		 * @param array                $args           Arguments passed to the delete method.
		 */
		do_action( 'wc_od_before_delete_delivery_range', $delivery_range, $args );

		$ranges = get_option( 'wc_od_delivery_ranges', array() );

		unset( $ranges[ $id ] );

		$deleted = update_option( 'wc_od_delivery_ranges', $ranges );

		$delivery_range->set_id( null );

		/**
		 * Fires after deleting a delivery range.
		 *
		 * @since 1.7.0
		 *
		 * @param int $id Delivery range ID.
		 */
		do_action( 'wc_od_delete_delivery_range', $id );

		return $deleted;
	}

	/**
	 * Gets the delivery range data.
	 *
	 * @since 1.7.0
	 *
	 * @param WC_OD_Delivery_Range $delivery_range Delivery range object.
	 * @return array
	 */
	protected function get_data( $delivery_range ) {
		$data = $delivery_range->get_data();

		unset( $data['id'], $data['meta_data'] );

		return $data;
	}

	/**
	 * Gets the delivery ranges.
	 *
	 * @since 1.7.0
	 *
	 * @return array
	 */
	public function get_ranges() {
		$ranges = get_option( 'wc_od_delivery_ranges', array() );

		foreach ( $ranges as $range_id => $data ) {
			$delivery_range = new WC_OD_Delivery_Range();
			$delivery_range->set_props( $data );
			$delivery_range->set_id( $range_id );
			$delivery_range->apply_changes();

			$ranges[ $range_id ] = $delivery_range;
		}

		return $ranges;
	}
}
