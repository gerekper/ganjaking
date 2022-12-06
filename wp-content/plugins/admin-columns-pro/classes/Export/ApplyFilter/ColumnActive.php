<?php

namespace ACP\Export\ApplyFilter;

use AC\ApplyFilter;
use AC\Column;

class ColumnActive implements ApplyFilter {

	/**
	 * @var Column
	 */
	private $column;

	public function __construct( Column $column ) {
		$this->column = $column;
	}

	public function apply_filters( $is_enabled ) {
		return ! apply_filters( 'ac/export/column/disable', ! $is_enabled, $this->column );
	}

}