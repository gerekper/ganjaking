<?php

namespace ACA\JetEngine\Column\Meta;

use AC\Settings\Column\NumberFormat;
use ACA\JetEngine\Column;
use ACA\JetEngine\Editing;
use ACA\JetEngine\Search;
use ACA\JetEngine\Sorting;
use ACP;

class Number extends Column\Meta
    implements ACP\Search\Searchable, ACP\Editing\Editable, ACP\Sorting\Sortable, ACP\ConditionalFormat\Formattable
{

    use Search\SearchableTrait,
        Sorting\SortableTrait,
        Editing\EditableTrait,
        ACP\ConditionalFormat\IntegerFormattableTrait;

    protected function register_settings()
    {
        $this->add_setting(new NumberFormat($this));
    }

}