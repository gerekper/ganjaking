<?php

namespace ACA\ACF\Export\Model;

use ACA;
use ACA\ACF\Column;
use ACP;

/**
 * @property Column/Repeater $column
 */
class RepeaterSubField extends ACP\Export\Model {

	public function __construct( Column\Repeater $column ) {
		parent::__construct( $column );
	}

	public function get_value( $id ) {
		$value = $this->column->get_value( $id );
		$delimiter = apply_filters( 'acp/acf/export/repeater/delimiter', ';', $this->column );

		return strip_tags( str_replace( $this->column->get_separator(), $delimiter, $value ) );
	}

}