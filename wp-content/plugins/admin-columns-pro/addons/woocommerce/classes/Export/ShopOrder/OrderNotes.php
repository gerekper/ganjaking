<?php

namespace ACA\WC\Export\ShopOrder;

use ACP;

/**
 * WooCommerce order title (default column) exportability model
 * @since 2.2.1
 */
class OrderNotes extends ACP\Export\Model {

	public function get_value( $id ) {
		return get_post( $id )->comment_count;
	}

}