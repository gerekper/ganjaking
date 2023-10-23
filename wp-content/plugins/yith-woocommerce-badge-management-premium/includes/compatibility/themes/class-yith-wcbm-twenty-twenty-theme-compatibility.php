<?php
/**
 * Twenty_Twenty Theme Compatibility Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManegement\Compatibility
 * @since   1.3.23
 */

defined( 'YITH_WCBM' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBM_Twenty_Twenty_Theme_Compatibility' ) ) {
	/**
	 * Twenty_Twenty Theme Compatibility Class
	 */
	class YITH_WCBM_Twenty_Twenty_Theme_Compatibility {
		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCBM_Twenty_Twenty_Theme_Compatibility
		 */
		protected static $instance;


		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WCBM_Twenty_Twenty_Theme_Compatibility
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 */
		private function __construct() {
			add_filter( 'yith_wcbm_is_allowed_badge_showing', array( $this, 'hide_in_theme_thumb' ) );
		}

		/**
		 * Hide theme thumbnail
		 *
		 * @param bool $allow Check if the badge is allowed.
		 *
		 * @return bool
		 */
		public function hide_in_theme_thumb( $allow ) {
			if ( $allow && function_exists( 'is_product' ) && is_product() && ! did_action( 'woocommerce_before_single_product' ) ) {
					$allow = false;
			}

			return $allow;
		}
	}
}
