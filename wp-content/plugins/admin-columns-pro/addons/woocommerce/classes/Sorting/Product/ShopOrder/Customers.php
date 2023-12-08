<?php

namespace ACA\WC\Sorting\Product\ShopOrder;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class Customers implements QueryBindings
{

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();
        $alias = $bindings->get_unique_alias('customer');

        $bindings->join(
            "
            LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS acsort_order_meta
                ON acsort_order_meta.meta_key = '_product_id' AND acsort_order_meta.meta_value = $wpdb->posts.ID
            LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS acsort_order_items
                ON acsort_order_meta.order_item_id = acsort_order_items.order_item_id AND acsort_order_items.order_item_type = 'line_item'
            LEFT JOIN $wpdb->posts AS ac_orders
                ON ac_orders.ID = acsort_order_items.order_id AND ac_orders.post_status = 'wc-completed'
            LEFT JOIN $wpdb->postmeta AS $alias
                ON ac_orders.ID = $alias.post_id AND $alias.meta_key = '_customer_user'
			"
        );
        $bindings->group_by("$wpdb->posts.ID");
        $bindings->order_by(
            SqlOrderByFactory::create_with_count("DISTINCT $alias.meta_value", (string)$order)
        );

        return $bindings;
    }

}