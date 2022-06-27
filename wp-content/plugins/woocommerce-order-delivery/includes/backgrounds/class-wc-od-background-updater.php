<?php
/**
 * Background Updater.
 *
 * Inspired in the WC_Background_Updater class.
 *
 * @package WC_OD/Backgrounds
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Background_Process', false ) ) {
	include_once WC_OD_PATH . 'includes/abstracts/abstract-wc-od-background-process.php';
}

/**
 * Class WC_OD_Background_Updater.
 */
class WC_OD_Background_Updater extends WC_OD_Background_Process {

	/**
	 * Initiate new background process.
	 *
	 * @since 1.4.0
	 */
	public function __construct() {
		$this->action = 'updater';

		parent::__construct();
	}

	/**
	 * Dispatch updater.
	 *
	 * Updater will still run via cron job if this fails for any reason.
	 *
	 * @since 1.4.0
	 */
	public function dispatch() {
		$dispatched = parent::dispatch();

		if ( is_wp_error( $dispatched ) ) {
			wc_od_log( sprintf( 'Unable to dispatch WooCommerce Order Delivery updater: %s', $dispatched->get_error_message() ), 'error', 'wc_od_db_updates' );
		}
	}

	/**
	 * Handle cron healthcheck.
	 *
	 * Restart the background process if not already running
	 * and data exists in the queue.
	 *
	 * @since 1.4.0
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
	 *
	 * @since 1.4.0
	 */
	protected function schedule_event() {
		if ( ! wp_next_scheduled( $this->cron_hook_identifier ) ) {
			wp_schedule_event( time() + 10, $this->cron_interval_identifier, $this->cron_hook_identifier );
		}
	}

	/**
	 * Is the updater running?
	 *
	 * @since 1.4.0
	 *
	 * @return boolean
	 */
	public function is_updating() {
		return false === $this->is_queue_empty();
	}

	/**
	 * Task.
	 *
	 * @since 1.4.0
	 *
	 * @param string $callback Update callback function.
	 * @return string|bool
	 */
	protected function task( $callback ) {
		include_once WC_OD_PATH . 'includes/wc-od-update-functions.php';

		$result = false;

		if ( is_callable( $callback ) ) {
			wc_od_log( sprintf( 'Running %s callback', $callback ), 'info', 'wc_od_db_updates' );
			$result = (bool) call_user_func( $callback );

			if ( $result ) {
				wc_od_log( sprintf( '%s callback needs to run again', $callback ), 'info', 'wc_od_db_updates' );
			} else {
				wc_od_log( sprintf( 'Finished running %s callback', $callback ), 'info', 'wc_od_db_updates' );
			}
		} else {
			wc_od_log( sprintf( 'Could not find %s callback', $callback ), 'notice', 'wc_od_db_updates' );
		}

		return $result ? $callback : false;
	}

	/**
	 * Complete.
	 *
	 * @since 1.4.0
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
