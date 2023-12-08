<?php

declare(strict_types=1);

namespace ACP\ConditionalFormat\Formatter;

use AC\Column;
use ACP\ConditionalFormat\Formatter;

class RawValueFormatter implements Formatter
{

    /**
     * @var string
     */
    protected $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function get_type(): string
    {
        return $this->type;
    }

    public function format(string $value, $id, Column $column, string $operator_group): string
    {
        return (string)$column->get_raw_value($id);
    }

}