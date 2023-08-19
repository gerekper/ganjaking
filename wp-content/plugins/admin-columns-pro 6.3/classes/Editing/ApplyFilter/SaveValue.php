<?php

namespace ACP\Editing\ApplyFilter;

use AC\Column;

class SaveValue
{

    private $id;

    private $column;

    public function __construct(int $id, Column $column)
    {
        $this->id = $id;
        $this->column = $column;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function apply_filters($value)
    {
        return apply_filters('acp/editing/save_value', $value, $this->column, $this->id);
    }

}