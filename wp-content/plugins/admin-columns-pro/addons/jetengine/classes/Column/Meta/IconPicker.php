<?php

namespace ACA\JetEngine\Column\Meta;

use ACA\JetEngine\Column;
use ACA\JetEngine\Editing;
use ACA\JetEngine\Search;
use ACA\JetEngine\Sorting;
use ACP;

class IconPicker extends Column\Meta
    implements ACP\Search\Searchable, ACP\Sorting\Sortable, ACP\Editing\Editable
{

    use Search\SearchableTrait,
        Sorting\SortableTrait,
        Editing\EditableTrait;
}