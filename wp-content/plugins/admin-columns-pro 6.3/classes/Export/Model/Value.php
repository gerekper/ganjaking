<?php

namespace ACP\Export\Model;

use AC\Column;
use ACP\Export\Service;

class Value implements Service {

	private $column;

	public function __construct( Column $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		$value = $this->column->get_value( $id );

		return $value !== $this->column->get_empty_char()
			? $value
			: '';
	}

}