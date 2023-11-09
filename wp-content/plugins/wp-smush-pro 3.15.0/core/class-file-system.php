<?php

namespace Smush\Core;

class File_System {
	public function file_get_contents( $path, $use_include_path = false, $context = null, $offset = 0, $length = null ) {
		if ( ! $this->is_valid_path( $path ) ) {
			return false;
		}

		$args = array( $path, $use_include_path, $context, $offset );
		if ( ! is_null( $length ) ) {
			// Even though the default value of $length is 'null', an empty string is returned when 'null' is passed as $length. So, we only include it when a non-null value is provided.
			$args[] = $length;
		}

		return call_user_func_array( 'file_get_contents', $args );
	}

	public function file_exists( $path ) {
		return $this->validate_and_call( 'file_exists', $path );
	}

	public function unlink( $path ) {
		return $this->validate_and_call( 'unlink', $path );
	}

	public function copy( $source, $destination ) {
		return $this->validate_and_call( 'copy', $source, $destination );
	}

	public function is_file( $file ) {
		return $this->validate_and_call( 'is_file', $file );
	}

	public function is_dir( $path ) {
		return $this->validate_and_call( 'is_dir', $path );
	}

	public function filesize( $file ) {
		return $this->validate_and_call( 'filesize', $file );
	}

	public function getimagesize( $path ) {
		return $this->validate_and_call( 'getimagesize', $path );
	}

	private function validate_and_call( $callback, ...$args ) {
		foreach ( $args as $arg ) {
			if ( ! $this->is_valid_path( $arg ) ) {
				return false;
			}
		}

		return call_user_func_array( $callback, $args );
	}

	private function is_valid_path( $path ) {
		return false === stripos( $path, 'phar://' );
	}
}