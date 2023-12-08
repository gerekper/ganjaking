<?php

namespace ACA\WC\Search\Product;

use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class StockThreshold extends Comparison\Meta
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
            Operators::LTE,
            Operators::GTE,
            Operators::BETWEEN,
        ], false);

        parent::__construct(
            $operators,
            '_low_stock_amount',
            Value::INT
        );
    }

}