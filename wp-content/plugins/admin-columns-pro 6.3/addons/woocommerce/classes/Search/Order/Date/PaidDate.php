<?php

namespace ACA\WC\Search\Order\Date;

use ACA\WC\Search;
use ACP;
use ACP\Search\Operators;
use ACP\Search\Value;

class PaidDate extends ACP\Search\Comparison
{

    use WcDateValueTrait;

    public function __construct()
    {
        parent::__construct(
            new Operators([
                Operators::EQ,
                Operators::GT,
                Operators::LT,
                Operators::BETWEEN,
                Operators::GT_DAYS_AGO,
                Operators::LT_DAYS_AGO,
                Operators::TODAY,
            ]),
            Value::DATE,
            new ACP\Search\Labels\Date()
        );
    }

    protected function create_query_bindings($operator, Value $value)
    {
        $bindings = new ACP\Search\Query\Bindings\QueryArguments();

        $bindings->query_arguments([
            'date_paid' => $this->get_wc_formatted_date_comparison_value($operator, $value),
        ]);

        return $bindings;
    }

}