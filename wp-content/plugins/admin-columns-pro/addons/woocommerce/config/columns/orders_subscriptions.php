<?php

use ACA\WC\Column;

return [
    // Original
    Column\OrderSubscription\Original\EndDate::class,
    Column\OrderSubscription\Original\LastOrderDate::class,
    Column\OrderSubscription\Original\NextPaymentDate::class,
    Column\OrderSubscription\Original\OrderTitle::class,
    Column\OrderSubscription\Original\OrderItems::class,
    Column\OrderSubscription\Original\Orders::class,
    Column\OrderSubscription\Original\RecurringTotal::class,
    Column\OrderSubscription\Original\Status::class,
    Column\OrderSubscription\Original\StartDate::class,
    Column\OrderSubscription\Original\TrialEndDate::class,

    // Common Order Columns
    Column\Order\Address\BillingAddress::class,
    Column\Order\Address\ShippingAddress::class,
    Column\Order\CouponsUsed::class,
    Column\Order\CreatedVersion::class,
    Column\Order\Currency::class,
    Column\Order\Customer::class,
    Column\Order\Discount::class,
    Column\Order\Fees::class,
    Column\Order\Ip::class,
    Column\Order\Notes::class,
    Column\Order\OrderNumber::class,
    Column\Order\PaymentMethod::class,
    Column\Order\Product::class,
    Column\Order\Purchased::class,
    Column\Order\Refunds::class,
    Column\Order\Shipping::class,
    Column\Order\ShippingMethod::class,
    Column\Order\Subtotal::class,
    Column\Order\Tax::class,

    // Subscription Columns
    Column\OrderSubscription\AutoRenewal::class,
    Column\OrderSubscription\BillingPeriod::class,
    Column\OrderSubscription\BillingInterval::class,
];