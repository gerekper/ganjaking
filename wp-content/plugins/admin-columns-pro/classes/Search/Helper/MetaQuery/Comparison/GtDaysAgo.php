<?php

namespace ACP\Search\Helper\MetaQuery\Comparison;

use ACP\Search\Helper\DateValueFactory;
use ACP\Search\Helper\MetaQuery;
use ACP\Search\Operators;
use ACP\Search\Value;
use DateTime;

class GtDaysAgo extends MetaQuery\Comparison
{

    public function __construct(string $key, Value $value)
    {
        $date = new DateTime();
        $date->modify(sprintf('-%s days', $value->get_value()));

        $value_factory = new DateValueFactory($value->get_type());

        parent::__construct($key, Operators::LT, $value_factory->create_single_day($date));
    }

}