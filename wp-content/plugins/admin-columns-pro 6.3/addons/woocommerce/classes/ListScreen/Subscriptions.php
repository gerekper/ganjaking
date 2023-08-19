<?php

namespace ACA\WC\ListScreen;

use ACA\WC\Column\ShopOrder;
use ACA\WC\Column\ShopSubscription;
use ACP;

class Subscriptions extends ACP\ListScreen\Post
{

    public function __construct()
    {
        parent::__construct('shop_subscription');

        $this->group = 'woocommerce';
    }

    protected function register_column_types(): void
    {
        parent::register_column_types();

        $this->register_column_types_from_list([
            ShopOrder\Address\Shipping::class,
            ShopOrder\Address\Billing::class,
            ShopOrder\Customer::class,
            ShopOrder\CouponsUsed::class,
            ShopOrder\Currency::class,
            ShopOrder\Discount::class,
            ShopOrder\Downloads::class,
            ShopOrder\Product::class,
            ShopOrder\ProductDetails::class,
            ShopOrder\Order::class,
            ShopOrder\Purchased::class,
            ShopOrder\Subtotal::class,
            ShopOrder\Tax::class,
            ShopOrder\Tax::class,
            ShopOrder\Totals::class,
            ShopSubscription\AutoRenewal::class,
            ShopSubscription\EndDate::class,
            ShopSubscription\LastPaymentDate::class,
            ShopSubscription\NextPaymentDate::class,
            ShopSubscription\Orders::class,
            ShopSubscription\OrderItems::class,
            ShopSubscription\RecurringTotal::class,
            ShopSubscription\StartDate::class,
            ShopSubscription\Status::class,
            ShopSubscription\SubscriptionDate::class,
            ShopSubscription\TotalRevenue::class,
            ShopSubscription\TrailEndDate::class,
        ]);
    }

}