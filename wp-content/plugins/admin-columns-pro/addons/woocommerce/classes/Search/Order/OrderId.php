<?php

namespace ACA\WC\Search\Order;

use ACA\WC\Search;
use ACP\Search\Operators;
use ACP\Search\Value;

class OrderId extends OrderField
{

    public function __construct()
    {
        parent::__construct(
            'id',
            new Operators([
                Operators::EQ,
                Operators::GT,
                Operators::GTE,
                Operators::LT,
                Operators::LTE,
                Operators::BETWEEN,
            ]),
            Value::INT
        );
    }

}