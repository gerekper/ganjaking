<?php
/**
 * WC_PB_Background_Updater class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Async_Request', false ) ) {
	include_once( WC_ABSPATH . 'includes/libraries/wp-async-request.php' );
}

if ( ! class_exists( 'WP_Background_Process', false ) ) {
	include_once( WC_ABSPATH . 'includes/libraries/wp-background-process.php' );
}

/**
 * Background Updater.
 *
 * Uses https://github.com/A5hleyRich/wp-background-processing to handle DB
 * updates in the background.
 *
 * @class    WC_PB_Background_Updater
 * @version  5.7.1
 */
class WC_PB_Background_Updater extends WP_Background_Process {

	/**
	 * Initiate new background process.
	 */
	public function __construct() {

		// Uses unique prefix per blog so each blog has its own queue.
		$this->prefix = 'wp_' . get_current_blog_id();
		$this->action = 'wc_pb_updater';

		parent::__construct();
	}

	/**
	 * Returns the cron action identifier.
	 *
	 * @since  5.7.1
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
			WC_PB_Core_Compatibility::log( sprintf( 'Unable to dispatch WooCommerce Product Bundles updater: %s', $dispatched->get_error_message() ), 'error', 'wc_pb_db_updates' );
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

		include_once( WC_PB_ABSPATH . 'includes/wc-pb-update-functions.php' );

		if ( is_callable( $callback ) ) {

			WC_PB_Core_Compatibility::log( sprintf( '- Running %s callback...', $callback ), 'info', 'wc_pb_db_updates' );

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

			WC_PB_Core_Compatibility::log( $message, 'info', 'wc_pb_db_updates' );

		} else {

			$result = false;

			WC_PB_Core_Compatibility::log( sprintf( '- Could not find %s callback.', $callback ), 'notice', 'wc_pb_db_updates' );
		}

		return in_array( $result, array( -1, -2 ) ) ? $callback : false;
	}

	/**
	 * When all tasks complete, update plugin db version and create log entry.
	 */
	protected function complete() {
		WC_PB_Install::update_complete();
		parent::complete();
	}
}
