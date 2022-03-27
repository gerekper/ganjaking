<?php
/**
 * Singleton.
 *
 * @package WC_Store_Credit/Traits
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Trait WC_Store_Credit_Singleton_Trait.
 */
trait WC_Store_Credit_Singleton_Trait {

	/**
	 * The single instance of the class.
	 *
	 * @var mixed
	 */
	protected static $instance = null;

	/**
	 * Constructor.
	 *
	 * @since 4.0.0
	 */
	protected function __construct() {}

	/**
	 * Gets the single instance of the class.
	 *
	 * @since 4.0.0
	 *
	 * @return mixed The class instance.
	 */
	final public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Prevents cloning.
	 *
	 * @since 4.0.0
	 */
	private function __clone() {
		wc_doing_it_wrong( __FUNCTION__, 'Cloning is forbidden.', '4.0.0' );
	}

	/**
	 * Prevents unserializing.
	 *
	 * @since 4.0.0
	 */
	final public function __wakeup() {
		wc_doing_it_wrong( __FUNCTION__, 'Unserializing instances of this class is forbidden.', '4.0.0' );
	}
}
