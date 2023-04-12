<?php
/**
 * Extra Product Options CSV Importer/Exporter
 *
 * @package Extra Product Options/Admin
 * @version 6.0
 * phpcs:disable Generic.Files.OneObjectStructurePerFile
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options CSV import/export class
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */
final class THEMECOMPLETE_EPO_ADMIN_CSV {

	/**
	 * Error loading string
	 *
	 * @var string
	 */
	private $error_loading_string = '';

	/**
	 * If mb_detect_encoding is supported
	 *
	 * @var bool
	 */
	private $is_active = true;

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		if ( ! function_exists( 'mb_detect_encoding' ) ) {
			$this->error_loading_string = '<p>' . esc_html__( 'The php functions mb_detect_encoding and mb_convert_encoding are required to import and export CSV files. Please ask your hosting provider to enable this function.', 'woocommerce-tm-extra-product-options' ) . '</p>';
			$this->is_active            = false;
		}

	}

	/**
	 * Check if the system supports mb_detect_encoding
	 *
	 * @param string $type Action type.
	 * @since 1.0
	 */
	public function check_if_active( $type = '' ) {
		if ( ! $this->is_active ) {
			switch ( $type ) {
				case 'download':
				case 'export_by_id':
				case 'export_by_product_id':
					wp_die( wp_kses_post( $this->error_loading_string ) );
					break;

				default:
					// $this->error_loading_string is escaped
					wp_send_json(
						[
							'error'   => 1,
							'message' => $this->error_loading_string,
						]
					);
					break;
			}
		}
	}

	/**
	 * Removes utf-8 BOM
	 *
	 * @param string $text Text to remove BOM from.
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
	 * @param string $data Data to check.
	 * @param string $enc Encoding.
	 * @since 1.0
	 */
	public function format_data_from_csv( $data, $enc ) {
		return ( 'UTF-8' === $enc ) ? $data : utf8_encode( $data );
	}

	/**
	 * Import option data
	 *
	 * @since 6.1
	 */
	public function do_options_import() {
		$import  = $this->check_for_import();
		$message = $import['message'];
		$data    = [];

		if ( isset( $import['data']['file'] ) ) {
			$file        = $import['data']['file'];
			$enc         = $import['data']['enc'];
			$parsed_data = [];
			$raw_headers = [];

			$handle = fopen( $file['tmp_name'], 'r' ); // phpcs:ignore WordPress.WP.AlternativeFunctions
			if ( false !== $handle ) {
				$csv = new THEMECOMPLETE_CONVERT_ARRAY_TO_CSV();

				$start_pos = 0;
				while ( ( $header = fgetcsv( $handle, 0, $csv->delimiter ) ) !== false ) { // phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition
					$header = $this->remove_utf8_bom( $header );

					$position = ftell( $handle );

					if ( ! empty( $header[0] ) ) {
						$start_pos = $position;
						break;
					}
				}

				if ( (int) 0 !== (int) $start_pos ) {
					fseek( $handle, $start_pos );
				}

				while ( ( $postmeta = fgetcsv( $handle, 0, $csv->delimiter ) ) !== false ) { // phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition
					$row = [];
					foreach ( $header as $key => $heading ) {
						$heading   = $this->remove_utf8_bom( trim( $heading ) );
						$s_heading = $heading;
						$s_heading = $this->format_data_from_csv( $s_heading, $enc );

						if ( '' === $s_heading ) {
							continue;
						}
						$row[ $s_heading ]         = ( isset( $postmeta[ $key ] ) ) ? $this->format_data_from_csv( $postmeta[ $key ], $enc ) : '';
						$raw_headers[ $s_heading ] = $heading;
					}

					$parsed_data[] = $row;
					unset( $postmeta, $row );
				}
				fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions
			}

			$data = $parsed_data;
		}

		return [
			'data'    => $data,
			'message' => $message,
		];
	}

	/**
	 * Import lookup table data
	 *
	 * @since 6.1
	 */
	public function do_lookuptable_import() {
		$import  = $this->check_for_import();
		$message = $import['message'];
		$data    = [];

		if ( isset( $import['data']['file'] ) ) {
			$file        = $import['data']['file'];
			$enc         = $import['data']['enc'];
			$parsed_data = [];
			$raw_data    = [];
			$headers     = [];

			$handle = fopen( $file['tmp_name'], 'r' ); // phpcs:ignore WordPress.WP.AlternativeFunctions
			if ( false !== $handle ) {
				$csv        = new THEMECOMPLETE_CONVERT_ARRAY_TO_CSV();
				$table_name = '';
				while ( ( $header = fgetcsv( $handle, 0, $csv->delimiter ) ) !== false ) { // phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition
					$header = $this->remove_utf8_bom( $header );
					if ( ! empty( $header[0] ) && ! is_numeric( $header[0] ) && 'max' !== strtolower( $header[0] ) ) {
						$table_name = $this->remove_utf8_bom( trim( $header[0] ) );
						$table_name = $this->format_data_from_csv( $table_name, $enc );
					}
					if ( $table_name ) {
						$row = [];
						foreach ( $header as $key => $heading ) {
							$heading = $this->remove_utf8_bom( trim( $heading ) );
							$heading = $this->format_data_from_csv( $heading, $enc );
							if ( 'max' !== $heading && $table_name !== $heading ) {
								$heading = wc_format_decimal( $heading, false, true );
							}
							$row[ $key ] = $heading;
						}
						$raw_data[ $table_name ][] = $row;
					}
				}

				foreach ( $raw_data as $raw_name => $data ) {
					foreach ( $data[0] as $x ) {
						if ( '' === $x || ( ! is_numeric( $x ) && 'max' !== $x ) ) {
							continue;
						}
						$headers[ $raw_name ][] = $x;
					}
				}

				foreach ( $raw_data as $raw_name => $data ) {
					unset( $data[0] );
					foreach ( $headers[ $raw_name ] as $ckey => $column_data ) {
						foreach ( $data as $xline ) {
							$key = $xline[0];
							if ( '' === $key ) {
								continue;
							}
							foreach ( $xline as $xkey => $x ) {
								if ( 0 === $xkey ) {
									continue;
								}
								if ( $xkey === $ckey + 1 ) {
									$parsed_data[ $raw_name ][ $column_data ][ $key ] = $x;
								}
							}
						}
					}
				}

				fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions
			}

			$data = $parsed_data;
		}

		return [
			'data'    => $data,
			'message' => $message,
		];
	}

	/**
	 * Check if there is data to be imported
	 *
	 * @since 1.0
	 */
	public function check_for_import() {
		$files   = $_FILES;
		$data    = [];
		$message = esc_html__( 'Invalid CSV file.', 'woocommerce-tm-extra-product-options' );
		if ( isset( $files['builder_import_file'] ) ) {

			$passed = true;
			$file   = $files['builder_import_file'];

			if ( ! empty( $file['name'] ) ) {
				if ( ! empty( $file['error'] ) ) {
					$passed = false;
					// Courtesy of php.net, the strings that describe the error indicated in $_FILES[{form field}]['error'].
					$upload_error_strings = [
						false,
						esc_html__( 'The uploaded file exceeds the upload_max_filesize directive in php.ini.', 'woocommerce-tm-extra-product-options' ),
						esc_html__( 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.', 'woocommerce-tm-extra-product-options' ),
						esc_html__( 'The uploaded file was only partially uploaded.', 'woocommerce-tm-extra-product-options' ),
						esc_html__( 'No file was uploaded.', 'woocommerce-tm-extra-product-options' ),
						'',
						esc_html__( 'Missing a temporary folder.', 'woocommerce-tm-extra-product-options' ),
						esc_html__( 'Failed to write file to disk.', 'woocommerce-tm-extra-product-options' ),
						esc_html__( 'File upload stopped by extension.', 'woocommerce-tm-extra-product-options' ),
					];
					if ( isset( $upload_error_strings[ $file['error'] ] ) ) {
						$message = $upload_error_strings[ $file['error'] ];
					}
				}
				$check_filetype = wp_check_filetype( $file['name'] );
				$check_filetype = $check_filetype['ext'];
				if ( ! $check_filetype ) {
					$passed  = false;
					$message = esc_html__( 'Sorry, this file type is not permitted for security reasons.', 'woocommerce-tm-extra-product-options' ) . ' (' . pathinfo( $check_filetype, PATHINFO_EXTENSION ) . ')';
				}
			} else {
				$passed  = false;
				$message = esc_html__( 'No file found.', 'woocommerce-tm-extra-product-options' );
			}
			if ( $passed ) {
				$enc = mb_detect_encoding( $file['tmp_name'], 'UTF-8, ISO-8859-1', true );

				if ( $enc ) {
					setlocale( LC_ALL, 'en_US.' . $enc );
				}
				ini_set( 'auto_detect_line_endings', true );

				$data['file'] = $file;
				$data['enc']  = $enc;
			}
		} else {
			$message = esc_html__( 'Invalid import method used.', 'woocommerce-tm-extra-product-options' );
		}

		return [
			'data'    => $data,
			'message' => $message,
		];
	}

	/**
	 * Parse imported data (for options)
	 *
	 * @since 6.1
	 */
	public function parse_imported_data() {

		$parsed_data = [];
		$import      = $this->do_options_import();
		$message     = $import['message'];
		$import      = $import['data'];
		$data        = [];

		if ( ! empty( $import ) ) {
			$parsed_data = $import;

			foreach ( $parsed_data as $key => $value ) {
				foreach ( $value as $k => $v ) {
					$k = trim( $k );
					$k = $this->remove_utf8_bom( $k );
					if ( strpos( $k, 'multiple_' ) === 0 ) {
						$v = THEMECOMPLETE_EPO_HELPER()->array_unserialize( $v );

						if ( THEMECOMPLETE_EPO_HELPER()->str_endsswith( $k, 'checkboxes_options_default_value' ) && is_array( $v ) ) {
							$data[ $k ][] = $v;
						} elseif ( THEMECOMPLETE_EPO_HELPER()->str_endsswith( $k, 'options_default_value' ) && is_array( $v ) ) {
							$data[ $k ][] = $v[0];
						} else {
							$data[ $k ][] = $v;
						}
					} elseif ( strpos( $k, 'variations_options' ) === 0 ) {
						$v = themecomplete_maybe_unserialize( $v );
						if ( is_array( $v ) ) {
							foreach ( $v as $ok => $ov ) {
								$data[ $k ][ $ok ] = $ov;
							}
						}
					} else {
						if ( 'product_productids' === $k && false !== strpos( $v, '|' ) ) {
							$v = explode( '|', $v );
						}
						$data[ $k ][] = $v;
					}
				}

				if ( $data ) {
					$message = esc_html__( 'File imported.', 'woocommerce-tm-extra-product-options' );
				}
			}
		}

		return [
			'data'    => $data,
			'message' => $message,
		];
	}

	/**
	 * Parse imported data (for options)
	 *
	 * @since 6.1
	 */
	public function parse_imported_lookuptable() {

		$parsed_data = [];
		$import      = $this->do_lookuptable_import();
		$message     = $import['message'];
		$import      = $import['data'];
		$data        = [];
		$table_names = [];

		if ( ! empty( $import ) ) {
			$parsed_data = $import;

			$parsed_headers = [];

			foreach ( $parsed_data as $key => $value ) {
				$table_names[] = $key;
				foreach ( $value as $k => $v ) {
					if ( is_numeric( $k ) || 'max' === $k ) {
						$data = true;
						break;
					}
				}
			}

			if ( $data ) {
				$message = esc_html__( 'File imported.', 'woocommerce-tm-extra-product-options' );
			}
		} else {
			$message = esc_html__( 'The CSV file is invalid!', 'woocommerce-tm-extra-product-options' );
		}

		return [
			'table_names' => $table_names,
			'data'        => $parsed_data,
			'message'     => $message,
		];
	}

	/**
	 * Cleans the csv data
	 *
	 * @param array $import Import array.
	 * @since 1.0
	 */
	public function clean_csv_data( $import ) {
		$remove_keys  = [];
		$clean_import = [];
		$element_keys = [];
		if ( ! is_array( $import ) || ! isset( $import['sections'] ) ) {
			return $clean_import;
		}
		foreach ( $import['sections'] as $key => $value ) {
			if ( '' === $value ) {
				$remove_keys[] = $key;
			}
		}
		if ( isset( $import['element_type'] ) ) {
			foreach ( $import['element_type'] as $key => $value ) {
				if ( ! isset( $element_keys[ $value ] ) ) {
					$element_keys[ $value ] = [];
				}
				$element_keys[ $value ][] = count( $element_keys[ $value ] );
			}
		}
		foreach ( $import as $key => $value ) {
			if ( 'element_type' !== $key && 'div_size' !== $key ) {
				$split       = explode( '_', $key );
				$element_key = false;
				if ( isset( $split[0] ) && 'multiple' === $split[0] ) {
					if ( isset( $split[1] ) ) {
						$element_key = $split[1];
					}
				} else {
					$element_key = $split[0];
				}

				foreach ( $import[ $key ] as $k => $v ) {
					if ( 'sections' === $element_key || 'section' === $element_key ) {
						if ( ! in_array( $k, $remove_keys ) ) { // phpcs:ignore WordPress.PHP.StrictInArray
							$clean_import[ $key ][ $k ] = $v;
						}
					} else {
						if ( isset( $element_keys[ $element_key ] ) && in_array( $k, $element_keys[ $element_key ] ) ) { // phpcs:ignore WordPress.PHP.StrictInArray
							$clean_import[ $key ][ $k ] = $v;
						}
					}
				}
			}
		}
		$clean_import['element_type'] = isset( $import['element_type'] ) ? $import['element_type'] : [ '' ];
		$clean_import['div_size']     = $import['div_size'];

		return $clean_import;
	}

	/**
	 * Import csv
	 *
	 * @since 1.0
	 */
	public function import() {
		check_ajax_referer( 'import-nonce', 'security' );
		$message        = $this->debug_post_max_size();
		$import         = $this->parse_imported_data();
		$import_message = $import['message'];
		$import         = $import['data'];

		if ( ! empty( $import ) ) {
			$message = $import_message;
			if ( ! isset( $_REQUEST['is_original_post'] ) || ! empty( $_REQUEST['is_original_post'] ) ) {
				$import = $this->clean_csv_data( $import );
				$import = THEMECOMPLETE_EPO_HELPER()->recreate_element_ids( $import );
			}
			if ( ! empty( $import ) ) {
				$import = [ 'tm_meta' => [ 'tmfbuilder' => $import ] ];
				set_transient( 'tc_import_csv', $import, DAY_IN_SECONDS );
				if ( ! empty( $_REQUEST['import_override'] ) ) {
					set_transient( 'tc_import_override', sanitize_text_field( wp_unslash( $_REQUEST['import_override'] ) ), DAY_IN_SECONDS );
				}

				// save_imported_csv returns internal HTML code.
				// already escaped where needed.
				ob_start();
				if ( isset( $_REQUEST['post_id'] ) ) {
					THEMECOMPLETE_EPO_ADMIN_GLOBAL()->save_imported_csv( absint( wp_unslash( $_REQUEST['post_id'] ) ) );
				}
				$options = ob_get_clean();

				$json_result = [
					'result'   => 1,
					'message'  => $message,
					'jsobject' => THEMECOMPLETE_EPO_BUILDER()->jsbuilder,
				];

				if ( $options ) {
					$json_result['options'] = $options;
				}

				wp_send_json( $json_result );
			} else {
				$message = esc_html__( 'Invalid CSV file!', 'woocommerce-tm-extra-product-options' );
			}
		}
		// $message is escaped
		wp_send_json(
			[
				'result'  => 0,
				'message' => $message,
			]
		);

	}

	/**
	 * Debug post_max_size
	 *
	 * @since 6.1
	 */
	public function debug_post_max_size() {
		check_ajax_referer( 'import-nonce', 'security' );
		$this->check_if_active( 'import' );

		$message  = '';
		$post_max = ini_get( 'post_max_size' );

		if ( empty( $_FILES )
			&& empty( $_POST )
			&& isset( $_SERVER['REQUEST_METHOD'] )
			&& 'post' === strtolower( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			&& isset( $_SERVER['CONTENT_LENGTH'] )
			&& (float) $_SERVER['CONTENT_LENGTH'] > $post_max
			&& (
				! isset( $_GET ) || // @phpstan-ignore-line
				(
					isset( $_GET ) && // @phpstan-ignore-line
					isset( $_GET['post_type'] )
					&& isset( $_GET['action'] )
					&& ! THEMECOMPLETE_EPO_HELPER()->str_startswith( sanitize_text_field( wp_unslash( $_GET['post_type'] ) ), 'ct_template' )
					&& ! THEMECOMPLETE_EPO_HELPER()->str_startswith( sanitize_text_field( wp_unslash( $_GET['action'] ) ), 'oxy_render_' )
				)
			)
		) {
			/* translators: post max size  */
			$message = sprintf( esc_html__( 'Trying to upload files larger than %s is not allowed!', 'woocommerce-tm-extra-product-options' ), $post_max );
		}
		return $message;
	}

	/**
	 * Import lookup table csv
	 *
	 * @since 6.1
	 */
	public function lookuptable_import() {
		check_ajax_referer( 'import-nonce', 'security' );

		if ( isset( $_REQUEST['import_override'] ) ) {
			$import_override = absint( wp_unslash( $_REQUEST['import_override'] ) );
			if ( 1 === $import_override ) {
				if ( isset( $_REQUEST['post_id'] ) ) {
					$post_id = absint( wp_unslash( $_REQUEST['post_id'] ) );
					$import  = get_transient( 'tc_lookuptable_import_csv_' . $post_id );
					if ( ! empty( $import ) ) {
						// save_imported_csv returns internal HTML code.
						// already escaped where needed.
						ob_start();
						THEMECOMPLETE_EPO_ADMIN_LOOKUPTABLE()->save_imported_csv( $post_id );
						$table = ob_get_clean();

						$json_result = [
							'result'   => 1,
							'message'  => esc_html__( 'File imported with new tables.', 'woocommerce-tm-extra-product-options' ),
							'jsobject' => [ $import ],
						];
						if ( $table ) {
							$json_result['table'] = $table;
						}
					} else {
						$json_result = [
							'result'  => 0,
							'message' => esc_html__( 'There was an error importing the CSV file.', 'woocommerce-tm-extra-product-options' ),
						];
					}
					wp_send_json( $json_result );
				}
			}
		}

		$message        = $this->debug_post_max_size();
		$import         = $this->parse_imported_lookuptable();
		$table_names    = $import['table_names'];
		$import_message = $import['message'];
		$import         = $import['data'];

		if ( ! empty( $import ) ) {
			$message = $import_message;
			$table   = false;
			if ( isset( $_REQUEST['post_id'] ) ) {
				$post_id = absint( wp_unslash( $_REQUEST['post_id'] ) );
				set_transient( 'tc_lookuptable_import_csv_' . $post_id, $import, DAY_IN_SECONDS );

				$builder = themecomplete_get_post_meta( $post_id, 'lookuptable_meta', true );
				if ( is_array( $builder ) ) {
					$builder_table_names = [];
					foreach ( $builder as $table_name => $data ) {
						$builder_table_names[] = $table_name;
					}
					foreach ( $builder_table_names as $key => $name ) {
						if ( isset( $table_names[ $key ] ) && $name !== $table_names[ $key ] ) {
							$json_result = [
								'result'    => 1,
								'different' => 1,
								'message'   => esc_html__( 'Table names are different. Do you want to override the tables?', 'woocommerce-tm-extra-product-options' ),
							];
							wp_send_json( $json_result );
						}
					}
				}

				// save_imported_csv returns internal HTML code.
				// already escaped where needed.
				ob_start();
				THEMECOMPLETE_EPO_ADMIN_LOOKUPTABLE()->save_imported_csv( $post_id );
				$table = ob_get_clean();
			}

			$json_result = [
				'result'   => 1,
				'message'  => $message,
				'jsobject' => [ $import ],
			];

			if ( $table ) {
				$json_result['table'] = $table;
			}

			wp_send_json( $json_result );
		} else {
			if ( empty( $message ) ) {
				$message = $import_message;
			}
		}
		// $message is escaped
		wp_send_json(
			[
				'result'  => 0,
				'message' => $message,
			]
		);

	}

	/**
	 * Export lookup table csv
	 *
	 * @param string $var Variable to use.
	 * @since 6.3
	 */
	public function export_lookuptable( $var = '' ) {

		$this->check_if_active( 'export' );
		check_ajax_referer( 'export-nonce', 'security' );

		$tm_meta = '';
		$json    = [];
		if ( ! empty( $var ) && isset( $_REQUEST[ $var ] ) ) {

			$tm_metas = json_decode( nl2br( wp_unslash( $_REQUEST[ $var ] ) ), true ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			if ( ! empty( $tm_metas )
				&& is_array( $tm_metas )
			) {

				$csv      = new THEMECOMPLETE_CONVERT_ARRAY_TO_CSV();
				$tm_meta  = $csv->convert_lookuptable( $tm_metas );
				$sitename = sanitize_key( get_bloginfo( 'name' ) );
				$sitename = ( ! empty( $sitename ) ) ? $sitename . '.' : $sitename;
				$filename = $sitename . 'users.' . gmdate( 'Y-m-d-H-i-s' ) . '.csv';

				set_transient( 'tc_export_' . $filename, $tm_meta, DAY_IN_SECONDS );

				$sendback = esc_url_raw( add_query_arg( 'filename', $filename, admin_url( 'edit.php?post_type=product&page=' . THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK . '&action=download' ) ) );
				$json     = [ 'result' => $sendback ];
			} else {
				$json = [
					'error'   => 1,
					'message' => esc_html__( 'Invalid data!', 'woocommerce-tm-extra-product-options' ),
				];
			}
		} else {
			$json = [
				'error'   => 1,
				'message' => esc_html__( 'Invalid request!', 'woocommerce-tm-extra-product-options' ),
			];
		}

		wp_send_json(
			$json
		);

	}

	/**
	 * Export csv
	 *
	 * @param string $var Variable to use.
	 * @since 1.0
	 */
	public function export( $var = '' ) {

		$this->check_if_active( 'export' );
		check_ajax_referer( 'export-nonce', 'security' );

		$tm_meta  = '';
		$sendback = '';
		if ( ! empty( $var ) && isset( $_REQUEST[ $var ] ) ) {

			$tm_metas = json_decode( nl2br( wp_unslash( $_REQUEST[ $var ] ) ), true ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			if ( ! empty( $tm_metas )
				&& is_array( $tm_metas )
				&& isset( $tm_metas['tm_meta'] )
				&& is_array( $tm_metas['tm_meta'] )
				&& isset( $tm_metas['tm_meta']['tmfbuilder'] )
			) {

				$csv = new THEMECOMPLETE_CONVERT_ARRAY_TO_CSV();
				if ( ! isset( $_REQUEST['is_original_post'] ) || ! empty( $_REQUEST['is_original_post'] ) ) {
					$tm_meta = $csv->convert( THEMECOMPLETE_EPO_HELPER()->recreate_element_ids( $tm_metas['tm_meta']['tmfbuilder'] ) );
				} else {
					$tm_meta = $csv->convert( $tm_metas['tm_meta']['tmfbuilder'] );
				}
				$sitename = sanitize_key( get_bloginfo( 'name' ) );

				$sitename = ( ! empty( $sitename ) ) ? $sitename . '.' : $sitename;
				$filename = $sitename . 'users.' . gmdate( 'Y-m-d-H-i-s' ) . '.csv';

				set_transient( 'tc_export_' . $filename, $tm_meta, DAY_IN_SECONDS );

				$sendback = esc_url_raw( add_query_arg( 'filename', $filename, admin_url( 'edit.php?post_type=product&page=' . THEMECOMPLETE_EPO_GLOBAL_POST_TYPE_PAGE_HOOK . '&action=download' ) ) );

			}
		}

		wp_send_json(
			[ 'result' => $sendback ]
		);

	}

	/**
	 * Export builder
	 *
	 * @param array   $tm_meta Builder meta data.
	 * @param boolean $recreate If the internal element ids should be recreated.
	 * @param integer $post_id The post id.
	 * @since 6.0
	 */
	private function export_builder( $tm_meta = [], $recreate = true, $post_id = 0 ) {

		if ( ! empty( $tm_meta )
			&& is_array( $tm_meta )
			&& isset( $tm_meta['tmfbuilder'] )
			&& is_array( $tm_meta['tmfbuilder'] )
		) {

			if ( $recreate ) {
				$tm_meta = THEMECOMPLETE_EPO_HELPER()->recreate_element_ids( $tm_meta );
			}
			$tm_meta = $tm_meta['tmfbuilder'];

			$csv     = new THEMECOMPLETE_CONVERT_ARRAY_TO_CSV();
			$tm_meta = $csv->convert( $tm_meta );

			$sitename = sanitize_key( get_bloginfo( 'name' ) );
			if ( ! empty( $sitename ) ) {
				$sitename .= '.';
			}
			$filename = $sitename . 'form.' . $post_id . '.' . gmdate( 'Y-m-d-H-i-s' ) . '.csv';

			set_transient( 'tc_export_' . $filename, $tm_meta, DAY_IN_SECONDS );
			$this->download( $filename );

		}

	}

	/**
	 * Export csv by product id
	 *
	 * @param integer $post_id The post id.
	 * @since 1.0
	 */
	public function export_by_product_id( $post_id = 0 ) {
		$this->check_if_active( 'export_by_product_id' );

		$tm_meta = [];
		$epos    = THEMECOMPLETE_EPO()->get_product_tm_epos( $post_id );

		if ( is_array( $epos ) && isset( $epos['global_ids'] ) && is_array( $epos['global_ids'] ) ) {

			foreach ( $epos['global_ids'] as $post ) {

				$id   = $post->ID;
				$type = $post->post_type;

				$meta = themecomplete_get_post_meta( $id, 'tm_meta', true );

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

		$this->export_builder( $tm_meta, false, $post_id );

	}

	/**
	 * Export csv by form id
	 *
	 * @param integer $post_id The post id.
	 * @since 1.0
	 */
	public function export_by_id( $post_id = 0 ) {
		$this->check_if_active( 'export_by_id' );

		check_ajax_referer( 'tmexport_form_nonce_' . $post_id, 'security' );

		$tm_meta = themecomplete_get_post_meta( $post_id, 'tm_meta', true );

		$this->export_builder( $tm_meta, true, $post_id );

	}

	/**
	 * Download csv
	 *
	 * @param string $filename The file name.
	 * @since 1.0
	 */
	public function download( $filename = '' ) {
		$this->check_if_active( 'download' );

		if ( isset( $_REQUEST['filename'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$filename = sanitize_text_field( wp_unslash( $_REQUEST['filename'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
		if ( function_exists( 'set_time_limit' ) ) {
			set_time_limit( 0 );
		}
		if ( function_exists( 'apache_setenv' ) ) {
			apache_setenv( 'no-gzip', 1 ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions
		}
		ini_set( 'zlib.output_compression', 0 ); // phpcs:ignore WordPress.PHP.IniSet

		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Content-Type: text/csv; charset=UTF-8', true );
		if ( ! empty( $filename ) ) {
			$csv = get_transient( 'tc_export_' . $filename );
			if ( false !== $csv ) {
				delete_transient( 'tc_export_' . $filename );
				// fix for Excel both on Windows and OS X.
				$csv = mb_convert_encoding( $csv, 'UTF-8' );
				$csv = pack( 'H*', 'EFBBBF' ) . $csv;

				// No escape required or allowed here.
				die( wp_check_invalid_utf8( apply_filters( 'wc_epo_download_csv', $csv ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput
			}
		}
		die();
	}

}


/**
 * Convert a PHP array into CSV
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */
final class THEMECOMPLETE_CONVERT_ARRAY_TO_CSV {

	/**
	 * The delimiter
	 *
	 * @var string
	 */
	public $delimiter;

	/**
	 * The text separator
	 *
	 * @var string
	 */
	public $text_separator;

	/**
	 * The replace text separator
	 *
	 * @var string
	 */
	public $replace_text_separator;

	/**
	 * The line delimiter
	 *
	 * @var string
	 */
	public $line_delimiter;

	/**
	 * Class Constructor
	 *
	 * @param string $delimiter The delimiter.
	 * @param string $text_separator The text separator.
	 * @param string $replace_text_separator The replace text separator.
	 * @param string $line_delimiter The line delimiter.
	 * @since 1.0
	 */
	public function __construct( $delimiter = ',', $text_separator = '"', $replace_text_separator = '""', $line_delimiter = "\n" ) {
		$this->delimiter              = $delimiter;
		$this->text_separator         = $text_separator;
		$this->replace_text_separator = $replace_text_separator;
		$this->line_delimiter         = $line_delimiter;
	}

	/**
	 * Replaces the data
	 *
	 * @param string $data Data to replace.
	 * @since 1.0
	 */
	public function replace_data( $data = '' ) {
		return $this->text_separator
			. str_replace( $this->text_separator, $this->replace_text_separator, $data )
			. $this->text_separator;
	}

	/**
	 * Formats the data
	 *
	 * @param string $data Data to format.
	 * @since 1.0
	 */
	public function format_data( $data = '' ) {
		$data = (string) ( $data );
		$enc  = mb_detect_encoding( $data, 'UTF-8, ISO-8859-1', true );
		$data = ( 'UTF-8' === $enc ) ? $data : utf8_encode( $data );

		return $data;
	}

	/**
	 * Converts the data
	 *
	 * @param array $input Data array to convert.
	 * @since 1.0
	 */
	public function convert( $input = [] ) {
		$lines  = [];
		$header = [];
		$row    = [];
		$csv    = '';
		foreach ( $input as $key => $v ) {
			$header[ $key ] = $key;
			$line           = $this->convertline( $v );
			$lines[ $key ]  = $line;
		}
		// Re-order headers.
		$header = [ 'div_size' => $header['div_size'] ] + $header;
		$header = [ 'element_type' => $header['element_type'] ] + $header;
		$lines  = [ 'div_size' => $lines['div_size'] ] + $lines;
		$lines  = [ 'element_type' => $lines['element_type'] ] + $lines;

		foreach ( $lines as $key => $value ) {
			if ( ! empty( $value ) && is_array( $value ) ) {
				if ( 'variations_options' === $key ) {
					$v              = serialize( $value ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions
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
			$value = [];
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
	 * Converts the lookuptable data
	 *
	 * @param array $input Data array to convert.
	 * @since 1.0
	 */
	public function convert_lookuptable( $input = [] ) {
		$csv           = '';
		$input_length  = count( $input );
		$input_counter = 0;
		foreach ( $input as $table_name => $table_data ) {
			$input_counter++;

			$lines  = [];
			$y_axis = [];
			$row    = [];

			$lines[0] = [ $table_name ];
			foreach ( $table_data as $x_axis => $y ) {
				$lines[0][ count( $lines[0] ) ] = $x_axis;
				foreach ( $y as $y_key => $y_value ) {
					if ( ! isset( $y_axis[ $y_key ] ) ) {
						$y_axis[ $y_key ] = [ $y_key ];
					}
					array_push( $y_axis[ $y_key ], $y_value );
				}
			}
			$counter = 1;
			foreach ( $y_axis as $y_data_key => $y_data ) {
				$lines[ $counter ] = $y_data;
				$counter ++;
			}

			foreach ( $lines as $key => $value ) {
				// @phpstan-ignore-next-line
				if ( ! empty( $value ) && is_array( $value ) ) {
					foreach ( $value as $k => $v ) {
						$v = $this->format_data( $v );
						// Don't add text_separator for the table name.
						if ( ! empty( $key ) || ! empty( $k ) ) {
							$v = $this->replace_data( $v );
						}

						$row[ $key ][ $k ] = $v;
					}
				}
			}

			$result = [];
			foreach ( $row as $sub_array ) {
				$result[] = implode( $this->delimiter, $sub_array );
			}
			$csv_part = implode( $this->line_delimiter, $result );

			$csv = $csv . $csv_part;
			if ( $input_counter < $input_length ) {
				$csv = $csv . $this->line_delimiter . $this->line_delimiter;
			}
		}

		return $csv;
	}

	/**
	 * Convert a single line
	 *
	 * @param mixed $line Line to convert.
	 * @since 1.0
	 */
	private function convertline( $line ) {
		$csv_line = [];
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
