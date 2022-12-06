<?php

namespace ACA\WC\Editing\ShopCoupon;

use ACP;
use ACP\Editing\View;
use WC_Coupon;

class ExpiryDate implements ACP\Editing\Service {

	public function get_view( string $context ): ?View {
		return ( new ACP\Editing\View\Date() )->set_clear_button( true );
	}

	public function get_value( $id ) {
		$coupon = new WC_Coupon( $id );
		$date = $coupon->get_date_expires();

		return $date
			? $date->date( 'Y-m-d' )
			: false;
	}

	public function update( int $id, $data ): void {
		$expires = $data ? strtotime( $data ) : '';

		$coupon = new WC_Coupon( $id );
		$coupon->set_date_expires( $expires );
		$coupon->save();
	}

}