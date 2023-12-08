<?php

namespace ACA\WC\Search\Order\Customer;

use ACA\WC\Search;
use ACP\Search\Operators;
use ACP\Search\Value;

class UserId extends UserField
{

    public function __construct()
    {
        parent::__construct(
            'ID',
            new Operators([
                Operators::EQ,
                Operators::BETWEEN,
                Operators::LT,
                Operators::LTE,
                Operators::GT,
                Operators::GTE,
            ]),
            Value::INT
        );
    }

}