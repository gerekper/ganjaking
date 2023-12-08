<?php

declare(strict_types=1);

namespace ACP\Column\Comment;

use AC;
use ACP\Export;
use ACP\Search;
use ACP\Sorting;

class Response extends AC\Column\Comment\Response
    implements Sorting\Sortable, Export\Exportable, Search\Searchable
{

    public function sorting()
    {
        return new Sorting\Model\Comment\Response();
    }

    public function export()
    {
        return new Export\Model\Comment\Response();
    }

    public function search()
    {
        return new Search\Comparison\Comment\Post();
    }

}