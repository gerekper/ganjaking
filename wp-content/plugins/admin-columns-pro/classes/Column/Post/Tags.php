<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Search;
use ACP\Sorting;

class Tags extends AC\Column\Post\Tags
    implements Sorting\Sortable, Editing\Editable, Export\Exportable, Search\Searchable
{

    public function sorting()
    {
        return new Sorting\Model\Post\Taxonomy($this->get_taxonomy());
    }

    public function editing()
    {
        return new Editing\Service\Post\Taxonomy($this->get_taxonomy(), true);
    }

    public function export()
    {
        return new Export\Model\Post\Taxonomy($this->get_taxonomy());
    }

    public function search()
    {
        return new Search\Comparison\Post\Taxonomy($this->get_taxonomy());
    }

}