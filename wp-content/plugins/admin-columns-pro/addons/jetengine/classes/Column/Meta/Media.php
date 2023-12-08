<?php

namespace ACA\JetEngine\Column\Meta;

use ACA\JetEngine\Column;
use ACA\JetEngine\Editing;
use ACA\JetEngine\Search;
use ACA\JetEngine\Sorting;
use ACA\JetEngine\Value;
use ACP;

class Media extends Column\Meta
    implements ACP\Search\Searchable, ACP\Editing\Editable, ACP\Sorting\Sortable, ACP\ConditionalFormat\Formattable
{

    use Search\SearchableTrait,
        Editing\EditableTrait,
        Sorting\SortableTrait,
        Value\DefaultValueFormatterTrait,
        ACP\ConditionalFormat\FilteredHtmlFormatTrait;
}