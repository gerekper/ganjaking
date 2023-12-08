<?php

namespace ACA\MetaBox\Search\Comparison\Table;

use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;

class DateIso extends TableStorage
{

    public function __construct(Operators $operators, string $table, string $column)
    {
        parent::__construct($operators, $table, $column, Value::DATE, new Labels\Date());
    }

    protected function get_subquery(string $operator, Value $value): string
    {
        switch ($operator) {
            case Operators::PAST:
                $value = new Value(date('Y-m-d H:i'), $value->get_type());
                $operator = Operators::LT;

                break;
            case Operators::FUTURE:
                $value = new Value(date('Y-m-d H:i'), $value->get_type());
                $operator = Operators::GT;

                break;
            case Operators::TODAY:
                $value = new Value(date('Y-m-d'), $value->get_type());
                $operator = Operators::EQ;
        }

        if (Operators::EQ === $operator) {
            $value = new Value(
                [
                    $value->get_value() . ' 00:00',
                    $value->get_value() . ' 23:59',
                ],
                Value::DATE
            );
            $operator = Operators::BETWEEN;
        }

        return parent::get_subquery($operator, $value);
    }

}