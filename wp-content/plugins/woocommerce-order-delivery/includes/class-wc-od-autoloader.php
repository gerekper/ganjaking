<?php
/**
 * Plugin Class Autoloader
 *
 * @package WC_OD
 * @since   1.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Autoloader' ) ) {
	/**
	 * Loads the classes on demand.
	 *
	 * @since 1.1.0
	 */
	class WC_OD_Autoloader {

		/**
		 * Constructor.
		 *
		 * @since 1.1.0
		 */
		public function __construct() {
			if ( function_exists( '__autoload' ) ) {
				spl_autoload_register( '__autoload' );
			}

			spl_autoload_register( array( $this, 'autoload' ) );
		}

		/**
		 * Auto-load classes on demand to reduce memory consumption.
		 *
		 * @since 1.1.0
		 *
		 * @param string $class The class to load.
		 */
		public function autoload( $class ) {
			$class = strtolower( $class );
			$file  = $this->get_file_name_from_class( $class );

			/**
			 * Filters the autoload classes.
			 *
			 * @since 1.0.0
			 *
			 * @param array $autoload An array with pairs ( pattern => $path ).
			 */
			$autoload = apply_filters(
				'wc_od_autoload',
				array(
					'wc_od_admin_field_' => WC_OD_PATH . 'includes/admin/fields/',
					'wc_od_meta_box_'    => WC_OD_PATH . 'includes/admin/meta-boxes/',
					'wc_od_collection'   => WC_OD_PATH . 'includes/collections/',
					'wc_od_event'        => WC_OD_PATH . 'includes/events/',
					'wc_od_'             => WC_OD_PATH . 'includes/',
				)
			);

			foreach ( $autoload as $prefix => $path ) {
				if ( 0 === strpos( $class, $prefix ) ) {
					$this->load_file( $path . $file );
					break;
				}
			}
		}

		/**
		 * Take a class name and turn it into a file name.
		 *
		 * @since 1.1.0
		 *
		 * @param  string $class The class name.
		 * @return string The file name.
		 */
		private function get_file_name_from_class( $class ) {
			return 'class-' . str_replace( '_', '-', $class ) . '.php';
		}

		/**
		 * Include a class file.
		 *
		 * @since 1.1.0
		 *
		 * @param  string $path The file path.
		 * @return bool successful or not
		 */
		private function load_file( $path ) {
			if ( $path && is_readable( $path ) ) {
				include_once $path;

				return true;
			}

			return false;
		}

	}
}

new WC_OD_Autoloader();
