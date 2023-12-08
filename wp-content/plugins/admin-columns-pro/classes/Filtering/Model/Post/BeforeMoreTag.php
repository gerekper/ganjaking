<?php

namespace ACP\Filtering\Model\Post;

use AC\Column;
use ACP\Search;

/**
 * @deprecated NEWVERSION
 */
class BeforeMoreTag extends Search\Comparison\Post\BeforeMoreTag
{

    public function __construct(Column $column)
    {
        parent::__construct();
    }

}