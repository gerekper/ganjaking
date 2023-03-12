<?php

namespace ACA\ACF\Export\Model;

use ACA;
use ACA\ACF\Column;
use ACP;

class RepeaterSubField implements ACP\Export\Service {

	private $column;

	public function __construct( Column\Repeater $column ) {
		$this->column = $column;
	}

	public function get_value( $id ) {
		$value = $this->column->get_value( $id );
		$delimiter = (string) apply_filters( 'acp/acf/export/repeater/delimiter', ';', $this->column );

		return strip_tags( str_replace( $this->column->get_separator(), $delimiter, $value ) );
	}

}