<?php

declare(strict_types=1);

namespace ACA\MetaBox\Sorting;

use ACA\MetaBox;
use ACA\MetaBox\Column;
use ACP;
use ACP\Sorting\AbstractModel;

abstract class Factory
{

    public function create(Column $column): AbstractModel
    {
        if ($column->is_clonable()) {
            return new ACP\Sorting\Model\Disabled();
        }

        if ($column->get_storage() === MetaBox\StorageAware::CUSTOM_TABLE) {
            return $this instanceof TableStorageFactory
                ? $this->create_table_storage($column)
                : new ACP\Sorting\Model\Disabled();
        }

        return $this->create_default($column);
    }

    abstract protected function create_default(Column $column): AbstractModel;

}