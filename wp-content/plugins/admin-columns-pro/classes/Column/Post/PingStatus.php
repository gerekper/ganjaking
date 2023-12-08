<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

class PingStatus extends AC\Column\Post\PingStatus
    implements Sorting\Sortable, Editing\Editable, Search\Searchable
{

    public function sorting()
    {
        return new Sorting\Model\Post\PostField('ping_status');
    }

    public function editing()
    {
        return new Editing\Service\Post\PingStatus();
    }

    public function search()
    {
        return new Search\Comparison\Post\PingStatus();
    }

}