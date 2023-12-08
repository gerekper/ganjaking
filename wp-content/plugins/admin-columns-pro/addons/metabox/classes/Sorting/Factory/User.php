<?php

namespace ACA\MetaBox\Sorting\Factory;

use AC;
use ACA\MetaBox\Column;
use ACA\MetaBox\Sorting;
use ACP;
use ACP\Sorting\FormatValue\SettingFormatter;
use ACP\Sorting\Model\MetaFormatFactory;

final class User extends Sorting\Factory implements Sorting\TableStorageFactory
{

    public function create_table_storage(Column $column)
    {
        return (new TableStorageFactory)->create_table_storage($column);
    }

    public function create_default(Column $column)
    {
        $setting = $column->get_setting(AC\Settings\Column\User::NAME);

        $model = (new ACP\Sorting\Model\MetaRelatedUserFactory())->create(
            $column->get_meta_type(),
            (string)$setting->get_value(),
            $column->get_meta_key()
        );

        return $model
            ?: (new MetaFormatFactory())->create(
                $column->get_meta_type(),
                $column->get_meta_key(),
                new SettingFormatter($setting),
                null,
                [
                    'taxonomy' => $column->get_taxonomy(),
                    'post_type' => $column->get_post_type(),
                ]
            );
    }

}