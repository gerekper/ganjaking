<?php

namespace ACA\ACF\Search\Comparison\Repeater;

use ACA\ACF\Search\Comparison;
use ACP\Query\Bindings;
use ACP\Search\Operators;
use ACP\Search\Value;

class Toggle extends Comparison\Repeater
{

    public function __construct(string $meta_type, string $parent_key, string $sub_key)
    {
        $operators = new Operators([
            Operators::NOT_IS_EMPTY,
            Operators::IS_EMPTY,
        ]);

        parent::__construct($meta_type, $parent_key, $sub_key, $operators);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        return parent::create_query_bindings(
            $operator === Operators::NOT_IS_EMPTY ? Operators::EQ : $operator,
            new Value(
                1,
                Value::INT
            )
        );
    }

}