<?php
/**
 * Settings class
 *
 * @package YITH\FAQPluginForWordPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_FAQ_Settings' ) ) {

	/**
	 * Manages plugin settings
	 *
	 * @class   YITH_FAQ_Settings
	 * @since   1.0.0
	 * @author  YITH <plugins@yithemes.com>
	 * @package YITH\FAQPluginForWordPress
	 */
	class YITH_FAQ_Settings {

		/**
		 * Options array
		 *
		 * @var array
		 */
		private $options = array();

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var YITH_FAQ_Settings
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_FAQ_Settings
		 * @since 1.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		private function __construct() {
		}

		/**
		 * Load plugin options
		 *
		 * @param string $parent The option parent container.
		 *
		 * @return  array
		 * @since   1.0.0
		 */
		private function get_options( $parent ) {

			if ( ! isset( $this->options[ $parent ] ) ) {

				$options = get_option( "yit_{$parent}_options" );

				if ( '' === $options ) {
					$options = array();
					update_option( "yit_{$parent}_options", array() );
				}

				$this->options[ $parent ] = $options;

			}

			return $this->options[ $parent ];

		}

		/**
		 * Get selected option
		 *
		 * @param string $parent  The option parent container.
		 * @param string $key     The option key.
		 * @param mixed  $default The default value.
		 *
		 * @return  mixed
		 * @since   1.0.0
		 */
		public function get_option( $parent, $key, $default = false ) {
			$options = $this->get_options( $parent );

			return is_array( $options ) && array_key_exists( $key, $options ) ? $options[ $key ] : $default;
		}

	}

}
