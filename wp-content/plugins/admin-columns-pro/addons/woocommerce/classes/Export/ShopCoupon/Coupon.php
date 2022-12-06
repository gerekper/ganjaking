<?php

namespace ACA\WC\Export\ShopCoupon;

use ACP;
use WC_Coupon;

/**
 * WooCommerce coupon basic information (default column) exportability model
 * @since 2.2.1
 */
class Coupon extends ACP\Export\Model {

	public function get_value( $id ) {
		$coupon = new WC_Coupon( $id );

		return $coupon->get_code();
	}

}