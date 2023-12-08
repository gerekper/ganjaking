<?php

namespace ACP\Search\Comparison\Post;

use ACP\Search\Operators;
use ACP\Search\Value;

class Order extends PostField
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::GT,
            Operators::GTE,
            Operators::LT,
            Operators::LTE,
            Operators::BETWEEN,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        parent::__construct($operators, Value::INT);
    }

    protected function get_field(): string
    {
        return 'menu_order';
    }

}