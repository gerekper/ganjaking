<?php

namespace ACA\WC\Sorting\ShopOrder;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class OrderWeight implements QueryBindings
{

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias = $bindings->get_unique_alias('weight');

        $sub_query = "
			SELECT ID, SUM(total) as orderweight
			FROM (
				SELECT woi.order_id AS ID, woim2.meta_value*pm.meta_value AS total
				FROM {$wpdb->prefix}woocommerce_order_items AS woi
				LEFT JOIN ( 
					SELECT order_item_id, meta_value AS product_id FROM (
						SELECT * FROM {$wpdb->prefix}woocommerce_order_itemmeta
						WHERE meta_key = '_product_id' OR meta_key = '_variation_id'
						ORDER BY meta_value DESC
						LIMIT 1000000000
					) AS sq
					GROUP BY order_item_id
				) AS woim ON woi.order_item_id = woim.order_item_id
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS woim2 ON woi.order_item_id = woim2.order_item_id AND woim2.meta_key = '_qty'
				LEFT JOIN $wpdb->postmeta as pm ON woim.product_id = pm.post_id AND pm.meta_key = '_weight'
				WHERE woi.order_item_type = 'line_item'
			) AS total_order_weight
			GROUP BY total_order_weight.ID
		";

        $bindings->join("LEFT JOIN ( $sub_query ) AS $alias ON $wpdb->posts.ID = $alias.ID");
        $bindings->group_by("$wpdb->posts.ID");
        $bindings->order_by(
            SqlOrderByFactory::create("$alias.orderweight", (string)$order)
        );

        return $bindings;
    }

}