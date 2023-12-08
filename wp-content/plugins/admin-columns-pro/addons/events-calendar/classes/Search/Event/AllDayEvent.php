<?php

namespace ACA\EC\Search\Event;

use ACP\Search;
use ACP\Search\Comparison;

class AllDayEvent extends Comparison\Meta
{

    public function __construct()
    {
        parent::__construct(
            new Search\Operators([
                Search\Operators::IS_EMPTY,
                Search\Operators::NOT_IS_EMPTY,
            ]),
            '_EventAllDay',
            null,
            new Search\Labels([
                Search\Operators::IS_EMPTY     => __('Not All Day Events', 'codepress-admin-columns'),
                Search\Operators::NOT_IS_EMPTY => __('All Day Events', 'codepress-admin-columns'),
            ])
        );
    }

}