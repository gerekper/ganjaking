<?php

namespace ACP\Column\Post;

use AC;
use ACP\ConditionalFormat;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

class Status extends AC\Column\Post\Status
    implements Editing\Editable, Sorting\Sortable, Search\Searchable, ConditionalFormat\Formattable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function sorting()
    {
        return new Sorting\Model\Post\Status();
    }

    public function editing()
    {
        return new Editing\Service\Post\PostStatus($this->get_post_type(), new Editing\ApplyFilter\PostStatus($this));
    }

    public function search()
    {
        return new Search\Comparison\Post\Status($this->get_post_type());
    }

}