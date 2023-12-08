<?php

namespace ACA\JetEngine\Sorting\ModelFactory;

use AC;
use ACP;

final class Post
{

    public function create(
        $meta_type,
        $meta_key,
        $multiple,
        AC\Settings\Column\Post $setting,
        array $args = []
    ) {
        return $multiple
            ? $this->create_multiple_relation_model($meta_type, $meta_key, $setting, $args)
            : $this->create_single_relation_model($meta_type, $meta_key, $setting, $args);
    }

    private function create_single_relation_model(
        $meta_type,
        $meta_key,
        AC\Settings\Column\Post $setting,
        array $args = []
    ) {
        $model = (new ACP\Sorting\Model\MetaRelatedPostFactory())->create($meta_type, $setting->get_value(), $meta_key);

        return $model
            ?: (new ACP\Sorting\Model\MetaFormatFactory())->create(
                $meta_type,
                $meta_key,
                new ACP\Sorting\FormatValue\SettingFormatter($setting),
                null,
                $args
            );
    }

    private function create_multiple_relation_model(
        $meta_type,
        $meta_key,
        AC\Settings\Column\Post $setting,
        array $args = []
    ) {
        return (new ACP\Sorting\Model\MetaFormatFactory())->create(
            $meta_type,
            $meta_key,
            new ACP\Sorting\FormatValue\SerializedSettingFormatter(
                new ACP\Sorting\FormatValue\SettingFormatter($setting)
            ),
            null,
            $args
        );
    }

}