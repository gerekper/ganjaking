<?php

namespace ACA\WC\Sorting\ShopCoupon;

use ACP\Sorting\Model\Post\MetaMapping;

class Type extends MetaMapping {

	public function __construct() {
		parent::__construct( 'discount_type', $this->get_sorted_fields() );
	}

	private function get_sorted_fields() {
		$types = wc_get_coupon_types();
		natcasesort( $types );

		return array_keys( $types );
	}

}