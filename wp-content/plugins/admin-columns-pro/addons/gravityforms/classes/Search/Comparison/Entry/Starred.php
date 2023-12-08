<?php

namespace ACA\GravityForms\Search\Comparison\Entry;

use ACA\GravityForms\Search\Query\Bindings;
use ACP;
use ACP\Search\Value;

class Starred extends ACP\Search\Comparison
{

    public function __construct()
    {
        $operators = new ACP\Search\Operators([
            ACP\Search\Operators::IS_EMPTY,
            ACP\Search\Operators::NOT_IS_EMPTY,
        ]);

        parent::__construct($operators, ACP\Search\Value::STRING);
    }

    protected function create_query_bindings(string $operator, Value $value): ACP\Query\Bindings
    {
        $starred_value = $operator === ACP\Search\Operators::IS_EMPTY ? 0 : 1;

        return (new Bindings)->where(sprintf('`is_starred` = %d', $starred_value));
    }

}