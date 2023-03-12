<?php

namespace ACP\Export\Model;

use AC\Column;
use ACP\Export\Service;

class RawValue implements Service {

	private $column;

	public function __construct( Column $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		$raw_value = $this->column->get_raw_value( $id );

		return is_scalar( $raw_value )
			? (string) $raw_value
			: '';
	}

}