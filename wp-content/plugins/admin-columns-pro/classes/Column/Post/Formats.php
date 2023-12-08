<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

class Formats extends AC\Column\Post\Formats
    implements Editing\Editable, Sorting\Sortable, Search\Searchable
{

    public function sorting()
    {
        return new Sorting\Model\Post\Taxonomy($this->get_taxonomy());
    }

    public function editing()
    {
        return new Editing\Service\Post\Formats();
    }

    public function search()
    {
        return new Search\Comparison\Post\Formats($this->get_taxonomy());
    }

}