<?php

namespace ACP\Filtering\Model\Comment;

use AC\Column;
use ACP\Search;

/**
 * @deprecated NEWVERSION
 */
class Approved extends Search\Comparison\Comment\Approved
{

    public function __construct(Column $column)
    {
        parent::__construct();
    }

}