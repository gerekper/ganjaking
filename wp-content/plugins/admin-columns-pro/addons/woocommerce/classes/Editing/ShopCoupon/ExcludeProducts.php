<?php

namespace ACA\WC\Editing\ShopCoupon;

use ACA\WC\Editing\ProductRelations;
use ACA\WC\Editing\Storage;

class ExcludeProducts extends ProductRelations {

	public function __construct() {
		parent::__construct( new Storage\Coupon\ExcludedProducts() );
	}

}