<?php

namespace ACA\WC\Export\ShopCoupon;

use ACP;
use WC_Coupon;

/**
 * WooCommerce coupon amount (default column) exportability model
 * @since 2.2.1
 */
class Amount extends ACP\Export\Model {

	public function get_value( $id ) {
		$coupon = new WC_Coupon( $id );

		return $coupon->get_amount();
	}

}