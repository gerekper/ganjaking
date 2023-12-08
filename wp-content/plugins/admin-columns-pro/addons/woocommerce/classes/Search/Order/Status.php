<?php

namespace ACA\WC\Search\Order;

use AC\Helper\Select\Options;
use ACA\WC\Search;
use ACP;
use ACP\Search\Operators;

class Status extends OrderField implements ACP\Search\Comparison\Values
{

    public function __construct()
    {
        parent::__construct(
            'status',
            new Operators([
                Operators::EQ,
                Operators::NEQ,
            ])
        );
    }

    public function get_values(): Options
    {
        return Options::create_from_array(wc_get_order_statuses());
    }

}