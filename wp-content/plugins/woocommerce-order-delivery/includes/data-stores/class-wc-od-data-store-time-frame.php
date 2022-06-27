<?php
/**
 * Data Store: Time Frame
 *
 * @package WC_OD/Data_Stores
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Data_Store', false ) ) {
	include_once WC_OD_PATH . 'includes/abstracts/abstract-wc-od-data-store.php';
}

/**
 * Class WC_OD_Data_Store_Time_Frame.
 */
class WC_OD_Data_Store_Time_Frame extends WC_OD_Data_Store implements WC_Object_Data_Store_Interface {

	/**
	 * Meta type.
	 *
	 * @var string
	 */
	protected $meta_type = 'time_frame';

	/**
	 * Maps the metadata with the object properties.
	 *
	 * @since 2.0.0
	 *
	 * @var array An array of pairs [meta_key => property_key].
	 */
	protected $meta_key_to_props = array(
		'shipping_methods_option' => 'shipping_methods_option',
		'shipping_methods'        => 'shipping_methods',
		'number_of_orders'        => 'number_of_orders',
		'fee_amount'              => 'fee_amount',
		'fee_label'               => 'fee_label',
		'fee_tax_status'          => 'fee_tax_status',
		'fee_tax_class'           => 'fee_tax_class',
	);

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		$this->internal_meta_keys = array_keys( $this->meta_key_to_props );
	}

	/**
	 * Creates a new time frame in the database.
	 *
	 * @since 2.0.0
	 *
	 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
	 *
	 * @param WC_OD_Time_Frame $time_frame Time frame object.
	 */
	public function create( &$time_frame ) {
		global $wpdb;

		$result = $wpdb->insert(
			$wpdb->time_frames,
			array(
				'time_frame_title' => $time_frame->get_title(),
				'time_frame_from'  => $time_frame->get_time_from(),
				'time_frame_to'    => $time_frame->get_time_to(),
			)
		);

		if ( ! $result || ! $wpdb->insert_id ) {
			return;
		}

		$time_frame->set_id( $wpdb->insert_id );

		$this->save_meta_data( $time_frame );

		$time_frame->save_meta_data();
		$time_frame->apply_changes();
		$this->clear_cache( $time_frame );

		/**
		 * Fires after creating a time frame
		 *
		 * @since 2.0.0
		 *
		 * @param WC_OD_Time_Frame $time_frame Time frame object.
		 */
		do_action( 'wc_od_time_frame_created', $time_frame );
	}

	/**
	 * Reads a time frame from the database.
	 *
	 * @since 2.0.0
	 *
	 * @throws Exception If the time frame is invalid.
	 *
	 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
	 *
	 * @param WC_OD_Time_Frame $time_frame Time frame object.
	 */
	public function read( &$time_frame ) {
		global $wpdb;

		$time_frame->set_defaults();

		// Get from cache if available.
		$data = wp_cache_get( $this->get_cache_object_key( $time_frame ), 'time_frames' );

		if ( false === $data ) {
			$object = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT * FROM {$wpdb->time_frames} WHERE time_frame_id = %d LIMIT 1;",
					$time_frame->get_id()
				)
			);

			if ( $object ) {
				$data = array_merge(
					array(
						'title'     => $object->time_frame_title,
						'time_from' => $object->time_frame_from,
						'time_to'   => $object->time_frame_to,
					),
					$this->read_meta_data( $time_frame )
				);

				wp_cache_set( $this->get_cache_object_key( $time_frame ), $data, 'time_frames' );
			}
		}

		if ( ! $data ) {
			throw new Exception( 'Invalid time frame.' );
		}

		$time_frame->set_props( $data );
		$time_frame->read_meta_data();
		$time_frame->set_object_read();

		/**
		 * Fires after loading the time frame data.
		 *
		 * @since 2.0.0
		 *
		 * @param WC_OD_Time_Frame $time_frame Time frame object.
		 */
		do_action( 'wc_od_time_frame_loaded', $time_frame );
	}

	/**
	 * Updates a time frame in the database.
	 *
	 * @since 2.0.0
	 *
	 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
	 *
	 * @param WC_OD_Time_Frame $time_frame Time frame object.
	 */
	public function update( &$time_frame ) {
		global $wpdb;

		$changes = $time_frame->get_changes();

		if ( array_intersect( array( 'title', 'time_from', 'time_to' ), array_keys( $changes ) ) ) {
			$wpdb->update(
				$wpdb->time_frames,
				array(
					'time_frame_title' => $time_frame->get_title(),
					'time_frame_from'  => $time_frame->get_time_from(),
					'time_frame_to'    => $time_frame->get_time_to(),
				),
				array( 'time_frame_id' => $time_frame->get_id() )
			);
		}

		$this->save_meta_data( $time_frame );

		$time_frame->save_meta_data();
		$time_frame->apply_changes();
		$this->clear_cache( $time_frame );

		/**
		 * Fires after updating the time frame.
		 *
		 * @since 2.0.0
		 *
		 * @param WC_OD_Time_Frame $time_frame Time frame object.
		 */
		do_action( 'wc_od_time_frame_updated', $time_frame );
	}

	/**
	 * Deletes a time frame from the database.
	 *
	 * @since 2.0.0
	 *
	 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
	 *
	 * @param WC_OD_Time_Frame $time_frame Time frame object.
	 * @param array            $args       Optional. Additional arguments. Default empty.
	 * @return bool
	 */
	public function delete( &$time_frame, $args = array() ) {
		global $wpdb;

		$time_frame_id = $time_frame->get_id();

		if ( ! $time_frame_id ) {
			return false;
		}

		/**
		 * Fires before deleting a time frame.
		 *
		 * @since 2.0.0
		 *
		 * @param WC_OD_Time_Frame $time_frame Time frame object.
		 * @param array            $args       Arguments passed to the delete method.
		 */
		do_action( 'wc_od_before_delete_time_frame', $time_frame, $args );

		$wpdb->delete( $wpdb->time_frames, array( 'time_frame_id' => $time_frame_id ) );
		$wpdb->delete( $wpdb->time_framemeta, array( 'time_frame_id' => $time_frame_id ) );
		$this->clear_cache( $time_frame );

		/**
		 * Fires after deleting a time frame.
		 *
		 * @since 2.0.0
		 *
		 * @param int $time_frame_id Time frame ID.
		 */
		do_action( 'wc_od_time_frame_deleted', $time_frame_id );

		return true;
	}

	/**
	 * Gets the cache object key for the specified time frame.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_OD_Time_Frame $time_frame Time frame object.
	 * @return string
	 */
	protected function get_cache_object_key( $time_frame ) {
		return 'time_frame_' . $time_frame->get_id();
	}

	/**
	 * Clears the time frame's cache.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_OD_Time_Frame $time_frame Time frame object.
	 */
	protected function clear_cache( $time_frame ) {
		wp_cache_delete( $this->get_cache_object_key( $time_frame ), 'time_frames' );
	}
}
