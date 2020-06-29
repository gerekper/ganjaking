<?php

namespace ACP\Type\License;

use LogicException;

final class RenewalMethod {

	const METHOD_AUTO = 'auto';
	const METHOD_MANUAL = 'manual';

	/**
	 * @var string
	 */
	private $method;

	public function __construct( $method ) {
		if ( ! self::is_valid( $method ) ) {
			throw new LogicException( 'Invalid renewal method.' );
		}

		$this->method = $method;
	}

	/**
	 * @return bool
	 */
	public function is_auto_renewal() {
		return self::METHOD_AUTO === $this->method;
	}

	/**
	 * @return bool
	 */
	public function is_manual_renewal() {
		return self::METHOD_MANUAL === $this->method;
	}

	/**
	 * @return string
	 */
	public function get_value() {
		return $this->method;
	}

	/**
	 * @param string $method
	 *
	 * @return bool
	 */
	public static function is_valid( $method ) {
		return in_array( $method, [ self::METHOD_AUTO, self::METHOD_MANUAL ], true );
	}

}