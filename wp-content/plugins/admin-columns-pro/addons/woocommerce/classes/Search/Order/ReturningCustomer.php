<?php

namespace ACA\WC\Search\Order;

use AC\Helper\Select\Options;
use ACA\WC\Search;
use ACP;
use ACP\Query\Bindings;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

class ReturningCustomer extends ACP\Search\Comparison implements ACP\Search\Comparison\Values
{

    public function __construct()
    {
        parent::__construct(
            new Operators([
                Operators::EQ,
            ])
        );
    }

    protected function create_query_bindings($operator, Value $value): ACP\Query\Bindings
    {
        global $wpdb;

        $bindings = new Bindings();
        $alias = $bindings->get_unique_alias('stats');

        $bindings->join(
            "
                JOIN {$wpdb->prefix}wc_order_stats AS $alias 
                ON {$wpdb->prefix}wc_orders.id = $alias.order_id
                "
        );
        $bindings->where(ComparisonFactory::create("{$alias}.returning_customer", $operator, $value)());

        return $bindings;
    }

    public function get_values(): Options
    {
        return Options::create_from_array([
            0 => __('False'),
            1 => __('True'),
        ]);
    }

}