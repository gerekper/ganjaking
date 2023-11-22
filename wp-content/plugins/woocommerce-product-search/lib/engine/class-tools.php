<?php
/**
 * class-tools.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 5.0.0
 */

namespace com\itthinx\woocommerce\search\engine;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Engine tools.
 */
class Tools {

	/**
	 * Convert all values in the array to int.
	 *
	 * @param array $values
	 */
	public static function int( &$values ) {

		foreach ( $values as $key => $value ) {
			$values[$key] = (int) $value;
		}
	}

	/**
	 * Reduce the array to unique values.
	 *
	 * @param array $values
	 */
	public static function unique( &$values ) {

		$values = array_keys( array_flip( $values ) );
	}

	/**
	 * Convert all valus in the array to int and reduce to unique values.
	 *
	 * @param array $values
	 */
	public static function unique_int( &$values ) {

		foreach ( $values as $key => $value ) {
			$values[$key] = (int) $value;
		}
		$values = array_keys( array_flip( $values ) );
	}
}
