<?php

namespace ACP\Editing\Service;

use AC\Helper\Select\Option;
use AC\MetaType;
use AC\Settings\Column\CustomFieldType;
use AC\Settings\Column\DateFormat;
use AC\Type\ToggleOptions;
use ACP;
use ACP\ApplyFilter\CustomField\StoredDateFormat;
use ACP\Column;
use ACP\Editing;
use ACP\Editing\PaginatedOptions;
use ACP\Editing\Service;
use ACP\Editing\Settings;
use ACP\Editing\View;
use ACP\Settings\Column\SerializedArray;

class CustomFieldServiceFactory
{

    public function unsupported_field_types(): array
    {
        return [
            CustomFieldType::TYPE_COUNT,
            CustomFieldType::TYPE_NON_EMPTY,
        ];
    }

    private function get_date_save_format(Column\CustomField $column): string
    {
        $setting = $column->get_setting('date_save_format');

        if ( ! $setting instanceof DateFormat) {
            return DateFormat::FORMAT_DATE;
        }

        return (new StoredDateFormat($column))->apply_filters(
            $setting->get_date_save_format()
        );
    }

    public function create(Column\CustomField $column): ?Service
    {
        $unsupported_field_types = $this->unsupported_field_types();
        $field_type = $column->get_field_type();
        $storage = new Editing\Storage\Meta($column->get_meta_key(), new MetaType($column->get_meta_type()));

        if (in_array($field_type, $unsupported_field_types, true)) {
            return null;
        }

        switch ($column->get_field_type()) {
            case CustomFieldType::TYPE_ARRAY :
                return new Service\SerializedMeta($storage, $this->get_serialized_keys($column));
            case CustomFieldType::TYPE_BOOLEAN :
                return new Service\Basic(
                    new View\Toggle(
                        new ToggleOptions(
                            new Option('0', __('False', 'codepress-admin-columns')),
                            new Option('1', __('True', 'codepress-admin-columns'))
                        )
                    ),
                    $storage
                );
            case CustomFieldType::TYPE_COLOR :
                return new Service\Basic((new View\Color())->set_clear_button(true), $storage);
            case CustomFieldType::TYPE_DATE :
                $date_format = $this->get_date_save_format($column);

                switch ($date_format) {
                    case DateFormat::FORMAT_UNIX_TIMESTAMP:
                    case DateFormat::FORMAT_DATETIME:
                        return new Service\DateTime(
                            (new View\DateTime())->set_clear_button(true),
                            $storage,
                            $date_format
                        );
                    default :
                        return new Service\Date(
                            (new View\Date())->set_clear_button(true),
                            $storage,
                            $date_format
                        );
                }
            case CustomFieldType::TYPE_IMAGE :
                return new Service\Basic((new View\Image())->set_clear_button(true), $storage);
            case CustomFieldType::TYPE_MEDIA :
                return new Service\Basic((new View\Media())->set_multiple(true)->set_clear_button(true), $storage);
            case ACP\Settings\Column\CustomFieldType::TYPE_SELECT :
                return new Service\Basic(
                    (new View\Select($column->get_select_options()))->set_clear_button(true),
                    $storage
                );
            case CustomFieldType::TYPE_URL :
                return new Service\Basic(
                    (new View\InternalLink())->set_clear_button(true)->set_placeholder(
                        __('Paste URL or type to search')
                    ), $storage
                );
            case CustomFieldType::TYPE_NUMERIC :
                return new Service\ComputedNumber($storage);
            case CustomFieldType::TYPE_POST :
                $post_types = (array)apply_filters('acp/editing/settings/post_types', [], $column);

                return new Service\Posts(
                    (new View\AjaxSelect())->set_clear_button(true),
                    $storage,
                    new PaginatedOptions\Posts($post_types)
                );
            case CustomFieldType::TYPE_USER :
                return new Service\User(
                    (new View\AjaxSelect())->set_clear_button(true),
                    $storage,
                    new PaginatedOptions\Users()
                );
            default :
                $type = $this->get_editable_type($column) ?: 'textarea';

                switch ($type) {
                    case Settings\EditableType\Text::TYPE_WYSIWYG:
                        return new Service\Basic((new View\Wysiwyg())->set_clear_button(true), $storage);
                    case Settings\EditableType\Text::TYPE_TEXTAREA:
                        return new Service\Basic((new View\TextArea())->set_clear_button(true), $storage);
                    case Settings\EditableType\Text::TYPE_TEXT:
                    default:
                        return new Service\Basic((new View\Text())->set_clear_button(true), $storage);
                }
        }
    }

    private function get_serialized_keys(Column\CustomField $column): array
    {
        $setting = $column->get_setting('array_keys');

        return $setting instanceof SerializedArray
            ? $setting->get_keys()
            : [];
    }

    private function get_editable_type(Column\CustomField $column): ?string
    {
        $setting = $column->get_setting('edit');

        if ($setting instanceof Settings) {
            $editable_type = $setting->get_section(Settings\EditableType::NAME);
            if ($editable_type instanceof Settings\EditableType) {
                return $editable_type->get_editable_type();
            }
        }

        return null;
    }
}