<?php

namespace ACP\Search\Comparison\Meta;

use ACP\Search\Comparison\Meta;
use ACP\Search\Operators;
use ACP\Search\Value;

class Decimal extends Meta
{

    public function __construct(string $meta_key)
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::NEQ,
            Operators::GT,
            Operators::LT,
            Operators::GTE,
            Operators::LTE,
            Operators::BETWEEN,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        // Filtering on decimals do not work with VALUE::INT
        parent::__construct($operators, $meta_key, Value::DECIMAL);
    }

}