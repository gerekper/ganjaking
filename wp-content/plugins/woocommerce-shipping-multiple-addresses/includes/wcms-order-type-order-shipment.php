<?php
/**
 * Class WC_MS_Order_Type_Order_Shipment file.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_MS_Order_Type_Order_Shipment
 */
class WC_MS_Order_Type_Order_Shipment extends WC_Order {
	/**
	 * Get internal type (post type.)
	 *
	 * @return string
	 */
	public function get_type() {
		return 'order_shipment';
	}
}
