<?php
/**
 * Data Store: Delivery Day
 *
 * @package WC_OD/Data_Stores
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Data_Store', false ) ) {
	include_once WC_OD_PATH . 'includes/abstracts/abstract-wc-od-data-store.php';
}

/**
 * Class WC_OD_Data_Store_Delivery_Day.
 */
class WC_OD_Data_Store_Delivery_Day extends WC_OD_Data_Store implements WC_Object_Data_Store_Interface {

	/**
	 * Creates a new delivery day in the database.
	 *
	 * This method is called for the delivery day with ID zero, but the action is the same as updating it.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_OD_Delivery_Day $delivery_day Delivery day object.
	 */
	public function create( &$delivery_day ) {
		// Be sure the ID is not another falsy value.
		if ( 0 === $delivery_day->get_id() ) {
			$this->update( $delivery_day );
		}
	}

	/**
	 * Reads a delivery day from the database.
	 *
	 * @since 2.0.0
	 *
	 * @throws Exception If the delivery day is invalid.
	 *
	 * @param WC_OD_Delivery_Day $delivery_day Delivery day object.
	 */
	public function read( &$delivery_day ) {
		$day_id = $delivery_day->get_id();

		if ( $day_id > 6 ) {
			throw new Exception( 'Invalid delivery day.' );
		}

		$data = $this->read_delivery_day( $day_id );

		unset( $data['time_frames'] );

		$delivery_day->set_defaults();
		$delivery_day->set_props( $data );
		$delivery_day->set_weekday( $day_id );
		$delivery_day->set_object_read();

		/**
		 * Fires after loading the delivery day data.
		 *
		 * @since 2.0.0
		 *
		 * @param WC_OD_Delivery_Day $delivery_day Delivery day object.
		 */
		do_action( 'wc_od_delivery_day_loaded', $delivery_day );
	}

	/**
	 * Updates a delivery day in the database.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_OD_Delivery_Day $delivery_day Delivery day object.
	 */
	public function update( &$delivery_day ) {
		$delivery_day->save_time_frames();
		$delivery_day->apply_changes();

		$this->save_delivery_day( $delivery_day );

		/**
		 * Fires after updating the delivery day.
		 *
		 * @since 2.0.0
		 *
		 * @param WC_OD_Delivery_Day $delivery_day Delivery day object.
		 */
		do_action( 'wc_od_delivery_day_updated', $delivery_day );
	}

	/**
	 * Deletes a delivery day from the database.
	 *
	 * Delivery days cannot be deleted, so we just reset them.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_OD_Delivery_Day $delivery_day Delivery day object.
	 * @param array              $args         Optional. Additional arguments. Default empty.
	 * @return bool
	 */
	public function delete( &$delivery_day, $args = array() ) {
		$day_id = $delivery_day->get_id();

		if ( is_null( $day_id ) ) {
			return false;
		}

		/**
		 * Fires before deleting a delivery day.
		 *
		 * @since 2.0.0
		 *
		 * @param WC_OD_Delivery_Day $delivery_day Delivery day object.
		 * @param array             $args       Arguments passed to the delete method.
		 */
		do_action( 'wc_od_before_delete_delivery_day', $delivery_day, $args );

		$delivery_day->set_defaults();

		$this->save_delivery_day( $delivery_day );

		/**
		 * Fires after deleting a delivery day.
		 *
		 * @since 2.0.0
		 *
		 * @param int $delivery_day_id Delivery day ID.
		 */
		do_action( 'wc_od_delivery_day_deleted', $day_id );

		return true;
	}

	/**
	 * Reads the delivery day data from the database.
	 *
	 * @since 2.0.0
	 *
	 * @param int $day_id Delivery day ID.
	 * @return array
	 */
	protected function read_delivery_day( $day_id ) {
		$delivery_days = get_option( 'wc_od_delivery_days', array() );

		return ( isset( $delivery_days[ $day_id ] ) ? $delivery_days[ $day_id ] : array() );
	}

	/**
	 * Saves the delivery day data in the database.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_OD_Delivery_Day $delivery_day Delivery day object.
	 */
	protected function save_delivery_day( $delivery_day ) {
		$delivery_days = get_option( 'wc_od_delivery_days', array() );

		$data = $delivery_day->get_data_without( array( 'id', 'meta_data' ) );

		$data['time_frames'] = $delivery_day->get_time_frames()->keys();

		$delivery_days[ $delivery_day->get_id() ] = $data;

		update_option( 'wc_od_delivery_days', $delivery_days );
	}

	/**
	 * Reads the time frames from the database.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_OD_Delivery_Day $delivery_day Delivery day object.
	 * @return array
	 */
	public function read_time_frames( $delivery_day ) {
		$day_id = $delivery_day->get_id();

		if ( is_null( $day_id ) ) {
			return array();
		}

		$data = $this->read_delivery_day( $day_id );

		/*
		 * At this point, the time frames can be an array with the IDs or
		 * arrays with data in case the migration script was not executed.
		 */
		$time_frames = ( isset( $data['time_frames'] ) ? $data['time_frames'] : array() );

		// Replace the time frames by objects.
		$objects = array_map( 'wc_od_get_time_frame', $time_frames );

		// Remove falsy values.
		$objects = array_filter( $objects );

		// Assign the proper indices. If we use the method add_time_frame() here, we might enter into an infinite loop.
		$time_frames = array();

		foreach ( $objects as $time_frame ) {
			$time_frame_id = $time_frame->get_id();

			if ( ! $time_frame_id ) {
				$time_frame_id = 'new:' . count( $time_frames );
			}

			$time_frames[ $time_frame_id ] = $time_frame;
		}

		return $time_frames;
	}
}
