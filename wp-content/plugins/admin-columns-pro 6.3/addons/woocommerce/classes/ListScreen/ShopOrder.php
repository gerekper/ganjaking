<?php

namespace ACA\WC\ListScreen;

use ACA\WC\Column;
use ACP;

class ShopOrder extends ACP\ListScreen\Post
{

    public function __construct()
    {
        parent::__construct('shop_order');

        $this->group = 'woocommerce';
    }

    protected function register_column_types(): void
    {
        parent::register_column_types();

        $this->register_column_types_from_list([
            Column\ShopOrder\Address\Billing::class,
            Column\ShopOrder\Address\Shipping::class,
            Column\ShopOrder\Actions::class,
            Column\ShopOrder\BillingAddress::class,
            Column\ShopOrder\CouponsUsed::class,
            Column\ShopOrder\Currency::class,
            Column\ShopOrder\Customer::class,
            Column\ShopOrder\CustomerMessage::class,
            Column\ShopOrder\CustomerNote::class,
            Column\ShopOrder\Date::class,
            Column\ShopOrder\Discount::class,
            Column\ShopOrder\Downloads::class,
            Column\ShopOrder\Fees::class,
            Column\ShopOrder\IP::class,
            Column\ShopOrder\IsCustomer::class,
            Column\ShopOrder\Notes::class,
            Column\ShopOrder\Order::class,
            Column\ShopOrder\OrderDate::class,
            Column\ShopOrder\OrderNotes::class,
            Column\ShopOrder\OrderNumber::class,
            Column\ShopOrder\OrderNumberOriginal::class,
            Column\ShopOrder\PaidAmount::class,
            Column\ShopOrder\PaymentMethod::class,
            Column\ShopOrder\Product::class,
            Column\ShopOrder\ProductCategories::class,
            Column\ShopOrder\ProductDetails::class,
            Column\ShopOrder\ProductTags::class,
            Column\ShopOrder\Purchased::class,
            Column\ShopOrder\Refunds::class,
            Column\ShopOrder\Shipping::class,
            Column\ShopOrder\ShippingAddress::class,
            Column\ShopOrder\ShippingMethod::class,
            Column\ShopOrder\Status::class,
            Column\ShopOrder\StatusIcon::class,
            Column\ShopOrder\Subtotal::class,
            Column\ShopOrder\Tax::class,
            Column\ShopOrder\Total::class,
            Column\ShopOrder\Totals::class,
            Column\ShopOrder\TotalWeight::class,
            Column\ShopOrder\TransactionID::class,
            Column\ShopOrder\WcActions::class,
        ]);
    }

}