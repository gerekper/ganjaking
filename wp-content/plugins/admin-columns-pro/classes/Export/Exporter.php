<?php

namespace ACP\Export;

/**
 * Base class for exporters, which handle the construction of the file content for an an exported
 * list screen. Extending classes should generally implement exporting functionality for a specific
 * file format, such as CSV
 * @since 1.0
 */
abstract class Exporter {

	/**
	 * Rows to be exported. Format: array of associative arrays, where each associative array
	 * denotes a row; a key should be the column name, and the values should be the corresponding
	 * value
	 * @since 1.0
	 * @var array
	 */
	private $data;

	/**
	 * Column header labels. Should, for each column key, contain the corresponding label to be
	 * outputted in the exported file
	 * @since 1.0
	 * @var array
	 */
	private $column_labels;

	/**
	 * Export the data to a temporary file. The file should be the CSV, Excel or other export file
	 * such that it can be downloaded by the user
	 *
	 * @param resource $fh File reference pointer of file to write to
	 *
	 * @return string
	 * @since 1.0
	 */
	abstract public function export( $fh );

	/**
	 * Load an array of data to the exporter
	 *
	 * @param array $data Data array. See the property $data for the expected format
	 *
	 * @since 1.0
	 */
	public function load_data( $data ) {
		$this->data = $data;
	}

	/**
	 * Retrieve the data loaded to the exporter
	 * @return array Data array. See the property $data for the returned format
	 * @since 1.0
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Load an array of column labels to the exporter
	 *
	 * @param array $column_labels Column labels array. See the property $column_labels for the
	 *                             expected format
	 *
	 * @since 1.0
	 */
	public function load_column_labels( $column_labels ) {
		$this->column_labels = $column_labels;
	}

	/**
	 * Retrieve the column labels loaded to the exporter
	 * @return array Column labels array. See the property $data for the returned format
	 * @since 1.0
	 */
	public function get_column_labels() {
		return $this->column_labels;
	}

}