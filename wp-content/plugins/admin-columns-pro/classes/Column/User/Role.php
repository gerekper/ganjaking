<?php

namespace ACP\Column\User;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Search;
use ACP\Sorting;

class Role extends AC\Column\User\Role
    implements Editing\Editable, Sorting\Sortable, Export\Exportable, Search\Searchable
{

    public function sorting()
    {
        return new Sorting\Model\User\Roles($this->get_meta_key());
    }

    public function editing()
    {
        return new Editing\Service\User\Role(false);
    }

    public function search()
    {
        return new Search\Comparison\User\Role($this->get_meta_key());
    }

    public function export()
    {
        return new Export\Model\User\Role(false);
    }

}