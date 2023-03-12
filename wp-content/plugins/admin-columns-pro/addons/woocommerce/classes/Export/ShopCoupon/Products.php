<?php

namespace ACA\WC\Export\ShopCoupon;

use ACP;
use WC_Coupon;

class Products implements ACP\Export\Service {

	public function get_value( $id ) {
		$coupon = new WC_Coupon( $id );
		$product_ids = $coupon->get_product_ids();

		if ( count( $product_ids ) < 1 ) {
			return '';
		}

		return implode( ', ', $product_ids );
	}

}
