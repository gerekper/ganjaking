<?php

declare(strict_types=1);

namespace ACP\Search\Helper\Select\Meta;

use AC\Helper\Select\Options;
use AC\Meta\Query;
use DateTime;

class DateOptionsFactory
{

    private $meta_query;

    private $date_format;

    public function __construct(Query $meta_query, string $date_format = 'Y-m-d')
    {
        $this->meta_query = $meta_query;
        $this->date_format = $date_format;
    }

    public function create_label(string $value): string
    {
        $date = DateTime::createFromFormat('Ym', $value);

        return $date ? $date->format('F Y') : $value;
    }

    public function create_options(): Options
    {
        $options = [];

        foreach ($this->meta_query->get() as $meta_value) {
            $date = DateTime::createFromFormat($this->date_format, $meta_value);

            if ( ! $date) {
                continue;
            }

            $options[$date->format('Ym')] = $date->format('F Y');
        }

        $options = array_reverse($options, true);

        return Options::create_from_array($options);
    }
}