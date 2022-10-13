<?php
/**
 * Logs class for centralized logging management.
 * REMINDERS:
 * - Use html output with <pre> and dump all request / responses
 * - Consider keeping / using Kint
 *
 * @package NeverSettle\WooCommerce-Amazon-Fulfillment
 * @since 4.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'NS_MCF_Logs' ) ) {

	/**
	 * Logs class.
	 */
	class NS_MCF_Logs extends NS_MCF_Integration {

		/**
		 * The state of allowing log entries of type = 'error' to be written or not.
		 *
		 * @var bool $is_error_log_on Class property to control whether or not error logging can occur.
		 */
		private $is_error_log_on = true;

		/**
		 * Logger instance.
		 *
		 * @var WC_Logger
		 */
		public static $log = false;


		/**
		 * Initiate the class.
		 */
		public function init() {
			// Create the logs directory if it doesn't exist.
			$this->ns_fba->file_utils->create_directory( $this->ns_fba->plugin_path . 'logs' );
		}

		/**
		 * Add an entry to the logs based on passed in parameters.
		 *
		 * @param  mixed $mixed  the value to return instead of dumping.
		 * @return false|string
		 */
		public function var_dump_val( $mixed = null ) {
			ob_start();
			var_dump( $mixed ); // phpcs:ignore.
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}

		/**
		 * Add an entry to the logs based on passed in parameters.
		 *
		 * @param mixed  $content Text or data that makes up the log entry.
		 * @param string $mode Mode for the entry to create for future flexibility to call other logging mechanisms.
		 * @param string $file Destination file of the entry.
		 */
		public function add_entry( $content = '', string $mode = 'test', string $file = '' ) {
			// Ensure we have enough depth in Kint levels to record values for Amazon requests and responses.
			Kint::$max_depth = 12;

			// TODO: Define the exact modes we want to implement and how the logging for each should implement.
			switch ( $mode ) {
				case 'error':
					if ( $this->is_error_log_on ) {
						// phpcs:ignore.
						error_log( print_r( $content, true ) );
					}
					break;
				case 'debug':
					if ( $this->ns_fba->is_debug ) {
						// phpcs:ignore.
						error_log( @Kint::dump( $content ), 3, $file );
					}
					break;
				case 'info':
					// phpcs:ignore.
					error_log( @Kint::dump( $content ), 3, $file );
					break;
				case 'wc':
					if ( $this->ns_fba->is_debug ) {
						if ( empty( self::$log ) ) {
							self::$log = wc_get_logger();
						}
						if ( ! is_string( $content ) || is_array( $content ) || is_object( $content ) ) {
							// phpcs:ignore.
							$content = print_r( $content, true );
						}
						self::$log->log( 'info', $content, array( 'source' => 'wc_amazon_fulfillment' . $file ) );
					}
					break;
				case 'test':
					if ( is_array( $content ) || is_object( $content ) ) {
						// phpcs:ignore.
						error_log( @Kint::dump( $content ), 3, $file );
					} elseif ( is_string( $content ) ) {
						// phpcs:ignore.
						error_log( @Kint::dump( gmdate( 'Y.m.d-H:i:s' ) . ' :: ' . $content ), 3, $file );
					} else {
						// phpcs:ignore.
						error_log( @Kint::dump( $this->var_dump_val( $content ) ), 3, $file );
					}
					break;
				default:
					break;
			}
		}
	} // End class.
}
