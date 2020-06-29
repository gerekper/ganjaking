<?php
/**
 * Class YITH_WCWTL_Importer.
 *
 * @package YITH WooCommerce Waiting List
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'YITH_WCWTL_Importer' ) ) {
	/**
	 * Waiting List Importer - handles file import.
	 *
	 * @package     YITH WooCommerce Waiting List
	 * @version     1.6.0
	 */
	class YITH_WCWTL_Importer {

		/**
		 * CSV file.
		 * @var string
		 */
		protected $file = '';

		/**
		 * Params array.
		 * @var array
		 */
		protected $params = '';

		/**
		 * Raw keys - CSV raw headers.
		 *
		 * @var array
		 */
		protected $raw_keys = array();

		/**
		 * Raw data.
		 *
		 * @var array
		 */
		protected $raw_data = array();

		/**
		 * Valid filetypes
		 * @var array
		 */
		protected static $valid_filetypes = array( 'csv' => 'text/csv' );

		/**
		 * YITH_WCWTL_Importer constructor.
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 * @param string $file
		 * @param array  $params
		 */
		public function __construct( $file, $params = array() ) {

			$default_args = array(
				'lines'              => -1,
				'product_id'         => 0,
				'overwrite_existing' => false,
				'map_column'         => 0,
				'delimiter'          => ',',
				'enclosure'          => '"',
				'escape'             => "\0",
			);

			$this->params = wp_parse_args( $params, $default_args );
			$this->file   = $file;
		}

		/**
		 * Get valid filetypes
		 *
		 * @since  1.6.0
		 * @access static
		 * @author Francesco Licandro
		 * @return array
		 */
		public static function get_valid_filetypes() {
			return self::$valid_filetypes;
		}

		/**
		 * Check whether a file is a valid CSV file.
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 * @param string $file File path.
		 * @return bool
		 */
		public static function is_file_valid_csv( $file ) {
			$filetype = wp_check_filetype( $file, self::$valid_filetypes );
			if ( in_array( $filetype['type'], self::$valid_filetypes, true ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Read file.
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 */
		public function read_file() {

			try {
				if ( $this->file && self::is_file_valid_csv( $this->file ) ) {
					$handle = fopen( $this->file, 'r' ); // @codingStandardsIgnoreLine.
					if ( false !== $handle ) {

						$this->raw_keys = array_map( 'trim', fgetcsv( $handle, 0, $this->params['delimiter'], $this->params['enclosure'], $this->params['escape'] ) ); // @codingStandardsIgnoreLine
						// Remove BOM signature from the first item.
						if ( isset( $this->raw_keys[0] ) ) {
							$this->raw_keys[0] = $this->remove_utf8_bom( $this->raw_keys[0] );
						}

						while ( 1 ) {
							$row = fgetcsv( $handle, 0, $this->params['delimiter'], $this->params['enclosure'], $this->params['escape'] );
							if ( false !== $row ) {
								$this->raw_data[] = $row;
								if ( 0 === --$this->params['lines'] ) {
									break;
								}
							} else {
								break;
							}
						}
					}
				}
			} catch ( Exception $e ) {

			}
		}

		/**
		 * Remove UTF-8 BOM signature.
		 *
		 * @param string $string String to handle.
		 *
		 * @return string
		 */
		protected function remove_utf8_bom( $string ) {
			if ( 'efbbbf' === substr( bin2hex( $string ), 0, 6 ) ) {
				$string = substr( $string, 3 );
			}

			return $string;
		}

		/**
		 * Get raw keys
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 * @return array
		 */
		public function get_raw_keys() {
			return $this->raw_keys;
		}

		/**
		 * Get raw data
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 * @return array
		 */
		public function get_raw_data() {
			return $this->raw_data;
		}

		/**
		 * Run the importer
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 * @return boolean
		 */
		public function run() {

			// read the file
			$this->read_file();

			if ( $this->params['overwrite_existing'] ) {
				yith_waitlist_empty( $this->params['product_id'] );
			}

			$emails = array();
			foreach ( $this->raw_data as $data ) {
				foreach ( $data as $column_key => $column_value ) {
					if ( $column_key !== $this->params['map_column'] ) {
						continue;
					}

					$emails[] = $column_value;
				}
			}

			unlink( $this->file ); // delete file

			empty( $emails ) || yith_waitlist_register_users_bulk( $emails, $this->params['product_id'] );
			return true;
		}
	}
}
