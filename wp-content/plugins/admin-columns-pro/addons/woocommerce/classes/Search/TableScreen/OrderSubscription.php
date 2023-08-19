<?php

namespace ACA\WC\Search\TableScreen;

use ACP\Search;

class OrderSubscription extends Search\TableScreen
{

    public function register(): void
    {
        parent::register();

        add_action(
            'woocommerce_order_list_table_restrict_manage_orders',
            [$this, 'filters_markup'],
            1
        );
    }

}