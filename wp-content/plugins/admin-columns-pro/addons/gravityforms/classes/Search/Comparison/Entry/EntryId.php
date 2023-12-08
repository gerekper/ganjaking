<?php

namespace ACA\GravityForms\Search\Comparison\Entry;

use ACA\GravityForms\Search\Query\Bindings;
use ACP;
use ACP\Search\Value;

class EntryId extends ACP\Search\Comparison
{

    public function __construct()
    {
        $operators = new ACP\Search\Operators([
            ACP\Search\Operators::EQ,
            ACP\Search\Operators::LT,
            ACP\Search\Operators::GT,
            ACP\Search\Operators::BETWEEN,
        ]);

        parent::__construct($operators, ACP\Search\Value::INT);
    }

    protected function create_query_bindings(string $operator, Value $value): ACP\Query\Bindings
    {
        $comparison = ACP\Search\Helper\Sql\ComparisonFactory::create('id', $operator, $value);

        return (new Bindings)->where($comparison());
    }

}