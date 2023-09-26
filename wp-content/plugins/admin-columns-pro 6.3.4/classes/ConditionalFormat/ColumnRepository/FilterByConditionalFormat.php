<?php

declare(strict_types=1);

namespace ACP\ConditionalFormat\ColumnRepository;

use AC\Column;
use AC\ColumnRepository\Filter;
use ACP\ConditionalFormat\Formattable;

class FilterByConditionalFormat implements Filter
{

    public function filter(array $columns): array
    {
        return array_filter($columns, [$this, 'is_valid']);
    }

    private function is_valid(Column $column): bool
    {
        return $column instanceof Formattable && $column->conditional_format();
    }

}