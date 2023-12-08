<?php

namespace ACP\Search\Comparison\Comment\Date;

use ACP\Search\Comparison;

class Gmt extends Comparison\Comment\Date
{

    protected function get_field(): string
    {
        return 'comment_date_gmt';
    }

}