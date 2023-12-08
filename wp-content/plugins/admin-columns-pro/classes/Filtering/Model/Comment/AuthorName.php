<?php

namespace ACP\Filtering\Model\Comment;

use AC\Column;
use ACP;

/**
 * @deprecated NEWVERSION
 */
class AuthorName extends ACP\Search\Comparison\Comment\Author
{

    public function __construct(Column $column)
    {
        parent::__construct();
    }

}