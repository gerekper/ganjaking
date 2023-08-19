<?php

namespace ACA\WC\Column\User;

use AC;
use ACA\WC\Sorting;
use ACP\ConditionalFormat\Formattable;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\ConditionalFormat\Formatter\DateFormatter;
use ACP\Sorting\Sortable;

class CustomerSince extends AC\Column implements Formattable, Sortable
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

        $date = $orders[0]->get_date_created();

        return $date ? $date->format('Y-m-d') : false;
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
        return new Sorting\User\OrderExtrema('min');
    }

}