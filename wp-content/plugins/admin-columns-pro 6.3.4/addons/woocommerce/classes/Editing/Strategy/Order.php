<?php

namespace ACA\WC\Editing\Strategy;

use AC\ListTable;
use ACA\WC\Editing;
use ACA\WC\ListTable\Orders;
use ACP;
use Automattic;

class Order implements ACP\Editing\Strategy
{

    public function user_can_edit_item(int $id): bool
    {
        return $this->user_can_edit();
    }

    public function user_can_edit(): bool
    {
        return current_user_can('edit_shop_orders');
    }

    public function get_query_request_handler(): ACP\Editing\RequestHandler
    {
        return new Editing\RequestHandler\Query\Order();
    }

    public function get_total_items(): int
    {
        return $this->get_list_table()->get_total_items();
    }

    protected function get_list_table(): ListTable
    {
        static $list_table = null;

        if (null === $list_table) {
            $list_table = new Orders(
                wc_get_container()->get(Automattic\WooCommerce\Internal\Admin\Orders\ListTable::class)
            );
        }

        return $list_table;
    }

}