<?php

namespace ACP\Filtering\Model\Post;

use AC;
use ACP\Search;

/**
 * @deprecated NEWVERSION
 */
class Sticky extends Search\Comparison\Post\Sticky
{

    public function __construct(AC\Column $column)
    {
        parent::__construct();
    }

}