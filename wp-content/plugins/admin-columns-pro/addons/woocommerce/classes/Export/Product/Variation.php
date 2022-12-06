<?php

namespace ACA\WC\Export\Product;

use ACP;

/**
 * WooCommerce product variation (default column) exportability model
 * @since 2.2.1
 */
class Variation extends ACP\Export\Model {

	public function get_value( $id ) {
		return count( $this->column->get_raw_value( $id ) );
	}

}