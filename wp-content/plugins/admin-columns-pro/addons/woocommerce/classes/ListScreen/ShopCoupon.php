<?php

namespace ACA\WC\ListScreen;

use ACA\WC\Column;
use ACP;

class ShopCoupon extends ACP\ListScreen\Post {

	public function __construct() {
		parent::__construct( 'shop_coupon' );

		$this->set_group( 'woocommerce' );
	}

	protected function register_column_types() {
		parent::register_column_types();

		$this->register_column_types_from_list( [
			Column\ShopCoupon\Amount::class,
			Column\ShopCoupon\Coupon::class,
			Column\ShopCoupon\CouponCode::class,
			Column\ShopCoupon\CouponDescription::class,
			Column\ShopCoupon\Description::class,
			Column\ShopCoupon\EmailRestrictions::class,
			Column\ShopCoupon\ExcludeProducts::class,
			Column\ShopCoupon\ExcludeProductsCategories::class,
			Column\ShopCoupon\ExpiryDate::class,
			Column\ShopCoupon\FreeShipping::class,
			Column\ShopCoupon\IncludeProducts::class,
			Column\ShopCoupon\Limit::class,
			Column\ShopCoupon\MaximumAmount::class,
			Column\ShopCoupon\MinimumAmount::class,
			Column\ShopCoupon\Orders::class,
			Column\ShopCoupon\Products::class,
			Column\ShopCoupon\ProductsCategories::class,
			Column\ShopCoupon\Type::class,
			Column\ShopCoupon\Usage::class,
			Column\ShopCoupon\UsedBy::class,
		] );
	}

}