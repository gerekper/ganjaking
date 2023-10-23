<?php
/**
 * Mobile modal opener button Shortcode
 *
 * Defines shortcode that output Button that will open filters modal on mobile
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes\Shortcodes
 * @version 4.0
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Shortcode_Mobile_Modal_Opener' ) ) {
	/**
	 * Shortcodes classes
	 */
	class YITH_WCAN_Shortcode_Mobile_Modal_Opener {
		/**
		 * Render shortcode, given attributes
		 *
		 * @return string Shortcode output
		 */
		public static function render() {
			if ( ! YITH_WCAN()->frontend ) {
				return '';
			}

			// Use method from frontend class, to output mobile modal opener.
			ob_start();
			YITH_WCAN()->frontend->mobile_modal_opener();
			return ob_get_clean();
		}

		/**
		 * Returns array of configuration for Gutenberg block fo this shortcode
		 *
		 * @return array Array of configuration.
		 */
		public static function get_gutenberg_config() {
			add_action( 'yith_plugin_fw_gutenberg_before_do_shortcode', array( __CLASS__, 'fix_for_gutenberg_block' ), 10, 1 );

			$blocks = array(
				'yith-wcan-mobile-modal-opener' => array(
					'style'          => 'yith-wcan-shortcodes',
					'script'         => 'yith-wcan-shortcodes',
					'title'          => _x( 'YITH Mobile Filters Modal Opener', '[GUTENBERG]: block name', 'yith-woocommerce-ajax-navigation' ),
					'description'    => _x( 'Show button to open filters modal on mobile. Note that you need to have a valid preset in the same page where you use this block. Content will only appear at mobile. You can use this block to place "Mobile filters modal opener" inside your page, when "Show as modal on mobile" option won\'t work for your product\'s loop', '[GUTENBERG]: block description', 'yith-woocommerce-ajax-navigation' ),
					'shortcode_name' => 'yith_wcan_mobile_modal_opener',
				),
			);

			return $blocks;
		}

		/**
		 * Set additional content to correctly preview gutenberg block of this shortcode
		 *
		 * @param string $shortcode Shortcode being rendered.
		 *
		 * @return void
		 */
		public static function fix_for_gutenberg_block( $shortcode ) {
			if ( 0 === strpos( $shortcode, '[yith_wcan_mobile_modal_opener' ) ) {
				?>
				<style>
					.yith-wcan-filters-opener {
						display: inline-block;
					}
				</style>
				<?php
			}
		}
	}
}
