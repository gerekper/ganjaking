<?php

namespace ACA\JetEngine\Column\Meta;

use ACA\JetEngine\Column;
use ACA\JetEngine\Editing;
use ACA\JetEngine\Field;
use ACA\JetEngine\Search;
use ACA\JetEngine\Sorting;
use ACA\JetEngine\Value;
use ACP;

/**
 * @property Field\Type\Radio $field
 */
class Radio extends Column\Meta
    implements ACP\Search\Searchable, ACP\Editing\Editable, ACP\Sorting\Sortable
{

    use Search\SearchableTrait,
        Sorting\SortableTrait,
        Editing\EditableTrait,
        Value\DefaultValueFormatterTrait;
}