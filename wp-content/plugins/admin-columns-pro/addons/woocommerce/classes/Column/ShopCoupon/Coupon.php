<?php

namespace ACA\WC\Column\ShopCoupon;

use AC;
use ACA\WC\Export;
use ACP;

/**
 * @since 2.2
 */
class Coupon extends AC\Column
	implements ACP\Export\Exportable {

	public function __construct() {
		$this->set_type( 'coupon' )
		     ->set_original( true );
	}

	public function export() {
		return new Export\ShopCoupon\Coupon();
	}

}