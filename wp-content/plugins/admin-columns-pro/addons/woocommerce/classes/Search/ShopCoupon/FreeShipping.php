<?php

namespace ACA\WC\Search\ShopCoupon;

use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class FreeShipping extends Comparison\Meta
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        parent::__construct($operators, 'free_shipping');
    }

    protected function get_meta_query(string $operator, Value $value): array
    {
        return [
            'key'   => $this->get_meta_key(),
            'value' => (Operators::IS_EMPTY === $operator) ? 'no' : 'yes',
        ];
    }

}