<?php
/**
 * Init features of the extended version
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Classes
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Extended' ) ) {
	/**
	 * WooCommerce Wishlist Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCWL_Extended extends YITH_WCWL {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCWL_Extended
		 * @since 2.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCWL_Extended
		 * @since 2.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct();

			YITH_WCWL_Emails();

			$this->init_plugin_emails_array();
		}

		/**
		 * Init an array of plugin emails
		 *
		 * @since  1.5.0
		 * @author Francesco Licandro
		 */
		public function init_plugin_emails_array() {
			$this->emails = array(
				'yith_wcwl_back_in_stock',
			);
		}

		/**
		 * Return url to unsubscribe from wishlist mailing lists
		 *
		 * @param int $user_id User id.
		 * @return string Unsubscribe url
		 * @see \YITH_WCWL_Form_Handler_Premium::unsubscribe
		 */
		public function get_unsubscribe_link( $user_id ) {
			// retrieve unique unsubscribe token.
			$unsubscribe_token            = get_user_meta( $user_id, 'yith_wcwl_unsubscribe_token', true );
			$unsubscribe_token_expiration = get_user_meta( $user_id, 'yith_wcwl_unsubscribe_token_expiration', true );

			// if user has no token, or previous token has expired, generate new unsubscribe token.
			if ( ! $unsubscribe_token || $unsubscribe_token_expiration < time() ) {
				$unsubscribe_token = wp_generate_password( 24, false, false );

				/**
				 * APPLY_FILTERS: yith_wcwl_unsubscribe_token_expiration
				 *
				 * Filter the expiration for the unsubscribe token.
				 *
				 * @param int    $token_expiration  Token expiration
				 * @param string $unsubscribe_token Unsubscribe token
				 *
				 * @return int
				 */
				$unsubscribe_token_expiration = apply_filters( 'yith_wcwl_unsubscribe_token_expiration', time() + 30 * DAY_IN_SECONDS, $unsubscribe_token );

				update_user_meta( $user_id, 'yith_wcwl_unsubscribe_token', $unsubscribe_token );
				update_user_meta( $user_id, 'yith_wcwl_unsubscribe_token_expiration', $unsubscribe_token_expiration );
			}

			/**
			 * APPLY_FILTERS: yith_wcwl_unsubscribe_url
			 *
			 * Filter the URL to unsubscribe for the plugin emails.
			 *
			 * @param string $url                          Unsubscribe URL
			 * @param int    $user_id                      User ID
			 * @param string $unsubscribe_token            Unsubscribe token
			 * @param int    $unsubscribe_token_expiration Unsubscribe token expiration
			 *
			 * @return string
			 */
			return apply_filters( 'yith_wcwl_unsubscribe_url', add_query_arg( 'yith_wcwl_unsubscribe', $unsubscribe_token, get_home_url() ), $user_id, $unsubscribe_token, $unsubscribe_token_expiration );
		}
	}
}

/**
 * Unique access to instance of YITH_WCWL_Premium class
 *
 * @return \YITH_WCWL_Extended
 * @since 2.0.0
 */
function YITH_WCWL_Extended() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid, Universal.Files.SeparateFunctionsFromOO
	return YITH_WCWL_Extended::get_instance();
}
