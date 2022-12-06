<?php

namespace ACA\WC\Export\ShopOrder;

use ACP;

/**
 * WooCommerce order billing address (default column) exportability model
 * @since 2.2.1
 */
class BillingAddress extends ACP\Export\Model {

	public function get_value( $id ) {
		$address = wc_get_order( $id )->get_formatted_billing_address();

		return preg_replace( '#<br\s*/?>#i', ', ', $address );
	}

}