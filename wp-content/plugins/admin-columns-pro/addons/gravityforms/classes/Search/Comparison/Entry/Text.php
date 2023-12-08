<?php

namespace ACA\GravityForms\Search\Comparison\Entry;

use ACA\GravityForms\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class Text extends Comparison\Entry
{

    public function __construct(string $field)
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::NEQ,
            Operators::CONTAINS,
            Operators::NOT_CONTAINS,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        parent::__construct($field, $operators, Value::STRING);
    }

}