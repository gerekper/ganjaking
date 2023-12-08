<?php

namespace ACP\Column\Comment;

use AC;
use ACP\Export;
use ACP\Search;

class Author extends AC\Column\Comment\Author
    implements Export\Exportable, Search\Searchable
{

    public function search()
    {
        return new Search\Comparison\Comment\Author();
    }

    public function export()
    {
        return new Export\Model\Comment\Author();
    }

}