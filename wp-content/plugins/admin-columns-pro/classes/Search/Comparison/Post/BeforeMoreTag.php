<?php

namespace ACP\Search\Comparison\Post;

use ACP;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

class BeforeMoreTag extends Comparison
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        parent::__construct($operators);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $operator = $operator === Operators::IS_EMPTY
            ? Operators::NOT_CONTAINS
            : Operators::CONTAINS;

        $value = new Value(
            '<!--more-->',
            $value->get_type()
        );

        $where = ComparisonFactory::create(
            $wpdb->posts . '.post_content',
            $operator,
            $value
        )->prepare();

        $bindings = new ACP\Query\Bindings\Post();
        $bindings->where($where);

        return $bindings;
    }

}