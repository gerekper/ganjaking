<?php

namespace ACP\Column\Comment;

use AC;
use ACP\ConditionalFormat;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

class AuthorName extends AC\Column\Comment\AuthorName
    implements Editing\Editable, Sorting\Sortable, Search\Searchable,
               ConditionalFormat\Formattable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function sorting()
    {
        return new Sorting\Model\Comment\OrderByNonUnique('comment_author');
    }

    public function editing()
    {
        return new Editing\Service\Basic(new Editing\View\Text(), new Editing\Storage\Comment\Field('comment_author'));
    }

    public function search()
    {
        return new Search\Comparison\Comment\AuthorName();
    }

}