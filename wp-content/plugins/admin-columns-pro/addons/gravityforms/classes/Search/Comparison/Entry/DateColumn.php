<?php

namespace ACA\GravityForms\Search\Comparison\Entry;

use ACA\GravityForms\Search\Query\Bindings;
use ACP;
use ACP\Search\Value;

class DateColumn extends ACP\Search\Comparison
{

    /**
     * @var string
     */
    private $column;

    public function __construct($column)
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

        parent::__construct($operators, ACP\Search\Value::DATE, new ACP\Search\Labels\Date());

        $this->column = $column;
    }

    protected function create_query_bindings(string $operator, Value $value): ACP\Query\Bindings
    {
        if ($operator === ACP\Search\Operators::EQ) {
            $operator = ACP\Search\Operators::BETWEEN;
            $value = new Value([
                $value->get_value() . ' 00:00',
                $value->get_value() . ' 23:59',
            ], Value::DATE);
        }

        $comparison = ACP\Search\Helper\Sql\ComparisonFactory::create($this->column, $operator, $value);

        return (new Bindings)->where($comparison());
    }

}