<?php

namespace ACA\WC\Search\Order;

use ACA\WC\Search;
use ACP\Search\Operators;
use ACP\Search\Value;

class OperationalDataPrice extends OperationalDataField
{

    public function __construct(string $field)
    {
        parent::__construct(
            $field,
            new Operators([
                Operators::EQ,
                Operators::LT,
                Operators::LTE,
                Operators::GT,
                Operators::GTE,
                Operators::BETWEEN,
            ]),
            Value::DECIMAL
        );
    }

}