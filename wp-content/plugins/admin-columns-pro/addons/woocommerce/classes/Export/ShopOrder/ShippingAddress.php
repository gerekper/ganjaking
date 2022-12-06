<?php

namespace ACA\WC\Export\ShopOrder;

use ACP;

/**
 * WooCommerce order shipping address (default column) exportability model
 * @since 2.2.1
 */
class ShippingAddress extends ACP\Export\Model {

	public function get_value( $id ) {
		$address = wc_get_order( $id )->get_formatted_shipping_address();

		return preg_replace( '#<br\s*/?>#i', ', ', $address );
	}

}