<?php
/**
 * String utilities.
 *
 * @since 2.0.0
 */

namespace KoiLab\WC_Currency_Converter\Utilities;

/**
 * Class String_Utils.
 */
class String_Utils {

	/**
	 * Removes the prefix from the string.
	 *
	 * @since 2.0.0
	 *
	 * @param string $string The string to parse.
	 * @param string $prefix The prefix to remove from.
	 * @return string
	 */
	public static function no_prefix( $string, $prefix ) {
		$length = strlen( $prefix );

		if ( $length && substr( $string, 0, $length ) === $prefix ) {
			$string = substr( $string, $length );
		}

		return $string;
	}

	/**
	 * Maybe adds the prefix to the string.
	 *
	 * @since 2.0.0
	 *
	 * @param string $string The string to parse.
	 * @param string $prefix The prefix to remove from.
	 * @return string
	 */
	public static function maybe_prefix( $string, $prefix ) {
		$string = self::no_prefix( $string, $prefix );

		return $prefix . $string;
	}
}
