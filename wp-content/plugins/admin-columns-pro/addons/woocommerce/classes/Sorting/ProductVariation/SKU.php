<?php

namespace ACA\WC\Sorting\ProductVariation;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class SKU implements QueryBindings
{

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();
        $alias_sku = $bindings->get_unique_alias('ssku');
        $alias_sku_parent = $bindings->get_unique_alias('ssku');

        $bindings->join(
            " 
			LEFT JOIN $wpdb->postmeta AS $alias_sku ON $alias_sku.post_id = $wpdb->posts.ID 
				AND $alias_sku.meta_key = '_sku'
			INNER JOIN $wpdb->posts AS acsort_parent ON acsort_parent.ID = $wpdb->posts.post_parent
				AND acsort_parent.post_type = 'product'
			LEFT JOIN $wpdb->postmeta AS $alias_sku_parent ON $alias_sku_parent.post_id = acsort_parent.ID 
				AND $alias_sku_parent.meta_key = '_sku'
		"
        );
        $bindings->group_by("$wpdb->posts.ID");
        $bindings->order_by(
            SqlOrderByFactory::create(
                "COALESCE( NULLIF( $alias_sku.meta_value, '' ), $alias_sku_parent.meta_value )",
                (string)$order,
                [
                    'esc_sql' => false,
                ]
            )
        );

        return $bindings;
    }

}