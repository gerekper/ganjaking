<?php

namespace ACP\ApplyFilter\CustomField;

use AC;

class StoredDateFormat
{

    private $column;

    public function __construct(AC\Column\CustomField $column)
    {
        $this->column = $column;
    }

    public function apply_filters(string $date_format): string
    {
        return (string)apply_filters('acp/custom_field/stored_date_format', $date_format, $this->column);
    }

}