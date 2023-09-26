<?php

namespace ACA\WC\Editing\TableRows;

use ACP;

class Order extends ACP\Editing\Ajax\TableRows
{

    public function register(): void
    {
        ob_start();
        add_action('woocommerce_order_list_table_prepare_items_query_args', [$this, 'handle_request']);
    }

    public function handle_request(): void
    {
        ob_clean();
        parent::handle_request();
    }

}