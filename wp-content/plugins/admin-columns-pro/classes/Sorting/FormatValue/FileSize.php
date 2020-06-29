<?php

namespace ACP\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class FileSize implements FormatValue {

	public function format_value( $file ) {
		if ( $this->is_relative_path( $file ) ) {
			$uploads = wp_get_upload_dir();

			if ( false === $uploads['error'] ) {
				$file = sprintf( "%s/%s", $uploads['basedir'], $file );
			}
		}

		return $file && is_file( $file )
			? filesize( $file )
			: null;
	}

	/**
	 * @param string $file
	 *
	 * @return bool
	 */
	private function is_relative_path( $file ) {
		return $file && 0 !== strpos( $file, '/' ) && ! preg_match( '|^.:\\\|', $file );
	}

}
