<?php
/**
 * Singleton pattern.
 *
 * @since 1.2.0
 */

namespace KoiLab\WC_Products_Compare\Internal\Traits;

defined( 'ABSPATH' ) || exit;

/**
 * Trait Singleton.
 */
trait Singleton {

	/**
	 * The single instance of the class.
	 *
	 * @var mixed
	 */
	protected static $instance = null;

	/**
	 * Constructor.
	 *
	 * @since 1.2.0
	 */
	protected function __construct() {}

	/**
	 * Gets the single instance of the class.
	 *
	 * @since 1.2.0
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
	 * @since 1.2.0
	 */
	private function __clone() {
		wc_doing_it_wrong( __FUNCTION__, 'Cloning is forbidden.', '1.2.0' );
	}

	/**
	 * Prevents unserializing.
	 *
	 * @since 1.2.0
	 */
	final public function __wakeup() {
		wc_doing_it_wrong( __FUNCTION__, 'Unserializing instances of this class is forbidden.', '1.2.0' );
	}
}
