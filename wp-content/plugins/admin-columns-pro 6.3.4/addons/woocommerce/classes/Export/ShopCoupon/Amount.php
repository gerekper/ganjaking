<?php

namespace ACA\WC\Export\ShopCoupon;

use ACP;
use WC_Coupon;

class Amount implements ACP\Export\Service {

	public function get_value( $id ) {
		return ( new WC_Coupon( $id ) )->get_amount();
	}

}