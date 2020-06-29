<?php
if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}

class Mfn_HB_Helper {

	/**
	 * Convert CamelCase to snake_case
	 */
	public static function camel_to_snake( $string ){

		$pattern = '/((?<=[^$])[A-Z0-9])/u';
		$string = strtolower( preg_replace( $pattern, '_$1', $string ) );

		return $string;
	}

	/**
	 * Convert CamelCase to other
	 */
	public static function camel_to_other( $string, $separator = ' ' ){

		$pattern = '/((?<=[^$])[A-Z0-9])/u';
		$string = strtolower( preg_replace( $pattern, $separator .'$1', $string ) );

		return $string;
	}

}
