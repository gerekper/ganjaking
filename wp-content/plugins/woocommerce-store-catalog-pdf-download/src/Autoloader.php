<?php
/**
 * Includes the composer Autoloader used for packages and classes in the src/ directory.
 *
 * @since 2.0.0
 */

namespace Themesquad\WC_Store_Catalog_PDF_Download;

defined( 'ABSPATH' ) || exit;

/**
 * Autoloader class.
 */
class Autoloader {

	/**
	 * Initializes the autoloader.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public static function init() {
		$autoloader = dirname( __DIR__ ) . '/vendor/autoload.php';

		return ( is_readable( $autoloader ) && ( require $autoloader ) );
	}
}
