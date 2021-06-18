<?php

namespace ACP\Type\License;

use LogicException;

final class Key {

	/**
	 * @var string
	 */
	private $key;

	public function __construct( $key ) {
		if ( ! self::is_valid( $key ) ) {
			throw new LogicException( 'Invalid license key.' );
		}

		$this->key = $key;
	}

	/**
	 * @return string
	 */
	public function get_value() {
		return $this->key;
	}

	public function equals( Key $key ) {
		return $this->get_value() === $key->get_value();
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	public static function is_valid( $key ) {
		return $key && is_string( $key ) && strlen( $key ) > 12 && false !== strpos( $key, '-' );
	}

}