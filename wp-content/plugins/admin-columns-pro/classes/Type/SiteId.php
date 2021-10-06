<?php

namespace ACP\Type;

use InvalidArgumentException;

class SiteId {

	/**
	 * @var string
	 */
	private $option;

	public function __construct( $option ) {
		$this->option = $option;

		if ( ! self::is_valid( $option ) ) {
			throw new InvalidArgumentException( 'Invalid site url.' );
		}
	}

	/**
	 * @param string $platform
	 *
	 * @return bool
	 */
	public static function is_valid( $option ) {
		return is_string( $option ) && $option;
	}

	/**
	 * @return string
	 */
	public function get_value() {
		return $this->option;
	}

	/**
	 * @return string
	 */
	public function get_hash() {
		return hash( 'crc32', $this->option );
	}

}