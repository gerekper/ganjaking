<?php
/**
 * WooCommerce Customer/Order/Coupon CSV Import Suite
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order/Coupon CSV Import Suite to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order/Coupon CSV Import Suite for your
 * needs please refer to http://docs.woocommerce.com/document/customer-order-csv-import-suite/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2020, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * WooCommerce CSV Import Suite Parser class for managing parsing of CSV files.	This
 * class is responsible for the physical parsing of the import files into
 * useful data structures, and provides some field validations/normalizations.
 * Basically prepares the import data for loading into the database by the import
 * classes.
 *
 * @since 1.0.0
 */
class WC_CSV_Import_Suite_Parser {


	/**
	 * Takes a heading and normalizes it based on the current importer type
	 *
	 * @since 1.0.0
	 * @param string $heading
	 * @return string
	 */
	public static function normalize_heading( $heading ) {

		$s_heading = trim( $heading );

		// lowercase and replace space with underscores if not a custom meta value
		if ( ! Framework\SV_WC_Helper::str_starts_with( $s_heading, 'meta:' ) ) {
			$s_heading = strtolower( $heading );
			$s_heading = str_replace( ' ', '_', $s_heading );
		}

		return $s_heading;
	}


	/**
	 * Format data from CSV
	 *
	 * @since 1.0.0
	 * @param string $data
	 * @param string $enc Encoding
	 * @return string
	 */
	private static function format_data_from_csv( $data, $enc ) {

		$data = ( 'UTF-8' == $enc ) ? $data : utf8_encode( $data );

		return trim( $data );
	}


	/**
	 * Reads lines from CSV-formatted $file, storing them into data arrays
	 * which are then passed off to the specific entity methods the next phase
	 * of parsing.
	 *
	 * @since 1.0.0
	 * @since 3.0.0 removed $delimiter param, added $options param
	 * @param string $file import file name
	 * @param array $options Optional. Parser options
	 * @return array Array containing parsed_data, raw_headers, last parsed
	 *               position and last parsed line number
	 */
	public static function parse( $file, $options = array() ) {

		$defaults = array(
			'delimiter'  => ',',
			'mapping'    => array(), // Column mapping
			'start_pos'  => 0,       // File start pointer position
			'end_pos'    => null,    // File end pointer position
			'max_lines'  => null,    // Maximum number of lines to read and parse
			'start_line' => 2,       // Start line - does not actually affect the file
									 // pointer, but will affect reported line numbers for parsed data
		);

		$options = wp_parse_args( $options, $defaults );

		// set locale
		$enc = mb_detect_encoding( $file, 'UTF-8, ISO-8859-1', true );

		if ( $enc ) {
			setlocale( LC_ALL, 'en_US.' . $enc );
		}

		@ini_set( 'auto_detect_line_endings', true );

		// parse $file
		$raw_headers = $parsed_data = array();
		$position = $line_num = null;

		// put all CSV data into an associative array
		if ( false !== ( $handle = fopen( $file, "r" ) ) ) {

			// get the CSV header row with column names
			$header = fgetcsv( $handle, 0, $options['delimiter'] );

			// seek start position (line)
			if ( 0 !== $options['start_pos'] ) {
				fseek( $handle, $options['start_pos'] );
			}

			$read_lines = 0;
			$position   = null;

			// ensure start line number is at least 2 (since 1st line is the header)
			$line_num   = $options['start_line'] > 2 ? $options['start_line'] : 2;

			// read the lines
			while ( false !== ( $line = fgetcsv( $handle, 0, $options['delimiter'] ) ) ) {

				$row = array();

				foreach ( $header as $key => $heading ) {

					// normalize the heading
					$s_heading = self::normalize_heading( $heading );

					// check if this heading is being mapped to a different field
					if ( isset( $options['mapping'][ $s_heading ] ) ) {

						if ( $options['mapping'][ $s_heading ] == 'import_as_meta' ) {
							$s_heading = 'meta:' . $s_heading;
						} else if ( $options['mapping'][ $s_heading ] == 'import_as_taxonomy' ) {
							$s_heading = 'tax:' . $s_heading;
						} else {
							$s_heading = esc_attr( $options['mapping'][ $s_heading ] );
						}
					}

					if ( $s_heading == '' ) {
						continue;
					}

					// add the parsed data, keyed off the normalized heading
					$row[ $s_heading ] = isset( $line[ $key ] )
														 ? self::format_data_from_csv( $line[ $key ], $enc )
														 : '';

					// raw Headers stores the actual column name in the CSV.
					// Used for column mapping options, error reporting, etc.
					$raw_headers[ $s_heading ] = $heading;
				}

				// get the position in file
				$position = ftell( $handle );

				// item key - a single item may span across multiple physical lines in
				// the CSV file (cells may include newlines). We want to use the first
				// line number for this item for reporting. This matches the behaviour
				// of Excel/Google Sheets, etc, as they display whole items per row, not
				// single physical lines. This also means that the reported line
				// (or rather, item) number may be different from the actual physical
				// line number in CSV file.
				$key = $line_num;

				// store line keys for each parsed line
				$parsed_data[ $key ] = $row;

				// clean up memory
				unset( $line, $row );

				$read_lines++; // increase total number of lines parsed

				if ( $options['end_pos'] && $position >= $options['end_pos'] || $options['max_lines'] && $read_lines >= $options['max_lines'] ) {
					break;
				}

				$line_num++; // increase line number for next round

			} // done reading lines

			fclose( $handle );
		}

		return array( $parsed_data, $raw_headers, $position, $line_num );
	}


	/**
	 * Get sample from CSV file
	 *
	 * Extracts a number of lines from the CSV file and returns them as string.
	 *
	 * @since 3.0.0
	 * @param string $file_path Path to file
	 * @param int $max_lines Optional. Maximum number of lines to extract. Defaults to 5.
	 * @return string|false Sample or false on failure
	 */
	public static function get_sample( $file_path, $max_lines = 5 ) {

		// need this to work with Mac line endings
		@ini_set( 'auto_detect_line_endings', true );

		$handle = fopen( $file_path, "r" );

		if ( ! $handle ) {
			return false;
		}

		$read_lines = 0;
		$sample     = '';

		while ( ! feof( $handle ) && $read_lines <= $max_lines ) {
			$sample .= fgets( $handle ) . '\n';
			$read_lines++;
		}

		fclose( $handle );

		return $sample;
	}


	/**
	* Parse sample data from CSV file
	*
	* Extracts a number of lines from the CSV file and returns them as array.
	*
	* @since 3.0.0
	* @param string $file_path Path to file
	* @param string $delimiter Optional. CSV delimiter
	* @param int $max_lines Optional. Maximum number of lines to extract. Defaults to 5.
	* @return array
	*/
	public static function parse_sample_data( $file_path, $delimiter = null, $max_lines = 5 ) {

		return self::parse( $file_path, array(
			'delimiter' => $delimiter,
			'max_lines' => $max_lines,
		) );
	}


	/**
	 * Generate HTML rows from the input array
	 *
	 * The first array item is considered to be the header row and
	 * is rendered inside <thead>.
	 *
	 * @since 3.0.0
	 * @param array $data
	 * @return string HTML rows
	 */
	public static function generate_html_rows( $data = array() ) {

		$output = '';

		foreach ( $data as $key => $fields ) {

			if ( 0 === $key ) {
				$output .= '<thead>';
			}

			$output .= '<tr>';

			$element = ( 0 === $key ) ? 'th' : 'td';

			foreach ( $fields as $field ) {
				$output .= "<{$element}>" . esc_html( $field ) . "</{$element}>";
			}

			$output .= '</tr>';

			if ( 0 === $key ) {
				$output .= '</thead>';
			}

		}

		return $output;
	}


}
