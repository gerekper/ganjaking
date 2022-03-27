<?php

namespace ACP\Export;

use AC\Column;
use ACP;

/**
 * Exportability model, which can be attached as an extension to a column. It handles custom
 * behaviour a column should exhibit when being exported
 */
abstract class Model {

	/**
	 * @var Column
	 */
	protected $column;

	public function __construct( Column $column ) {
		$this->column = $column;
	}

	/**
	 * Retrieve the value to be exported by the column for a specific item
	 *
	 * @param int $id
	 *
	 * @return string
	 */
	abstract public function get_value( $id );

	/**
	 * @return bool
	 */
	public function is_active() {
		return true;
	}

	public function get_column() {
		return $this->column;
	}

}