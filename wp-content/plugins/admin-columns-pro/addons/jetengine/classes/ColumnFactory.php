<?php

namespace ACA\JetEngine;

use ACA\JetEngine\Field\Field;
use ACA\JetEngine\Field\Type;

final class ColumnFactory
{

    /**
     * @param Field $field
     *
     * @return Column\Meta
     */
    public function create(Field $field)
    {
        $mapping = $this->get_field_mapping();

        $column = array_key_exists(get_class($field), $mapping)
            ? new $mapping[get_class($field)]()
            : null;

        if ( ! $column) {
            switch (true) {
                case $field instanceof Type\Select:
                    $column = $field->is_multiple()
                        ? new Column\Meta\MultiSelect()
                        : new Column\Meta\Select();
            }
        }

        if ( ! $column) {
            $column = new Column\Meta();
        }

        $column->set_type($field->get_name())
               ->set_label($field->get_title());

        return $column;
    }

    /**radio-labels radio-labels
     * @return string[]
     */
    private function get_field_mapping()
    {
        return [
            Type\ColorPicker::class => Column\Meta\ColorPicker::class,
            Type\Checkbox::class    => Column\Meta\Checkbox::class,
            Type\Date::class        => Column\Meta\Date::class,
            Type\DateTime::class    => Column\Meta\DateTime::class,
            Type\Gallery::class     => Column\Meta\Gallery::class,
            Type\IconPicker::class  => Column\Meta\IconPicker::class,
            Type\Media::class       => Column\Meta\Media::class,
            Type\Number::class      => Column\Meta\Number::class,
            Type\Posts::class       => Column\Meta\Post::class,
            Type\Radio::class       => Column\Meta\Radio::class,
            Type\Repeater::class    => Column\Meta\Repeater::class,
            Type\Switcher::class    => Column\Meta\Switcher::class,
            Type\Text::class        => Column\Meta\Text::class,
            Type\Textarea::class    => Column\Meta\Textarea::class,
            Type\Time::class        => Column\Meta\Time::class,
            Type\Wysiwyg::class     => Column\Meta\Wysiwyg::class,
        ];
    }

}