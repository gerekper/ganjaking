<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Autoloader {

	private $path;

	/**
	 * The Constructor, sets the path of the class directory.
	 *
	 * @param $path
	 */
	public function __construct( $path ) {
		$this->path = $path;
	}


	/**
	 * Autoloader load method. Load the class.
	 *
	 * @param $class_name
	 */
	public function load( $class_name ) {

		// Only autoload WooCommerce Sales Report Email classes
		if ( 0 === strpos( $class_name, 'WC_XR_' ) ) {

			// String to lower
			$class_name = strtolower( $class_name );

			// Format file name
			$file_name = 'class-wc-xr-' . str_ireplace( '_', '-', str_ireplace( 'WC_XR_', '', $class_name ) ) . '.php';

			// Setup the file path
			$file_path = $this->path;

			// Check if we need to extend the class path
			if ( strpos( $class_name, 'wc_xr_request' ) === 0 ) {
				$file_path .= 'requests/';
			}

			// Append file name to clas path
			$file_path .= $file_name;

			// Check & load file
			if ( file_exists( $file_path ) ) {
				require_once( $file_path );
			}

		}

	}

}