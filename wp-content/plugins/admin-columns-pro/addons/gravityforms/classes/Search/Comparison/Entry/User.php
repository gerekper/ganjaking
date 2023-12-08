<?php

namespace ACA\GravityForms\Search\Comparison\Entry;

use ACA\GravityForms\Search\Query\Bindings;
use ACP;
use ACP\Search\Operators;
use ACP\Search\UserValuesTrait;
use ACP\Search\Value;

class User extends ACP\Search\Comparison
    implements ACP\Search\Comparison\SearchableValues
{

    use UserValuesTrait;

    public function __construct()
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::CURRENT_USER,
        ]);

        parent::__construct($operators, Value::STRING);
    }

    protected function create_query_bindings(string $operator, Value $value): ACP\Query\Bindings
    {
        $comparison = ACP\Search\Helper\Sql\ComparisonFactory::create('created_by', $operator, $value);

        return (new Bindings())->where($comparison());
    }

}