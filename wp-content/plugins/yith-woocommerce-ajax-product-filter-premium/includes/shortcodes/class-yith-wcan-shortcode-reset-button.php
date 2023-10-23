<?php
/**
 * Reset button Shortcode
 *
 * Defines shortcode that output Reset Filters button
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes\Shortcodes
 * @version 4.0
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Shortcode_Reset_Button' ) ) {
	/**
	 * Shortcodes classes
	 */
	class YITH_WCAN_Shortcode_Reset_Button {
		/**
		 * Render shortcode, given attributes
		 *
		 * @return string Shortcode output
		 */
		public static function render() {
			if ( ! YITH_WCAN()->frontend ) {
				return '';
			}

			// Use method from frontend class, to output reset button.
			ob_start();
			YITH_WCAN()->frontend->reset_button();
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
				'yith-wcan-reset-button' => array(
					'style'          => 'yith-wcan-shortcodes',
					'script'         => 'yith-wcan-shortcodes',
					'title'          => _x( 'YITH Reset Filters Button', '[GUTENBERG]: block name', 'yith-woocommerce-ajax-navigation' ),
					'description'    => _x( 'Show reset filters button, when a filter is applied and button is enabled in plugins settings. You can use this block to place "Reset filters" button inside your page, when "Reset button position" option won\'t work for your product\'s loop', '[GUTENBERG]: block description', 'yith-woocommerce-ajax-navigation' ),
					'shortcode_name' => 'yith_wcan_reset_button',
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
			if ( 0 === strpos( $shortcode, '[yith_wcan_reset_button' ) ) {
				$_GET['min_price'] = 10;
				$_GET['max_price'] = 100;

				$_REQUEST[ YITH_WCAN_Query()->get_query_param() ] = 1;
			}
		}
	}
}
