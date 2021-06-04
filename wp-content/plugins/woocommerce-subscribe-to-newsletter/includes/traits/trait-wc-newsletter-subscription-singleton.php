<?php
/**
 * Singleton.
 *
 * @package WC_Newsletter_Subscription/Traits
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Trait WC_Newsletter_Subscription_Singleton.
 */
trait WC_Newsletter_Subscription_Singleton {

	/**
	 * The single instance of the class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 */
	protected function __construct() {}

	/**
	 * Gets the single instance of the class.
	 *
	 * @since 3.0.0
	 *
	 * @return object The class instance.
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
