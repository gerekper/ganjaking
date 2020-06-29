<?php
/**
 * WC_Free_Gift_Coupons_Background_Updater class
 *
 * @author   Kathy Darling <kathy@kathyisawesome.com>
 * @package  WooCommerce Free Gift Coupons
 * @since    2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Async_Request', false ) ) {
	include_once  WC_ABSPATH . 'includes/libraries/wp-async-request.php' ;
}

if ( ! class_exists( 'WP_Background_Process', false ) ) {
	include_once  WC_ABSPATH . 'includes/libraries/wp-background-process.php' ;
}

/**
 * Background Updater.
 *
 * Uses https://github.com/A5hleyRich/wp-background-processing to handle DB
 * updates in the background.
 *
 * @class    WC_Free_Gift_Coupons_Background_Updater
 * @version  2.0.0
 */
class WC_Free_Gift_Coupons_Background_Updater extends WP_Background_Process {

	/**
	 * Initiate new background process.
	 */
	public function __construct() {

		// Uses unique prefix per blog so each blog has its own queue.
		$this->prefix = 'wp_' . get_current_blog_id();
		$this->action = 'wc_fgc_updater';

		parent::__construct();
	}

	/**
	 * Returns the cron action identifier.
	 *
	 * @since  2.0.0
	 *
	 * @return string
	 */
	public function get_cron_hook_identifier() {
		return $this->cron_hook_identifier;
	}

	/**
	 * Dispatch updater.
	 */
	public function dispatch() {

		$dispatched = parent::dispatch();

		if ( is_wp_error( $dispatched ) ) {
			wc_get_logger()->error( sprintf( 'Unable to dispatch WooCommerce Free Gift Copuons updater: %s', $dispatched->get_error_message() ), 'wc_fgc_db_updates' );
		}
	}

	/**
	 * Handle cron healthcheck.
	 *
	 * Restart the background process if not already running and data exists in the queue.
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
	 * Schedule event.
	 */
	protected function schedule_event() {
		if ( ! wp_next_scheduled( $this->cron_hook_identifier ) ) {
			wp_schedule_event( time() + 10, $this->cron_interval_identifier, $this->cron_hook_identifier );
		}
	}

	/**
	 * Is the updater queue empty?
	 *
	 * @return boolean
	 */
	public function is_update_queued() {
		return ! empty( $this->data ) || false === $this->is_queue_empty();
	}

	/**
	 * Is the updater actually running?
	 *
	 * @return boolean
	 */
	public function is_process_running() {
		return parent::is_process_running();
	}

	/**
	 * Time exceeded.
	 *
	 * Ensures the batch never exceeds a sensible time limit.
	 * A timeout limit of 30s is common on shared hosting.
	 *
	 * @return bool
	 */
	public function time_exceeded() {
		return parent::time_exceeded();
	}

	/**
	 * Memory exceeded.
	 *
	 * Ensures the batch process never exceeds 90%
	 * of the maximum WordPress memory.
	 *
	 * @return bool
	 */
	public function memory_exceeded() {
		return parent::memory_exceeded();
	}
	
	/**
	 * Runs update tasks and creates log entries.
	 * Passes the object to task callbacks to allow in-task resource monitoring using the built-in methods.
	 * Handles multiple callback exit codes for restarting or requeuing a task.
	 *
	 * @param  string  $callback
	 * @return mixed
	 */
	protected function task( $callback ) {

		include_once  'wc-free-gift-coupons-update-functions.php' ;

		if ( is_callable( $callback ) ) {

			wc_get_logger()->info( sprintf( '- Running %s callback...', $callback ), 'wc_fgc_db_updates' );

			$result = call_user_func_array( $callback, array( $this ) );

			if ( -1 === $result ) {
				$message = sprintf( '- Restarting %s callback.', $callback );
				// Add this to ensure the task gets restarted right away.
				add_filter( $this->identifier . '_time_exceeded', '__return_true' );
			} elseif ( -2 === $result ) {
				$message = sprintf( '- Requeuing %s callback.', $callback );
			} else {
				$message = sprintf( '- Finished %s callback.', $callback );
			}

			wc_get_logger()->info( $message, 'wc_fgc_db_updates' );

		} else {

			$result = false;

			wc_get_logger()->notice( sprintf( '- Could not find %s callback.', $callback ), 'wc_fgc_db_updates' );
		}

		return in_array( $result, array( -1, -2 ), true ) ? $callback : false;
	}

	/**
	 * When all tasks complete, update plugin db version and create log entry.
	 */
	protected function complete() {
		WC_Free_Gift_Coupons_Install::update_complete();
		parent::complete();
	}
}
