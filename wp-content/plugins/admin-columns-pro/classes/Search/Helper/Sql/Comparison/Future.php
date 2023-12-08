<?php

namespace ACP\Search\Helper\Sql\Comparison;

use ACP\Search\Helper\DateValueFactory;
use ACP\Search\Helper\Sql\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;
use Exception;

class Future extends Comparison
{

    public function __construct($column, Value $value)
    {
        parent::__construct($column, Operators::FUTURE, $value);
    }

    protected function get_statement(): string
    {
        return sprintf('%s > ?', $this->column);
    }

    /**
     * @param Value $value
     *
     * @return Comparison
     * @throws Exception
     */
    public function bind_value(Value $value)
    {
        $value_factory = new DateValueFactory($value->get_type());

        return parent::bind_value($value_factory->create_today());
    }

}