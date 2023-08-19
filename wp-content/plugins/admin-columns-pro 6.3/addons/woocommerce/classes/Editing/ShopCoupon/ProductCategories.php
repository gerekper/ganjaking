<?php

namespace ACA\WC\Editing\ShopCoupon;

use ACA\WC\Editing;
use ACA\WC\Editing\Storage\Coupon\IncludedProductCategories;

class ProductCategories extends Editing\ProductCategories {

	public function __construct() {
		parent::__construct( new IncludedProductCategories() );
	}

}