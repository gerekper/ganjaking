<?php
/**
 * Active Filters Labels Shortcodes
 *
 * Defines shortcode that output Filters Preset
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes\Shortcodes
 * @version 4.0
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Shortcode_Active_Filters_Labels' ) ) {
	/**
	 * Shortcodes classes
	 */
	class YITH_WCAN_Shortcode_Active_Filters_Labels {
		/**
		 * Render shortcode, given attributes
		 *
		 * @return string Shortcode output
		 */
		public static function render() {
			if ( ! YITH_WCAN()->frontend ) {
				return '';
			}

			// Use method from frontend class, to output current labels.
			ob_start();
			YITH_WCAN()->frontend->active_filters_list();
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
				'yith-wcan-active-filters-labels' => array(
					'style'          => 'yith-wcan-shortcodes',
					'script'         => 'yith-wcan-shortcodes',
					'title'          => _x( 'YITH Active Filters Labels', '[GUTENBERG]: block name', 'yith-woocommerce-ajax-navigation' ),
					'description'    => _x( 'Show active filters in current selection, as labels. You can use this block to place "Active filters" labels inside your page, when "Active filters labels position" option won\'t work for your product\'s loop', '[GUTENBERG]: block description', 'yith-woocommerce-ajax-navigation' ),
					'shortcode_name' => 'yith_wcan_active_filters_labels',
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
			if ( 0 === strpos( $shortcode, '[yith_wcan_active_filters_labels' ) ) {
				$_GET['min_price']     = 10;
				$_GET['max_price']     = 100;
				$_GET['onsale_filter'] = 1;

				$_REQUEST[ YITH_WCAN_Query()->get_query_param() ] = 1;
			}
		}
	}
}
