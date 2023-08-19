<?php

namespace ACA\WC\Search\Order;

use ACA\WC\Search;
use ACP;
use ACP\Search\Operators;
use ACP\Search\Value;

class CreatedVersion extends ACP\Search\Comparison
{

    use OperatorMappingTrait;

    public function __construct()
    {
        parent::__construct(
            new Operators([
                Operators::EQ,
                Operators::NEQ,
                Operators::CONTAINS,
                Operators::NOT_CONTAINS,
            ])
        );
    }

    protected function create_query_bindings($operator, Value $value): ACP\Search\Query\Bindings
    {
        $bindings = new ACP\Search\Query\Bindings\QueryArguments();

        $bindings->query_arguments([
            'field_query' => [
                [
                    'field'   => 'version',
                    'value'   => $value->get_value(),
                    'compare' => $this->map_operator($operator),
                ],
            ],
        ]);

        return $bindings;
    }

}