<?php
/**
 * weLaunch Path Class
 *
 * @class weLaunch_Path
 * @version 4.0.0
 * @package weLaunch Framework
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'weLaunch_Path', false ) ) {

	/**
	 * Class weLaunch_Path
	 */
	class weLaunch_Path {

		/**
		 * Class init
		 */
		public static function init() {

		}

		/**
		 * Gets weLaunch path.
		 *
		 * @param string $relative_path Self explanitory.
		 *
		 * @return string
		 */
		public static function get_path( $relative_path ) {
			$path = weLaunch_Core::$welaunch_path . $relative_path;
			return $path;
		}

		/**
		 * Require class.
		 *
		 * @param string $relative_path Path.
		 */
		public static function require_class( $relative_path ) {
			$path = self::get_path( $relative_path );

			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}
	}

	weLaunch_Path::init();
}
