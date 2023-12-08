<?php

declare(strict_types=1);

namespace ACA\MetaBox\Sorting;

use ACA\MetaBox;
use ACA\MetaBox\Column;

abstract class Factory
{

    public function create(Column $column)
    {
        if ($column->is_clonable()) {
            return null;
        }

        if ($column->get_storage() === MetaBox\StorageAware::CUSTOM_TABLE) {
            return $this instanceof TableStorageFactory
                ? $this->create_table_storage($column)
                : null;
        }

        return $this->create_default($column);
    }

    abstract protected function create_default(Column $column);

}