<?php

namespace ACP\Search\Comparison\Meta;

use AC\Meta\Query;
use ACP\Search\Comparison;

class DateFactory
{

    public const FORMAT_UNIX_TIMESTAMP = 'U';
    public const FORMAT_DATETIME = 'Y-m-d H:i:s';
    public const FORMAT_DATE = 'Y-m-d';

    public static function create(string $date_format, string $meta_key, Query $query): Comparison
    {
        switch ($date_format) {
            case self::FORMAT_UNIX_TIMESTAMP :
                return new DateTime\Timestamp($meta_key, $query);
            case self::FORMAT_DATETIME :
                return new DateTime\ISO($meta_key, $query);
            case self::FORMAT_DATE :
                return new Date($meta_key, $query);
            default:
                return new Text($meta_key);
        }
    }

}