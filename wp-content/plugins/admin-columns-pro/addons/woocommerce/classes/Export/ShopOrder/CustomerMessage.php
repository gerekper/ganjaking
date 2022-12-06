<?php

namespace ACA\WC\Export\ShopOrder;

use ACP;

/**
 * WooCommerce order customer message (default column) exportability model
 * @since 2.2.1
 */
class CustomerMessage extends ACP\Export\Model {

	public function get_value( $id ) {
		return wc_get_order( $id )->get_customer_note();
	}

}