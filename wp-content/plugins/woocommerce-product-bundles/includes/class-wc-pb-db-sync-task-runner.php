<?php
/**
 * WC_PB_DB_Sync_Task_Runner class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Product Bundles
 * @since    5.5.0
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
 * Product Bundles DB Sync Task Runner.
 *
 * Uses https://github.com/A5hleyRich/wp-background-processing to handle tasks in the background.
 *
 * @class    WC_PB_DB_Sync_Task_Runner
 * @version  5.7.1
 */
class WC_PB_DB_Sync_Task_Runner extends WP_Background_Process {

	/**
	 * Initiate new background process.
	 */
	public function __construct() {

		// Uses unique prefix per blog so each blog has its own queue.
		$this->prefix = 'wp_' . get_current_blog_id();
		$this->action = 'wc_pb_db_sync_task_runner';

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
			WC_PB_Core_Compatibility::log( sprintf( 'Unable to dispatch task runner: %s', $dispatched->get_error_message() ), 'error', 'wc_pb_db_sync_tasks' );
		}

		return $dispatched;
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
	 * Any work to do?
	 *
	 * @return boolean
	 */
	public function is_queued() {
		return false === $this->is_queue_empty();
	}

	/**
	 * Is the task runner actually running?
	 *
	 * @return boolean
	 */
	public function is_running() {
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
	 * Runs stock status sync tasks.
	 *
	 * @param  array  $data
	 * @return mixed
	 */
	protected function task( $data ) {

		if ( ! empty( $data ) ) {

			if ( ! empty( $data[ 'delete_ids' ] ) ) {

				WC_PB_Core_Compatibility::log( sprintf( 'Discarding invalid IDs: [%s]', implode( ', ', $data[ 'delete_ids' ] ) ), 'notice', 'wc_pb_db_sync_tasks' );

				$data_store = WC_Data_Store::load( 'product-bundle' );
				$data_store->delete_bundled_items_stock_status( array_map( 'absint', $data[ 'delete_ids' ] ) );
			}

			$processed_ids = array();
			$delete_ids    = array();

			if ( ! empty( $data[ 'sync_ids' ] ) ) {

				$sync_ids = array_map( 'absint', $data[ 'sync_ids' ] );

				WC_PB_Core_Compatibility::log( sprintf( 'Syncing IDs: [%s]', implode( ', ', $sync_ids ) ), 'info', 'wc_pb_db_sync_tasks' );

				foreach ( $sync_ids as $id ) {

					if ( ( $product = wc_get_product( $id ) ) && $product->is_type( 'bundle' ) ) {
						if ( $product->sync_bundled_items_stock_status() ) {
							$product->get_data_store()->save_bundled_items_stock_status( $product );
						}
						$processed_ids[] = $id;
					} else {
						$delete_ids[] = $id;
					}

					if ( self::time_exceeded() || self::memory_exceeded() || sizeof( $processed_ids ) >= 50 ) {

						$resync_ids = array_diff( $sync_ids, $processed_ids, $delete_ids );

						// Anything left to process?
						if ( ! empty( $resync_ids ) ) {

							WC_PB_Core_Compatibility::log( sprintf( 'Restarting task - processed %s IDs.', sizeof( $processed_ids ) ), 'info', 'wc_pb_db_sync_tasks' );

							// Ensures that the remaining IDs will be processed before everything else in the queue.
							add_filter( $this->identifier . '_time_exceeded', '__return_true' );

							return array(
								'sync_ids'   => $resync_ids,
								'delete_ids' => $delete_ids
							);
						}
					}
				}

				if ( ! empty( $delete_ids ) ) {

					WC_PB_Core_Compatibility::log( sprintf( 'Discarding invalid IDs: [%s]', implode( ', ', $delete_ids ) ), 'notice', 'wc_pb_db_sync_tasks' );

					$data_store = WC_Data_Store::load( 'product-bundle' );
					$data_store->delete_bundled_items_stock_status( $delete_ids );
				}

				if ( sizeof( $processed_ids ) + sizeof( $delete_ids ) === sizeof( $sync_ids ) ) {
					WC_PB_Core_Compatibility::log( 'Task complete.', 'info', 'wc_pb_db_sync_tasks' );
					return false;
				}

			} else {

				WC_PB_Core_Compatibility::log( 'Task complete.', 'info', 'wc_pb_db_sync_tasks' );
			}
		}

		return false;
	}

	/**
	 * When all tasks complete, create a log entry.
	 */
	protected function complete() {
		WC_PB_Core_Compatibility::log( 'Sync complete.', 'info', 'wc_pb_db_sync_tasks' );
		parent::complete();
	}
}
