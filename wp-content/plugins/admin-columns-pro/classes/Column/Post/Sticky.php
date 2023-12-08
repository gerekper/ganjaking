<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

class Sticky extends AC\Column\Post\Sticky
    implements Editing\Editable, Sorting\Sortable, Search\Searchable
{

    public function sorting()
    {
        return new Sorting\Model\Post\Sticky();
    }

    public function editing()
    {
        return new Editing\Service\Post\Sticky();
    }

    public function search()
    {
        return new Search\Comparison\Post\Sticky();
    }

}