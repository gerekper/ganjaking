<?php

namespace ACA\WC\Editing\ShopCoupon;

use ACA\WC\Editing\View;
use ACP;
use WC_Coupon;

class Usage implements ACP\Editing\Service {

	public function get_view( string $context ): ?\ACP\Editing\View {
		return new View\Usage();
	}

	public function get_value( $id ) {
		$coupon = new WC_Coupon( $id );

		return (object) [
			'usage_limit'          => $coupon->get_usage_limit(),
			'usage_limit_per_user' => $coupon->get_usage_limit_per_user(),
			'usage_limit_products' => $coupon->get_limit_usage_to_x_items(),
		];
	}

	public function update( int $id, $data ): void {
		$value = (array) $data;
		$coupon = new WC_Coupon( $id );

		$coupon->set_usage_limit( $value['usage_limit'] );
		$coupon->set_usage_limit_per_user( $value['usage_limit_per_user'] );
		$coupon->set_limit_usage_to_x_items( $value['usage_limit_products'] );
		$coupon->save();
	}

}