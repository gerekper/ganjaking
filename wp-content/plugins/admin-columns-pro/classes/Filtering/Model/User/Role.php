<?php

namespace ACP\Filtering\Model\User;

use AC\Column\Meta;
use ACP\Search;

/**
 * @deprecated NEWVERSION
 */
class Role extends Search\Comparison\User\Role
{

    public function __construct(Meta $column)
    {
        parent::__construct($column->get_meta_key());
    }

}