<?php

namespace ACA\ACF\Value;

use ACA\ACF\Column;
use ACA\ACF\Field;

abstract class Formatter {

	/**
	 * @var Column
	 */
	protected $column;

	/**
	 * @var Field $field
	 */
	protected $field;

	public function __construct( Column $column, Field $field ) {
		$this->column = $column;
		$this->field = $field;
	}

	abstract public function format( $value, $id = null );

}