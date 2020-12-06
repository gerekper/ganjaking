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
		protected function __construct() {}

		/**
		 * Prevents cloning.
		 *
		 * @since 2.0.0
		 */
		private function __clone() {
			wc_doing_it_wrong( __FUNCTION__, __( 'Cloning is forbidden.', 'woocommerce-instagram' ), '2.0.0' );
		}

		/**
		 * Prevents unserializing.
		 *
		 * @since 2.0.0
		 */
		final public function __wakeup() {
			wc_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'woocommerce-instagram' ), '2.0.0' );
		}
	}
}
