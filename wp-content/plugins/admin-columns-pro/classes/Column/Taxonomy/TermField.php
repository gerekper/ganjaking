<?php

namespace ACP\Column\Taxonomy;

use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Value;

abstract class TermField extends Comparison
{

    abstract protected function get_field(): string;

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        $where = ComparisonFactory::create(
            't.' . $this->get_field(),
            $operator,
            $value
        )->prepare();

        $bindings = new Bindings();
        $bindings->where($where);

        return $bindings;
    }

}