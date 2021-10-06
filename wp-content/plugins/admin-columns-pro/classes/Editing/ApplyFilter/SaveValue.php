<?php

namespace ACP\Editing\ApplyFilter;

use AC;
use AC\Column;

class SaveValue implements AC\ApplyFilter {

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var Column
	 */
	private $column;

	public function __construct( $id, Column $column ) {
		$this->id = (int) $id;
		$this->column = $column;
	}

	/**
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function apply_filters( $value ) {
		return apply_filters( 'acp/editing/save_value', $value, $this->column, $this->id );
	}

}