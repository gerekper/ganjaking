<?php

namespace ACP\Export;

class ExportDirectory {

	/**
	 * Get the path and URL to the directory used for uploading
	 * @return array Two-dimensional associative array with keys "path" and "url", containing the
	 *   full path and the full URL to the export files directory, respectively
	 * @since 1.0
	 */
	public function get_dir() {
		// Base directory for uploads
		$upload_dir = wp_upload_dir();

		// Paths for exported files
		$suffix = 'admin-columns/export/';
		$export_path = trailingslashit( $upload_dir['basedir'] ) . $suffix;
		$export_url = trailingslashit( $upload_dir['baseurl'] ) . $suffix;
		$export_path_exists = true;

		// Maybe create export directory
		if ( ! is_dir( $export_path ) ) {
			$export_path_exists = wp_mkdir_p( $export_path );
		}

		return [
			'path'  => $export_path,
			'url'   => $export_url,
			'error' => $export_path_exists ? '' : __( 'Creation of Admin Columns export directory failed. Please make sure that your uploads folder is writable.', 'codepress-admin-columns' ),
		];
	}

	/**
	 * @return string
	 */
	public function get_path() {
		$dir = $this->get_dir();

		return $dir['path'];
	}

}