<?php

namespace ACP\Sorting\Type;

use LogicException;

class DataType {

	const STRING = 'string';
	const NUMERIC = 'numeric';
	const DATE = 'date';
	const DATETIME = 'datetime';
	const DECIMAL = 'decimal';

	/**
	 * @var string
	 */
	private $value;

	public function __construct( $value ) {
		if ( ! self::is_valid( $value ) ) {
			throw new LogicException( 'Invalid data type.' );
		}

		$this->value = $value;
	}

	/**
	 * @return string
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * @param string $value
	 *
	 * @return bool
	 */
	public static function is_valid( $value ) {
		return in_array( $value, [ self::STRING, self::NUMERIC, self::DATE, self::DATETIME, self::DECIMAL ] );
	}

}