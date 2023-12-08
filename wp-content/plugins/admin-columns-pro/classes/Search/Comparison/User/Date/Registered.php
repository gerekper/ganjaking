<?php

namespace ACP\Search\Comparison\User\Date;

use ACP\Search\Comparison;
use ACP\Search\Operators;

class Registered extends Comparison\User\Date
{

    public function __construct()
    {
        parent::__construct(
            new Operators([
                Operators::EQ,
                Operators::GT,
                Operators::LT,
                Operators::GTE,
                Operators::LTE,
                Operators::BETWEEN,
                Operators::TODAY,
                Operators::EQ_MONTH,
                Operators::EQ_YEAR,
                Operators::LT_DAYS_AGO,
                Operators::GT_DAYS_AGO,
            ])
        );
    }

    protected function get_field(): string
    {
        return 'user_registered';
    }

}