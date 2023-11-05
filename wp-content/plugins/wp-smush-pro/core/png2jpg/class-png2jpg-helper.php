<?php

namespace Smush\Core\Png2Jpg;

use Exception;
use Imagick;
use Smush\Core\File_System;
use Smush\Core\Helper;

class Png2Jpg_Helper {
	const LARGE_PNG_SIZE = 3840;//4k.
	private $logger;
	/**
	 * @var File_System
	 */
	private $fs;

	public function __construct() {
		$this->logger = Helper::logger()->png2jpg();
		$this->fs     = new File_System();
	}

	/**
	 * @param $file_path string
	 * @param $width int
	 * @param $height int
	 *
	 * @return bool
	 */
	public function is_transparent( $file_path, $width, $height ) {
		if ( $this->supports_imagick() && $this->use_editor_for_transparency_check( $width, $height ) ) {
			try {
				return ( new Imagick( $file_path ) )->getImageAlphaChannel();
			} catch ( Exception $exception ) {
				$this->logger->error( 'Imagick: Error in checking PNG transparency ' . $exception->getMessage() );

				return false;
			}
		}

		// TODO: we removed GD transparency code because it didn't work, we should add an alternative that works

		return $this->file_contents_have_transparency( $file_path );
	}

	private function use_editor_for_transparency_check( $width, $height ) {
		return $width <= self::LARGE_PNG_SIZE && $height <= self::LARGE_PNG_SIZE;
	}

	private function file_contents_have_transparency( $file_path ) {
		// Simple check.
		// Src: http://camendesign.com/code/uth1_is-png-32bit.
		if ( ord( $this->fs->file_get_contents( $file_path, false, null, 25, 1 ) ) & 4 ) {
			$this->logger->info( sprintf( 'File [%s] is a PNG 32-bit.', $file_path ) );

			return true;
		}

		// Check for a transparent pixel line by line
		// Src: https://stackoverflow.com/a/43996262
		$handle = @fopen( $file_path, 'r' );
		if ( ! $handle ) {
			return false;
		}

		$contents     = '';
		$contain_plte = false;
		$contain_trns = false;
		while ( ! feof( $handle ) ) {
			$new_line = fread( $handle, 8192 );
			// Added previous line to avoid split a string while chunking.
			$contents .= $new_line;

			$contain_plte = $contain_plte || stripos( $contents, 'PLTE' ) !== false;
			$contain_trns = $contain_trns || stripos( $contents, 'tRNS' ) !== false;

			if ( $contain_plte && $contain_trns ) {
				$this->logger->info( sprintf( 'File [%s] is an PNG 8-bit.', $file_path ) );

				return true;
			}

			// Reset the content to save memory.
			$contents = $new_line;
		}

		return false;
	}

	/**
	 * Check if Imagick is available or not
	 *
	 * @return bool True/False Whether Imagick is available or not
	 */
	public function supports_imagick() {
		if ( ! class_exists( '\Imagick' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if GD is loaded
	 *
	 * @return bool True/False Whether GD is available or not
	 */
	public function supports_gd() {
		if ( ! function_exists( 'gd_info' ) ) {
			return false;
		}

		return true;
	}
}