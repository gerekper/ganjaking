<?php

namespace ACA\JetEngine\Value;

use ACA\JetEngine\Column\Meta;
use ACA\JetEngine\Field\Field;

abstract class Formatter implements ValueFormatter {

	/**
	 * @var Meta
	 */
	protected $column;

	/**
	 * @var Field
	 */
	protected $field;

	public function __construct( Meta $column, Field $field ) {
		$this->column = $column;
		$this->field = $field;
	}

	abstract public function format( $raw_value ): ?string;

}