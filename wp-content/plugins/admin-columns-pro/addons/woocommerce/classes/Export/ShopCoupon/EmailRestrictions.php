<?php

namespace ACA\WC\Export\ShopCoupon;

use ACP;

/**
 * @since 2.2.1
 */
class EmailRestrictions extends ACP\Export\Model {

	public function get_value( $id ) {
		return implode( ', ', $this->column->get_raw_value( $id ) );
	}

}