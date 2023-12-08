<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Product;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Model\WarningAware;
use ACP\Sorting\Type\ComputationType;
use ACP\Sorting\Type\Order;

class Dimensions implements WarningAware, QueryBindings
{

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias_l = $bindings->get_unique_alias('length');
        $alias_w = $bindings->get_unique_alias('width');
        $alias_h = $bindings->get_unique_alias('height');

        $bindings->join(
            "
				LEFT JOIN $wpdb->postmeta AS $alias_l
					ON $alias_l.post_id = $wpdb->posts.ID
					AND $alias_l.meta_key = '_length' 
				LEFT JOIN $wpdb->postmeta AS $alias_w
					ON $alias_w.post_id = $wpdb->posts.ID
					AND $alias_w.meta_key = '_width' 
				LEFT JOIN $wpdb->postmeta AS $alias_h
					ON $alias_h.post_id = $wpdb->posts.ID
					AND $alias_h.meta_key = '_height' 
				"
        );
        $bindings->group_by("$wpdb->posts.ID");
        $bindings->order_by(
            SqlOrderByFactory::create_with_computation(
                new ComputationType(ComputationType::SUM),
                "$alias_l.meta_value * $alias_w.meta_value * $alias_h.meta_value",
                (string)$order
            )
        );

        return $bindings;
    }

}