<?php

namespace ACP\Column\Comment;

use AC;
use ACP\ConditionalFormat;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

class AuthorEmail extends AC\Column\Comment\AuthorEmail
    implements Editing\Editable, Sorting\Sortable, Search\Searchable,
               ConditionalFormat\Formattable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function sorting()
    {
        return new Sorting\Model\Comment\OrderByNonUnique('comment_author_email');
    }

    public function editing()
    {
        return new Editing\Service\Basic(
            new Editing\View\Email(),
            new Editing\Storage\Post\Field('comment_author_email')
        );
    }

    public function search()
    {
        return new Search\Comparison\Comment\Email();
    }

}