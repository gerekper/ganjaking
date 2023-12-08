<?php

namespace ACA\WC\Sorting\Order;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class ShippingMethod implements QueryBindings
{

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias = $bindings->get_unique_alias('sortmethod');
        $table = $wpdb->prefix . 'wc_orders';

        $bindings->join(
            "
			LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS acsort_oi ON $table.id = acsort_oi.order_id
				AND acsort_oi.order_item_type = 'shipping'
			LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS $alias ON acsort_oi.order_item_id = $alias.order_item_id
				AND $alias.meta_key = 'method_id'
		"
        );
        $bindings->group_by("$table.id");
        $bindings->order_by(
            SqlOrderByFactory::create("$alias.meta_value", (string)$order)
        );

        return $bindings;
    }

}