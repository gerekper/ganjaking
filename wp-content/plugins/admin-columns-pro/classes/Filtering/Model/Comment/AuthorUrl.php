<?php

namespace ACP\Filtering\Model\Comment;

use AC\Column;
use ACP\Search\Comparison\Comment\Url;

/**
 * @deprecated NEWVERSION
 */
class AuthorUrl extends Url
{

    public function __construct(Column $column)
    {
        parent::__construct();
    }

}