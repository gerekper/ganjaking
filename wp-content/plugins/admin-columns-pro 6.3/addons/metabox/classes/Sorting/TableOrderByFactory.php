<?php

declare(strict_types=1);

namespace ACA\MetaBox\Sorting;

use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\CastType;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\EmptyValues;
use ACP\Sorting\Type\Order;

class TableOrderByFactory
{

    public static function create(string $meta_key, Order $order, DataType $data_type = null): string
    {
        $args = [
            'empty_values' => self::create_empty_values($data_type),
        ];

        if ($data_type) {
            $args['cast_type'] = (string)CastType::create_from_data_type($data_type);
        }

        return SqlOrderByFactory::create(
            sprintf('%s.%s', 'acsort_ct', $meta_key),
            (string)$order,
            $args
        );
    }

    private static function create_empty_values(DataType $data_type = null): array
    {
        $empty_values = [
            EmptyValues::NULL,
        ];

        if ( ! $data_type || in_array($data_type->get_value(), [DataType::STRING, DataType::NUMERIC], true)) {
            $empty_values[] = EmptyValues::EMPTY_STRING;
        }

        return $empty_values;
    }

}