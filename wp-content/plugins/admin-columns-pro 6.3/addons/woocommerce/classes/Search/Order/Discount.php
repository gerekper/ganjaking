<?php

namespace ACA\WC\Search\Order;

use ACA\WC\Search;
use ACP;
use ACP\Search\Operators;
use ACP\Search\Value;

class Discount extends ACP\Search\Comparison
{

    public function __construct()
    {
        parent::__construct(
            new Operators([
                Operators::EQ,
                Operators::LT,
                Operators::GT,
                Operators::BETWEEN,
            ]),
            Value::DECIMAL
        );
    }

    protected function create_query_bindings($operator, Value $value)
    {
        $bindings = new ACP\Search\Query\Bindings\QueryArguments();

        $bindings->query_arguments([
            'field_query' => [
                [
                    'field'   => 'discount_total',
                    'value'   => $value->get_value(),
                    'compare' => $operator,
                ],
            ],
        ]);

        return $bindings;
    }

}