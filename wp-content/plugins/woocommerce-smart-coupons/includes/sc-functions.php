<?php
/**
 * Smart Coupons Additional Functions
 *
 * @author      StoreApps
 * @since       3.3.0
 * @version     1.0
 *
 * @package     woocommerce-smart-coupons/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'str_getcsv' ) ) {
	/**
	 * Parse a CSV string into an array
	 *
	 * For PHP version lower than 5.3.0
	 *
	 * @param  string $input     The string to parse.
	 * @param  string $delimiter Set the field delimiter (one character only).
	 * @param  string $enclosure Set the field enclosure character (one character only).
	 * @param  string $escape    Set the escape character (one character only). Defaults as a backslash (\).
	 * @return array  $data      An indexed array containing the fields read.
	 */
	function str_getcsv( $input, $delimiter = ',', $enclosure = '"', $escape = '\\' ) {
		$fivembs = 5 * 1024 * 1024;
		$fp      = fopen( "php://temp/maxmemory:$fivembs", 'r+' ); // phpcs:ignore
		fputs( $fp, $input );
		rewind( $fp );

		$data = fgetcsv( $fp, 0, $delimiter, $enclosure ); // $escape only got added in 5.3.0.

		fclose( $fp ); // phpcs:ignore
		return $data;
	}
}
