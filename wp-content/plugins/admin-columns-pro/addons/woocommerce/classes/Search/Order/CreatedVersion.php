<?php

namespace ACA\WC\Search\Order;

use ACA\WC\Scheme\OrderOperationalData;
use ACA\WC\Search;
use ACP\Search\Operators;

class CreatedVersion extends OperationalDataField
{

    public function __construct()
    {
        parent::__construct(
            OrderOperationalData::WOOCOMMERCE_VERSION,
            new Operators([
                Operators::EQ,
                Operators::NEQ,
                Operators::CONTAINS,
                Operators::NOT_CONTAINS,
            ])
        );
    }

}