<?php

namespace ACA\WC\Editing\ShopCoupon;

use AC\Helper\Select\Option;
use AC\Type\ToggleOptions;
use ACP;
use ACP\Editing\View;
use WC_Coupon;

class FreeShipping implements ACP\Editing\Service {

	public function get_view( string $context ): ?View {
		return new ACP\Editing\View\Toggle(
			new ToggleOptions(
				new Option( 'no' ), new Option( 'yes' )
			)
		);
	}

	public function get_value( $id ) {
		$coupon = new WC_Coupon( $id );

		return $coupon->get_free_shipping() ? 'yes' : 'no';
	}

	public function update( int $id, $data ): void {
		$coupon = new WC_Coupon( $id );
		$coupon->set_free_shipping( 'yes' === $data );
		$coupon->save();
	}

}