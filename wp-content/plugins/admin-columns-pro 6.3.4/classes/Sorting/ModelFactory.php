<?php

namespace ACP\Sorting;

use AC\Column;
use ACP\Sorting\Model\Disabled;

class ModelFactory
{

    public function create(Column $column): ?AbstractModel
    {
        if ( ! $column instanceof Sortable) {
            return null;
        }

        $model = apply_filters('acp/sorting/model', $column->sorting(), $column);

        if ($model instanceof Disabled) {
            return null;
        }

        return $model instanceof AbstractModel
            ? $model
            : null;
    }

}