<?php

namespace ACP\Editing\Value;

use InvalidArgumentException;

class RowsPerIteration {

	/**
	 * @var int
	 */
	private $value;

	public function __construct( $value ) {
		$this->value = (int) $value;

		$this->validate();
	}

	public function get_value() {
		return $this->value;
	}

	private function validate() {
		if ( $this->value < 1 ) {
			throw new InvalidArgumentException( 'Invalid rows per iteration.' );
		}
	}

}