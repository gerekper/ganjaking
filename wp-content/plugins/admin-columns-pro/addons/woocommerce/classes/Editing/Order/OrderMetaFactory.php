<?php

namespace ACA\WC\Editing\Order;

use AC;
use AC\Helper\Select\Option;
use AC\Settings\Column\DateFormat;
use AC\Type\ToggleOptions;
use ACA\WC\Column;
use ACA\WC\Editing;
use ACP;
use ACP\Settings\Column\CustomFieldType;

final class OrderMetaFactory
{

    public function create(Column\Order\Meta $column): ?ACP\Editing\Service
    {
        $meta_key = $column->get_meta_key();

        switch ($column->get_field_type()) {
            case CustomFieldType::TYPE_BOOLEAN:
                return new ACP\Editing\Service\Basic(
                    new ACP\Editing\View\Toggle(
                        new ToggleOptions(
                            new Option('0', __('False', 'codepress-admin-columns')),
                            new Option('1', __('True', 'codepress-admin-columns'))
                        )
                    ),
                    new Editing\Storage\Order\OrderMeta($meta_key)
                );
            case CustomFieldType::TYPE_COLOR:
                return new ACP\Editing\Service\Basic(
                    (new ACP\Editing\View\Color())->set_clear_button(true),
                    new Editing\Storage\Order\OrderMeta($meta_key)
                );
            case CustomFieldType::TYPE_DATE:
                $date_format = $this->get_date_save_format($column);

                switch ($date_format) {
                    case AC\Settings\Column\DateFormat::FORMAT_UNIX_TIMESTAMP:
                    case AC\Settings\Column\DateFormat::FORMAT_DATETIME:
                        return new ACP\Editing\Service\DateTime(
                            (new ACP\Editing\View\DateTime())->set_clear_button(true),
                            new Editing\Storage\Order\OrderMeta($meta_key),
                            $date_format
                        );
                    default :
                        return new ACP\Editing\Service\Date(
                            (new ACP\Editing\View\Date())->set_clear_button(true),
                            new Editing\Storage\Order\OrderMeta($meta_key),
                            $date_format
                        );
                }

            case CustomFieldType::TYPE_IMAGE:
                return new ACP\Editing\Service\Basic(
                    (new ACP\Editing\View\Image())->set_clear_button(true),
                    new Editing\Storage\Order\OrderMeta($meta_key)
                );
            case CustomFieldType::TYPE_MEDIA:
                return new ACP\Editing\Service\Basic(
                    (new ACP\Editing\View\Media())->set_clear_button(true),
                    new Editing\Storage\Order\OrderMeta($meta_key)
                );
            case CustomFieldType::TYPE_URL:
                return new ACP\Editing\Service\Basic(
                    (new ACP\Editing\View\Url())->set_clear_button(true),
                    new Editing\Storage\Order\OrderMeta($meta_key)
                );
            case CustomFieldType::TYPE_NUMERIC:
                return new ACP\Editing\Service\ComputedNumber(
                    new Editing\Storage\Order\OrderMeta($meta_key)
                );
            case CustomFieldType::TYPE_POST:
                return new ACP\Editing\Service\Post(
                    (new ACP\Editing\View\AjaxSelect())->set_clear_button(true),
                    new Editing\Storage\Order\OrderMeta($meta_key),
                    new ACP\Editing\PaginatedOptions\Posts([])
                );
            case CustomFieldType::TYPE_USER:
                return new ACP\Editing\Service\User(
                    (new ACP\Editing\View\AjaxSelect())->set_clear_button(true),
                    new Editing\Storage\Order\OrderMeta($meta_key),
                    new ACP\Editing\PaginatedOptions\Users()
                );

            case CustomFieldType::TYPE_COUNT:
            case CustomFieldType::TYPE_NON_EMPTY:
            case CustomFieldType::TYPE_ARRAY:
                return null;
            default:
                return new ACP\Editing\Service\Basic(
                    new ACP\Editing\View\Text(),
                    new Editing\Storage\Order\OrderMeta($meta_key)
                );
        }
    }

    private function get_date_save_format(Column\Order\Meta $column): string
    {
        $setting = $column->get_setting('date_save_format');

        return $setting instanceof DateFormat
            ? $setting->get_date_save_format()
            : DateFormat::FORMAT_DATE;
    }

}