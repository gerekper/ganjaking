<?php

namespace ACP\Column\Post;

use AC;
use ACP\ConditionalFormat;
use ACP\Search;
use ACP\Search\Searchable;
use ACP\Sorting;

class WordCount extends AC\Column\Post\WordCount
    implements Sorting\Sortable, ConditionalFormat\Formattable, Searchable
{

    use ConditionalFormat\IntegerFormattableTrait;

    public function sorting()
    {
        return new Sorting\Model\Post\WordCount();
    }

    public function search()
    {
        return new Search\Comparison\Post\WordCount();
    }

}