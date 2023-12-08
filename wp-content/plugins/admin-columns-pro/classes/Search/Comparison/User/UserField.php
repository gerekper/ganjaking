<?php

namespace ACP\Search\Comparison\User;

use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Value;

abstract class UserField extends Comparison
{

    abstract protected function get_field(): string;

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $where = ComparisonFactory::create(
            sprintf('%s.%s', $wpdb->users, $this->get_field()),
            $operator,
            $value
        )->prepare();

        return (new Bindings())->where($where);
    }

}