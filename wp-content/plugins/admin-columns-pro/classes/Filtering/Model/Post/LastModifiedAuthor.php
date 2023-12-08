<?php

namespace ACP\Filtering\Model\Post;

use AC\Column;
use ACP\Search;

/**
 * @deprecated NEWVERSION
 */
class LastModifiedAuthor extends Search\Comparison\Post\LastModifiedAuthor
{

    public function __construct(Column $column)
    {
        parent::__construct($column->get_post_type());
    }

}