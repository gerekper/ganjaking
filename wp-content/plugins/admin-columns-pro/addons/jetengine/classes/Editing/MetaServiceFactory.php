<?php

namespace ACA\JetEngine\Editing;

use AC\MetaType;
use ACA\JetEngine\Editing;
use ACA\JetEngine\Field\Field;
use ACA\JetEngine\Field\Type;
use ACP;

final class MetaServiceFactory
{

    /**
     * @return ACP\Editing\Service|false
     */
    public function create(Field $field, MetaType $meta_type)
    {
        $view = (new MetaViewFactory())->create($field);

        if ( ! $view) {
            return false;
        }

        switch (true) {
            case $field instanceof Type\Checkbox:
                return new ACP\Editing\Service\Basic(
                    $view,
                    new Editing\Storage\Meta\Checkbox(
                        $field->get_name(),
                        $meta_type,
                        $field->get_options(),
                        $field->value_is_array()
                    )
                );

            case $field instanceof Type\Date:
                return new ACP\Editing\Service\Date(
                    $view,
                    $this->create_meta_storage($field, $meta_type),
                    $field->is_timestamp() ? 'U' : 'Y-m-d'
                );

            case $field instanceof Type\DateTime:
                return new ACP\Editing\Service\DateTime(
                    $view,
                    $this->create_meta_storage($field, $meta_type),
                    $field->is_timestamp() ? 'U' : 'Y-m-d\TH:i'
                );

            case $field instanceof Type\Gallery:
                return new ACP\Editing\Service\Basic(
                    $view,
                    new Editing\Storage\Meta\Gallery($field->get_name(), $meta_type, $field->get_value_format())
                );

            case $field instanceof Type\Media:
                return new ACP\Editing\Service\Basic(
                    $view,
                    new Editing\Storage\Meta\Media($field->get_name(), $meta_type, $field->get_value_format())
                );

            case $field instanceof Type\Posts:
                return $field->is_multiple()
                    ? new Service\Meta\Posts(
                        $view,
                        $this->create_meta_storage($field, $meta_type),
                        new ACP\Editing\PaginatedOptions\Posts($field->get_related_post_types())
                    )
                    : new ACP\Editing\Service\Post(
                        $view,
                        $this->create_meta_storage($field, $meta_type),
                        new ACP\Editing\PaginatedOptions\Posts($field->get_related_post_types())
                    );

            default:
                return new ACP\Editing\Service\Basic($view, $this->create_meta_storage($field, $meta_type));
        }
    }

    private function create_meta_storage(Field $field, MetaType $meta_type): ACP\Editing\Storage\Meta
    {
        return new ACP\Editing\Storage\Meta($field->get_name(), $meta_type);
    }

}