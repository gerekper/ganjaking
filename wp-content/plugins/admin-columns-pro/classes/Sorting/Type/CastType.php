<?php

namespace ACP\Sorting\Type;

use LogicException;

/**
 * SQL cast type e.g. CHAR, DATE or SIGNED
 * @since 5.2
 */
class CastType {

	const SIGNED = 'SIGNED';
	const CHAR = 'CHAR';
	const DATE = 'DATE';
	const DATETIME = 'DATETIME';
	const BINARY = 'BINARY';

	/**
	 * @var string
	 */
	private $value;

	public function __construct( $value ) {
		if ( ! self::is_valid( $value ) ) {
			throw new LogicException( 'Invalid cast type.' );
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
		return in_array( $value, [ self::CHAR, self::SIGNED, self::DATE, self::DATETIME, self::BINARY ] );
	}

	static public function create_from_data_type( DataType $data_type ) {
		switch ( $data_type->get_value() ) {
			case DataType::NUMERIC :
				return new self( self::SIGNED );
			case DataType::DATE :
				return new self( self::DATE );
			case DataType::DATETIME :
				return new self( self::DATETIME );
			case DataType::STRING :
			default :
				return new self( self::CHAR );
		}
	}

}