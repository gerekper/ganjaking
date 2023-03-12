<?php
declare( strict_types=1 );

namespace ACP\Export\ColumnRepository\Sort;

use AC\ColumnRepository\Sort;

class ColumnNames implements Sort {

	/**
	 * @var string[]
	 */
	private $column_names;

	public function __construct( array $column_names ) {
		$this->column_names = $column_names;
	}

	public function sort( array $columns ): array {
		$ordered = [];

		foreach ( $this->column_names as $column_name ) {
			if ( ! isset( $columns[ $column_name ] ) ) {
				continue;
			}

			$ordered[ $column_name ] = $columns[ $column_name ];

			unset( $columns[ $column_name ] );
		}

		return array_merge( $ordered, $columns );
	}

}