<?php

namespace ACA\JetEngine;

use AC\ListScreen;
use ACA\JetEngine\Field\Field;
use ACA\JetEngine\Utils\Api;
use ACP;

final class FieldRepository
{

    /**
     * @var FieldFactory
     */
    private $field_factory;

    /**
     * @var ListScreen
     */
    private $list_screen;

    public function __construct(ListScreen $list_screen)
    {
        $this->list_screen = $list_screen;
        $this->field_factory = new FieldFactory();
    }

    public function find_by_column(Column\Meta $column): ?Field
    {
        $fields = $this->find_all();

        if (empty($fields)) {
            return null;
        }

        $field = array_filter($fields, static function ($field) use ($column) {
            return $field->get_name() === $column->get_type();
        });

        return empty($field) ? null : current($field);
    }

    /**
     * @return Field[]
     */
    public function find_all(): array
    {
        switch (true) {
            case $this->list_screen instanceof ListScreen\Post:
            case $this->list_screen instanceof ListScreen\Media:
                return $this->map_meta_types(
                    Api::MetaBox()->get_fields_for_context('post_type', $this->list_screen->get_post_type())
                );
            case $this->list_screen instanceof ACP\ListScreen\Taxonomy:
                return $this->map_meta_types(
                    Api::MetaBox()->get_fields_for_context('taxonomy', $this->list_screen->get_taxonomy())
                );
            case $this->list_screen instanceof ACP\ListScreen\User:
                $fields = array_merge(...array_values(Api::MetaBox()->get_fields_for_context('user')));

                return $this->map_meta_types($fields);
        }

        return [];
    }

    /**
     * @return Field[]
     */
    private function map_meta_types(array $meta_types): array
    {
        $fields = [];

        foreach ($meta_types as $field) {
            if (isset($field['object_type']) && $field['object_type'] === 'field') {
                $fields[] = $this->field_factory->create($field);
            }
        }

        return array_filter($fields);
    }

}