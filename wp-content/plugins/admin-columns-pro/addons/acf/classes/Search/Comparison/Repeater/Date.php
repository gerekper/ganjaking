<?php

namespace ACA\ACF\Search\Comparison\Repeater;

use ACA\ACF\Search\Comparison;
use ACP;
use ACP\Query\Bindings;
use ACP\Search\Operators;
use ACP\Search\Value;

class Date extends Comparison\Repeater
{

    public function __construct(string $meta_type, string $parent_key, string $sub_key)
    {
        $operators = new Operators([
            Operators::EQ,
            Operators::GT,
            Operators::LT,
            Operators::BETWEEN,
            Operators::FUTURE,
            Operators::PAST,
            Operators::TODAY,
        ]);

        parent::__construct(
            $meta_type,
            $parent_key,
            $sub_key,
            $operators,
            Value::DATE,
            false,
            new ACP\Search\Labels\Date()
        );
    }

    private function map_value(Value $value, string $operator): Value
    {
        switch ($operator) {
            case Operators::FUTURE:
            case Operators::PAST:
            case Operators::TODAY:
                return new Value(
                    date('Ymd'),
                    ACP\Search\Value::INT
                );
            default:
                return new Value(
                    $this->format_date($value->get_value()),
                    Value::INT
                );
        }
    }

    private function map_operator(string $operator): string
    {
        $mapping = [
            Operators::TODAY  => Operators::EQ,
            Operators::FUTURE => Operators::GT,
            Operators::PAST   => Operators::LT,
        ];

        return array_key_exists($operator, $mapping) ? $mapping[$operator] : $operator;
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        $value = $this->map_value($value, $operator);
        $operator = $this->map_operator($operator);

        return parent::create_query_bindings($operator, $value);
    }

    /**
     * @param array|string $value
     *
     * @return array|string
     */
    private function format_date($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->format_date($item);
            }
        } else {
            $value = date('Ymd', strtotime($value));
        }

        return $value;
    }

}