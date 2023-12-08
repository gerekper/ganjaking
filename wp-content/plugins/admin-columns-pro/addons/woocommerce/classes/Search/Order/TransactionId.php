<?php

namespace ACA\WC\Search\Order;

use ACA\WC\Search;
use ACP\Search\Operators;

class TransactionId extends OrderField
{

    public function __construct()
    {
        parent::__construct(
            'transaction_id',
            new Operators([
                Operators::IS_EMPTY,
                Operators::NOT_IS_EMPTY,
                Operators::CONTAINS,
                Operators::NOT_CONTAINS,
            ])
        );
    }

}