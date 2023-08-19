<?php

use ACA\WC\Column;

return [
	// Original
	Column\Order\Original\Billing::class,
	Column\Order\Original\Date::class,
	Column\Order\Original\Order::class,
	Column\Order\Original\Status::class,
	Column\Order\Original\ShipTo::class,
	Column\Order\Original\Total::class,

	// Address
	Column\Order\Address\BillingAddress::class,
	Column\Order\Address\ShippingAddress::class,

	// Dates
	Column\Order\Date\CompletedDate::class,
	Column\Order\Date\CreatedDate::class,
	Column\Order\Date\ModifiedDate::class,
	Column\Order\Date\PaidDate::class,

	// Custom
	Column\Order\CouponsUsed::class,
	Column\Order\Currency::class,
	Column\Order\Customer::class,
	Column\Order\CustomerNote::class,
	Column\Order\CreatedVia::class,
	Column\Order\CreatedVersion::class,
	Column\Order\Discount::class,
	Column\Order\Downloads::class,
	Column\Order\Fees::class,
	Column\Order\Ip::class,
	Column\Order\IsCustomer::class,
	Column\Order\Notes::class,
	Column\Order\OrderNumber::class,
	Column\Order\PaidAmount::class,
	Column\Order\PaymentMethod::class,
	Column\Order\Product::class,
	Column\Order\ProductTaxonomy::class,
	Column\Order\Purchased::class,
	Column\Order\Refunds::class,
	Column\Order\Shipping::class,
	Column\Order\ShippingMethod::class,
	Column\Order\Subtotal::class,
	Column\Order\Tax::class,
	Column\Order\TotalWeight::class,
	Column\Order\TransactionId::class,
];