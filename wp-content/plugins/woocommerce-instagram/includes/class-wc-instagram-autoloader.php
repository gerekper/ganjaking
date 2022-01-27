<?php
/**
 * Plugin Class Autoloader
 *
 * @package WC_Instagram
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Instagram_Autoloader' ) ) {
	/**
	 * Loads the classes on demand.
	 *
	 * @since 2.0.0
	 */
	class WC_Instagram_Autoloader {

		/**
		 * Path to the includes directory.
		 *
		 * @since 3.0.0
		 *
		 * @var string
		 */
		private $include_path;

		/**
		 * Constructor.
		 *
		 * @since 2.0.0
		 */
		public function __construct() {
			if ( function_exists( '__autoload' ) ) {
				spl_autoload_register( '__autoload' );
			}

			spl_autoload_register( array( $this, 'autoload' ) );

			$this->include_path = WC_INSTAGRAM_PATH . 'includes/';
		}

		/**
		 * Autoload classes on demand to reduce memory consumption.
		 *
		 * @since 2.0.0
		 *
		 * @param string $class The class to load.
		 */
		public function autoload( $class ) {
			$class = strtolower( $class );

			if ( 0 !== strpos( $class, 'wc_instagram_' ) ) {
				return;
			}

			$file = $this->get_file_name_from_class( $class );

			/**
			 * Filters autoload classes.
			 *
			 * @since 2.0.0
			 *
			 * @param array $autoload An array with pairs ( pattern => $path ).
			 */
			$autoload = apply_filters(
				'wc_instagram_autoload',
				array(
					'wc_instagram_admin_field_'            => $this->include_path . 'admin/fields/',
					'wc_instagram_admin_'                  => $this->include_path . 'admin/',
					'wc_instagram_settings_'               => $this->include_path . 'admin/settings/',
					'wc_instagram_api_node'                => $this->include_path . 'api/nodes/',
					'wc_instagram_api'                     => $this->include_path . 'api/',
					'wc_instagram_product_catalog_format'  => $this->include_path . 'product-catalog/formats/',
					'wc_instagram_product_catalog_item'    => $this->include_path . 'product-catalog/items/',
					'wc_instagram_product_catalog'         => $this->include_path . 'product-catalog/',
					'wc_instagram_google_product_category' => $this->include_path . 'product-catalog/',
					'wc_instagram_data_store'              => $this->include_path . 'data-stores/',
					'wc_instagram_'                        => $this->include_path,
				)
			);

			foreach ( $autoload as $prefix => $path ) {
				if ( 0 === strpos( $class, $prefix ) && $this->load_file( $path . $file ) ) {
					break;
				}
			}
		}

		/**
		 * Take a class name and turn it into a file name.
		 *
		 * @since 2.0.0
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
		 * @since 2.0.0
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

new WC_Instagram_Autoloader();
