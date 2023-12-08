<?php

namespace ACP\Sorting;

use AC\Column;
use ACP\Sorting\Model\QueryBindings;

class ModelFactory
{

    public function create_model(Column $column): ?AbstractModel
    {
        if ( ! $column instanceof Sortable) {
            return null;
        }

        $model = apply_filters('acp/sorting/model', $column->sorting(), $column);

        return $model instanceof AbstractModel
            ? $model
            : null;
    }

    public function create_bindings(Column $column): ?QueryBindings
    {
        if ( ! $column instanceof Sortable) {
            return null;
        }

        $model = apply_filters('acp/sorting/model', $column->sorting(), $column);

        return $model instanceof QueryBindings
            ? $model
            : null;
    }

}