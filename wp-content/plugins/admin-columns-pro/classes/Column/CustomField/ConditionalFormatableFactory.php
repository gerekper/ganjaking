<?php

namespace ACP\Column\CustomField;

use AC\Settings\Column\CustomFieldType;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\ConditionalFormat\Formatter\DateFormatter\FormatFormatter;
use ACP\ConditionalFormat\Formatter\IntegerFormatter;
use InvalidArgumentException;

class ConditionalFormatableFactory
{

    public static function create($field_type, array $args): ?FormattableConfig
    {
        switch ($field_type) {
            // Unsupported fields
            case CustomFieldType::TYPE_NON_EMPTY:
            case CustomFieldType::TYPE_BOOLEAN:
            case CustomFieldType::TYPE_MEDIA:
            case CustomFieldType::TYPE_COLOR:
            case CustomFieldType::TYPE_IMAGE:
                return null;

            case CustomFieldType::TYPE_DATE :
                if ( ! isset($args['date_format'])) {
                    throw new InvalidArgumentException('Missing date_format.');
                }

                return new FormattableConfig(new FormatFormatter($args['date_format']));

            case CustomFieldType::TYPE_NUMERIC :
            case CustomFieldType::TYPE_COUNT :
                return new FormattableConfig(new IntegerFormatter());

            default :
                return new FormattableConfig();
        }
    }

}