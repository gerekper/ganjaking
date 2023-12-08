<?php

namespace ACA\WC\Search\Order;

use ACA\WC\Search;
use ACP\Search\Operators;

class OrderKey extends OperationalDataField
{

    public function __construct()
    {
        parent::__construct(
            'order_key',
            new Operators([
                Operators::EQ,
                Operators::BEGINS_WITH,
                Operators::ENDS_WITH,
                Operators::CONTAINS,
                Operators::NOT_CONTAINS,
            ])
        );
    }

}