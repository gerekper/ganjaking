<?php

namespace ACP\Editing\ApplyFilter;

use AC;

class EditValue
{

    private $id;

    private $column;

    public function __construct(int $id, AC\Column $column)
    {
        $this->id = $id;
        $this->column = $column;
    }

    public function apply_filters($value)
    {
        $value = apply_filters('acp/editing/value', $value, $this->id, $this->column);

        return apply_filters('acp/editing/value/' . $this->column->get_type(), $value, $this->id, $this->column);
    }

}