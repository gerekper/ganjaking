<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Order;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\EmptyValues;
use ACP\Sorting\Type\Order;

class ItemsSold implements QueryBindings
{

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias = $bindings->get_unique_alias('acsort');

        $bindings->join(
            "LEFT JOIN {$wpdb->prefix}wc_order_stats AS $alias ON $alias.order_id = {$wpdb->prefix}wc_orders.id"
        );

        $bindings->order_by(
            SqlOrderByFactory::create(
                "$alias.num_items_sold",
                (string)$order,
                [
                    'empty_values' => [EmptyValues::NULL, EmptyValues::ZERO],
                ]
            )
        );

        return $bindings;
    }

}