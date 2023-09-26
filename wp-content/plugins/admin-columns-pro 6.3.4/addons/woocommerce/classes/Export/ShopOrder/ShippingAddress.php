<?php

namespace ACA\WC\Export\ShopOrder;

use ACP;

class ShippingAddress implements ACP\Export\Service {

	public function get_value( $id ) {
		$address = wc_get_order( $id )->get_formatted_shipping_address();

		return preg_replace( '#<br\s*/?>#i', ', ', $address );
	}

}