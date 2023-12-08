<?php

namespace ACP\Search\Comparison\Post;

use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Value;

abstract class PostField extends Comparison
{

    abstract protected function get_field(): string;

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $where = ComparisonFactory::create(
            $wpdb->posts . '.' . $this->get_field(),
            $operator,
            $value
        )->prepare();

        $bindings = new Bindings();
        $bindings->where($where);

        return $bindings;
    }

}