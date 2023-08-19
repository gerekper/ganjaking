<?php

namespace ACA\WC\Editing\ShopCoupon;

use ACA\WC\Editing\ProductCategories;
use ACA\WC\Editing\Storage\Coupon\ExcludedProductCategories;

class ExcludeProductCategories extends ProductCategories {

	public function __construct() {
		parent::__construct( new ExcludedProductCategories() );
	}

}