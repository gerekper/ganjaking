<?php

namespace ACA\WC\Search\OrderMeta;

use ACP\Search\Comparison\Meta;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;

class Timestamp extends Meta
{

    public function __construct(string $meta_key)
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::GT,
            Operators::GTE,
            Operators::LT,
            Operators::LTE,
            Operators::BETWEEN,
            Operators::TODAY,
            Operators::PAST,
            Operators::FUTURE,
            Operators::WITHIN_DAYS,
            Operators::LT_DAYS_AGO,
            Operators::GT_DAYS_AGO,
            Operators::EQ_YEAR,
            Operators::EQ_MONTH,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);

        parent::__construct($operators, $meta_key, Value::DATE, new Labels\Date());
    }

    protected function get_meta_query(string $operator, Value $value): array
    {
        $value = $this->map_value($value, $operator);

        switch ($operator) {
            case Operators::EQ:
                $operator = Operators::BETWEEN;
        }

        return parent::get_meta_query(
            $operator,
            $value
        );
    }

    protected function map_value(Value $value, string $operator): Value
    {
        switch ($operator) {
            case Operators::EQ:
                $time = $this->get_timestamp_value($value);

                return new Value(
                    [
                        $time,
                        $time + DAY_IN_SECONDS - 1,
                    ],
                    Value::INT
                );
            case Operators::BETWEEN:
                $_value = $value->get_value();

                return new Value([
                    $this->to_time($_value[0]),
                    $this->to_time($_value[1]) + DAY_IN_SECONDS - 1,
                ], Value::INT);
            case Operators::EQ_MONTH:
            case Operators::EQ_YEAR:
            case Operators::GT_DAYS_AGO:
            case Operators::LT_DAYS_AGO:
            case Operators::WITHIN_DAYS:
                return new Value($value->get_value(), Value::INT);

            default:
                return new Value($this->get_timestamp_value($value), Value::INT);
        }
    }

    /**
     * @param Value $value
     *
     * @return array|int
     */
    private function get_timestamp_value(Value $value)
    {
        return is_array($value->get_value())
            ? array_map([$this, 'to_time'], $value->get_value())
            : $this->to_time($value->get_value());
    }

    private function to_time($value): int
    {
        return (int)strtotime($value);
    }

}