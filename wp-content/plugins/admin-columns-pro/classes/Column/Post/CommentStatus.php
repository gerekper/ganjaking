<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

class CommentStatus extends AC\Column\Post\CommentStatus
    implements Editing\Editable, Sorting\Sortable, Search\Searchable
{

    public function sorting()
    {
        return new Sorting\Model\Post\PostField('comment_status');
    }

    public function editing()
    {
        return new Editing\Service\Post\CommentStatus();
    }

    public function search()
    {
        return new Search\Comparison\Post\CommentStatus();
    }

}