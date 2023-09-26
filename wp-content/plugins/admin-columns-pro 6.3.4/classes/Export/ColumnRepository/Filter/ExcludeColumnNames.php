<?php
declare( strict_types=1 );

namespace ACP\Export\ColumnRepository\Filter;

use AC\Column;
use AC\ColumnRepository\Filter;

class ExcludeColumnNames implements Filter {

	/**
	 * @var array
	 */
	private $column_names;

	public function __construct( array $column_names ) {
		$this->column_names = $column_names;
	}

	public function filter( array $columns ): array {
		return array_filter( $columns, [ $this, 'not_contains' ] );
	}

	private function not_contains( Column $column ): bool {
		return ! in_array( $column->get_name(), $this->column_names, true );
	}

}