<?php

namespace ACA\WC\Search\Order;

use ACA\WC\Scheme\Orders;
use ACA\WC\Search;
use ACP\Search\Operators;

class CustomerIp extends OrderField
{

    public function __construct()
    {
        parent::__construct(
            Orders::IP_ADDRESSS,
            new Operators([
                Operators::CONTAINS,
                Operators::NOT_CONTAINS,
            ])
        );
    }

}