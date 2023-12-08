<?php

namespace ACP\ApplyFilter;

use AC\Column;

class SelectOptions
{

    private $column;

    public function __construct(Column $column)
    {
        $this->column = $column;
    }

    public function apply_filters(array $options): array
    {
        return (array)apply_filters('acp/column/settings/select_options', $options, $this->column);
    }

}