<?php
/**
 * weLaunch Framework Instance Container Class
 * Automatically captures and stores all instances
 * of weLaunchFramework at instantiation.
 *
 * @package     weLaunch_Framework/Classes
 * @subpackage  Core
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'weLaunch_Instances', false ) ) {

	/**
	 * Class weLaunch_Instances
	 */
	class weLaunch_Instances {

		/**
		 * weLaunchFramework instances
		 *
		 * @var weLaunchFramework[]
		 */
		private static $instances;

		/**
		 * Get Instance
		 * Get weLaunch_Instances instance
		 * OR an instance of weLaunchFramework by [opt_name]
		 *
		 * @param  string|false $opt_name the defined opt_name.
		 *
		 * @return weLaunchFramework class instance
		 */
		public static function get_instance( $opt_name = false ) {

			if ( $opt_name && ! empty( self::$instances[ $opt_name ] ) ) {
				return self::$instances[ $opt_name ];
			}

			return new self();
		}

		/**
		 * Shim for old get_welaunch_instance method.
		 *
		 * @param  string|false $opt_name the defined opt_name.
		 *
		 * @return weLaunchFramework class instance
		 */
		public static function get_welaunch_instance( $opt_name = '' ) {
			return self::get_instance( $opt_name );
		}

		/**
		 * Get all instantiated weLaunchFramework instances (so far)
		 *
		 * @return [type] [description]
		 */
		public static function get_all_instances() {
			return self::$instances;
		}

		/**
		 * weLaunch_Instances constructor.
		 *
		 * @param mixed $welaunch_framework Is object.
		 */
		public function __construct( $welaunch_framework = false ) {
			if ( false !== $welaunch_framework ) {
				$this->store( $welaunch_framework );
			} else {
				add_action( 'welaunch/construct', array( $this, 'store' ), 5, 1 );
			}
		}

		/**
		 * Action hook callback.
		 *
		 * @param object $welaunch_framework Pointer.
		 */
		public function store( $welaunch_framework ) {
			if ( $welaunch_framework instanceof weLaunchFramework ) {
				$key                     = $welaunch_framework->args['opt_name'];
				self::$instances[ $key ] = $welaunch_framework;
			}
		}
	}
}

if ( ! class_exists( 'weLaunchFrameworkInstances' ) ) {
	class_alias( 'weLaunch_Instances', 'weLaunchFrameworkInstances' );
}

if ( ! function_exists( 'get_welaunch_instance' ) ) {
	/**
	 * Shim function that some theme oddly used.
	 *
	 * @param  string|false $opt_name the defined opt_name.
	 *
	 * @return weLaunchFramework class instance
	 */
	function get_welaunch_instance( $opt_name ) {
		return weLaunch_Instances::get_instance( $opt_name );
	}
}

if ( ! function_exists( 'get_all_welaunch_instances' ) ) {
	/**
	 * Fetch all instances of weLaunchFramework
	 * as an associative array.
	 *
	 * @return array        format ['opt_name' => $weLaunchFramework]
	 */
	function get_all_welaunch_instances() {
		return weLaunch_Instances::get_all_instances();
	}
}
