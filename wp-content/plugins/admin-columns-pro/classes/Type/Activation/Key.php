<?php

namespace ACP\Type\Activation;

use ACP\Type\ActivationToken;
use InvalidArgumentException;

final class Key implements ActivationToken {

	/**
	 * @var string
	 */
	private $token;

	public function __construct( $token ) {
		if ( ! self::is_valid( $token ) ) {
			throw new InvalidArgumentException( 'Invalid license token.' );
		}

		$this->token = $token;
	}

	/**
	 * @return string
	 */
	public function get_token() {
		return $this->token;
	}

	public function get_type() {
		return 'activation_key';
	}

	/**
	 * @param string $token
	 *
	 * @return bool
	 */
	public static function is_valid( $token ) {
		return $token && is_string( $token ) && strlen( $token ) > 12 && false !== strpos( $token, '-' );
	}

}