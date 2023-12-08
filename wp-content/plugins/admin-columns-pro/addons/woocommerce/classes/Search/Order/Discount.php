<?php

namespace ACA\WC\Search\Order;

use ACA\WC\Scheme\OrderOperationalData;
use ACA\WC\Search;
use ACP\Search\Operators;
use ACP\Search\Value;

class Discount extends OperationalDataField
{

    public function __construct()
    {
        parent::__construct(
            OrderOperationalData::DISCOUNT_TOTAL_AMOUNT,
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