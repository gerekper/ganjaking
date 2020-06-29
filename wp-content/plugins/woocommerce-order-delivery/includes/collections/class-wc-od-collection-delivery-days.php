<?php
/**
 * Collection: Delivery Days.
 *
 * @package WC_OD/Collections
 * @since   1.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_OD_Collection_Delivery_Days class.
 */
class WC_OD_Collection_Delivery_Days extends WC_OD_Collection {

	/**
	 * Constructor.
	 *
	 * @since 1.6.0
	 *
	 * @param array $items The collection items.
	 */
	public function __construct( array $items = array() ) {
		$delivery_days = $this->map_items( $items, array( $this, 'get_delivery_day' ) );

		parent::__construct( $delivery_days );
	}

	/**
	 * Sets a value at a specified offset.
	 *
	 * Casts the item to a delivery day object before inserting it.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $offset The offset to assign the value to.
	 * @param mixed $value The value to set.
	 */
	public function offsetSet( $offset, $value ) {
		parent::offsetSet( $offset, $this->get_delivery_day( $value, $offset ) );
	}

	/**
	 * Gets the delivery day instance.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $delivery_day Delivery day object or data.
	 * @param int   $weekday      The weekday number.
	 * @return WC_OD_Delivery_Day
	 */
	protected function get_delivery_day( $delivery_day, $weekday ) {
		if ( ! $delivery_day instanceof WC_OD_Delivery_Day ) {
			$delivery_day = new WC_OD_Delivery_Day( $delivery_day, $weekday );
		}

		return $delivery_day;
	}
}
