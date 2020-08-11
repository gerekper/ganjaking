<?php
/**
 * Extra Product Options CSV Importer/Exporter
 *
 * @package Extra Product Options/Admin
 * @version 4.9
 */
defined( 'ABSPATH' ) || exit;

/**
 * Convert a PHP array into CSV
 *
 * @package Extra Product Options/Classes
 * @version 4.9
 */
final class THEMECOMPLETE_CONVERT_ARRAY_TO_CSV {

	public $delimiter;
	public $text_separator;
	public $replace_text_separator;
	public $line_delimiter;

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct( $delimiter = ",", $text_separator = '"', $replace_text_separator = '""', $line_delimiter = "\n" ) {
		$this->delimiter              = $delimiter;
		$this->text_separator         = $text_separator;
		$this->replace_text_separator = $replace_text_separator;
		$this->line_delimiter         = $line_delimiter;
	}

	/**
	 * Replaces the data
	 *
	 * @since 1.0
	 */
	public function replace_data( $data ) {
		return $this->text_separator
		       . str_replace( $this->text_separator, $this->replace_text_separator, $data )
		       . $this->text_separator;
	}

	/**
	 * Formats the data
	 *
	 * @since 1.0
	 */
	public function format_data( $data ) {
		$data = (string) ( $data );
		$enc  = mb_detect_encoding( $data, 'UTF-8, ISO-8859-1', TRUE );
		$data = ( $enc == 'UTF-8' ) ? $data : utf8_encode( $data );

		return $data;
	}

	/**
	 * Converts the data
	 *
	 * @since 1.0
	 */
	public function convert( $input ) {
		$lines  = array();
		$header = array();
		$row    = array();
		$csv    = '';
		foreach ( $input as $key => $v ) {
			$header[ $key ] = $key;
			$line           = $this->convertline( $v );
			$lines[ $key ]  = $line;
		}
		// Re-oder headers 
		$header = array( 'div_size' => $header['div_size'] ) + $header;
		$header = array( 'element_type' => $header['element_type'] ) + $header;
		$lines  = array( 'div_size' => $lines['div_size'] ) + $lines;
		$lines  = array( 'element_type' => $lines['element_type'] ) + $lines;

		foreach ( $lines as $key => $value ) {
			if ( ! empty( $value ) && is_array( $value ) ) {
				if ( $key == "variations_options" ) {
					$v              = serialize( $value );
					$v              = $this->format_data( $v );
					$v              = $this->replace_data( $v );
					$row[0][ $key ] = $v;
				} else {
					foreach ( $value as $k => $v ) {
						$v                 = THEMECOMPLETE_EPO_HELPER()->array_serialize( $v );
						$v                 = $this->format_data( $v );
						$v                 = $this->replace_data( $v );
						$row[ $k ][ $key ] = $v;
					}
				}
			}
		}
		foreach ( $row as $k ) {
			$value = array();
			foreach ( $header as $key => $vkey ) {
				if ( isset( $k[ $key ] ) ) {
					$value[] = $k[ $key ];
				} else {
					$value[] = '';
				}
			}
			$csv .= implode( $this->delimiter, $value ) . $this->line_delimiter;
		}
		$csv = implode( $this->delimiter, $header ) . $this->line_delimiter . $csv;

		return $csv;
	}

	/**
	 * Convert a single line
	 *
	 * @since 1.0
	 */
	private function convertline( $line ) {
		$csv_line = array();
		if ( is_array( $line ) ) {
			foreach ( $line as $key => $v ) {
				$csv_line[ $key ] = is_array( $v ) ?
					$this->convertline( $v ) :
					$v;
			}
		} else {
			$csv_line[] = $line;
		}

		return $csv_line;
	}
}

/**
 * Extra Product Options TM CSV import/export class
 *
 * @package Extra Product Options/Classes
 * @version 4.9
 */
final class THEMECOMPLETE_EPO_ADMIN_CSV {

	private $error_loading_string = '';
	private $is_active = TRUE;

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		if ( ! function_exists( 'mb_detect_encoding' ) ) {
			$this->error_loading_string = '<p>' . esc_html__( 'The php functions mb_detect_encoding and mb_convert_encoding are required to import and export CSV files. Please ask your hosting provider to enable this function.', 'woocommerce-tm-extra-product-options' ) . '</p>';
			$this->is_active            = FALSE;
		}

	}

	/**
	 * Check if the system supports mb_detect_encoding
	 *
	 * @since 1.0
	 */
	public function check_if_active( $type = "" ) {
		if ( ! $this->is_active ) {
			switch ( $type ) {
				case 'download':
				case 'export_by_id':
				case 'export_by_product_id':
					wp_die( $this->error_loading_string );
					break;

				default:
					// $this->error_loading_string is escaped
					wp_send_json( array( 'error' => 1, 'message' => $this->error_loading_string ) );
					break;
			}
		}
	}

	/**
	 * Removes utf-8 BOM
	 *
	 * @since 1.0
	 */
	public function remove_utf8_bom( $text ) {
		$bom  = pack( 'H*', 'EFBBBF' );
		$text = preg_replace( "/^$bom/", '', $text );

		return $text;
	}

	/**
	 * Decides if data needs to be utf-8 encoded
	 *
	 * @since 1.0
	 */
	public function format_data_from_csv( $data, $enc ) {
		return ( $enc == 'UTF-8' ) ? $data : utf8_encode( $data );
	}

	/**
	 * Check if there is data to be imported
	 *
	 * @since 1.0
	 */
	public function check_for_import() {
		$data    = array();
		$message = esc_html__( "Invalid CSV file.", 'woocommerce-tm-extra-product-options' );
		if ( isset( $_FILES['builder_import_file'] ) ) {

			$passed = TRUE;
			$file   = $_FILES['builder_import_file'];

			if ( ! empty( $file['name'] ) ) {
				if ( ! empty( $file['error'] ) ) {
					$passed = FALSE;
					// Courtesy of php.net, the strings that describe the error indicated in $_FILES[{form field}]['error'].
					$upload_error_strings = array( FALSE,
					                               esc_html__( "The uploaded file exceeds the upload_max_filesize directive in php.ini.", 'woocommerce-tm-extra-product-options' ),
					                               esc_html__( "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.", 'woocommerce-tm-extra-product-options' ),
					                               esc_html__( "The uploaded file was only partially uploaded.", 'woocommerce-tm-extra-product-options' ),
					                               esc_html__( "No file was uploaded.", 'woocommerce-tm-extra-product-options' ),
					                               '',
					                               esc_html__( "Missing a temporary folder.", 'woocommerce-tm-extra-product-options' ),
					                               esc_html__( "Failed to write file to disk.", 'woocommerce-tm-extra-product-options' ),
					                               esc_html__( "File upload stopped by extension.", 'woocommerce-tm-extra-product-options' )
					);
					if ( isset( $upload_error_strings[ $file['error'] ] ) ) {
						$message = $upload_error_strings[ $file['error'] ];
					}
				}
				$check_filetype = wp_check_filetype( $file['name'] );
				$check_filetype = $check_filetype['ext'];
				if ( ! $check_filetype ) {
					$passed  = FALSE;
					$message = esc_html__( "Sorry, this file type is not permitted for security reasons.", 'woocommerce-tm-extra-product-options' ) . ' (' . pathinfo( $check_filetype, PATHINFO_EXTENSION ) . ')';
				}
			} else {
				$passed  = FALSE;
				$message = esc_html__( "No file found.", 'woocommerce-tm-extra-product-options' );
			}
			if ( $passed ) {

				$start_pos = 0;
				$end_pos   = NULL;
				$enc       = mb_detect_encoding( $file['tmp_name'], 'UTF-8, ISO-8859-1', TRUE );

				if ( $enc ) {
					setlocale( LC_ALL, 'en_US.' . $enc );
				}
				ini_set( 'auto_detect_line_endings', TRUE );

				$parsed_data = array();
				$raw_headers = array();

				if ( ( $handle = fopen( $file['tmp_name'], "r" ) ) !== FALSE ) {
					$csv = new THEMECOMPLETE_CONVERT_ARRAY_TO_CSV();

					while ( ( $header = fgetcsv( $handle, 0, $csv->delimiter ) ) !== FALSE ) {
						$header = $this->remove_utf8_bom( $header );

						$position = ftell( $handle );

						if ( ! empty( $header[0] ) ) {
							$start_pos = $position;
							break;
						}
					}

					if ( $start_pos != 0 ) {
						fseek( $handle, $start_pos );
					}

					while ( ( $postmeta = fgetcsv( $handle, 0, $csv->delimiter ) ) !== FALSE ) {
						$row = array();
						foreach ( $header as $key => $heading ) {
							$heading   = strtolower( $this->remove_utf8_bom( trim( $heading ) ) );
							$s_heading = $heading;
							$s_heading = $this->format_data_from_csv( $s_heading, $enc );

							if ( $s_heading == '' ) {
								continue;
							}
							$row[ $s_heading ]         = ( isset( $postmeta[ $key ] ) ) ? $this->format_data_from_csv( $postmeta[ $key ], $enc ) : '';
							$raw_headers[ $s_heading ] = $heading;
						}

						$parsed_data[] = $row;
						unset( $postmeta, $row );
						$position = ftell( $handle );
						if ( $end_pos && $position >= $end_pos ) {
							break;
						}
					}
					fclose( $handle );
				}

				foreach ( $parsed_data as $key => $value ) {
					foreach ( $value as $k => $v ) {
						$k = trim( $k );
						$k = $this->remove_utf8_bom( $k );
						if ( strpos( $k, "multiple_" ) === 0 ) {
							$v = THEMECOMPLETE_EPO_HELPER()->array_unserialize( $v );

							if ( THEMECOMPLETE_EPO_HELPER()->str_endsswith( $k, 'checkboxes_options_default_value' ) && is_array( $v ) ) {
								$data[ $k ][] = $v;
							} elseif ( THEMECOMPLETE_EPO_HELPER()->str_endsswith( $k, 'options_default_value' ) && is_array( $v ) ) {
								$data[ $k ][] = $v[0];
							} else {
								$data[ $k ][] = $v;
							}
						} elseif ( strpos( $k, "variations_options" ) === 0 ) {
							$v = maybe_unserialize( $v );
							if ( is_array( $v ) ) {
								foreach ( $v as $ok => $ov ) {
									$data[ $k ][ $ok ] = $ov;
								}
							}
						} else {
							$data[ $k ][] = $v;
						}
					}

					if ( $data ) {
						$message = esc_html__( "File imported.", 'woocommerce-tm-extra-product-options' );
					}
				}

			}
		} else {
			$message = esc_html__( "Invalid import method used.", 'woocommerce-tm-extra-product-options' );
		}

		return array( 'data' => $data, 'message' => $message );
	}

	/**
	 * Cleans the csv data
	 *
	 * @since 1.0
	 */
	public function clean_csv_data( $import ) {
		$remove_keys  = array();
		$clean_import = array();
		$element_keys = array();
		if ( ! is_array( $import ) || ! isset( $import['sections'] ) ) {
			return $clean_import;
		}
		foreach ( $import['sections'] as $key => $value ) {
			if ( $value == "" ) {
				$remove_keys[] = $key;
			}
		}
		if ( isset( $import['element_type'] ) ) {
			foreach ( $import['element_type'] as $key => $value ) {
				if ( ! isset( $element_keys[ $value ] ) ) {
					$element_keys[ $value ] = array();
				}
				$element_keys[ $value ][] = count( $element_keys[ $value ] );
			}
		}
		foreach ( $import as $key => $value ) {
			if ( $key != "element_type" && $key != "div_size" ) {
				$split       = explode( "_", $key );
				$element_key = FALSE;
				if ( isset( $split[0] ) && $split[0] == "multiple" ) {
					if ( isset( $split[1] ) ) {
						$element_key = $split[1];
					}
				} else {
					$element_key = $split[0];
				}

				foreach ( $import[ $key ] as $k => $v ) {
					if ( $element_key == "sections" || $element_key == "section" ) {
						if ( ! in_array( $k, $remove_keys ) ) {
							$clean_import[ $key ][ $k ] = $v;
						}
					} else {
						if ( isset( $element_keys[ $element_key ] ) && in_array( $k, $element_keys[ $element_key ] ) ) {
							$clean_import[ $key ][ $k ] = $v;
						}
					}
				}
			}
		}
		$clean_import["element_type"] = isset( $import['element_type'] ) ? $import['element_type'] : array( "" );
		$clean_import["div_size"]     = $import['div_size'];

		return $clean_import;
	}

	/**
	 * Import csv
	 *
	 * @since 1.0
	 */
	public function import() {
		check_ajax_referer( 'import-nonce', 'security' );

		$this->check_if_active( "import" );
		if ( ! isset( $_SESSION ) ) {
			session_start();
		}
		$message = '';
		$postMax = ini_get( 'post_max_size' );

		// post_max_size debug 
		if ( empty( $_FILES )
		     && empty( $_POST )
		     && isset( $_SERVER['REQUEST_METHOD'] )
		     && strtolower( $_SERVER['REQUEST_METHOD'] ) === 'post'
		     && isset( $_SERVER['CONTENT_LENGTH'] )
		     && (float) $_SERVER['CONTENT_LENGTH'] > $postMax
		) {
			$message = sprintf( esc_html__( 'Trying to upload files larger than %s is not allowed!', 'woocommerce-tm-extra-product-options' ), $postMax );
		}

		$import  = $this->check_for_import();
		$message = $import['message'];
		$import  = $import['data'];

		if ( ! empty( $import ) ) {

			if ( ! isset( $_REQUEST["is_original_post"] ) || ! empty( $_REQUEST["is_original_post"] ) ) {
				$import = $this->clean_csv_data( $import );
				$import = THEMECOMPLETE_EPO_HELPER()->recreate_element_ids( $import );
			}
			if ( ! empty( $import ) ) {
				$import                 = array( "tm_meta" => array( "tmfbuilder" => $import ) );
				$_SESSION['import_csv'] = $import;
				if ( ! empty( $_REQUEST['import_override'] ) ) {
					$_SESSION['import_override'] = $_REQUEST['import_override'];
				}

				// save_imported_csv returns internal HTML code
				// already escaped where needed
				ob_start();
				THEMECOMPLETE_EPO_ADMIN_GLOBAL()->save_imported_csv( $_REQUEST['post_id'] );
				$options = ob_get_clean();

				$json_result = array( 'result' => 1, 'message' => $message, 'jsobject' => THEMECOMPLETE_EPO_BUILDER()->jsbuilder );

				if ( $options ) {
					$json_result['options'] = $options;
				}

				wp_send_json( $json_result );
			} else {
				$message = esc_html__( 'Invalid CSV file!', 'woocommerce-tm-extra-product-options' );
			}
		}
		// $message is escaped
		wp_send_json( array( 'result' => 0, 'message' => $message ) );

	}

	/**
	 * Export csv
	 *
	 * @since 1.0
	 */
	public function export( $var ) {
		$this->check_if_active( "export" );

		check_ajax_referer( 'export-nonce', 'security' );
		$tm_meta  = '';
		$sendback = '';
		if ( isset( $_REQUEST[ $var ] ) ) {

			$tm_metas = json_decode( nl2br( stripslashes( $_REQUEST[ $var ] ) ), TRUE );

			if ( ! empty( $tm_metas )
			     && is_array( $tm_metas )
			     && isset( $tm_metas['tm_meta'] )
			     && is_array( $tm_metas['tm_meta'] )
			     && isset( $tm_metas['tm_meta']['tmfbuilder'] )
			) {

				$csv = new THEMECOMPLETE_CONVERT_ARRAY_TO_CSV();
				if ( ! isset( $_REQUEST["is_original_post"] ) || ! empty( $_REQUEST["is_original_post"] ) ) {
					$tm_meta = $csv->convert( THEMECOMPLETE_EPO_HELPER()->recreate_element_ids( $tm_metas['tm_meta']['tmfbuilder'] ) );
				} else {
					$tm_meta = $csv->convert( $tm_metas['tm_meta']['tmfbuilder'] );
				}
				$sitename = sanitize_key( get_bloginfo( 'name' ) );

				$sitename = ( ! empty( $sitename ) ) ? $sitename . '.' : $sitename;
				$filename = $sitename . 'users.' . date( 'Y-m-d-H-i-s' ) . '.csv';

				if ( ! isset( $_SESSION ) ) {
					session_start();
				}
				$_SESSION[ $filename ] = $tm_meta;

				$sendback = esc_url_raw( add_query_arg( 'filename', $filename, admin_url( "edit.php?post_type=product&page=" . THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK . "&action=download" ) ) );

			}
		}

		wp_send_json(
			array( 'result' => $sendback, )
		);

	}

	/**
	 * Export csv by product id
	 *
	 * @since 1.0
	 */
	public function export_by_product_id( $post_id = 0 ) {
		$this->check_if_active( "export_by_product_id" );

		$tm_meta = array();
		$epos    = THEMECOMPLETE_EPO()->get_product_tm_epos( $post_id );

		if ( is_array( $epos ) && isset( $epos['global_ids'] ) && is_array( $epos['global_ids'] ) ) {

			foreach ( $epos['global_ids'] as $post ) {

				$id   = $post->ID;
				$type = $post->post_type;

				$meta = themecomplete_get_post_meta( $id, 'tm_meta', TRUE );

				if ( ! empty( $meta )
				     && is_array( $meta )
				     && isset( $meta['tmfbuilder'] )
				     && is_array( $meta['tmfbuilder'] )
				) {

					$meta    = THEMECOMPLETE_EPO_HELPER()->recreate_element_ids( $meta );
					$tm_meta = array_merge_recursive( $tm_meta, $meta );

				}

			}

		}

		if ( ! empty( $tm_meta )
		     && is_array( $tm_meta )
		     && isset( $tm_meta['tmfbuilder'] )
		     && is_array( $tm_meta['tmfbuilder'] )
		) {

			$tm_meta = $tm_meta['tmfbuilder'];

			$csv     = new THEMECOMPLETE_CONVERT_ARRAY_TO_CSV();
			$tm_meta = $csv->convert( $tm_meta );

			$sitename = sanitize_key( get_bloginfo( 'name' ) );
			if ( ! empty( $sitename ) ) {
				$sitename .= '.';
			}
			$filename = $sitename . 'form.' . $post_id . '.' . date( 'Y-m-d-H-i-s' ) . '.csv';

			if ( ! isset( $_SESSION ) ) {
				session_start();
			}

			$_SESSION[ $filename ] = $tm_meta;
			$this->download( $filename );

		}

	}

	/**
	 * Export csv by form id
	 *
	 * @since 1.0
	 */
	public function export_by_id( $post_id = 0 ) {
		$this->check_if_active( "export_by_id" );

		check_ajax_referer( 'tmexport_form_nonce_' . $post_id, 'security' );

		$tm_meta = themecomplete_get_post_meta( $post_id, 'tm_meta', TRUE );

		if ( ! empty( $tm_meta )
		     && is_array( $tm_meta )
		     && isset( $tm_meta['tmfbuilder'] )
		     && is_array( $tm_meta['tmfbuilder'] )
		) {

			$tm_meta = THEMECOMPLETE_EPO_HELPER()->recreate_element_ids( $tm_meta );
			$tm_meta = $tm_meta['tmfbuilder'];

			$csv     = new THEMECOMPLETE_CONVERT_ARRAY_TO_CSV();
			$tm_meta = $csv->convert( $tm_meta );

			$sitename = sanitize_key( get_bloginfo( 'name' ) );
			if ( ! empty( $sitename ) ) {
				$sitename .= '.';
			}
			$filename = $sitename . 'form.' . $post_id . '.' . date( 'Y-m-d-H-i-s' ) . '.csv';

			if ( ! isset( $_SESSION ) ) {
				session_start();
			}

			$_SESSION[ $filename ] = $tm_meta;
			$this->download( $filename );

		}

	}

	/**
	 * Download csv
	 *
	 * @since 1.0
	 */
	public function download( $filename = 0 ) {
		$this->check_if_active( "download" );
		if ( ! isset( $_SESSION ) ) {
			session_start();
		}
		if ( isset( $_REQUEST['filename'] ) ) {
			$filename = $_REQUEST['filename'];
		}
		if ( function_exists( 'set_time_limit' ) ) {
			set_time_limit( 0 );
		}
		if ( function_exists( 'apache_setenv' ) ) {
			apache_setenv( 'no-gzip', 1 );
		}
		ini_set( 'zlib.output_compression', 0 );

		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Content-Type: text/csv; charset=UTF-8', TRUE );
		if ( ! empty( $filename ) ) {
			if ( isset( $_SESSION[ $filename ] ) ) {
				$csv = $_SESSION[ $filename ];
				unset( $_SESSION[ $filename ] );
				// fix for Excel both on Windows and OS X  
				$csv = mb_convert_encoding( $csv, 'UTF-8' );
				$csv = pack( 'H*', 'EFBBBF' ) . $csv;

				// No escape required or allowed here
				die( wp_check_invalid_utf8( apply_filters( 'wc_epo_download_csv', $csv ) ) );
			}
		}
		die();
	}

}

