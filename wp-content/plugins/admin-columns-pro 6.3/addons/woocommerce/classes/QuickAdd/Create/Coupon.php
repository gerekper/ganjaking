<?php

namespace ACA\WC\QuickAdd\Create;

use ACP;
use WC_Coupon;
use WP_User;

class Coupon implements ACP\QuickAdd\Model\Create {

	public function create() {
		$coupon = new WC_Coupon();
		$coupon->set_code( $this->generate_code() );
		$coupon->set_discount_type( 'percent' );
		$coupon->save();

		return $coupon->get_id();
	}

	private function generate_code() {
		$characters = "ABCDEFGHJKMNPQRSTUVWXYZ23456789";

		return substr( str_shuffle( $characters ), 0, 8 );
	}

	public function has_permission( WP_User $user ) {
		return user_can( $user, get_post_type_object( 'shop_coupon' )->cap->create_posts );
	}

}