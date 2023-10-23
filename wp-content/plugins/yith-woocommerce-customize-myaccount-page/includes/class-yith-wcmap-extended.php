<?php
/**
 * Main class. Extended version.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCMAP_Extended', false ) ) {
	/**
	 * YITH WooCommerce Customize My Account Page
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMAP_Extended extends YITH_WCMAP {

		/**
		 * Constructor
		 *
		 * @since  1.0.0
		 */
		protected function __construct() {
			parent::__construct();
			// Load compatibilities.
			add_action( 'init', array( $this, 'load_compatibilities' ), 10 );
			// Email register.
			add_filter( 'woocommerce_email_classes', array( $this, 'add_woocommerce_emails' ) );

			// Locate core templates.
			add_filter( 'woocommerce_locate_core_template', array( $this, 'locate_core_template' ), 10, 3 );

			// Filter default user avatar.
			add_filter( 'get_avatar_url', array( $this, 'default_avatar_url' ), 10, 3 );
		}

		/**
		 * Get admin class
		 *
		 * @since 3.12.0
		 * @return string
		 */
		protected function get_admin_class() {
			return 'YITH_WCMAP_Admin_Extended';
		}

		/**
		 * Get frontend class
		 *
		 * @since 3.12.0
		 * @return string
		 */
		protected function get_frontend_class() {
			return 'YITH_WCMAP_Frontend_Extended';
		}

		/**
		 * Filters woocommerce available mails, to add plugin related ones
		 *
		 * @since  2.5.0
		 * @param array $emails An array of registered emails.
		 * @return array
		 */
		public function add_woocommerce_emails( $emails ) {
			$emails['YITH_WCMAP_Verify_Account'] = include YITH_WCMAP_DIR . 'includes/email/class-yith-wcmap-verify-account.php';
			return $emails;
		}

		/**
		 * Locate core template file
		 *
		 * @param string $core_file     Template full path.
		 * @param string $template      Template in use.
		 * @param string $template_base Template base path.
		 *
		 * @return string
		 * @since 3.20.0
		 */
		public function locate_core_template( $core_file, $template, $template_base ) {
			$custom_template = array(
				'email/customer-verify-account.php',
				'email/plain/customer-verify-account.php',
			);

			if ( in_array( $template, $custom_template, true ) ) {
				$core_file = YITH_WCMAP_TEMPLATE_PATH . $template;
			}

			return $core_file;
		}

		/**
		 * Filter default avatar url to get the custom one
		 *
		 * @since  3.0.0
		 * @param string $url         The URL of the avatar.
		 * @param mixed  $id_or_email The Gravatar to retrieve. Accepts a user ID, Gravatar MD5 hash,
		 *                            user email, WP_User object, WP_Post object, or WP_Comment object.
		 * @param array  $args        Arguments passed to get_avatar_data(), after processing.
		 * @return array
		 */
		public function default_avatar_url( $url, $id_or_email, $args ) {
			$opts = get_option( 'yith_wcmap_avatar', array() );
			if ( ! empty( $opts['default'] ) && ! empty( $opts['custom_default'] ) && 'custom' === $opts['default'] ) {
				$url = $opts['custom_default'];
			}

			return $url;
		}

		/**
		 * Return an array of compatible plugins
		 *
		 * @since  3.0.0
		 * @return array
		 */
		protected function get_compatible_plugins() {
			$plugins = array(
				'wishlist'   => defined( 'YITH_WCWL' ) && YITH_WCWL,
				'gift-cards' => defined( 'YITH_YWGC_EXTENDED' ) || defined( 'YITH_YWGC_PREMIUM' ),
			);

			/**
			 * APPLY_FILTERS: yith_wcmap_get_plugins_endpoints_array
			 *
			 * Filters the compatible plugins.
			 *
			 * @param array $plugins Compatible plugins.
			 *
			 * @return array
			 */
			return apply_filters( 'yith_wcmap_get_plugins_endpoints_array', $plugins );
		}

		/**
		 * Load compatibility classes
		 *
		 * @access public
		 * @since  2.3
		 */
		public function load_compatibilities() {

			$plugins = array_filter( $this->get_compatible_plugins() );
			if ( empty( $plugins ) ) {
				return;
			}

			foreach ( array_keys( $plugins ) as $plugin ) {
				$class = 'YITH_WCMAP_' . ucwords( str_replace( '-', '_', $plugin ) ) . '_Compatibility';
				if ( class_exists( $class ) ) {
					new $class();
				}
			}
		}
	}
}
