<?php

namespace ACP\Type;

use InvalidArgumentException;

class Platform {

	const LOCAL = 'local';
	const PRODUCTION = 'production';

	/**
	 * @var string
	 */
	private $platform;

	public function __construct( $platform ) {
		$this->platform = $platform;

		if ( ! self::is_valid( $platform ) ) {
			throw new InvalidArgumentException( 'Invalid platform.' );
		}
	}

	/**
	 * @param string $platform
	 *
	 * @return bool
	 */
	public static function is_valid( $platform ) {
		return in_array( $platform, [ self::LOCAL, self::PRODUCTION ] );
	}

	/**
	 * @return string
	 */
	public function get_value() {
		return $this->platform;
	}

	/**
	 * @param Platform $platform
	 *
	 * @return bool
	 */
	public function equals( Platform $platform ) {
		return $this->get_value() === $platform->get_value();
	}

}