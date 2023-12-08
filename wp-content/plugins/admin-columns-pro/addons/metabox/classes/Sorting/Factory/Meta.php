<?php

namespace ACA\MetaBox\Sorting\Factory;

use ACA\MetaBox\Column;
use ACA\MetaBox\Sorting;
use ACP\Sorting\Model\MetaFactory;
use ACP\Sorting\Type\DataType;

class Meta extends Sorting\Factory implements Sorting\TableStorageFactory
{

    public function create_table_storage(Column $column)
    {
        return (new TableStorageFactory())->create_table_storage($column, $this->get_data_type($column));
    }

    protected function create_default(Column $column)
    {
        return (new MetaFactory())->create(
            $column->get_meta_type(),
            $column->get_meta_key(),
            $this->get_data_type($column)
        );
    }

    private function get_data_type(Column $column): DataType
    {
        return $column instanceof Column\Number
            ? new DataType(DataType::NUMERIC)
            : new DataType(DataType::STRING);
    }

}