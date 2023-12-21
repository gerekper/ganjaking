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
	 * Removes the prefix from the text.
	 *
	 * @since 2.0.0
	 * @since 2.2.0 Renamed parameter `$string` to `$text`.
	 *
	 * @param string $text The text to parse.
	 * @param string $prefix The prefix to remove from.
	 * @return string
	 */
	public static function no_prefix( $text, $prefix ) {
		$length = strlen( $prefix );

		if ( $length && substr( $text, 0, $length ) === $prefix ) {
			$text = substr( $text, $length );
		}

		return $text;
	}

	/**
	 * Maybe adds the prefix to the text.
	 *
	 * @since 2.0.0
	 * @since 2.2.0 Renamed parameter `$string` to `$text`.
	 *
	 * @param string $text The string to parse.
	 * @param string $prefix The prefix to remove from.
	 * @return string
	 */
	public static function maybe_prefix( $text, $prefix ) {
		$text = self::no_prefix( $text, $prefix );

		return $prefix . $text;
	}
}
