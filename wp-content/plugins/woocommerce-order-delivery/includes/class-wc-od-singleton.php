<?php
/**
 * Singleton Class
 *
 * @package WC_OD
 * @since   1.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Singleton' ) ) {
	/**
	 * The class that implements the singleton pattern.
	 *
	 * @since 1.1.0
	 */
	abstract class WC_OD_Singleton {

		/**
		 * Gets the *Singleton* instance of this class.
		 *
		 * @since 1.1.0
		 *
		 * @staticvar WC_OD_Singleton $instance The *Singleton* instances of this class.
		 * @return WC_OD_Singleton The *Singleton* instance.
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
		 * @since 1.1.0
		 */
		protected function __construct() {}

		/**
		 * Throw error on object clone.
		 *
		 * @since 1.1.0
		 */
		private function __clone() {
			// Cloning instances of the class is forbidden.
			wc_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce-order-delivery' ), '1.0.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @since 1.1.0
		 */
		private function __wakeup() {
			// Unserializing instances of the class is forbidden.
			wc_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce-order-delivery' ), '1.0.0' );
		}

	}
}
