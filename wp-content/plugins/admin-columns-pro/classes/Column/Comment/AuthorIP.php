<?php

namespace ACP\Column\Comment;

use AC;
use ACP\ConditionalFormat;
use ACP\Search;
use ACP\Sorting;

class AuthorIP extends AC\Column\Comment\AuthorIP
    implements Sorting\Sortable, Search\Searchable, ConditionalFormat\Formattable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function sorting()
    {
        return new Sorting\Model\Comment\OrderByNonUnique('comment_author_IP');
    }

    public function search()
    {
        return new Search\Comparison\Comment\IP();
    }

}
