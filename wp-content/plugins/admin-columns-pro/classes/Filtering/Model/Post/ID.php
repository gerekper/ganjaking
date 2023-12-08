<?php

namespace ACP\Filtering\Model\Post;

use ACP\Search;

/**
 * @deprecated NEWVERSION
 */
class ID extends Search\Comparison\Post\ID
{

    public function __construct($column)
    {
        parent::__construct();
    }

}