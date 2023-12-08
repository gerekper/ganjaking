<?php

namespace ACP\Filtering\Model\Comment;

use AC\Column;
use ACP\Search;

/**
 * @deprecated NEWVERSION
 */
class PostType extends Search\Comparison\Comment\PostType
{

    public function __construct(Column $column)
    {
        parent::__construct();
    }

}