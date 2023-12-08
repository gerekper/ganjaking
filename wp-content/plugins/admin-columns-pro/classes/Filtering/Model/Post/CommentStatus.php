<?php

namespace ACP\Filtering\Model\Post;

use AC;
use ACP\Search;

/**
 * @deprecated NEWVERSION
 */
class CommentStatus extends Search\Comparison\Post\CommentStatus
{

    public function __construct(AC\Column $column)
    {
        parent::__construct();
    }

}