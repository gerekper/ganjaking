<?php

declare(strict_types=1);

namespace ACA\WC\Search\LabelFormatter;

use AC\Helper\Select\Generic\LabelFormatter;
use DateTime;

class DateMonth implements LabelFormatter
{

    public function format_label(string $value): string
    {
        $date = DateTime::createFromFormat('Ym', $value);

        return $date
            ? $date->format('F Y')
            : '';
    }

}