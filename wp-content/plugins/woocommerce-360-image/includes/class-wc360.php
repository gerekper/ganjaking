<?php
/**
 * WooCommerce 360° Image Main Class
 *
 * @package WooCommerce 360° Image
 * @since   1.0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_360_Image' ) ) {
	/**
	 * WC360 Main Class.
	 *
	 * @deprecated 1.3.0
	 */
	class WC_360_Image extends \Themesquad\WC_360_Image\Plugin {

		/**
		 * The class constructor.
		 *
		 * @deprecated 1.3.0
		 */
		protected function __construct() {
			wc_deprecated_function( __FUNCTION__, '1.3.0', 'Themesquad\WC_360_Image\Plugin' );

			parent::__construct();
		}

		/**
		 * Gets the single instance of the class.
		 *
		 * @since 1.0.0
		 * @deprecated 1.3.0
		 *
		 * @return mixed The class instance.
		 */
		public static function get_instance() {
			wc_deprecated_function( __FUNCTION__, '1.3.0', 'Themesquad\WC_360_Image\Plugin::instance()' );

			return self::instance();
		}

		/**
		 * Add permalinks settings action link to the plugins page.
		 *
		 * @deprecated 1.3.0
		 *
		 * @param array $links Links.
		 * @return array
		 */
		public function add_action_links( $links ) {
			wc_deprecated_function( __FUNCTION__, '1.3.0' );

			return $links;
		}

		/**
		 * Fire when plugin is activated
		 *
		 * @since 1.0.0
		 * @deprecated 1.3.0
		 */
		public static function activate() {
			wc_deprecated_function( __FUNCTION__, '1.3.0' );
		}
	}

}
