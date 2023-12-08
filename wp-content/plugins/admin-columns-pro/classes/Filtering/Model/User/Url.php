<?php

namespace ACP\Filtering\Model\User;

use AC\Column;
use ACP\Search;

/**
 * @deprecated NEWVERSION
 */
class Url extends Search\Comparison\User\Url
{

    public function __construct(Column $column)
    {
        parent::__construct();
    }

}