<?php

namespace ACA\MetaBox\Sorting\Factory;

use AC;
use ACA\MetaBox\Column;
use ACA\MetaBox\Sorting;
use ACP\Sorting\FormatValue;
use ACP\Sorting\Model\MetaFormatFactory;
use ACP\Sorting\Model\MetaRelatedPostFactory;

final class Post extends Sorting\Factory implements Sorting\TableStorageFactory
{

    public function create_table_storage(Column $column)
    {
        return (new TableStorageFactory())->create_table_storage($column);
    }

    protected function create_default(Column $column)
    {
        $setting = $column->get_setting(AC\Settings\Column\Post::NAME);

        $model = (new MetaRelatedPostFactory())->create(
            $column->get_meta_type(),
            $setting->get_value(),
            $column->get_meta_key()
        );

        if ( ! $model) {
            $model = (new MetaFormatFactory())->create(
                $column->get_meta_type(),
                $column->get_meta_key(),
                new FormatValue\SettingFormatter($setting),
                null,
                [
                    'taxonomy' => $column->get_taxonomy(),
                    'post_type' => $column->get_post_type(),
                ]
            );
        }

        return $model;
    }

}