<?php

namespace ACA\MetaBox\Export\Model;

use ACA\MetaBox\Column;
use ACP;

class StrippedValue implements ACP\Export\Service {

	protected $column;

	public function __construct( Column $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		$value = $this->column->get_value( $id );
		$value = str_replace( $this->column->get_clone_divider(), ' | ', $value );

		return strip_tags( $value );
	}

}