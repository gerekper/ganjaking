<?php
/**
 * Autoloader class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 */

defined( 'YITH_WCMAP' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCMAP_Autoloader', false ) ) {
	/**
	 * Manage plugin file autoloader
	 *
	 * @class      YITH_WCMAP_Autoloader
	 * @since      4.0.0
	 * @package   YITH WooCommerce Customize My Account Page
	 */
	class YITH_WCMAP_Autoloader {

		/**
		 * Constructor
		 *
		 * @since  4.0.0
		 */
		public function __construct() {
			if ( function_exists( '__autoload' ) ) {
				spl_autoload_register( '__autoload' );
			}

			spl_autoload_register( array( $this, 'autoload' ) );
		}

		/**
		 * Get mapped file. Array of class => file to use on autoload.
		 *
		 * @since  4.0.0
		 * @auhtor YITH
		 * @return array
		 */
		protected function get_mapped_files() {
			/**
			 * APPLY_FILTERS: yith_wcmap_autoload_mapped_files
			 *
			 * Filters the mapped filters to autoload.
			 *
			 * @param array $files Mapped files.
			 *
			 * @return array
			 */
			return apply_filters( 'yith_wcmap_autoload_mapped_files', array() );
		}

		/**
		 * Autoload callback
		 *
		 * @since  1.0.0
		 * @param string $class The class to load.
		 */
		public function autoload( $class ) {
			$class = str_replace( '_', '-', strtolower( $class ) );
			if ( false === strpos( $class, 'yith-wcmap' ) ) {
				return; // Pass over.
			}

			$base_path = YITH_WCMAP_DIR . 'includes/';
			// Check first for mapped files.
			$mapped = $this->get_mapped_files();
			if ( isset( $mapped[ $class ] ) ) {
				$file = $base_path . $mapped[ $class ];
			} else {
				if ( false !== strpos( $class, 'admin' ) ) {
					$base_path .= 'admin/';
				} elseif ( false !== strpos( $class, 'compatibility' ) ) {
					$base_path .= 'compatibilities/';
				}

				$file = $base_path . 'class-' . $class . '.php';
			}

			if ( is_readable( $file ) ) {
				require_once $file;
			}
		}
	}
}

new YITH_WCMAP_Autoloader();
