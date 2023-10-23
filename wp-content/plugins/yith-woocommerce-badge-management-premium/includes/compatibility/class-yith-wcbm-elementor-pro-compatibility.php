<?php
/**
 * Elementor Pro Compatibility Class
 *
 * @auhtor  Arcifa Giuseppe <giuseppe.arcifa@yithemes.com>
 * @package YITH\BadgeManagementPremium\Compatibility
 * @since   2.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'YITH_WCBM_Elementor_Pro_Compatibility' ) ) {
	/**
	 * Elementor Pro Compatibility Class
	 */
	class YITH_WCBM_Elementor_Pro_Compatibility {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCBM_Elementor_Pro_Compatibility
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Return the class instance
		 *
		 * @return YITH_WCBM_Elementor_Pro_Compatibility
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * YITH_WCBM_Elementor_Pro_Compatibility constructor.
		 */
		public function __construct() {
			add_filter( 'yith_wcbm_add_badge_tags_in_wp_kses_allowed_html', '__return_true' );
		}
	}
}
