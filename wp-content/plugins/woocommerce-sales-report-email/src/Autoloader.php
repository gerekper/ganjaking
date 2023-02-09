<?php
/**
 * Includes the composer Autoloader used for packages and classes in the src/ directory.
 *
 * @since 1.2.0
 */

namespace Themesquad\WC_Sales_Report_Email;

defined( 'ABSPATH' ) || exit;

/**
 * Autoloader class.
 */
class Autoloader {

	/**
	 * Initializes the autoloader.
	 *
	 * @since 1.2.0
	 *
	 * @return bool
	 */
	public static function init() {
		$autoloader = dirname( __DIR__ ) . '/vendor/autoload.php';

		return ( is_readable( $autoloader ) && ( require $autoloader ) );
	}
}
