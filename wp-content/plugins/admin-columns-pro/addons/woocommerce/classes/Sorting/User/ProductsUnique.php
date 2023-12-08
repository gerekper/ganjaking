<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\User;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class ProductsUnique implements QueryBindings
{

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias_order = $bindings->get_unique_alias('order_count');
        $alias_product = $bindings->get_unique_alias('order_product_lookup');

        $statuses = array_map('esc_sql', wc_get_is_paid_statuses());
        $statuses_sql = "( 'wc-" . implode("','wc-", $statuses) . "' )";

        $bindings->join(
            " 
            LEFT JOIN {$wpdb->prefix}wc_orders AS $alias_order ON $alias_order.customer_id = $wpdb->users.ID
                AND $alias_order.status IN $statuses_sql
            LEFT JOIN {$wpdb->prefix}wc_order_product_lookup AS $alias_product ON $alias_product.order_id = $alias_order.id
            "
        );

        $bindings->group_by("$wpdb->users.ID");

        $bindings->order_by(
            SqlOrderByFactory::create_with_count(
                sprintf('%s.%s', $alias_product, 'product_id'),
                (string)$order
            )
        );

        return $bindings;
    }

}