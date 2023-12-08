<?php

namespace ACA\WC\Column\ShopOrder\Address;

use ACA\WC\Column\ShopOrder\Address;
use ACA\WC\Settings;
use WC_Order;

class Shipping extends Address
{

    public function __construct()
    {
        $this->set_type('column-wc-order_shipping_address')
             ->set_label(__('Shipping Address', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    protected function get_meta_key_prefix(): string
    {
        return '_shipping_';
    }

    protected function get_formatted_address(WC_Order $order): string
    {
        return $order->get_formatted_shipping_address();
    }

    public function get_setting_address_object(): Settings\Address
    {
        return new Settings\Address($this);
    }

}