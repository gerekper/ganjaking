<?php

declare(strict_types=1);

namespace ACP\Search;

use AC\Column;

class ComparisonFactory
{

    public function create(Column $column): ?Comparison
    {
        if ($column instanceof Searchable) {
            return $column->search() ?: null;
        }

        return null;
    }

}