<?php

namespace ACA\WC\Editing\ShopCoupon;

use ACP;
use ACP\Editing\View;
use WC_Coupon;

class Type implements ACP\Editing\Service {

	public function get_view( string $context ): ?View {
		return new ACP\Editing\View\Select( wc_get_coupon_types() );
	}

	public function get_value( $id ) {
		return ( new WC_Coupon( $id ) )->get_discount_type();
	}

	public function update( int $id, $data ): void {
		$coupon = new WC_Coupon( $id );
		$coupon->set_discount_type( $data );
		$coupon->save();
	}

}