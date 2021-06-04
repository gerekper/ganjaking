<?php
/**
 * Plugin Class Autoloader
 *
 * @package WC_Newsletter_Subscription
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Autoloader class.
 *
 * @since 3.0.0
 */
class WC_Newsletter_Subscription_Autoloader {

	/**
	 * Path to the includes directory.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	private $include_path = '';

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		if ( function_exists( '__autoload' ) ) {
			spl_autoload_register( '__autoload' );
		}

		spl_autoload_register( array( $this, 'autoload' ) );

		$this->include_path = WC_NEWSLETTER_SUBSCRIPTION_PATH . 'includes/';
	}

	/**
	 * Auto-load classes on demand to reduce memory consumption.
	 *
	 * @since 3.0.0
	 *
	 * @param string $class The class to load.
	 */
	public function autoload( $class ) {
		$class = strtolower( $class );

		if ( 0 !== strpos( $class, 'wc_newsletter_subscription_' ) ) {
			return;
		}

		$file = $this->get_file_name_from_class( $class );

		/**
		 * Filters the autoload classes.
		 *
		 * @since 3.0.0
		 *
		 * @param array $autoload An array with pairs ( pattern => $path ).
		 */
		$autoload = apply_filters(
			'wc_newsletter_subscription_autoload',
			array(
				'wc_newsletter_subscription_provider_' => $this->include_path . 'providers/',
				'wc_newsletter_subscription_admin_'    => $this->include_path . 'admin/',
				'wc_newsletter_subscription_'          => $this->include_path,
			)
		);

		foreach ( $autoload as $prefix => $path ) {
			if ( 0 === strpos( $class, $prefix ) && $this->load_file( $path . $file ) ) {
				break;
			}
		}
	}

	/**
	 * Take a class name and turn it into a file name.
	 *
	 * @since 3.0.0
	 *
	 * @param  string $class The class name.
	 * @return string The file name.
	 */
	private function get_file_name_from_class( $class ) {
		return 'class-' . str_replace( '_', '-', $class ) . '.php';
	}

	/**
	 * Include a class file.
	 *
	 * @since 3.0.0
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

new WC_Newsletter_Subscription_Autoloader();
