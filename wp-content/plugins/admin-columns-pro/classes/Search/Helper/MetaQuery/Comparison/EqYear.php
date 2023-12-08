<?php

namespace ACP\Search\Helper\MetaQuery\Comparison;

use ACP\Search\Helper\DateValueFactory;
use ACP\Search\Helper\MetaQuery\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class EqYear extends Comparison
{

    public function __construct(string $key, Value $value)
    {
        $value_factory = new DateValueFactory($value->get_type());

        parent::__construct($key, Operators::BETWEEN, $value_factory->create_range_year($value->get_value()));
    }

}