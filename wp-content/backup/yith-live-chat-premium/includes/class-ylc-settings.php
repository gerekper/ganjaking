<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YLC_Settings' ) ) {

	class YLC_Settings {

		/**
		 * @var $_options array options array
		 */
		private $_options = array();

		/**
		 * Single instance of the class
		 *
		 * @var \YLC_Settings
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YLC_Settings
		 * @since 1.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		private function __construct() {
		}

		/**
		 * Load plugin options
		 *
		 * @since   1.0.0
		 *
		 * @param   $parent string
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		private function get_options( $parent ) {
			if ( ! isset( $this->_options[ $parent ] ) ) {

				$options = get_option( "yit_{$parent}_options" );

				if ( $options == '' ) {
					$options = array();
					update_option( "yit_{$parent}_options", array() );
				}

				$this->_options[ $parent ] = $options;

			}

			return $this->_options[ $parent ];
		}

		/**
		 * Get selected option
		 *
		 * @since   1.0.0
		 *
		 * @param   $parent  string
		 * @param   $key     string
		 * @param   $default mixed
		 *
		 * @return  mixed
		 * @author  Alberto Ruggiero
		 */
		public function get_option( $parent, $key, $default = false ) {
			$options = $this->get_options( $parent );

			return is_array( $options ) && array_key_exists( $key, $options ) ? $options[ $key ] : $default;
		}

	}

}