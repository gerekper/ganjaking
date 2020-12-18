<?php

namespace ACP\Export\Exporter;

use ACP\Export\Exporter;

class CSV extends Exporter {

	/**
	 * @param resource $fh
	 */
	public function export( $fh ) {
		/**
		 * Filters the delimiter to use in exporting to the CSV file format
		 *
		 * @param string $delimiter Delimiter to use
		 * @param CSV    $exporter  Exporter class instance
		 *
		 * @since 1.0
		 */
		$delimiter = apply_filters( 'ac/export/exporter_csv/delimiter', ',', $this );

		// Column headers
		$column_labels = $this->get_column_labels();

		if ( $column_labels ) {
			// Writes UTF8 BOM for Excel support
			fprintf( $fh, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );

			fputcsv( $fh, $this->get_column_labels(), $delimiter );
		}

		// Get data to export
		$data = $this->get_data();

		// Output all items
		foreach ( $data as $item ) {
			fputcsv( $fh, array_map( [ $this, 'format_output' ], $item ), $delimiter );
		}
	}

	/**
	 * Format the output to a string. For scalars (integers, strings, etc.), it returns the input
	 * value cast to a string. For arrays, it (deeply) applies this function to the array values
	 * and returns them in a comma-separated string
	 *
	 * @param mixed $value Input value
	 *
	 * @return string Formatted value
	 * @since 1.0
	 */
	private function format_output( $value ) {
		if ( is_scalar( $value ) ) {

			// convert HTML entities to symbols
			$value = html_entity_decode( $value, ENT_QUOTES, 'utf-8' );

			// Remove newlines from value
			return str_replace( PHP_EOL, ' ', strval( $value ) );
		}

		if ( is_array( $value ) ) {
			return implode( ', ', array_map( [ $this, 'format_output' ], $value ) );
		}

		return '';
	}

}