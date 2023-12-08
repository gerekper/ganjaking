<?php

namespace ACA\WC\Search\ProductVariation\Parent;

use ACP\Search\Operators;
use ACP\Search\Value;

class Id extends Field
{

    public function __construct()
    {
        parent::__construct(
            'ID',
            new Operators([
                Operators::EQ,
                Operators::LTE,
                Operators::GTE,
                Operators::BETWEEN,
                Operators::CONTAINS,
                Operators::NOT_CONTAINS,
            ]),
            Value::INT
        );
    }

}