<?php

namespace ACA\WC\Export\ShopCoupon;

use ACP;
use WC_Coupon;

/**
 * WooCommerce coupon type (default column) exportability model
 * @since 2.2.1
 */
class Type extends ACP\Export\Model {

	public function get_value( $id ) {
		$coupon = new WC_Coupon( $id );

		return wc_get_coupon_type( $coupon->get_discount_type() );
	}

}