<?php

namespace ACA\GravityForms\Search\Comparison\Entry;

use ACA\GravityForms\Search;
use ACP;
use ACP\Query\Bindings;
use ACP\Search\Value;

class Date extends Search\Comparison\Entry
{

    public function __construct($field)
    {
        $operators = new ACP\Search\Operators([
            ACP\Search\Operators::EQ,
            ACP\Search\Operators::LT,
            ACP\Search\Operators::GT,
            ACP\Search\Operators::BETWEEN,
            ACP\Search\Operators::TODAY,
            ACP\Search\Operators::LT_DAYS_AGO,
            ACP\Search\Operators::GT_DAYS_AGO,
        ]);

        parent::__construct($field, $operators, ACP\Search\Value::DATE, new ACP\Search\Labels\Date());
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        if ($operator === ACP\Search\Operators::TODAY) {
            $operator = ACP\Search\Operators::EQ;
            $value = new Value(
                date('Y-m-d'),
                $value->get_type()
            );
        }

        return parent::create_query_bindings($operator, $value);
    }

}