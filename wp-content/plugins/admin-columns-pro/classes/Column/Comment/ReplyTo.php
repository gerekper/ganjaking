<?php

namespace ACP\Column\Comment;

use AC;
use ACP\ConditionalFormat;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

class ReplyTo extends AC\Column\Comment\ReplyTo
    implements Sorting\Sortable, Search\Searchable, ConditionalFormat\Formattable,
               Editing\Editable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function sorting()
    {
        return new Sorting\Model\Comment\OrderByNonUnique('comment_parent');
    }

    public function search()
    {
        return new Search\Comparison\Comment\ReplyTo();
    }

    public function editing()
    {
        return new Editing\Service\Comment\CommentParent();
    }

}