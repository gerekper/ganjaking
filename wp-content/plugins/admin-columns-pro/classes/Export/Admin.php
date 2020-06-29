<?php

namespace ACP\Export;

use AC;
use AC\Registrable;
use ACP\Export\HideOnScreen;
use ACP\Settings\ListScreen\HideOnScreenCollection;
use Exception;

/**
 * Handles general functionality for admin screens
 * @since 1.0
 */
class Admin implements Registrable {

	/**
	 * @var ExportDirectory
	 */
	private $export_dir;

	public function __construct( ExportDirectory $export_dir ) {
		$this->export_dir = $export_dir;
	}

	public function register() {
		add_action( 'admin_init', [ $this, 'maybe_download_export' ] );
		add_action( 'acp/admin/settings/hide_on_screen', [ $this, 'add_hide_on_screen' ], 10, 2 );
	}

	public function add_hide_on_screen( HideOnScreenCollection $collection, AC\ListScreen $list_screen ) {
		if ( $list_screen instanceof ListScreen ) {
			$collection->add( new HideOnScreen\Export(), 50 );
		}
	}

	/**
	 * Check whether a request was sent for downloading an export file, and if so, offer it for
	 * download (if the user has permission to download it)
	 * @since 1.0
	 */
	public function maybe_download_export() {
		$export_download = filter_input( INPUT_GET, 'acp-export-download' );

		if ( ! $export_download ) {
			return;
		}

		// Base directory for the export file
		$fname = md5( get_current_user_id() . $export_download ) . '.csv';

		// Final file CSV contents
		$content = '';

		// Counter for merging the decrypted value of multiple files
		$counter = 0;

		while ( true ) {
			// Construct full file path
			$fpath = $this->export_dir->get_path() . $fname;
			$fpath .= '-' . $counter . '.csv';

			// Check whether the file to load exists
			if ( ! file_exists( $fpath ) ) {
				if ( $counter === 0 ) {
					wp_die( __( 'The requested file does not exist.', 'codepress-admin-columns' ) );
				}

				break;
			}

			// Get contents of file
			$file_content = file_get_contents( $fpath );

			// Decrypt the file contents
			try {
				$cryptor = new Cryptor();
				$key = Utility\Users::get_user_encryption_key();
				$result = $cryptor->decrypt( $file_content, $key );
				$content .= $result;
			} catch ( Exception $e ) {
				wp_die( __( 'The requested file could not be processed.', 'codepress-admin-columns' ) );
			}

			$counter++;
		}

		$file = new DownloadableFile();
		$file->load_content_string( $content );

		$prefix = filter_input( INPUT_GET, 'acp-export-filename-prefix', FILTER_SANITIZE_STRING );

		if ( $prefix ) {
			$prefix .= '-';
		}

		$file->export( $prefix . 'export-' . current_time( 'Y-m-d' ) . '.csv' );
	}

}