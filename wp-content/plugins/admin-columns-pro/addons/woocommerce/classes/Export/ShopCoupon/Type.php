<?php

namespace ACA\WC\Export\ShopCoupon;

use ACP;
use WC_Coupon;

class Type implements ACP\Export\Service {

	public function get_value( $id ) {
		$coupon = new WC_Coupon( $id );
		$type = $coupon->get_discount_type();

		return $type
			? wc_get_coupon_type( $type )
			: '';
	}

}