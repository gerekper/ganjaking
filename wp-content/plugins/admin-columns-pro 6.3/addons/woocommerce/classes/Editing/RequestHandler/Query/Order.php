<?php

namespace ACA\WC\Editing\RequestHandler\Query;

use AC\Request;
use ACP;
use ACP\Editing\ApplyFilter;

class Order implements ACP\Editing\RequestHandler
{

    /**
     * @var Request
     */
    private $request;

    public function handle(Request $request)
    {
        $this->request = $request;

        ob_start();

        add_filter('woocommerce_order_list_table_prepare_items_query_args', [$this, 'send'], PHP_INT_MAX - 100);
    }

    private function get_rows_per_iteration(): int
    {
        return (new ApplyFilter\RowsPerIteration($this->request))->apply_filters(2000);
    }

    public function send(array $args): void
    {
        ob_get_clean();

        $args['return'] = 'ids';
        $args['type'] = 'shop_order';
        $args['page'] = (int)$this->request->filter('ac_page', 1, FILTER_SANITIZE_NUMBER_INT);
        $args['limit'] = $this->get_rows_per_iteration();

        $orders = wc_get_orders($args);
        $order_ids = $orders->orders;

        $response = new ACP\Editing\Response\QueryRows($order_ids, $this->get_rows_per_iteration());
        $response->success();
    }
}