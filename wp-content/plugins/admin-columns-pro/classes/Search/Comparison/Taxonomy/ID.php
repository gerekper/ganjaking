<?php

namespace ACP\Search\Comparison\Taxonomy;

use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Value;

class ID extends Comparison
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
        ]);

        parent::__construct($operators, Value::INT);
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        $where = ComparisonFactory::create(
            't.term_id',
            $operator,
            $value
        )->prepare();

        $bindings = new Bindings();
        $bindings->where($where);

        return $bindings;
    }

}