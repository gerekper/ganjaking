<?php

namespace ACA\MetaBox\Sorting\Factory;

use ACA\MetaBox;
use ACA\MetaBox\Column;
use ACA\MetaBox\Sorting;
use ACP;
use ACP\Sorting\Type\DataType;

final class Date extends Sorting\Factory implements MetaBox\Sorting\CloneableFactory, Sorting\TableStorageFactory
{

    public function create_table_storage(Column $column)
    {
        if ( ! $column->is_clonable()) {
            $data_type = $column->get_saved_format() === 'U'
                ? new DataType(DataType::NUMERIC)
                : new DataType(DataType::DATETIME);

            return (new TableStorageFactory())->create_table_storage($column, $data_type);
        }

        return null;
    }

    protected function create_default(Column $column)
    {
        /**
         * @var Column\Date $column
         */
        if ($column->get_saved_format() === 'U') {
            return (new ACP\Sorting\Model\MetaFactory())->create(
                $column->get_meta_type(),
                $column->get_meta_key(),
                new DataType(DataType::NUMERIC)
            );
        }

        return (new ACP\Sorting\Model\MetaFactory())->create($column->get_meta_type(), $column->get_meta_key());
    }

    public function create_cloneable(Column $column)
    {
        return null;
    }

}