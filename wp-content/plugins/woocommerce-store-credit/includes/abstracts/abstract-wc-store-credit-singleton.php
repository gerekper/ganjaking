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
	 * Gets the *Singleton* instance of this class.
	 *
	 * @since 3.0.0
	 *
	 * @staticvar WC_Store_Credit_Singleton $instance The *Singleton* instances of this class.
	 * @return mixed The *Singleton* instance.
	 */
	public static function instance() {
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
	 * Cloning instances of the class is forbidden.
	 *
	 * @since 3.0.0
	 */
	private function __clone() {
		wc_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning is forbidden.', 'woocommerce-store-credit' ), '3.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 3.0.0
	 */
	private function __wakeup() {
		wc_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of this class is forbidden.', 'woocommerce-store-credit' ), '3.0.0' );
	}
}
