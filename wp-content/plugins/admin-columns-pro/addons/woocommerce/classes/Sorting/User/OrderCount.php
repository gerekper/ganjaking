<?php

declare(strict_types=1);

namespace ACA\WC\Sorting\User;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class OrderCount implements QueryBindings
{

    private $status;

    public function __construct(array $status = [])
    {
        $this->status = $status;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();

        $where_status = $this->status
            ? sprintf(
                " AND acsort_orders.status IN ( '%s' )",
                implode("','", array_map('esc_sql', $this->status))
            )
            : '';

        $alias = $bindings->get_unique_alias('order_count');

        $bindings->join(
            " 
            LEFT JOIN {$wpdb->prefix}wc_orders AS $alias ON $alias.customer_id = $wpdb->users.ID
                $where_status
            "
        );

        $bindings->group_by("$wpdb->users.ID");

        $bindings->order_by(
            SqlOrderByFactory::create_with_count(
                sprintf('%s.%s', $alias, 'id'),
                (string)$order
            )
        );

        return $bindings;
    }

}