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
		 * Gets the *Singleton* instance of this class.
		 *
		 * @since 2.0.0
		 *
		 * @staticvar WC_Instagram_Singleton $instance The *Singleton* instances of this class.
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
		 * @since 2.0.0
		 */
		protected function __construct() {}

		/**
		 * Cloning instances of the class is forbidden.
		 *
		 * @since 2.0.0
		 */
		private function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning is forbidden.', 'woocommerce-instagram' ), '2.0.0' );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @since 2.0.0
		 */
		private function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of this class is forbidden.', 'woocommerce-instagram' ), '2.0.0' );
		}

	}
}
