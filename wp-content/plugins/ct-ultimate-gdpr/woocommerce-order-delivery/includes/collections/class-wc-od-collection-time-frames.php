<?php
/**
 * Collection: Time Frames.
 *
 * @package WC_OD/Collections
 * @since   1.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_OD_Collection_Time_Frames class.
 */
class WC_OD_Collection_Time_Frames extends WC_OD_Collection {

	/**
	 * Constructor.
	 *
	 * @since 1.6.0
	 *
	 * @param array $items The collection items.
	 */
	public function __construct( array $items = array() ) {
		$time_frames = $this->map_items( $items, array( $this, 'get_time_frame' ) );

		parent::__construct( $time_frames );
	}

	/**
	 * Sets a value at a specified offset.
	 *
	 * Casts the item to a time frame object before inserting it.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $offset The offset to assign the value to.
	 * @param mixed $value The value to set.
	 */
	public function offsetSet( $offset, $value ) {
		parent::offsetSet( $offset, $this->get_time_frame( $value, $offset ) );
	}

	/**
	 * Gets the time frame instance.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $time_frame Time frame object or data.
	 * @param int   $id         The time frame ID.
	 * @return WC_OD_Time_Frame
	 */
	protected function get_time_frame( $time_frame, $id ) {
		if ( ! $time_frame instanceof WC_OD_Time_Frame ) {
			$time_frame = new WC_OD_Time_Frame( $time_frame, $id );
		}

		return $time_frame;
	}
}
