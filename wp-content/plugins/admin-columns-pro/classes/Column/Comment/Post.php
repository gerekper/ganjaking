<?php

namespace ACP\Column\Comment;

use AC;
use ACP\ConditionalFormat;
use ACP\Export;
use ACP\Search;

class Post extends AC\Column\Comment\Post
    implements Export\Exportable, Search\Searchable, ConditionalFormat\Formattable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function export()
    {
        return new Export\Model\StrippedValue($this);
    }

    public function search()
    {
        return new Search\Comparison\Comment\Post();
    }

}