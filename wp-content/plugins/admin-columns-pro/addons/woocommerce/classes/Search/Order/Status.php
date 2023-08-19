<?php

namespace ACA\WC\Search\Order;

use AC\Helper\Select\Options;
use ACA\WC\Search;
use ACP;
use ACP\Search\Operators;
use ACP\Search\Value;

class Status extends ACP\Search\Comparison implements ACP\Search\Comparison\Values
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
                    'field'   => 'status',
                    'value'   => $value->get_value(),
                    'compare' => $operator,
                ],
            ],
        ]);

        return $bindings;
    }

    public function get_values(): Options
    {
        return Options::create_from_array(wc_get_order_statuses());
    }

}