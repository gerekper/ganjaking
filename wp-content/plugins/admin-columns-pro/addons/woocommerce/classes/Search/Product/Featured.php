<?php

namespace ACA\WC\Search\Product;

use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;

class Featured extends Comparison
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        $labels = new Labels([
            Operators::NOT_IS_EMPTY => __('Is featured', 'codepress-admin-columns'),
            Operators::IS_EMPTY     => __('Is not featured', 'codepress-admin-columns'),
        ]);

        parent::__construct($operators, null, $labels);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        $bindings = new Bindings\Post();
        $bindings->tax_query($this->get_tax_query($operator));

        return $bindings;
    }

    /**
     * @param string $operator
     *
     * @return array
     */
    public function get_tax_query($operator)
    {
        $product_visibility_term_ids = wc_get_product_visibility_term_ids();
        $operator = $operator === Operators::IS_EMPTY ? 'NOT IN' : 'IN';

        return [
            'taxonomy' => 'product_visibility',
            'field'    => 'term_taxonomy_id',
            'terms'    => [$product_visibility_term_ids['featured']],
            'operator' => $operator,
        ];
    }

}