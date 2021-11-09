<?php

namespace WPMailSMTP\Pro\Emails\Logs\Export;

use WP_Error;
use WPMailSMTP\Uploads;
use WPMailSMTP\Helpers\Helpers;
use WPMailSMTP\Vendor\Goodby\CSV\Export\Standard\CsvFileObject;
use WPMailSMTP\Vendor\XLSXWriter;

/**
 * Export file.
 *
 * @since 2.8.0
 */
class File {

	/**
	 * Write data to export file.
	 *
	 * @since 2.8.0
	 *
	 * @param Request $request Export request.
	 *
	 * @return true|WP_Error
	 */
	public function write( $request ) {

		// Include libraries.
		require_once wp_mail_smtp()->plugin_path . '/vendor/autoload.php';

		$type = $request->get_data( 'type' );

		if ( $type === 'xlsx' ) {
			// Write to the .xlsx file.
			return $this->write_xlsx( $request );
		} elseif ( $type === 'eml' ) {
			// Writing to the .eml or .zip file.
			return $this->write_eml( $request );
		} else {
			// Writing to the .csv file.
			return $this->write_csv( $request );
		}
	}

	/**
	 * Write data to .csv file.
	 *
	 * @since 2.8.0
	 *
	 * @param Request $request Export request.
	 *
	 * @return true|WP_Error
	 */
	public function write_csv( $request ) {

		$export_file = $this->get_tmp_filename( $request->get_request_id() );

		if ( is_wp_error( $export_file ) ) {
			return $export_file;
		}

		$csv       = new CsvFileObject( $export_file, 'a' );
		$separator = Export::get_config( 'export', 'csv_export_separator' );
		$enclosure = '"';

		$data = new TableData( $request );

		if ( $request->get_arg( 'step' ) === 1 ) {
			$csv->fputcsv( $data->get_columns(), $separator, $enclosure );
		}

		foreach ( $data->get_row() as $row ) {
			$csv->fputcsv( $row, $separator, $enclosure );
		}

		$csv->fflush();

		return true;
	}

	/**
	 * Write data to .xlsx file.
	 *
	 * @since 2.8.0
	 *
	 * @param Request $request Export request.
	 *
	 * @return true|WP_Error
	 */
	public function write_xlsx( $request ) {

		$export_file = $this->get_tmp_filename( $request->get_request_id() );

		if ( is_wp_error( $export_file ) ) {
			return $export_file;
		}

		$writer     = new XLSXWriter();
		$sheet_name = 'WPMailSMTP';

		$data = new TableData( $request );

		if ( $request->get_arg( 'step' ) === 1 ) {
			$widths = array_map(
				function ( $header ) {
					return strlen( $header ) + 2;
				},
				$data->get_columns()
			);

			$writer->writeSheetHeader( $sheet_name, array_fill_keys( $data->get_columns(), 'string' ), [ 'widths' => $widths ] );
		}

		foreach ( $data->get_row() as $row ) {
			$writer->writeSheetRow( $sheet_name, $row, [ 'wrap_text' => true ] );
		}

		$writer->writeToFile( $export_file );

		return true;
	}

	/**
	 * Write data to .eml or .zip file.
	 *
	 * @since 2.9.0
	 *
	 * @param Request $request Export request.
	 *
	 * @return true|WP_Error
	 */
	public function write_eml( $request ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		$export_file = $this->get_tmp_filename( $request->get_request_id() );

		if ( is_wp_error( $export_file ) ) {
			return $export_file;
		}

		$data = new EMLData( $request );

		$db_args = $request->get_data( 'db_args' );

		if ( ! empty( $db_args['id'] ) ) {
			foreach ( $data->get_content() as $value ) {
				list( , $content ) = $value;
				file_put_contents( $export_file, $content ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
			}
		} else {
			$zip      = new \ZipArchive();
			$zip_open = $zip->open( $export_file, \ZipArchive::CREATE );

			if ( $zip_open !== true ) {
				return new WP_Error(
					'zip_creating',
					sprintf( /* translators: %s - Error code. */
						esc_html__( 'An error occurred when creating a zip file. Error code: %s.', 'wp-mail-smtp-pro' ),
						$zip_open
					)
				);
			}

			// Include polyfill if mbstring PHP extension is not enabled.
			if ( ! function_exists( 'mb_substr' ) ) {
				Helpers::include_mbstring_polyfill();
			}

			foreach ( $data->get_content() as $value ) {
				list( $email, $content ) = $value;

				$filename = $email->get_id() . '-' . sanitize_title( mb_substr( $email->get_subject(), 0, 30 ) ) . '.eml';
				$zip->addFromString( $filename, $content );
			}

			$zip->close();
		}

		return true;
	}

	/**
	 * Output the file.
	 *
	 * @since 2.8.0
	 *
	 * @param Request $request Export request.
	 *
	 * @throws \Exception In case of file error.
	 */
	public function output_file( $request ) {

		if ( empty( $request->get_request_id() ) ) {
			throw new \Exception( Export::get_config( 'errors', 'unknown_request' ) );
		}

		$export_file = $this->get_tmp_filename( $request->get_request_id() );

		if ( is_wp_error( $export_file ) ) {
			throw new \Exception( $export_file->get_error_message() );
		}

		clearstatcache( true, $export_file );

		if ( ! is_readable( $export_file ) || is_dir( $export_file ) ) {
			throw new \Exception( Export::get_config( 'errors', 'file_not_readable' ) );
		}

		if ( @filesize( $export_file ) === 0 ) { //phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			throw new \Exception( Export::get_config( 'errors', 'file_empty' ) );
		}

		$db_args = $request->get_data( 'db_args' );

		if ( ! empty( $db_args['id'] ) ) {
			$file_name = 'wp-mail-smtp-email-log-' . $db_args['id'];
		} else {
			$file_name = 'wp-mail-smtp-email-logs';
		}

		$file_ext = $request->get_data( 'type' );

		if ( $file_ext === 'eml' ) {
			$file_ext = 'zip';

			if ( ! empty( $db_args['id'] ) ) {
				$file_ext = 'eml';
			}
		}

		$file_name .= '-' . current_time( 'Y-m-d-H-i-s' ) . '.' . $file_ext;

		/**
		 * Filters export filename.
		 *
		 * @since 2.8.0
		 *
		 * @param string  $file_name Filename.
		 * @param Request $request   Export request.
		 */
		$file_name = apply_filters( 'wp_mail_smtp_pro_emails_logs_export_file_output_file', $file_name, $request );

		$this->http_headers( sanitize_file_name( $file_name ), $request->get_data( 'type' ) );

		readfile( $export_file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_readfile

		exit;
	}

	/**
	 * Temporary files directory.
	 *
	 * @since 2.8.0
	 *
	 * @return string|WP_Error
	 */
	public function get_tmpdir() {

		$upload_dir = Uploads::upload_dir();

		if ( is_wp_error( $upload_dir ) ) {
			return $upload_dir;
		}

		$upload_path = $upload_dir['path'];

		$export_path = trailingslashit( $upload_path ) . 'export';
		if ( ! file_exists( $export_path ) ) {
			wp_mkdir_p( $export_path );
		}

		// Check if the .htaccess exists in the upload directory, if not - create it.
		Uploads::create_upload_dir_htaccess_file();

		// Check if the index.html exists in the directories, if not - create it.
		Uploads::create_index_html_file( $upload_path );
		Uploads::create_index_html_file( $export_path );

		// Normalize slashes for Windows.
		$export_path = wp_normalize_path( $export_path );

		return $export_path;
	}

	/**
	 * Full pathname to the Temporary file.
	 *
	 * @since 2.8.0
	 *
	 * @param string $request_id Request id.
	 *
	 * @return string|WP_Error
	 */
	public function get_tmp_filename( $request_id ) {

		$export_dir = $this->get_tmpdir();

		if ( is_wp_error( $export_dir ) ) {
			return $export_dir;
		}

		$export_file = $export_dir . '/' . sanitize_key( $request_id );
		touch( $export_file );

		return $export_file;
	}

	/**
	 * Send HTTP headers for file download.
	 *
	 * @since 2.8.0
	 *
	 * @param string $file_name File name.
	 * @param string $type      File type.
	 */
	public function http_headers( $file_name, $type ) {

		nocache_headers();
		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: text/' . $type );
		header( 'Content-Disposition: attachment; filename=' . $file_name );
		header( 'Content-Transfer-Encoding: binary' );
	}
}
