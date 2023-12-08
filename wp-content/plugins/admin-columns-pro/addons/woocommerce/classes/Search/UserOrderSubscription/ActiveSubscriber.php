<?php

namespace ACA\WC\Search\UserOrderSubscription;

use ACP;
use ACP\Query\Bindings;
use ACP\Search\Operators;
use ACP\Search\Value;

class ActiveSubscriber extends ACP\Search\Comparison
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);
        parent::__construct($operators);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = new Bindings();
        $alias_order = $bindings->get_unique_alias('orders');
        $bindings->join(
            "
            LEFT JOIN {$wpdb->prefix}wc_orders AS $alias_order ON $wpdb->users.ID = $alias_order.customer_id 
                AND $alias_order.type = 'shop_subscription' AND $alias_order.status = 'wc-active'
        "
        );

        $compare = $operator === Operators::IS_EMPTY ? 'IS NULL' : 'IS NOT NULL';
        $bindings->where("$alias_order.id $compare");

        return $bindings;
    }

}