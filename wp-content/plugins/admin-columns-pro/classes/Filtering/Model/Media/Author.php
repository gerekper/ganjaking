<?php

namespace ACP\Filtering\Model\Media;

use ACP\Search;

/**
 * @deprecated NEWVERSION
 */
class Author extends Search\Comparison\Post\Author
{

    public function __construct()
    {
        parent::__construct('attachment');
    }

}