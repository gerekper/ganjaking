<?php

namespace ACA\MetaBox\Sorting\Factory;

use ACA\MetaBox\Column;
use ACA\MetaBox\Sorting;
use ACP\Sorting\Type\DataType;

final class TableStorageFactory
{

    public function create_table_storage(Column $column, DataType $data_type = null)
    {
        switch ($column->get_meta_type()) {
            case 'user':
                return new Sorting\Model\User\Table($column->get_storage_table(), $column->get_meta_key(), $data_type);
            case 'post':
                return new Sorting\Model\Post\Table($column->get_storage_table(), $column->get_meta_key(), $data_type);
            case 'term':
                return new Sorting\Model\Taxonomy\Table(
                    $column->get_storage_table(),
                    $column->get_meta_key(),
                    $data_type
                );
            default:
                return null;
        }
    }

}