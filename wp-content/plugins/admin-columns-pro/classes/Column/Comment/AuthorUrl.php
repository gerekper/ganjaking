<?php

namespace ACP\Column\Comment;

use AC;
use ACP\ConditionalFormat;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

class AuthorUrl extends AC\Column\Comment\AuthorUrl
    implements Editing\Editable, Sorting\Sortable, Search\Searchable,
               ConditionalFormat\Formattable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function sorting()
    {
        return new Sorting\Model\Comment\OrderByNonUnique('comment_author_url');
    }

    public function editing()
    {
        return new Editing\Service\Basic(
            (new Editing\View\Url())->set_clear_button(true),
            new Editing\Storage\Comment\Field('comment_author_url')
        );
    }

    public function search()
    {
        return new Search\Comparison\Comment\Url();
    }

}