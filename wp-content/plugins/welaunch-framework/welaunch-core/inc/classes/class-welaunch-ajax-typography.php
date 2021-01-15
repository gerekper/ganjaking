<?php
/**
 * weLaunch Typography AJAX Class
 *
 * @class weLaunch_Core
 * @version 4.0.0
 * @package weLaunch Framework
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'weLaunch_AJAX_Typography', false ) ) {

	/**
	 * Class weLaunch_AJAX_Typography
	 */
	class weLaunch_AJAX_Typography extends weLaunch_Class {

		/**
		 * weLaunch_AJAX_Typography constructor.
		 *
		 * @param object $parent RedusFramework object.
		 */
		public function __construct( $parent ) {
			parent::__construct( $parent );
			add_action( 'wp_ajax_welaunch_update_google_fonts', array( $this, 'google_fonts_update' ) );
		}

		/**
		 * Google font AJAX callback
		 *
		 * @return mixed
		 */
		public function google_fonts_update() {
			$field_class = 'weLaunch_typography';

			if ( ! class_exists( $field_class ) ) {
				$dir = str_replace( '/classes', '', weLaunch_Functions_Ex::wp_normalize_path( dirname( __FILE__ ) ) );

				// phpcs:ignore WordPress.NamingConventions.ValidHookName
				$class_file = apply_filters( 'welaunch-typeclass-load', $dir . '/fields/typography/class-welaunch-typography.php', $field_class );
				if ( $class_file ) {
					require_once $class_file;
				}
			}

			if ( class_exists( $field_class ) && method_exists( $field_class, 'google_fonts_update_ajax' ) ) {
				$f = new $field_class( array(), '', $this->parent );

				return $f->google_fonts_update_ajax();
			}

			die();
		}
	}
}
