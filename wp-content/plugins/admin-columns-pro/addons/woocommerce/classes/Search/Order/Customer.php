<?php

namespace ACA\WC\Search\Order;

use ACA\WC\Search;
use ACP;
use ACP\Query\Bindings;
use ACP\Search\Operators;
use ACP\Search\Value;

class Customer extends ACP\Search\Comparison implements ACP\Search\Comparison\SearchableValues
{

    use ACP\Search\UserValuesTrait;

    public function __construct()
    {
        parent::__construct(
            new Operators([
                Operators::EQ,
                Operators::IS_EMPTY,
                Operators::NOT_IS_EMPTY,
            ])
        );
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        $bindings = new Bindings\QueryArguments();

        $compare = '=';
        $customer_id = $value->get_value();

        switch ($operator) {
            case Operators::IS_EMPTY:
                $customer_id = 0;
                break;
            case Operators::NOT_IS_EMPTY:
                $compare = '!=';
                $customer_id = 0;
        }

        $bindings->query_arguments([
            'field_query' => [
                [
                    'field'   => 'customer_id',
                    'value'   => $customer_id,
                    'compare' => $compare,
                ],
            ],
        ]);

        return $bindings;
    }

}