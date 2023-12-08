<?php

namespace ACA\WC\Search\Product;

use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class Rating extends Comparison\Meta
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::GT,
            Operators::GTE,
            Operators::LT,
            Operators::LTE,
            Operators::BETWEEN,
        ]);

        parent::__construct($operators, '_wc_average_rating', Value::INT);
    }

}