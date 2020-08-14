<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YLC_Logger' ) ) {

	class YLC_Logger {

		/**
		 * @var string log file path
		 */
		protected $file;

		/**
		 * Single instance of the class
		 *
		 * @var \YLC_Logger
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YLC_Logger
		 * @since 1.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 *
		 * @since   1.4.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$this->file = YLC_DIR . '/logs/errors.txt';

		}

		/**
		 * Send email
		 *
		 * @since   1.4.0
		 *
		 * @param  $message
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function error( $message ) {

			if ( apply_filters('ylc_debug_log', false) ) {

				$dt = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
				file_put_contents( $this->file, implode( ' -- ', array(
					'ERROR',
					$dt->format( DATE_ATOM ),
					$message,
					PHP_EOL
				) ), FILE_APPEND );

			}
		}

	}

	function YLC_Logger() {
		return YLC_Logger::get_instance();
	}

}