<?php

namespace ACP\Search\Comparison\Comment\Date;

use ACP\Search\Comparison;

class Date extends Comparison\Comment\Date
{

    protected function get_field(): string
    {
        return 'comment_date';
    }

}