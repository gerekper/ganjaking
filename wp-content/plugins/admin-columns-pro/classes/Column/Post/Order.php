<?php

namespace ACP\Column\Post;

use AC;
use ACP\ConditionalFormat;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

class Order extends AC\Column\Post\Order
    implements Sorting\Sortable, Editing\Editable, Search\Searchable, ConditionalFormat\Formattable
{

    use ConditionalFormat\IntegerFormattableTrait;

    public function sorting()
    {
        return new Sorting\Model\OrderByMultiple(['menu_order', 'ID']);
    }

    public function editing()
    {
        return new Editing\Service\Post\Order();
    }

    public function search()
    {
        return new Search\Comparison\Post\Order();
    }

}