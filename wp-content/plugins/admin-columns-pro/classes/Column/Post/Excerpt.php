<?php

namespace ACP\Column\Post;

use AC;
use ACP\ConditionalFormat;
use ACP\Editing;
use ACP\Export;
use ACP\Search;
use ACP\Sorting;

class Excerpt extends AC\Column\Post\Excerpt
    implements Sorting\Sortable, Editing\Editable, Export\Exportable, Search\Searchable,
               ConditionalFormat\Formattable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function sorting()
    {
        return new Sorting\Model\Post\PostExcerpt();
    }

    public function editing()
    {
        return new Editing\Service\Post\Excerpt();
    }

    public function export()
    {
        return new Export\Model\StrippedRawValue($this);
    }

    public function search()
    {
        return new Search\Comparison\Post\Excerpt();
    }

}