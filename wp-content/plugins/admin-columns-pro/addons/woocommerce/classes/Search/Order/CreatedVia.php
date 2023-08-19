<?php

namespace ACA\WC\Search\Order;

use AC\Helper\Select\Options;
use ACA\WC\Search;
use ACP;
use ACP\Search\Operators;
use ACP\Search\Value;

class CreatedVia extends ACP\Search\Comparison implements ACP\Search\Comparison\RemoteValues
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

    protected function create_query_bindings($operator, Value $value): ACP\Search\Query\Bindings
    {
        $bindings = new ACP\Search\Query\Bindings\QueryArguments();

        $bindings->query_arguments([
            'field_query' => [
                [
                    'field'   => 'created_via',
                    'value'   => $value->get_value(),
                    'compare' => $operator,
                ],
            ],
        ]);

        return $bindings;
    }

    public function get_values(): Options
    {
        global $wpdb;

        $sql = "SELECT DISTINCT(created_via) 
			FROM {$wpdb->prefix}wc_order_operational_data";

        $values = $wpdb->get_col($sql);
        $options = array_combine($values, $values);

        return Options::create_from_array($options ? array_filter($options) : []);
    }

}