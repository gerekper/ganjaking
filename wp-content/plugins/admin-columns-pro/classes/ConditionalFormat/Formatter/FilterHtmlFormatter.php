<?php

namespace ACP\ConditionalFormat\Formatter;

use AC\Column;
use ACP\ConditionalFormat\Formatter;

final class FilterHtmlFormatter implements Formatter
{

    /**
     * @var Formatter
     */
    private $formatter;

    public function __construct(Formatter $formatter)
    {
        $this->formatter = $formatter;
    }

    public function get_type(): string
    {
        return $this->formatter->get_type();
    }

    public function format(string $value, $id, Column $column, string $operator_group): string
    {
        $value = trim(strip_tags($value));

        return $this->formatter->format($value, $id, $column, $operator_group);
    }

}