<?php

namespace ACP\Filtering\Model\Post;

use AC;
use ACP\Search;

/**
 * @deprecated NEWVERSION
 */
class CommentCount extends Search\Comparison\Post\CommentCount
{

    public function __construct(AC\Column $column)
    {
        parent::__construct();
    }

}