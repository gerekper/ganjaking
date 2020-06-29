<?php

namespace ACP\Type\License;

use LogicException;

final class RenewalDiscount {

	/**
	 * @var int
	 */
	private $discount;

	public function __construct( $discount ) {
		if ( ! self::is_valid( $discount ) ) {
			throw new LogicException( 'Invalid discount.' );
		}
		$this->discount = $discount;
	}

	/**
	 * @return int
	 */
	public function get_value() {
		return $this->discount;
	}

	/**
	 * @param int $discount
	 *
	 * @return bool
	 */
	public static function is_valid( $discount ) {
		return is_int( $discount ) && $discount >= 0 && $discount < 100;
	}

}