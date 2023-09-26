<?php

namespace ACA\WC\Column\ShopCoupon;

use AC;
use ACP;
use WC_Coupon;

/**
 * @since 1.0
 */
class CouponCode extends AC\Column
	implements ACP\Editing\Editable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'coupon_code' )
		     ->set_original( true );
	}

	public function get_value( $id ) {
		return null;
	}

	public function get_raw_value( $id ) {
		return ( new WC_Coupon( $id ) )->get_code();
	}

	public function editing() {
		return new ACP\Editing\Service\Basic(
			( new ACP\Editing\View\Text() )->set_js_selector( 'strong > a ' ),
			new ACP\Editing\Storage\Post\Field( 'post_title' )
		);
	}

	public function search() {
		return new ACP\Search\Comparison\Post\Title();
	}

}