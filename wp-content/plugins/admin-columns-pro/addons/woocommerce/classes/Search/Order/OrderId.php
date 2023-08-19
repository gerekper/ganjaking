<?php

namespace ACA\WC\Search\Order;

use ACA\WC\Search;
use ACP;
use ACP\Search\Operators;
use ACP\Search\Value;

class OrderId extends ACP\Search\Comparison
{

    public function __construct()
    {
        parent::__construct(
            new Operators([
                Operators::EQ,
                Operators::GT,
                Operators::LT,
                Operators::BETWEEN,
            ]),
            Value::INT
        );
    }

    protected function create_query_bindings($operator, Value $value)
    {
        $bindings = new ACP\Search\Query\Bindings\QueryArguments();

        $bindings->query_arguments([
            'field_query' => [
                [
                    'field'   => 'id',
                    'value'   => $value->get_value(),
                    'compare' => $operator,
                ],
            ],
        ]);

        return $bindings;
    }

}