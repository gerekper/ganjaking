<?php

namespace ACA\GravityForms\Search\Comparison\Entry;

use ACA\GravityForms\Search\Query\Bindings;
use ACP;
use ACP\Search\Operators;
use ACP\Search\Value;

class PaymentAmount extends ACP\Search\Comparison
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::LT,
            Operators::GT,
            Operators::BETWEEN,
        ]);

        parent::__construct($operators, Value::DECIMAL);
    }

    protected function create_query_bindings(string $operator, Value $value): ACP\Query\Bindings
    {
        $comparison = ACP\Search\Helper\Sql\ComparisonFactory::create('payment_amount', $operator, $value);

        return (new Bindings())->where($comparison());
    }

}