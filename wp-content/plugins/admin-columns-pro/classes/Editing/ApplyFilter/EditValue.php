<?php

namespace ACP\Editing\ApplyFilter;

use AC;

class EditValue implements AC\ApplyFilter {

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var AC\Column
	 */
	private $column;

	public function __construct( $id, AC\Column $column ) {
		$this->id = (int) $id;
		$this->column = $column;
	}

	public function apply_filters( $value ) {
		$value = apply_filters( 'acp/editing/value', $value, $this->id, $this->column );

		return apply_filters( 'acp/editing/value/' . $this->column->get_type(), $value, $this->id, $this->column );
	}

}