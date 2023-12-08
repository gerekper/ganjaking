<?php

namespace ACA\JetEngine\Column\Meta;

use ACA\JetEngine\Column;
use ACA\JetEngine\Search;
use ACA\JetEngine\Sorting;
use ACP;

class Time extends Column\Meta
    implements ACP\Search\Searchable, ACP\Sorting\Sortable
{

    use Search\SearchableTrait,
        Sorting\SortableTrait;
}