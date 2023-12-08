<?php

declare(strict_types=1);

namespace ACP\ConditionalFormat\Formatter\DateFormatter;

use AC\Column;
use ACP\ConditionalFormat\Formatter\DateFormatter;
use ACP\Expression\DateOperators;
use DateTime;

class FormatFormatter extends DateFormatter
{

    private $format;

    public function __construct(string $format = null)
    {
        parent::__construct();

        $this->format = $format;
    }

    public function format(string $value, $id, Column $column, string $operator_group): string
    {
        $value = parent::format($value, $id, $column, $operator_group);

        if ($operator_group === DateOperators::class) {
            $format = $this->format;
            $raw_value = $column->get_raw_value($id);

            if ( ! $raw_value) {
                return $value;
            }

            if ( ! $this->format) {
                $format = 'U';
                $raw_value = (string)strtotime((string)$raw_value);
            }

            $date = DateTime::createFromFormat($format, $raw_value);

            if ($date) {
                $value = $date->format('Y-m-d');
            }
        }

        return $value;
    }

}