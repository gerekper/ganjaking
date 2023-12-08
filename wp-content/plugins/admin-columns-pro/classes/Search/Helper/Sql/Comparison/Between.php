<?php

namespace ACP\Search\Helper\Sql\Comparison;

use ACP\Search\Helper\Sql\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;
use LogicException;

class Between extends Comparison
{

    public function __construct($column, Value $value)
    {
        parent::__construct($column, Operators::BETWEEN, $value);
    }

    protected function get_statement(): string
    {
        return sprintf('%s BETWEEN ? AND ?', $this->column);
    }

    public function bind_value(Value $value)
    {
        $type = $value->get_type();
        $values = $value->get_value();

        if ( ! is_array($values) && count($values) !== 2) {
            throw new LogicException('This statement requires an array with two values.');
        }

        foreach ($values as $v) {
            parent::bind_value(new Value($v, $type));
        }

        return $this;
    }

}