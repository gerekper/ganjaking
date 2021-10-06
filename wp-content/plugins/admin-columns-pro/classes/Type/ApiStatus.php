<?php

namespace ACP\Type;

use InvalidArgumentException;

class ApiStatus {

	const STATUS_OK = 1;
	const STATUS_FAIL = 2;

	/**
	 * @var string
	 */
	private $status;

	public function __construct( $status ) {
		$this->status = $status;

		if ( ! self::is_valid( $status ) ) {
			throw new InvalidArgumentException( 'Invalid server status.' );
		}
	}

	/**
	 * @param string $status
	 *
	 * @return bool
	 */
	public static function is_valid( $status ) {
		return in_array( $status, [ self::STATUS_OK, self::STATUS_FAIL ] );
	}

	/**
	 * @return string
	 */
	public function get_value() {
		return $this->status;
	}

	/**
	 * @param ApiStatus $status
	 *
	 * @return bool
	 */
	public function equals( ApiStatus $status ) {
		return $this->get_value() === $status->get_value();
	}

}