<?php

namespace ACA\MetaBox\Search\Comparison\Table;

use ACP\Search\Operators;
use ACP\Search\Value;

trait MultiMapTrait
{

    protected function map_operator(string $operator): string
    {
        switch ($operator) {
            case Operators::EQ:
                return Operators::CONTAINS;
            case Operators::NEQ:
                return Operators::NOT_CONTAINS;
            default:
                return $operator;
        }
    }

    protected function map_value(Value $value, string $operator): Value
    {
        if (in_array($operator, [Operators::CONTAINS, Operators::NOT_CONTAINS], true)) {
            $value = new Value(
                serialize($value->get_value()),
                $value->get_type()
            );
        }

        return $value;
    }

}