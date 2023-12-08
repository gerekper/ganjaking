<?php

namespace ACA\GravityForms\Search\Comparison\Entry;

use ACP;
use ACP\Query\Bindings;
use ACP\Search\Value;

class Choices extends Choice
{

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        $_value = new Value(
            sprintf('"%s"', $value->get_value()),
            $value->get_type()
        );

        $_operator = ACP\Search\Operators::EQ
            ? ACP\Search\Operators::CONTAINS
            : ACP\Search\Operators::NOT_CONTAINS;

        return parent::create_query_bindings($_operator, $_value);
    }

}