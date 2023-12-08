<?php

namespace ACA\WC\Sorting\ShopOrder;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\ComputationType;
use ACP\Sorting\Type\Order;

class ItemCount implements QueryBindings
{

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();
        $alias = $bindings->get_unique_alias('quantity');

        $bindings->join(
            "
			LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS acsort_order_items ON $wpdb->posts.ID = acsort_order_items.order_id
				AND acsort_order_items.order_item_type = 'line_item'
			LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS $alias ON $alias.order_item_id = acsort_order_items.order_item_id
				AND $alias.meta_key = '_qty'
		"
        );
        $bindings->group_by("$wpdb->posts.ID");
        $bindings->order_by(
            SqlOrderByFactory::create_with_computation(
                new ComputationType(ComputationType::SUM),
                "$alias.meta_value",
                (string)$order
            )
        );

        return $bindings;
    }

}