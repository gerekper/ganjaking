<?php
/**
 * Delivery ranges
 *
 * Handles the storage and retrieval of delivery ranges.
 *
 * @package WC_OD
 * @since   1.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_OD_Delivery_Ranges.
 */
class WC_OD_Delivery_Ranges {

	/**
	 * Gets the delivery ranges from the database.
	 *
	 * @since 1.7.0
	 *
	 * @return WC_OD_Delivery_Range[]
	 */
	public static function get_ranges() {
		try {
			$data_store = WC_Data_Store::load( 'delivery_range' );

			return $data_store->get_ranges();
		} catch ( Exception $e ) {
			return array();
		}
	}

	/**
	 * Gets the delivery range by ID.
	 *
	 * @since 1.7.0
	 *
	 * @param int $range_id The range ID.
	 * @return WC_OD_Delivery_Range|false The delivery range object. False on failure.
	 */
	public static function get_range( $range_id ) {
		try {
			return new WC_OD_Delivery_Range( $range_id );
		} catch ( Exception $e ) {
			return false;
		}
	}

	/**
	 * Gets the delivery range for the specified shipping method.
	 *
	 * @since 1.7.0
	 *
	 * @param string $shipping_method The shipping method.
	 * @return WC_OD_Delivery_Range The delivery range object.
	 */
	public static function get_range_matching_shipping_method( $shipping_method ) {
		$ranges = self::get_ranges();

		foreach ( $ranges as $range ) {
			// Match found.
			if ( $range->is_valid_for_shipping_method( $shipping_method ) ) {
				return $range;
			}
		}

		// Use the default delivery range as a fallback.
		return self::get_range( 0 );
	}
}
