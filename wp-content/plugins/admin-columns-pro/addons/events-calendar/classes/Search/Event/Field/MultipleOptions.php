<?php

namespace ACA\EC\Search\Event\Field;

use ACP\Query\Bindings;
use ACP\Search\Operators;
use ACP\Search\Value;

class MultipleOptions extends Options
{

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        switch ($operator) {
            case Operators::EQ:
                return parent::create_query_bindings(Operators::CONTAINS, $value);
            case Operators::NEQ:
                return parent::create_query_bindings(Operators::NOT_CONTAINS, $value);
            default:
                return parent::create_query_bindings($operator, $value);
        }
    }
}