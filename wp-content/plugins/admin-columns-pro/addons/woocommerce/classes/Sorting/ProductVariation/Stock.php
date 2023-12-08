<?php

namespace ACA\WC\Sorting\ProductVariation;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class Stock implements QueryBindings
{

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias = $bindings->get_unique_alias('sort');
        $alias_stock = $bindings->get_unique_alias('sort');

        $bindings->join(
            " 
			INNER JOIN $wpdb->posts AS acsort_parent ON acsort_parent.ID = $wpdb->posts.post_parent
				AND acsort_parent.post_type = 'product'
			LEFT JOIN $wpdb->postmeta AS $alias ON $alias.post_id = $wpdb->posts.ID 
				AND $alias.meta_key = '_stock' AND $alias.meta_value <> ''
			LEFT JOIN $wpdb->postmeta AS $alias_stock ON $alias_stock.post_id = acsort_parent.ID 
				AND $alias_stock.meta_key = '_stock' AND $alias_stock.meta_value <> ''
		"
        );
        $bindings->order_by(
            SqlOrderByFactory::create(
                "CAST( COALESCE( NULLIF( $alias.meta_value, '' ), $alias_stock.meta_value ) AS SIGNED )",
                (string)$order,
                [
                    'esc_sql' => false,
                ]
            )
        );

        return $bindings;
    }

}