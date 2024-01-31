<?php

namespace ACP\Column\Comment;

use AC;
use ACP\ConditionalFormat;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

class Type extends AC\Column\Comment\Type
    implements Editing\Editable, Sorting\Sortable, Search\Searchable, ConditionalFormat\Formattable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function sorting()
    {
        return new Sorting\Model\Comment\OrderByNonUnique('comment_type');
    }

    public function editing()
    {
        return new Editing\Service\Basic(new Editing\View\Text(), new Editing\Storage\Comment\Field('comment_type'));
    }

    public function search()
    {
        return new Search\Comparison\Comment\Type();
    }

}