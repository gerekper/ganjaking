<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\User;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\ComputationType;
use ACP\Sorting\Type\Order;

class TotalSales implements QueryBindings
{

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $statuses = array_map('esc_sql', wc_get_is_paid_statuses());
        $statuses_sql = "( 'wc-" . implode("','wc-", $statuses) . "' )";

        $alias = $bindings->get_unique_alias('total_sales');

        $bindings->join(
            " 
            LEFT JOIN {$wpdb->prefix}wc_orders AS $alias ON $alias.customer_id = $wpdb->users.ID
                AND $alias.status IN $statuses_sql
            "
        );

        $bindings->group_by("$wpdb->users.ID");

        $bindings->order_by(
            SqlOrderByFactory::create_with_computation(
                new ComputationType(ComputationType::SUM),
                sprintf('%s.%s', $alias, 'total_amount'),
                (string)$order
            )
        );

        return $bindings;
    }

}