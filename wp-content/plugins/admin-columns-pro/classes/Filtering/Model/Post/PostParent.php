<?php

namespace ACP\Filtering\Model\Post;

use AC;
use ACP\Search;

/**
 * @deprecated NEWVERSION
 */
class PostParent extends Search\Comparison\Post\PostParent
{

    public function __construct(AC\Column $column)
    {
        parent::__construct($column->get_post_type());
    }

}