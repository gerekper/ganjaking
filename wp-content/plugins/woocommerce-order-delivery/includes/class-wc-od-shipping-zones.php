<?php
/**
 * Class to handle shipping zones.
 *
 * @package WC_OD
 * @since   2.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shipping zones class.
 */
class WC_OD_Shipping_Zones {

	/**
	 * Gets the shipping zones.
	 *
	 * The method `WC_Shipping_Zones::get_zones()` returns the zones' data, not `WC_Shipping_zone` objects.
	 *
	 * @since 2.2.0
	 *
	 * @return WC_Shipping_Zone[]
	 */
	public static function get_zones() {
		try {
			$data_store = WC_Data_Store::load( 'shipping-zone' );
			$raw_zones  = $data_store->get_zones();
			$zone_ids   = wp_list_pluck( $raw_zones, 'zone_id' );

			// Use the zone IDs as indices.
			$zones = array_combine( $zone_ids, array_map( array( __CLASS__, 'get_zone' ), $zone_ids ) );
		} catch ( Exception $e ) {
			$zones = array();
		}

		return $zones;
	}

	/**
	 * Gets the shipping zone.
	 *
	 * @since 2.2.0
	 *
	 * @param mixed $the_zone Shipping Zone object or ID.
	 * @return WC_Shipping_Zone
	 */
	public static function get_zone( $the_zone ) {
		return ( $the_zone instanceof WC_Shipping_Zone ? $the_zone : new WC_Shipping_Zone( $the_zone ) );
	}
}
