<?php
/**
 * Abstract compatibility class for YITH WooCommerce Customize My Account Page
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 3.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

if ( ! class_exists( 'YITH_WCMAP_Compatibility' ) ) {
	/**
	 * Class YITH_WCMAP_Compatibility
	 *
	 * @since 3.0.0
	 */
	abstract class YITH_WCMAP_Compatibility {

		/**
		 * The endpoint key
		 *
		 * @var string|array
		 */
		protected $endpoint_key = '';

		/**
		 * The endpoint data
		 *
		 * @var array
		 */
		protected $endpoint = array();

		/**
		 * Register the endpoint
		 *
		 * @since 3.0.0
		 */
		protected function register_endpoint() {
			if ( empty( $this->endpoint_key ) ) {
				return;
			}

			if ( is_array( $this->endpoint_key ) ) {

				foreach ( $this->endpoint_key as $key ) {
					if ( empty( $this->endpoint[ $key ] ) ) {
						continue;
					}

					$default = yith_wcmap_get_default_endpoint_options( $key );
					$this->register_item( $key, array_merge( $default, $this->endpoint[ $key ] ) );
				}
			} else {
				$default = yith_wcmap_get_default_endpoint_options( $this->endpoint_key );
				$this->register_item( $this->endpoint_key, array_merge( $default, $this->endpoint ) );
			}
		}

		/**
		 * Register a single item
		 *
		 * @since 3.0.0
		 * @param string $key The item key.
		 * @param array  $data The item data.
		 */
		protected function register_item( $key, $data ) {
			YITH_WCMAP()->items->register_plugin_item( $key, $data );
		}
	}
}
