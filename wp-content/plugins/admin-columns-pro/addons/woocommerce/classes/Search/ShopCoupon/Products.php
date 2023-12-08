<?php

namespace ACA\WC\Search\ShopCoupon;

use ACA\WC\Helper\Select\ProductValuesTrait;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class Products extends Comparison\Meta
    implements Comparison\SearchableValues
{

    use ProductValuesTrait;

    public function __construct($meta_key)
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        parent::__construct($operators, $meta_key);
    }

    protected function get_meta_query(string $operator, Value $value): array
    {
        if (Operators::EQ === $operator) {
            return [
                'relation' => 'OR',
                [
                    'key'     => $this->get_meta_key(),
                    'value'   => '^' . $value->get_value(),
                    'compare' => 'REGEXP',
                ],
                [
                    'key'     => $this->get_meta_key(),
                    'value'   => '$' . $value->get_value(),
                    'compare' => 'REGEXP',
                ],
                [
                    'key'     => $this->get_meta_key(),
                    'value'   => sprintf(',%s,', $value->get_value()),
                    'compare' => 'LIKE',
                ],
            ];
        }

        return parent::get_meta_query($operator, $value);
    }
}