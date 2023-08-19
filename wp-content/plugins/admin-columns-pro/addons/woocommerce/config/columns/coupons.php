<?php

use ACA\WC\Column;

// do not remove
$columns = [];

$columns[] = [
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
    Column\ShopCoupon\Products::class,
    Column\ShopCoupon\ProductsCategories::class,
    Column\ShopCoupon\Type::class,
    Column\ShopCoupon\Usage::class,
    Column\ShopCoupon\UsedBy::class,
];

if (ACA_WC_USE_HPOS) {
    $columns[] = [
        Column\ShopCoupon\Orders::class,
    ];
} else {
    $columns[] = [
        Column\ShopCoupon\ShopOrder\Orders::class,
    ];
}

return array_merge(...$columns);