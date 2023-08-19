<?php

namespace ACA\WC\Helper\Price;

class Rounding {

	/**
	 * @param float $price
	 * @param int   $decimals
	 *
	 * @return float
	 */
	public function up( $price, $decimals = 0 ) {
		$decimals = rtrim( $decimals, 0 );
		if ( ! $decimals ) {
			$decimals = 0;
		}

		$digits = strlen( $decimals );
		$divider = pow( 10, $digits );

		$rounding = absint( $decimals );
		$fraction = absint( $divider * ( $price - floor( $price ) ) );

		if ( $fraction < $rounding ) {
			return floor( $price ) + ( $rounding / $divider );
		}

		return floor( $price ) + 1 + ( $rounding / $divider );
	}

	/**
	 * @param float $price
	 * @param int   $decimals
	 *
	 * @return float
	 */
	public function down( $price, $decimals = 0 ) {
		if ( $this->price_digits_are_same( $price, $decimals ) ) {
			return $price;
		}

		$decimals = rtrim( $decimals, 0 );
		$digits = strlen( $decimals );
		$divider = pow( 10, $digits );

		$rounding = absint( $decimals );
		$fraction = absint( $divider * ( $price - floor( $price ) ) );

		if ( $fraction >= $rounding ) {
			return floor( $price ) + ( $rounding / $divider );
		}

		return floor( $price ) - 1 + ( $rounding / $divider );
	}

	/**
	 * @param float $price
	 * @param int   $decimals
	 *
	 * @return bool
	 */
	private function price_digits_are_same( $price, $decimals ) {
		$price_digits = explode( '.', $price );
		$price_decimals = rtrim( $price_digits[1], 0 );
		$decimals = rtrim( $decimals, 0 );

		return $price_decimals === $decimals;
	}

}