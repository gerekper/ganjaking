<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\Order;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class CustomerFullname implements QueryBindings
{

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $alias = $bindings->get_unique_alias('wcs_cf');

        $table_orders = $wpdb->prefix . 'wc_orders';
        $table_customer = $wpdb->prefix . 'wc_customer_lookup';

        $bindings->join(
            sprintf(
                "\nLEFT JOIN %s AS $alias ON $alias.user_id = %s.customer_id",
                esc_sql($table_customer),
                esc_sql($table_orders)
            )
        );

        $bindings->order_by(
            SqlOrderByFactory::create_with_concat(
                [
                    "$alias.first_name",
                    "$alias.last_name",
                ],
                (string)$order
            )
        );

        return $bindings;
    }

}