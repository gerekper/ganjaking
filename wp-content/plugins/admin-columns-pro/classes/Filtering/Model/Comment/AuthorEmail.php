<?php

namespace ACP\Filtering\Model\Comment;

use AC\Column;
use ACP\Search\Comparison\Comment\Email;

/**
 * @deprecated NEWVERSION
 */
class AuthorEmail extends Email
{

    public function __construct(Column $column)
    {
        parent::__construct();
    }

}