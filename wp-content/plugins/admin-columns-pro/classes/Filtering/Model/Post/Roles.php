<?php

namespace ACP\Filtering\Model\Post;

use AC;
use ACP\Search;

/**
 * @deprecated NEWVERSION
 */
class Roles extends Search\Comparison\Post\AuthorRole
{

    public function __construct(AC\Column $column)
    {
        parent::__construct();
    }

}