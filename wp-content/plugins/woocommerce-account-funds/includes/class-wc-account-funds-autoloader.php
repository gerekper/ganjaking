<?php
/**
 * Plugin Class Autoloader
 *
 * @package WC_Account_Funds
 * @since   2.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Autoloader class.
 */
class WC_Account_Funds_Autoloader {

	/**
	 * Path to the includes directory.
	 *
	 * @since 2.5.0
	 *
	 * @var string
	 */
	private $include_path = '';

	/**
	 * Constructor.
	 *
	 * @since 2.5.0
	 */
	public function __construct() {
		if ( function_exists( '__autoload' ) ) {
			spl_autoload_register( '__autoload' );
		}

		spl_autoload_register( array( $this, 'autoload' ) );

		$this->include_path = WC_ACCOUNT_FUNDS_PATH . 'includes/';
	}

	/**
	 * Autoload classes on demand to reduce memory consumption.
	 *
	 * @since 2.5.0
	 * @since 3.0.0 Renamed parameter `$class` to `$classname`.
	 *
	 * @param string $classname The class to load.
	 */
	public function autoload( $classname ) {
		$classname = strtolower( $classname );

		if ( 0 !== strpos( $classname, 'wc_account_funds_' ) ) {
			return;
		}

		$file = $this->get_file_name_from_class( $classname );

		/**
		 * Filters the autoload classes.
		 *
		 * @since 2.5.0
		 *
		 * @param array $autoload An array with pairs ( pattern => $path ).
		 */
		$autoload = apply_filters(
			'wc_account_funds_autoload',
			array(
				'wc_account_funds_integration_' => $this->include_path . 'integrations/',
				'wc_account_funds_admin_'       => $this->include_path . 'admin/',
				'wc_account_funds_'             => $this->include_path,
			)
		);

		foreach ( $autoload as $prefix => $path ) {
			if ( 0 === strpos( $classname, $prefix ) && $this->load_file( $path . $file ) ) {
				break;
			}
		}
	}

	/**
	 * Take a class name and turn it into a file name.
	 *
	 * @since 2.5.0
	 * @since 3.0.0 Renamed parameter `$class` to `$classname`.
	 *
	 * @param  string $classname The class name.
	 * @return string The file name.
	 */
	private function get_file_name_from_class( $classname ) {
		return 'class-' . str_replace( '_', '-', $classname ) . '.php';
	}

	/**
	 * Include a class file.
	 *
	 * @since 2.5.0
	 *
	 * @param string $path The file path.
	 * @return bool successful or not
	 */
	private function load_file( $path ) {
		if ( $path && is_readable( $path ) ) {
			include_once $path;

			return true;
		}

		return false;
	}
}

new WC_Account_Funds_Autoloader();
