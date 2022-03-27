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
 * @deprecated 4.0.0 Use the WC_Store_Credit_Singleton_Trait instead.
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
	protected function __construct() {
		wc_deprecated_function( 'WC_Store_Credit_Singleton::__construct', '4.0.0', 'WC_Store_Credit_Singleton_Trait' );
	}

	/**
	 * Prevents cloning.
	 *
	 * @since 3.0.0
	 */
	private function __clone() {
		wc_doing_it_wrong( __FUNCTION__, 'Cloning is forbidden.', '3.0.0' );
	}

	/**
	 * Prevents unserializing.
	 *
	 * @since 3.0.0
	 */
	final public function __wakeup() {
		wc_doing_it_wrong( __FUNCTION__, 'Unserializing instances of this class is forbidden.', '3.0.0' );
	}
}
