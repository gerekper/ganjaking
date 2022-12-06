<?php

namespace ACA\MetaBox\Sorting;

use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\CastType;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\EmptyValues;

class TableOrderByFactory {

	public static function create( $meta_key, DataType $data_type, $order ) {
		$field = sprintf( '%s.%s', 'acsort_ct', $meta_key );
		$cast_type = CastType::create_from_data_type( $data_type );

		$empty_values = [ EmptyValues::NULL ];

		if ( in_array( $cast_type->get_value(), [ CastType::CHAR, CastType::SIGNED ], true ) ) {
			$empty_values[] = EmptyValues::EMPTY_STRING;
		}

		return SqlOrderByFactory::create( $field, $order, [ 'cast_type' => $cast_type->get_value(), 'empty_values' => $empty_values ] );
	}

}