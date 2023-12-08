<?php

namespace ACA\GravityForms\Search\Comparison\Entry;

use ACA\GravityForms\Search;
use ACP\Search\Operators;
use ACP\Search\Value;

class Consent extends Search\Comparison\Entry
{

    public function __construct($field)
    {
        $operators = new Operators([
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        parent::__construct($field, $operators, Value::STRING);
    }

}