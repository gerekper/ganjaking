<?php

namespace ACA\WC\Search\Order;

use ACA\WC\Search;
use ACP;
use ACP\Search\Operators;
use ACP\Search\Value;

class CustomerIp extends ACP\Search\Comparison
{

    public function __construct()
    {
        parent::__construct(
            new Operators([
                Operators::CONTAINS,
                Operators::NOT_CONTAINS,
            ])
        );
    }

    protected function create_query_bindings($operator, Value $value)
    {
        $bindings = new ACP\Search\Query\Bindings\QueryArguments();

        $bindings->query_arguments([
            'field_query' => [
                [
                    'field'   => 'customer_ip_address',
                    'value'   => $value->get_value(),
                    'compare' => $operator === Operators::CONTAINS ? 'LIKE' : 'NOT LIKE',
                ],
            ],
        ]);

        return $bindings;
    }

}