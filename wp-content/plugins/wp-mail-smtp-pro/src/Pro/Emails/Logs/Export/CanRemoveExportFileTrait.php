<?php

namespace WPMailSMTP\Pro\Emails\Logs\Export;

/**
 * Remove export file trait.
 *
 * @since 2.8.0
 */
trait CanRemoveExportFileTrait {

	/**
	 * Remove export file.
	 *
	 * @since 2.8.0
	 *
	 * @param string $request_id Request id.
	 */
	protected function remove_export_file( $request_id ) {

		// Just to be safe.
		$request_id = sanitize_key( $request_id );

		$file        = new File();
		$export_file = $file->get_tmp_filename( $request_id );

		if ( is_wp_error( $export_file ) ) {
			return;
		}

		if ( is_file( $export_file ) ) {
			unlink( $export_file );
		}
	}
}
