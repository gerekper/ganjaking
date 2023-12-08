<?php

namespace ACA\MetaBox\Search\Comparison\Table;

use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;

class Timestamp extends TableStorage
{

    public function __construct($operators, $table, $column)
    {
        parent::__construct($operators, $table, $column, Value::DATE, new Labels\Date());
    }

    protected function get_subquery(string $operator, Value $value): string
    {
        $time = is_array($value->get_value())
            ? array_map([$this, 'to_time'], $value->get_value())
            : $this->to_time($value->get_value());

        switch ($operator) {
            case Operators::EQ:
                $operator = Operators::BETWEEN;
                $value = new Value(
                    [
                        $time,
                        $time + DAY_IN_SECONDS - 1,
                    ],
                    Value::INT
                );

                break;
            default:
                $value = new Value($time, Value::INT);
        }

        return parent::get_subquery($operator, $value);
    }

    /**
     * @param string $value
     *
     * @return int
     */
    private function to_time($value)
    {
        return (int)strtotime($value);
    }
}