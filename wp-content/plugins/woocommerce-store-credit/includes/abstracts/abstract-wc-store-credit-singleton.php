<?php
/**
 * Singleton Class.
 *
 * @package WC_Store_Credit/Abstracts
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * The class that implements the singleton pattern.
 *
 * @since 3.0.0
 */
abstract class WC_Store_Credit_Singleton {

	/**
	 * Gets the single instance of the class.
	 *
	 * @since 3.0.0
	 *
	 * @return mixed The class instance.
	 */
	final public static function instance() {
		static $instance = null;

		if ( null === $instance ) {
			$instance = new static();
		}

		return $instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 */
	protected function __construct() {}

	/**
	 * Prevents cloning.
	 *
	 * @since 3.0.0
	 */
	private function __clone() {
		wc_doing_it_wrong( __FUNCTION__, __( 'Cloning is forbidden.', 'woocommerce-store-credit' ), '3.0.0' );
	}

	/**
	 * Prevents unserializing.
	 *
	 * @since 3.0.0
	 */
	final public function __wakeup() {
		wc_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'woocommerce-store-credit' ), '3.0.0' );
	}
}
