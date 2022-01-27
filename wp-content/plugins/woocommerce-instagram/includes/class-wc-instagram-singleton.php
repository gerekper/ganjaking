<?php
/**
 * Singleton Class
 *
 * @package WC_Instagram
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Instagram_Singleton' ) ) {
	/**
	 * The class that implements the singleton pattern.
	 *
	 * @since 2.0.0
	 * @deprecated 4.0.0 Use the WC_Instagram_Singleton_Trait instead.
	 */
	abstract class WC_Instagram_Singleton {

		/**
		 * Gets the single instance of the class.
		 *
		 * @since 2.0.0
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
		 * @since 2.0.0
		 */
		protected function __construct() {
			wc_deprecated_function( 'WC_Instagram_Singleton::__construct', '4.0.0', 'WC_Instagram_Singleton_Trait' );
		}

		/**
		 * Prevents cloning.
		 *
		 * @since 2.0.0
		 */
		private function __clone() {
			wc_doing_it_wrong( __FUNCTION__, 'Cloning is forbidden.', '2.0.0' );
		}

		/**
		 * Prevents unserializing.
		 *
		 * @since 2.0.0
		 */
		final public function __wakeup() {
			wc_doing_it_wrong( __FUNCTION__, 'Unserializing instances of this class is forbidden.', '2.0.0' );
		}
	}
}
