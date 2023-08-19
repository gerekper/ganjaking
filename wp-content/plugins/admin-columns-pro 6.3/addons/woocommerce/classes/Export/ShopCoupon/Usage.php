<?php

namespace ACA\WC\Export\ShopCoupon;

use ACP;
use WC_Coupon;

class Usage implements ACP\Export\Service {

	public function get_value( $id ) {
		$coupon = new WC_Coupon( $id );

		$usage_count = $coupon->get_usage_count();
		$usage_limit = $coupon->get_usage_limit();

		$limit_string = $usage_limit ?: __( 'Infinity', 'codepress-admin-columns' );

		return sprintf( '%d / %s', $usage_count, $limit_string );
	}

}