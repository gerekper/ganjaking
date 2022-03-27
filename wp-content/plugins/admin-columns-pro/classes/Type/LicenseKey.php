<?php

namespace ACP\Type;

use LogicException;

final class LicenseKey implements ActivationToken {

	const SOURCE_DATABASE = 'database';
	const SOURCE_CODE = 'code';

	/**
	 * @var string
	 */
	private $key;

	/**
	 * @var string
	 */
	private $source;

	public function __construct( $key, $source = null ) {
		if ( ! self::is_valid( $key ) ) {
			throw new LogicException( 'Invalid license key.' );
		}

		if ( self::SOURCE_DATABASE !== $source ) {
			$source = self::SOURCE_CODE;
		}

		$this->key = $key;
		$this->source = $source;
	}

	public function get_token() {
		return $this->key;
	}

	public function get_type() {
		return 'subscription_key';
	}

	public function get_source() {
		return $this->source;
	}

	public function equals( LicenseKey $key ) {
		return $this->get_token() === $key->get_token();
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