<?php

namespace ACP\Search\Comparison\Post\Date;

use ACP\Query\Bindings;
use ACP\Search\Value;

class PostPublished extends PostDate
{

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $bindings = parent::create_query_bindings($operator, $value);
        $bindings->where(
            sprintf(
                "%s AND $wpdb->posts.post_status = 'publish'",
                $bindings->get_where()
            )
        );

        return $bindings;
    }

}