<?php

namespace ACP\Export;

use AC\Column;

/**
 * Exportability model, which can be attached as an extension to a column. It handles custom
 * behaviour a column should exhibit when being exported
 * @deprecated 6.1
 */
abstract class Model implements Service {

	protected $column;

	public function __construct( Column $column ) {
		$this->column = $column;
	}

	public function get_column() {
		return $this->column;
	}

}