<?php

namespace ACA\WC\Search\Product;

use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;

class ReviewsEnabled extends Comparison
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        $labels = new Labels([
            Operators::NOT_IS_EMPTY   => __('Open'),
            Operators::IS_EMPTY => __('Closed'),
        ]);

        parent::__construct($operators, null, $labels);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $value = new Value(
            (Operators::IS_EMPTY === $operator) ? 'closed' : 'open',
            $value->get_type()
        );

        $where = ComparisonFactory::create(
            "{$wpdb->posts}.comment_status",
            Operators::EQ,
            $value
        );

        $bindings = new Bindings();
        $bindings->where($where());

        return $bindings;
    }

}