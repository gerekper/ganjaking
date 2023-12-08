<?php

namespace ACA\WC\Sorting\Product;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Model\WarningAware;
use ACP\Sorting\Type\CastType;
use ACP\Sorting\Type\Order;

class Variation implements WarningAware, QueryBindings
{

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias = $bindings->get_unique_alias('sort');

        $sub_query = "
            SELECT ac_variation_count.ID, count( * ) AS count, post_parent
            FROM $wpdb->posts ac_variation_count
            WHERE post_type = 'product_variation'
                AND post_status = 'publish'
            GROUP BY post_parent
        ";

        $bindings->join(
            "LEFT JOIN ($sub_query) AS $alias ON $alias.post_parent = $wpdb->posts.ID"
        );
        $bindings->group_by("$wpdb->posts.ID");
        $bindings->order_by(
            SqlOrderByFactory::create(
                "$alias.count",
                (string)$order,
                [
                    'cast_type' => CastType::SIGNED,
                ]
            )
        );

        return $bindings;
    }

}