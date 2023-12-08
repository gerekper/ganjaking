<?php

namespace ACA\WC\Sorting\Order;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class ShippingMethodLabel implements QueryBindings
{

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias = $bindings->get_unique_alias('sortmethod');
        $table = $wpdb->prefix . 'wc_orders';

        $bindings->join(
            "
			LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS $alias ON $table.id = $alias.order_id
				AND $alias.order_item_type = 'shipping'
		"
        );
        $bindings->group_by("$table.id");
        $bindings->order_by(
            SqlOrderByFactory::create("$alias.order_item_name", (string)$order)
        );

        return $bindings;
    }

}