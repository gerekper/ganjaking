<?php

namespace ACA\MetaBox\Value\Formatter;

use AC\Column;
use ACA\MetaBox\Value\Formatter;

class SettingFormatter implements Formatter {

	/**
	 * @var Column
	 */
	private $column;

	public function __construct( Column $column ) {
		$this->column = $column;
	}

	public function format( $value, $id = null ) {
		return $this->column->get_formatted_value( $value, $id );
	}

}