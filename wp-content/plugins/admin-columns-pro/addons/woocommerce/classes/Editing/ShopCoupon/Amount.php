<?php

namespace ACA\WC\Editing\ShopCoupon;

use ACP;
use ACP\Editing\View;
use WC_Coupon;

class Amount implements ACP\Editing\Service {

	public function get_view( string $context ): ?View {
		return ( new ACP\Editing\View\Number() )->set_min( 0 )->set_step( 'any' );
	}

	public function get_value( $id ) {
		return ( new WC_Coupon( $id ) )->get_amount();
	}

	public function update( int $id, $data ): void {
		$coupon = new WC_Coupon( $id );
		$coupon->set_amount( $data );
		$coupon->save();
	}

}