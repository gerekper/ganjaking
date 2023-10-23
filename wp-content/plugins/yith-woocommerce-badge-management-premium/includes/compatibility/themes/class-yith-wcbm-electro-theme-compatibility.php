<?php
/**
 * Electro Theme Compatibility Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManegement\Compatibility
 * @since   1.3.6
 */

defined( 'YITH_WCBM' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBM_Electro_Theme_Compatibility' ) ) {
	/**
	 * Electro Theme Compatibility Class
	 */
	class YITH_WCBM_Electro_Theme_Compatibility {
		/**
		 * The instance of the class
		 *
		 * @var YITH_WCBM_Electro_Theme_Compatibility
		 */
		protected static $instance;

		/**
		 * Singleton implementation
		 *
		 * @return YITH_WCBM_Basel_Theme_Compatibility
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * YITH_WCBM_Electro_Theme_Compatibility constructor.
		 */
		private function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 999 );

			$is_ajax_request = defined( 'DOING_AJAX' ) && DOING_AJAX;
			if ( ! is_admin() || $is_ajax_request ) {
				add_filter( 'electro_single_product_thumbnails_single_html', array( YITH_WCBM_Frontend(), 'show_badge_on_product_thumbnail' ), 10, 2 );
			}
		}

		/**
		 * Enqueue scripts
		 */
		public function enqueue_scripts() {
			$css = '.container-image-and-badge{position: static !important;}';
			wp_add_inline_style( 'yith_wcbm_badge_style', $css );
		}
	}
}
