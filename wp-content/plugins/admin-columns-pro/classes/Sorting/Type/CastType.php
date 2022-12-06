<?php

namespace ACP\Sorting\Type;

use LogicException;

/**
 * SQL cast type e.g. CHAR, DATE or SIGNED
 * @since 5.2
 */
class CastType {

	public const SIGNED = 'SIGNED';
	public const CHAR = 'CHAR';
	public const DECIMAL = 'DECIMAL(60,10)';
	public const DATE = 'DATE';
	public const DATETIME = 'DATETIME';
	public const BINARY = 'BINARY';

	/**
	 * @var string
	 */
	private $value;

	public function __construct( string $value ) {
		if ( ! self::is_valid( $value ) ) {
			throw new LogicException( 'Invalid cast type.' );
		}

		$this->value = $value;
	}

	/**
	 * @return string
	 */
	public function get_value(): string {
		return $this->value;
	}

	public function __toString(): string {
		return $this->value;
	}

	/**
	 * @param string $value
	 *
	 * @return bool
	 */
	public static function is_valid( $value ): bool {
		return in_array( $value, [ self::CHAR, self::SIGNED, self::DATE, self::DATETIME, self::BINARY, self::DECIMAL ] );
	}

	public static function create_from_data_type( DataType $data_type ): self {
		switch ( $data_type->get_value() ) {
			case DataType::NUMERIC :
				return new self( self::SIGNED );
			case DataType::DATE :
				return new self( self::DATE );
			case DataType::DATETIME :
				return new self( self::DATETIME );
			case DataType::DECIMAL:
				return new self( self::DECIMAL );
			case DataType::STRING :
			default :
				return new self( self::CHAR );
		}
	}

}