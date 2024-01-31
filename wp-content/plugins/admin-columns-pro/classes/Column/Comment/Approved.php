<?php

namespace ACP\Column\Comment;

use AC;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

class Approved extends AC\Column\Comment\Approved
    implements Editing\Editable, Sorting\Sortable, Search\Searchable
{

    public function sorting()
    {
        return new Sorting\Model\Comment\OrderByNonUnique('comment_approved');
    }

    public function editing()
    {
        return new Editing\Service\Comment\Approved();
    }

    public function search()
    {
        return new Search\Comparison\Comment\Approved();
    }

}