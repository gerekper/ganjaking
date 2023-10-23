<?php
/**
 * Filter Widgets
 *
 * Defines all the widgets supported by the plugin
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes
 * @version 4.0.2
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Widgets' ) ) {
	/**
	 * Widgets classes
	 */
	class YITH_WCAN_Widgets {
		/**
		 * A list of available plugin widgets
		 *
		 * @var array $available_widgets
		 */
		private static $available_widgets;

		/**
		 * Register shortcode and performs all shortcodes related ops
		 *
		 * @reutrn void
		 */
		public static function init() {
			// init available shortcodes.
			self::$available_widgets = apply_filters(
				'yith_wcan_widgets',
				array(
					'YITH_WCAN_Navigation_Widget',
					'YITH_WCAN_Reset_Navigation_Widget',
					'YITH_WCAN_Filters_Widget',
				)
			);

			add_action( 'widgets_init', array( __CLASS__, 'init_widgets' ) );
		}

		/**
		 * Register plugin shortcodes
		 *
		 * @return void
		 */
		public static function init_widgets() {
			if ( empty( self::$available_widgets ) ) {
				return;
			}

			foreach ( self::$available_widgets as $classname ) {
				$filename = 'class-' . strtolower( str_replace( '_', '-', $classname ) );
				$filepath = YITH_WCAN_INC . 'widgets/' . $filename . '.php';

				if ( ! file_exists( $filepath ) ) {
					continue;
				}

				if ( false !== strpos( $filename, '-premium' ) ) {
					$parent_filename = str_replace( '-premium', '', $filename );
					$parent_filepath = YITH_WCAN_INC . 'widgets/' . $parent_filename . '.php';

					if ( file_exists( $parent_filepath ) ) {
						include_once $parent_filepath;
					}
				}

				include_once $filepath;

				if ( class_exists( $classname ) ) {
					register_widget( $classname );
				}
			}
		}
	}
}
