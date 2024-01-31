<?php

namespace ACP\Column\Comment;

use AC;
use ACP\ConditionalFormat;
use ACP\Search;
use ACP\Sorting;

class Agent extends AC\Column\Comment\Agent
    implements Sorting\Sortable, Search\Searchable, ConditionalFormat\Formattable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function sorting()
    {
        return new Sorting\Model\Comment\OrderByNonUnique('comment_agent');
    }

    public function search()
    {
        return new Search\Comparison\Comment\Agent();
    }

}