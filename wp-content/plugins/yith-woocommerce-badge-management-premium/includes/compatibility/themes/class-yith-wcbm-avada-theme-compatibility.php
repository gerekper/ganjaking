<?php
/**
 * Avada Theme Compatibility Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManegement\Compatibility
 */

defined( 'YITH_WCBM' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBM_Avada_Theme_Compatibility' ) ) {
	/**
	 * Avada Theme Compatibility Class
	 */
	class YITH_WCBM_Avada_Theme_Compatibility {
		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCBM_Avada_Theme_Compatibility
		 */
		protected static $instance;

		/**
		 * Singleton implementation
		 *
		 * @return YITH_WCBM_Avada_Theme_Compatibility
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * YITH_WCBM_Avada_Theme_Compatibility constructor.
		 */
		private function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'add_inline_style' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'add_inline_style' ) );
		}

		/**
		 * Add inline style to frontend CSS to fix the style.
		 */
		public function add_inline_style() {
			$style = '
				.yith-wcbm-badge{ 
					z-index: 50 !important; 
				}
				
				.crossfade-images:hover .yith-wcbm-badge img:not(.hover-image) {
					opacity: initial !important;
				}
			';
			wp_add_inline_style( 'yith_wcbm_badge_style', $style );
		}
	}
}
