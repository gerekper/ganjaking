<?php

namespace ACP\Filtering\Model\User;

use AC\Column;
use ACP\Search;

/**
 * @deprecated NEWVERSION
 */
class Email extends Search\Comparison\User\Email
{

    public function __construct(Column $column)
    {
        parent::__construct();
    }

}