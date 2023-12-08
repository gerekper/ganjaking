<?php

namespace ACP\Filtering\Model\User;

use AC\Column;
use ACP\Search;

/**
 * @deprecated NEWVERSION
 */
class RichEditing extends Search\Comparison\User\TrueFalse
{

    public function __construct(Column $column)
    {
        parent::__construct('rich_editing');
    }

}