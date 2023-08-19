<?php

namespace ACA\WC\Export\Strategy;

use AC;
use AC\ListTable;
use ACA\WC\ListTable\Orders;
use ACP\Export\Strategy;
use Automattic;
use Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableQuery;

class Order extends Strategy
{

    /**
     * @var string
     */
    private $order_type;

    public function __construct(AC\ListScreen $list_screen, $order_type = 'shop_order')
    {
        parent::__construct($list_screen);

        $this->order_type = $order_type;
    }

    public function get_total_items(): int
    {
        return $this->get_list_table()->get_total_items();
    }

    protected function ajax_export(): void
    {
        ob_start();
        add_filter('woocommerce_order_list_table_prepare_items_query_args', [$this, 'catch_posts'], 1000);
        add_filter('woocommerce_orders_table_query_clauses', [$this, 'alter_clauses'], 100, 2);
    }

    public function alter_clauses($clauses, OrdersTableQuery $query): array
    {
        $ids = $this->get_requested_ids();

        if ($ids) {
            $column = $query->get_table_name('orders') . '.ID';
            $ids = array_map('absint', $ids);

            $clauses['where'] .= sprintf(' AND %s IN( %s )', $column, implode(',', $ids));
        }

        return $clauses;
    }

    public function catch_posts($args): void
    {
        ob_get_clean();

        $args['return'] = 'ids';
        $args['type'] = $this->order_type;
        $args['page'] = $this->get_export_counter() + 1;
        $args['limit'] = $this->get_num_items_per_iteration();

        $orders = wc_get_orders($args);

        $this->export($orders->orders);
    }

    protected function get_list_table(): ListTable
    {
        return new Orders(
            wc_get_container()->get(Automattic\WooCommerce\Internal\Admin\Orders\ListTable::class)
        );
    }

}