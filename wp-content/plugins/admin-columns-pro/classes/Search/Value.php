<?php

namespace ACP\Search;

use LogicException;

final class Value {

	const INT = 'int';
	const DECIMAL = 'decimal';
	const STRING = 'string';
	const DATE = 'date';

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var mixed
	 */
	protected $value;

	/**
	 * @param mixed       $value
	 * @param null|string $type
	 */
	public function __construct( $value, $type = null ) {
		if ( null === $type ) {
			$type = self::STRING;
		}

		$this->type = $type;
		$this->value = $value;

		$this->validate_type();
	}

	private function validate_type() {
		$types = [ self::INT, self::DECIMAL, self::STRING, self::DATE ];

		if ( ! in_array( $this->type, $types ) ) {
			throw new LogicException( 'Invalid type found.' );
		}
	}

	/**
	 * @return string
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * @return mixed
	 */
	public function get_value() {
		return $this->value;
	}

}