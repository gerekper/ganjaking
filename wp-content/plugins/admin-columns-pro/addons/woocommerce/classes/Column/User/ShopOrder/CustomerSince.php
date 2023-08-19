<?php

namespace ACA\WC\Column\User\ShopOrder;

use AC;
use ACA\WC\Sorting;
use ACP\ConditionalFormat\Formattable;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\ConditionalFormat\Formatter\DateFormatter;
use ACP\Sorting\Sortable;

class CustomerSince extends AC\Column implements Sortable, Formattable
{

    public function __construct()
    {
        $this->set_type('column-wc-user-customer_since')
             ->set_label(__('Customer Since', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_raw_value($customer_id)
    {
        $orders = wc_get_orders([
            'limit'       => 1,
            'status'      => 'wc-completed',
            'customer_id' => $customer_id,
            'orderby'     => 'date',
            'order'       => 'ASC',
        ]);

        if ( ! $orders) {
            return false;
        }

        $order = $orders[0];

        $date = $order->get_date_created();

        if ( ! $date) {
            return false;
        }

        return $date->format('Y-m-d');
    }

    public function conditional_format(): ?FormattableConfig
    {
        return new FormattableConfig(new DateFormatter\FormatFormatter('Y-m-d'));
    }

    public function register_settings()
    {
        $this->add_setting(new AC\Settings\Column\Date($this));
    }

    public function sorting()
    {
        return new Sorting\User\ShopOrder\FirstOrder(['wc-completed']);
    }

}