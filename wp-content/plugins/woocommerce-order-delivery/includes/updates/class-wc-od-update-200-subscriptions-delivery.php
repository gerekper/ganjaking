<?php
/**
 * Update: 2.0.0 Subscriptions Delivery.
 *
 * @package WC_OD/Updates
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Updates the subscriptions' delivery details.
 */
class WC_OD_Update_200_Subscriptions_Delivery {

	/**
	 * How many subscriptions process per batch.
	 *
	 * @var int
	 */
	protected $subscriptions_per_page = 10;

	/**
	 * The query offset.
	 *
	 * @var int
	 */
	protected $offset = 0;

	/**
	 * Delivery days collection.
	 *
	 * @var WC_OD_Collection_Delivery_Days
	 */
	private $delivery_days;

	/**
	 * Time frames grouped by delivery day.
	 *
	 * @var array
	 */
	private $time_frames = array();

	/**
	 * Stores the time frame IDs grouped by delivery day.
	 *
	 * @var array
	 */
	private $time_frame_ids = array();

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		$data = wp_parse_args(
			get_option( 'wc_od_update_200_subscriptions_delivery', array() ),
			array(
				'offset' => 0,
			)
		);

		$this->offset        = $data['offset'];
		$this->delivery_days = wc_od_get_delivery_days();
	}

	/**
	 * Executes the update.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function update() {
		$subscriptions = $this->fetch_subscriptions( $this->offset );

		foreach ( $subscriptions as $subscription ) {
			$this->process_subscription( $subscription );
			$this->offset++;
		}

		// Run again.
		if ( $this->subscriptions_per_page <= count( $subscriptions ) ) {
			update_option( 'wc_od_update_200_subscriptions_delivery', array( 'offset' => $this->offset ) );
			return true;
		}

		// There is no more subscriptions to process.
		delete_option( 'wc_od_update_200_subscriptions_delivery' );

		return false;
	}

	/**
	 * Fetches the subscriptions which contain delivery details.
	 *
	 * @since 2.0.0
	 *
	 * @param int $offset The query offset.
	 * @return array
	 */
	protected function fetch_subscriptions( $offset = 0 ) {
		return wcs_get_subscriptions(
			array(
				'subscriptions_per_page' => $this->subscriptions_per_page,
				'offset'                 => $offset,
				'subscription_status'    => array(
					'active',
					'on-hold',
					'pending',
				),
				'meta_query'             => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					'relation' => 'OR',
					array(
						'key'     => '_delivery_time_frame',
						'compare' => 'EXISTS',
					),
					array(
						'key'     => '_delivery_days',
						'compare' => 'EXISTS',
					),
				),
			)
		);
	}

	/**
	 * Processes the subscription.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_Subscription $subscription Subscription object.
	 */
	protected function process_subscription( $subscription ) {
		$modified = $this->process_time_frame_meta( $subscription );
		$modified = ( $this->process_delivery_days_meta( $subscription ) || $modified );

		if ( $modified ) {
			$subscription->save_meta_data();
		}
	}

	/**
	 * Processes the subscription meta '_delivery_time_frame'.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_Subscription $subscription Subscription object.
	 * @return bool
	 */
	protected function process_time_frame_meta( $subscription ) {
		$meta_value = $subscription->get_meta( '_delivery_time_frame' );

		if ( ! $meta_value || is_numeric( $meta_value ) ) {
			return false;
		}

		// Update the meta value with the format 'time_frame:index' by the real time frame ID.
		$delivery_date = $subscription->get_meta( '_delivery_date' );
		$time_frame_id = false;

		if ( $delivery_date ) {
			$day_id   = (int) gmdate( 'w', strtotime( $delivery_date ) );
			$position = wc_od_parse_time_frame_id( $meta_value );

			$time_frame_id = $this->get_time_frame_id( $day_id, $position );
		}

		if ( $time_frame_id ) {
			$subscription->update_meta_data( '_delivery_time_frame', $time_frame_id );
		} else {
			$subscription->delete_meta_data( '_delivery_time_frame' );
		}

		return true;
	}

	/**
	 * Processes the subscription meta '_delivery_days'.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_Subscription $subscription Subscription object.
	 * @return bool
	 */
	protected function process_delivery_days_meta( $subscription ) {
		$meta_value = $subscription->get_meta( '_delivery_days' );

		if ( ! $meta_value ) {
			return false;
		}

		$modified = false;

		foreach ( $meta_value as $day_id => $data ) {
			if ( empty( $data['time_frame'] ) || is_numeric( $data['time_frame'] ) ) {
				continue;
			}

			$modified = true;
			$position = wc_od_parse_time_frame_id( $data['time_frame'] );

			$time_frame_id = ( false !== $position ? $this->get_time_frame_id( $day_id, $position ) : false );

			$meta_value[ $day_id ]['time_frame'] = (string) $time_frame_id;
		}

		if ( $modified ) {
			$subscription->update_meta_data( '_delivery_days', $meta_value );
		}

		return $modified;
	}

	/**
	 * Gets the time frames for the specified delivery day.
	 *
	 * @since 2.0.0
	 *
	 * @param int $day_id Delivery day ID.
	 * @return WC_OD_Collection_Time_Frames
	 */
	private function get_time_frames_for_day( $day_id ) {
		// Cache the result.
		if ( ! isset( $this->time_frames[ $day_id ] ) ) {
			$this->time_frames[ $day_id ] = $this->delivery_days->get( $day_id )->get_time_frames();
		}

		return $this->time_frames[ $day_id ];
	}

	/**
	 * Gets the time frame ID for the specified delivery day and position.
	 *
	 * @since 2.0.0
	 *
	 * @param int $day_id   Delivery day ID.
	 * @param int $position The position of the time frame to fetch.
	 * @return int|false The time frame ID. False otherwise.
	 */
	private function get_time_frame_id( $day_id, $position ) {
		if ( ! isset( $this->time_frame_ids[ $day_id ][ $position ] ) ) {
			// Initialize the delivery day group.
			if ( ! isset( $this->time_frame_ids[ $day_id ] ) ) {
				$this->time_frame_ids[ $day_id ] = array();
			}

			$time_frames = $this->get_time_frames_for_day( $day_id );
			$time_frame  = wc_od_get_time_frame_at_position( $time_frames, $position );

			// Cache the result.
			$this->time_frame_ids[ $day_id ][ $position ] = ( $time_frame ? $time_frame->get_id() : false );
		}

		return $this->time_frame_ids[ $day_id ][ $position ];
	}
}
