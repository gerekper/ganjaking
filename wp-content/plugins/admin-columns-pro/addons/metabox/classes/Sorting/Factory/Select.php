<?php

namespace ACA\MetaBox\Sorting\Factory;

use ACA\MetaBox\Column;
use ACP\Sorting\Model\MetaMappingFactory;

final class Select extends Meta
{

    protected function create_default(Column $column)
    {
        $options = $column->get_field_setting('options');
        natcasesort($options);

        return (new MetaMappingFactory())->create(
            $column->get_meta_type(),
            $column->get_meta_key(),
            array_keys($options)
        );
    }

}