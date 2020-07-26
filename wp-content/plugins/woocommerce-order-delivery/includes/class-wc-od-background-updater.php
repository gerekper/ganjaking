<?php
/**
 * Background Updater
 *
 * Inspired in the WC_Background_Updater class.
 *
 * TODO: Extend from the WC_Background_Updater class when we can change the $prefix and $action properties without
 * they are being overwritten by the extended class.
 *
 * @version 1.4.0
 * @package WC_OD
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_Async_Request', false ) ) {
	include_once dirname( WC_PLUGIN_FILE ) . '/includes/libraries/wp-async-request.php';
}

if ( ! class_exists( 'WP_Background_Process', false ) ) {
	include_once dirname( WC_PLUGIN_FILE ) . '/includes/libraries/wp-background-process.php';
}

/**
 * WC_OD_Background_Updater class.
 */
class WC_OD_Background_Updater extends WP_Background_Process {

	/**
	 * Initiate new background process.
	 */
	public function __construct() {
		// Uses unique prefix per blog so each blog has separate queue.
		$this->prefix = 'wp_' . get_current_blog_id();
		$this->action = 'wc_od_updater';

		parent::__construct();
	}

	/**
	 * Dispatch updater.
	 *
	 * Updater will still run via cron job if this fails for any reason.
	 */
	public function dispatch() {
		$dispatched = parent::dispatch();

		if ( is_wp_error( $dispatched ) ) {
			wc_od_log( sprintf( 'Unable to dispatch WooCommerce Order Delivery updater: %s', $dispatched->get_error_message() ), 'error', 'wc_od_db_updates' );
		}
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
	 * Is the updater running?
	 *
	 * @return boolean
	 */
	public function is_updating() {
		return false === $this->is_queue_empty();
	}

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param string $callback Update callback function.
	 * @return string|bool
	 */
	protected function task( $callback ) {
		$logger = wc_get_logger();

		include_once dirname( __FILE__ ) . '/wc-od-update-functions.php';

		$result = false;

		if ( is_callable( $callback ) ) {
			wc_od_log( sprintf( 'Running %s callback', $callback ), 'info', 'wc_od_db_updates', $logger );
			$result = (bool) call_user_func( $callback );

			if ( $result ) {
				wc_od_log( sprintf( '%s callback needs to run again', $callback ), 'info', 'wc_od_db_updates', $logger );
			} else {
				wc_od_log( sprintf( 'Finished running %s callback', $callback ), 'info', 'wc_od_db_updates', $logger );
			}
		} else {
			wc_od_log( sprintf( 'Could not find %s callback', $callback ), 'notice', 'wc_od_db_updates', $logger );
		}

		return $result ? $callback : false;
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		wc_od_log( 'Data update complete', 'info', 'wc_od_db_updates' );

		parent::complete();

		/**
		 * Fires when the plugin updater finished.
		 *
		 * @since 1.6.0
		 */
		do_action( 'wc_od_updater_complete' );
	}
}
