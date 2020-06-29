<?php

namespace ACP\Sorting;

use ACP\Sorting\Type\DataType;

/**
 * Sorts an array ascending, maintains index association and returns keys
 */
class Sorter {

	/**
	 * @param array         $values     [ (int) $id => (string|int|bool) $value ]
	 * @param string        $order      ASC or DESC
	 * @param DataType|null $data_type  numeric or string
	 * @param bool          $show_empty Defaul true
	 *
	 * @return array
	 */
	public function sort( array $values, $order = 'ASC', DataType $data_type = null, $show_empty = true ) {
		if ( $order !== 'ASC' ) {
			$order = 'DESC';
		}

		if ( null === $data_type ) {
			$data_type = new DataType( DataType::STRING );
		}

		if ( ! $show_empty ) {
			$values = array_filter( $values, [ $this, 'is_not_empty' ] );
		}

		$values = array_map( [ $this, 'truncate' ], $values );

		switch ( $data_type->get_value() ) {
			case DataType::NUMERIC :
				asort( $values, SORT_NUMERIC );

				break;
			default :
				natcasesort( $values );
		}

		$ids = array_keys( $values );

		if ( 'DESC' === $order ) {
			$ids = array_reverse( $ids );
		}

		return $ids;
	}

	/**
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	private function truncate( $value ) {
		return $value && is_string( $value )
			? substr( $value, 0, 100 )
			: $value;
	}

	/**
	 * Allow zero values as non empty. Allows them to be sorted.
	 *
	 * @param string|int|bool $value
	 *
	 * @return bool
	 */
	private function is_not_empty( $value ) {
		return $value || 0 === $value || '0' === $value;
	}

}