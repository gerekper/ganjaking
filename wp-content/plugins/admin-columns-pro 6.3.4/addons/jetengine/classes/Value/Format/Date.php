<?php

namespace ACA\JetEngine\Value\Format;

use ACA\JetEngine\Column\Meta;
use ACA\JetEngine\Field;
use ACA\JetEngine\Value\Formatter;
use DateTime;

class Date extends Formatter
{

    /**
     * @var string
     */
    private $date_format;

    public function __construct(Meta $column, Field\Field $field, $date_format = 'Y-m-d')
    {
        parent::__construct($column, $field);

        $this->date_format = $date_format;
    }

    public function format($raw_value): ?string
    {
        if ( ! $raw_value) {
            return $this->column->get_empty_char();
        }

        $value = $this->field instanceof Field\TimeStamp && $this->field->is_timestamp()
            ? $raw_value
            : DateTime::createFromFormat($this->date_format, $raw_value);

        if ($value instanceof DateTime) {
            $value = $value->format('U');
        }

        return $this->column->get_formatted_value($value);
    }

}