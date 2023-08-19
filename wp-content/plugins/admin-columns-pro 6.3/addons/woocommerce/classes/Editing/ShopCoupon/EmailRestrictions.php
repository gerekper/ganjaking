<?php

namespace ACA\WC\Editing\ShopCoupon;

use ACP;
use ACP\Editing\View;
use WC_Coupon;

class EmailRestrictions implements ACP\Editing\Service {

	public function get_view( string $context ): ?View {
		return ( new ACP\Editing\View\MultiInput() )->set_clear_button( true );
	}

	public function get_value( $id ) {
		return ( new WC_Coupon( $id ) )->get_email_restrictions();
	}

	public function update( int $id, $data ): void {
		$coupon = new WC_Coupon( $id );
		$coupon->set_email_restrictions( $data );
		$coupon->save();
	}

}