<?php

namespace ACA\WC\Search\Order;

use AC;
use ACA\WC\Search;
use ACP;
use ACP\Search\Operators;
use ACP\Search\Value;

class Currency extends ACP\Search\Comparison implements ACP\Search\Comparison\Values
{

    public function __construct()
    {
        parent::__construct(
            new Operators([
                Operators::EQ,
                Operators::NEQ,
            ])
        );
    }

    protected function create_query_bindings($operator, Value $value)
    {
        $bindings = new ACP\Search\Query\Bindings\QueryArguments();

        $bindings->query_arguments([
            'currency' => $value->get_value(),
        ]);

        return $bindings;
    }

    public function get_values()
    {
        global $wpdb;

        $sql = "SELECT DISTINCT(currency) FROM {$wpdb->prefix}wc_orders";
        $entities = $wpdb->get_col($sql);

        $options = array_combine($entities, $entities);

        return AC\Helper\Select\Options::create_from_array($options);
    }

}