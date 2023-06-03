<?php

namespace Smush\Core\Png2Jpg;

use Exception;
use Imagick;
use Smush\Core\File_System;
use Smush\Core\Helper;

class Png2Jpg_Helper {
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
	 *
	 * @return bool
	 */
	public function is_transparent( $file_path ) {
		if ( $this->supports_imagick() ) {
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

	private function file_contents_have_transparency( $file_path ) {
		// Simple check.
		// Src: http://camendesign.com/code/uth1_is-png-32bit.
		if ( ord( $this->fs->file_get_contents( $file_path, false, null, 25, 1 ) ) & 4 ) {
			$this->logger->info( sprintf( 'File [%s] is a PNG 32-bit.', $file_path ) );

			return true;
		}

		$contents = $this->fs->file_get_contents( $file_path );
		if ( stripos( $contents, 'PLTE' ) !== false && stripos( $contents, 'tRNS' ) !== false ) {
			$this->logger->info( sprintf( 'File [%s] is an PNG 8-bit.', $file_path ) );

			return true;
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