<?php

namespace ACA\WC\Editing\ShopCoupon;

use ACP;
use ACP\Editing\View;
use WC_Coupon;

class MinimumAmount implements ACP\Editing\Service {

	public function get_view( string $context ): ?View {
		$view = new ACP\Editing\View\Number();

		return $view->set_step( 'any' )->set_min( 0 );
	}

	public function get_value( $id ) {
		return ( new WC_Coupon( $id ) )->get_minimum_amount();
	}

	public function update( int $id, $data ): void {
		$coupon = new WC_Coupon( $id );
		$coupon->set_minimum_amount( $data );
		$coupon->save();
	}

}