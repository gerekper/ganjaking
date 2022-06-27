<?php
/**
 * Plugin Class Autoloader
 *
 * @package WC_Store_Credit
 * @since   2.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Autoloader class.
 *
 * @since 2.4.0
 */
class WC_Store_Credit_Autoloader {

	/**
	 * Path to the includes directory.
	 *
	 * @since 4.0.0
	 *
	 * @var string
	 */
	private $include_path = '';

	/**
	 * Constructor.
	 *
	 * @since 2.4.0
	 */
	public function __construct() {
		if ( function_exists( '__autoload' ) ) {
			spl_autoload_register( '__autoload' );
		}

		spl_autoload_register( array( $this, 'autoload' ) );

		$this->include_path = WC_STORE_CREDIT_PATH . 'includes/';
	}

	/**
	 * Autoload classes on demand to reduce memory consumption.
	 *
	 * @since 2.4.0
	 *
	 * @param string $class The class to load.
	 */
	public function autoload( $class ) {
		$class = strtolower( $class );

		if ( 0 !== strpos( $class, 'wc_store_credit' ) ) {
			return;
		}

		$file = $this->get_file_name_from_class( $class );

		/**
		 * Filters autoload classes.
		 *
		 * @since 2.4.0
		 *
		 * @param array $autoload An array with pairs ( pattern => $path ).
		 */
		$autoload = apply_filters(
			'wc_store_credit_autoload',
			array(
				'wc_store_credit_integration_'    => $this->include_path . 'integrations/',
				'wc_store_credit_coupon_discount' => $this->include_path . 'coupon-discounts/',
				'wc_store_credit_item_discount'   => $this->include_path . 'item-discounts/',
				'wc_store_credit_discounts'       => $this->include_path . 'discounts/',
				'wc_store_credit_meta_box'        => $this->include_path . 'admin/meta-boxes/',
				'wc_store_credit_admin_'          => $this->include_path . 'admin/',
				'wc_store_credit_'                => $this->include_path,
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
	 * @since 2.4.0
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
	 * @since 2.4.0
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

new WC_Store_Credit_Autoloader();
