<?php

declare(strict_types=1);

namespace ACP\ConditionalFormat;

use AC\Column;

interface Formatter
{

    public const DATE = 'date';
    public const FLOAT = 'float';
    public const INTEGER = 'integer';
    public const STRING = 'string';

    public function get_type(): string;

    public function format(string $value, $id, Column $column, string $operator_group): string;

}