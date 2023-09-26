<?php

namespace ACA\WC\Export\ShopOrder;

use ACP;

class BillingAddress implements ACP\Export\Service {

	public function get_value( $id ) {
		$address = wc_get_order( $id )->get_formatted_billing_address();

		return preg_replace( '#<br\s*/?>#i', ', ', $address );
	}

}