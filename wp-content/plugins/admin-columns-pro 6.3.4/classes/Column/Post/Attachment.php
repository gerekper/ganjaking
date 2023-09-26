<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Search;
use ACP\Sorting;

class Attachment extends AC\Column\Post\Attachment
    implements Editing\Editable, Sorting\Sortable, Export\Exportable, Search\Searchable
{

    public function sorting()
    {
        return new Sorting\Model\Post\Attachment();
    }

    public function editing()
    {
        return new Editing\Service\Post\Attachment();
    }

    public function export()
    {
        return new Export\Model\Post\Attachment($this);
    }

    public function search()
    {
        return new Search\Comparison\Post\Attachment();
    }

}