<?php

use ACA\WC\Column;

return [
    Column\User\Address::class,
    Column\User\Country::class,
    Column\User\Ratings::class,
    Column\User\Reviews::class,

    Column\User\ShopOrder\TotalSales::class,
    Column\User\ShopOrder\CustomerSince::class,
    Column\User\ShopOrder\FirstOrder::class,
    Column\User\ShopOrder\LastOrder::class,
    Column\User\ShopOrder\OrderCount::class,
    Column\User\ShopOrder\Orders::class,
    Column\User\ShopOrder\Products::class,
    Column\User\ShopOrder\CouponsUsed::class,
];