<?php

namespace ACP\Filtering\Model\Taxonomy;

use ACP\Search;

/**
 * @deprecated NEWVERSION
 */
class ID extends Search\Comparison\Taxonomy\ID
{

    public function __construct($column)
    {
        parent::__construct();
    }

}