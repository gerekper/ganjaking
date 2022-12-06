<?php

namespace ACP\Editing\ApplyFilter;

use AC;

class BulkEditActive implements AC\ApplyFilter {

	/**
	 * @var AC\Column
	 */
	private $column;

	public function __construct( AC\Column $column ) {
		$this->column = $column;
	}

	public function apply_filters( $value ) {
		/**
		 * @deprecated 5.7
		 */
		$value = (bool) apply_filters( 'acp/editing/bulk-edit-active', $value, $this->column );

		return (bool) apply_filters( 'acp/editing/bulk/is_active', $value, $this->column, $this->column->get_list_screen() );
	}

}