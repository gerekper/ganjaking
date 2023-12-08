<?php

namespace ACP\Filtering\Model\User;

use AC\Column;
use ACP\Search;

/**
 * @deprecated NEWVERSION
 */
class ShowToolbar extends Search\Comparison\User\TrueFalse
{

    public function __construct(Column $column)
    {
        parent::__construct('show_admin_bar_front');
    }

}