<?php

namespace ACA\WC\Search\Order;

use ACA\WC\Search;
use ACP\Search\Operators;
use ACP\Search\Value;

class ShippingTotal extends OperationalDataField
{

    public function __construct()
    {
        parent::__construct(
            'shipping_total_amount',
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