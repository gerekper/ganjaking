<?php

namespace ACP\Column\Post;

use AC;
use ACP\Export;
use ACP\Search;
use ACP\Sorting;

class BeforeMoreTag extends AC\Column\Post\BeforeMoreTag
    implements Sorting\Sortable, Export\Exportable, Search\Searchable
{

    public function sorting()
    {
        return new Sorting\Model\Post\BeforeMoreTag();
    }

    public function export()
    {
        return new Export\Model\StrippedValue($this);
    }

    public function search()
    {
        return new Search\Comparison\Post\BeforeMoreTag();
    }

}