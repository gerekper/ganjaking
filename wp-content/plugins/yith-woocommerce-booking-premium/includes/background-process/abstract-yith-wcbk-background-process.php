<?php
/**
 * Class YITH_WCBK_Background_Process
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'WC_Background_Process', false ) ) {
	include_once YITH_WCBK_INCLUDES_PATH . '/libraries/class-wc-background-process.php';
}

if ( ! class_exists( 'YITH_WCBK_Background_Process' ) ) {
	/**
	 * Class YITH_WCBK_Background_Process
	 *
	 * Uses WC_Background_Process to handle DB updates in the background.
	 *
	 * @abstract
	 */
	abstract class YITH_WCBK_Background_Process extends WC_Background_Process {
		/**
		 * Initiate new background process.
		 */
		public function __construct() {
			// Uses unique prefix per blog so each blog has separate queue.
			$this->prefix = 'wp_' . get_current_blog_id();
			$this->action = 'yith_wcbk_bg_process_' . sanitize_title( $this->get_action_type() );

			parent::__construct();

			if ( $this->is_running() ) {
				$this->add_wc_notice( 'running' );
			}
		}

		/**
		 * Retrieve the action type
		 *
		 * @return string
		 */
		abstract public function get_action_type();

		/**
		 * Return a list of notices to show
		 * override this to set a list of notices to show
		 *
		 * @return array
		 */
		public function get_notices() {
			return array(
				'start'    => '', // Background process started.
				'running'  => '', // Background process is running.
				'complete' => '', // Background process completed.
			);
		}

		/**
		 * Add a WC Notice
		 *
		 * @param string $type The type.
		 */
		public function add_wc_notice( $type ) {
			$notices    = (array) $this->get_notices();
			$notice     = ! empty( $notices[ $type ] ) ? $notices[ $type ] : '';
			$notice_key = $this->action . '_notice';
			if ( $notice ) {
				WC_Admin_Notices::add_custom_notice( $notice_key, $notice );
			}
		}

		/**
		 * Dispatch updater.
		 *
		 * Updater will still run via cron job if this fails for any reason.
		 */
		public function dispatch() {
			$this->add_wc_notice( 'start' );
			yith_wcbk_maybe_debug( 'Background process started', YITH_WCBK_Logger_Groups::BACKGROUND_PROCESS );

			$dispatched = parent::dispatch();

			if ( is_wp_error( $dispatched ) ) {
				yith_wcbk_add_log( sprintf( 'Unable to dispatch Background Process: %s', $dispatched->get_error_message() ), YITH_WCBK_Logger_Types::ERROR, YITH_WCBK_Logger_Groups::BACKGROUND_PROCESS );
			}
		}

		/**
		 * Is running?
		 *
		 * @return bool
		 */
		public function is_running() {
			return false === $this->is_queue_empty();
		}

		/**
		 * Handle cron healthcheck
		 *
		 * Restart the background process if not already running
		 * and data exists in the queue.
		 */
		public function handle_cron_healthcheck() {
			if ( $this->is_process_running() ) {
				// Background process already running.
				return;
			}

			if ( $this->is_queue_empty() ) {
				// No data to process.
				$this->clear_scheduled_event();

				return;
			}

			$this->handle();
		}

		/**
		 * Schedule fallback event.
		 */
		protected function schedule_event() {
			if ( ! wp_next_scheduled( $this->cron_hook_identifier ) ) {
				wp_schedule_event( time() + 10, $this->cron_interval_identifier, $this->cron_hook_identifier );
			}
		}

		/**
		 * Task
		 *
		 * Return the modified item for further processing
		 * in the next pass through. Or, return false to remove the
		 * item from the queue.
		 *
		 * @param string $callback Update callback function.
		 *
		 * @return mixed
		 */
		protected function task( $callback ) {
			$callback_name = $callback;
			$params        = array();
			if ( is_array( $callback ) ) {
				if ( isset( $callback['callback'] ) ) {
					if ( isset( $callback['params'] ) ) {
						$params = $callback['params'];
					}
					$callback      = $callback['callback'];
					$callback_name = $callback;
				}

				if ( is_array( $callback ) && count( $callback ) === 2 ) {
					$class         = is_object( $callback[0] ) ? get_class( $callback[0] ) : $callback[0];
					$function      = $callback[1];
					$callback_name = "{$class}::{$function}";
				}
			}
			if ( is_callable( $callback ) ) {
				if ( ! ! $params ) {
					yith_wcbk_maybe_debug( sprintf( 'Running %s callback with params: %s', $callback_name, print_r( $params, true ) ), YITH_WCBK_Logger_Groups::BACKGROUND_PROCESS ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
					call_user_func_array( $callback, $params );
					yith_wcbk_maybe_debug( sprintf( 'Finished %s callback with params: %s', $callback_name, print_r( $params, true ) ), YITH_WCBK_Logger_Groups::BACKGROUND_PROCESS ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				} else {
					yith_wcbk_maybe_debug( sprintf( 'Running %s callback', $callback_name ), YITH_WCBK_Logger_Groups::BACKGROUND_PROCESS );
					call_user_func( $callback );
					yith_wcbk_maybe_debug( sprintf( 'Finished %s callback', $callback_name ), YITH_WCBK_Logger_Groups::BACKGROUND_PROCESS );
				}
			} else {
				yith_wcbk_add_log( sprintf( 'Could not find %s callback', $callback_name ), YITH_WCBK_Logger_Types::WARNING, YITH_WCBK_Logger_Groups::BACKGROUND_PROCESS );
			}

			return false;
		}

		/**
		 * Complete
		 *
		 * Override if applicable, but ensure that the below actions are
		 * performed, or, call parent::complete().
		 */
		protected function complete() {
			$this->add_wc_notice( 'complete' );
			yith_wcbk_maybe_debug( sprintf( 'Background process complete' ), YITH_WCBK_Logger_Groups::BACKGROUND_PROCESS );
			parent::complete();
		}
	}
}
