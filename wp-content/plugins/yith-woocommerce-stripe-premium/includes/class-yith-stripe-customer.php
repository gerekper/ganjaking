<?php
/**
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Stripe
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCSTRIPE' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCStripe_Customer' ) ) {
	/**
	 * WooCommerce Stripe main class
	 *
	 * @since 1.0.0
	 */
	class YITH_WCStripe_Customer {
		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCStripe_Customer
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCStripe_Customer
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @return string
		 * @since 1.0.0
		 */
		public function get_env() {
			if ( empty( $this->env ) ) {
				// Load form_field settings
				$settings  = get_option( 'woocommerce_' . YITH_WCStripe::$gateway_id . '_settings', null );
				$this->env = isset( $settings['enabled_test_mode'] ) && $settings['enabled_test_mode'] == 'yes' ? 'test' : 'live';
			}

			return $this->env;
		}

		/**
		 * Get customer info for a user into DB
		 *
		 * @since 1.0.0
		 */
		public function get_usermeta_info( $user_id ) {
			return get_user_meta( $user_id, $this->get_customer_usermeta_key(), true );
		}

		/**
		 * Update customer info for a user into DB
		 *
		 * @since 1.0.0
		 */
		public function update_usermeta_info( $user_id, $params = array() ) {
			return update_user_meta( $user_id, $this->get_customer_usermeta_key(), $params );
		}

		/**
		 * Delete customer info for a user into DB
		 *
		 * @since 1.0.0
		 */
		public function delete_usermeta_info( $user_id ) {
			return delete_user_meta( $user_id, $this->get_customer_usermeta_key() );
		}

		/**
		 * Update customer info for a user into DB
		 *
		 * @since 1.0.0
		 */
		public function want_save_cards( $user_id ) {
			$info = $this->get_usermeta_info( $user_id );

			return (bool) ( 'yes' == $info['save_cards'] ? true : false );
		}

		/**
		 * Return the name of user meta for the customer info
		 *
		 * @return string
		 * @since 1.0.0
		 */
		protected function get_customer_usermeta_key() {
			return '_' . $this->get_env() . '_stripe_customer_id';
		}
	}
}

/**
 * Unique access to instance of YITH_WCStripe_Customer class
 *
 * @return \YITH_WCStripe_Customer
 * @since 1.0.0
 */
function YITH_WCStripe_Customer() {
	return YITH_WCStripe_Customer::get_instance();
}