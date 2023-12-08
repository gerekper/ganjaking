<?php

namespace ACP\Filtering\Model\Post;

use AC;
use ACP\Search;

/**
 * @deprecated NEWVERSION
 */
class PingStatus extends Search\Comparison\Post\PingStatus
{

    public function __construct(AC\Column $column)
    {
        parent::__construct();
    }

}