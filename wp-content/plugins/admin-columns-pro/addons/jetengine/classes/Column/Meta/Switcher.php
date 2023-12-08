<?php

namespace ACA\JetEngine\Column\Meta;

use ACA\JetEngine\Column;
use ACA\JetEngine\Editing;
use ACA\JetEngine\Search;
use ACA\JetEngine\Sorting;
use ACP;

class Switcher extends Column\Meta implements ACP\Search\Searchable, ACP\Editing\Editable, ACP\Sorting\Sortable
{

    use Search\SearchableTrait,
        Sorting\SortableTrait,
        Editing\EditableTrait;

    public function get_values($id)
    {
        return ac_helper()->icon->yes_or_no($this->get_raw_value($id) === 'true');
    }

}