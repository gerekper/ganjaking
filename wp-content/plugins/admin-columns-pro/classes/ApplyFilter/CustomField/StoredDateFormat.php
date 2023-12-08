<?php

namespace ACP\ApplyFilter\CustomField;

use AC\Column\CustomField;

class StoredDateFormat
{

    private $column;

    public function __construct(CustomField $column)
    {
        $this->column = $column;
    }

    public function apply_filters(string $date_format): string
    {
        return (string)apply_filters('acp/custom_field/stored_date_format', $date_format, $this->column);
    }

}