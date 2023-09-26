<?php

namespace ACP\Editing\ApplyFilter;

use AC;

class PostStatus
{

    private $column;

    public function __construct(AC\Column $column)
    {
        $this->column = $column;
    }

    public function apply_filters(array $stati): array
    {
        return (array)apply_filters('acp/editing/post_statuses', $stati, $this->column);
    }

}