<?php

namespace ACA\WC\Export\ShopOrder;

use AC\Collection;
use ACP;

/**
 * @since 2.2.1
 */
class ProductCustomField extends ACP\Export\Model {

	public function get_value( $id ) {
		$collection = $this->column->get_raw_value( $id );

		if ( ! $collection instanceof Collection ) {
			return false;
		}

		return $collection->implode( ', ' );
	}

}