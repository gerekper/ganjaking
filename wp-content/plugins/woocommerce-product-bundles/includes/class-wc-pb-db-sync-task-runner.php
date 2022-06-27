<?php
/**
 * WC_PB_DB_Sync_Task_Runner class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Background_Process', false ) ) {
	include_once( WC_ABSPATH . 'includes/abstracts/class-wc-background-process.php' );
}

/**
 * Product Bundles DB Sync Task Runner.
 *
 * Uses https://github.com/A5hleyRich/wp-background-processing to handle tasks in the background.
 *
 * @class    WC_PB_DB_Sync_Task_Runner
 * @version  6.14.1
 */
class WC_PB_DB_Sync_Task_Runner extends WC_Background_Process {

	/**
	 * Fallback to cron every minute.
	 *
	 * @var int
	 */
	protected $cron_interval = 1;

	/**
	 * Limit the queue size just in case loopback + cron don't work.
	 *
	 * @var int
	 */
	protected $queue_max_size = 10;

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

		WC_PB_Core_Compatibility::log( 'Dispatching...', 'info', 'wc_pb_db_sync_tasks' );

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
	 * Get batch key prefix.
	 *
	 * @param  boolean $escape
	 * @return string
	 */
	protected function get_batch_key_prefix( $escaped = false ) {
		global $wpdb;
		return apply_filters( 'woocommerce_bundles_sync_task_runner_esc_batch_query_prefix', $escaped ) ? $wpdb->esc_like( $this->identifier . '_batch_' ) : $this->identifier . '_batch_';
	}

	/**
	 * Get batch.
	 *
	 * @return stdClass Return the first batch from the queue.
	 */
	protected function get_batch() {
		global $wpdb;

		$table        = $wpdb->options;
		$column       = 'option_name';
		$key_column   = 'option_id';
		$value_column = 'option_value';

		if ( is_multisite() ) {
			$table        = $wpdb->sitemeta;
			$column       = 'meta_key';
			$key_column   = 'meta_id';
			$value_column = 'meta_value';
		}

		$key = $this->get_batch_key_prefix( true ) . '%';

		$query = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE {$column} LIKE %s ORDER BY {$key_column} ASC LIMIT 1", $key ) ); // @codingStandardsIgnoreLine.

		$batch       = new stdClass();
		$batch->key  = $query->$column;
		$batch->data = array_filter( (array) maybe_unserialize( $query->$value_column ) );

		return $batch;
	}

	/**
	 * Is queue empty.
	 *
	 * @return bool
	 */
	protected function is_queue_empty() {
		global $wpdb;

		$table  = $wpdb->options;
		$column = 'option_name';

		if ( is_multisite() ) {
			$table  = $wpdb->sitemeta;
			$column = 'meta_key';
		}

		$key = $this->get_batch_key_prefix( true ) . '%';

		$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE {$column} LIKE %s", $key ) ); // @codingStandardsIgnoreLine.

		return ! ( $count > 0 );
	}

	/**
	 * Is queue full.
	 *
	 * @return bool
	 */
	protected function is_queue_full() {
		global $wpdb;

		$table  = $wpdb->options;
		$column = 'option_name';

		if ( is_multisite() ) {
			$table  = $wpdb->sitemeta;
			$column = 'meta_key';
		}

		$key = $this->get_batch_key_prefix( true ) . '%';

		$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE {$column} LIKE %s", $key ) ); // @codingStandardsIgnoreLine.

		return $count >= apply_filters( 'woocommerce_bundles_sync_task_runner_queue_max_size', $this->queue_max_size );
	}

	/**
	 * Delete all batches.
	 *
	 * @return WC_Background_Process
	 */
	public function delete_all_batches() {
		global $wpdb;

		$table  = $wpdb->options;
		$column = 'option_name';

		if ( is_multisite() ) {
			$table  = $wpdb->sitemeta;
			$column = 'meta_key';
		}

		$key = $this->get_batch_key_prefix( true ) . '%';

		$wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE {$column} LIKE %s", $key ) ); // @codingStandardsIgnoreLine.

		return $this;
	}

	/**
	 * Check if the queue is full before adding a new item.
	 *
	 * @return bool
	 */
	public function maybe_save() {

		$saved = false;

		if ( ! $this->is_queue_full() ) {
			parent::save();
			$saved = true;
		}

		return $saved;
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
				$data_store->delete_bundled_items_stock_sync_status( array_map( 'absint', $data[ 'delete_ids' ] ) );
			}

			$processed_ids = array();
			$delete_ids    = array();

			if ( ! empty( $data[ 'sync_ids' ] ) ) {

				$sync_ids = array_map( 'absint', $data[ 'sync_ids' ] );

				WC_PB_Core_Compatibility::log( sprintf( 'Syncing IDs: [%s]', implode( ', ', $sync_ids ) ), 'info', 'wc_pb_db_sync_tasks' );

				foreach ( $sync_ids as $id ) {

					if ( ( $product = wc_get_product( $id ) ) && $product->is_type( 'bundle' ) ) {
						$product->sync_stock();
						$processed_ids[] = $id;
					} else {
						$delete_ids[] = $id;
					}

					if ( self::time_exceeded() || self::memory_exceeded() || count( $processed_ids ) >= 50 ) {

						$resync_ids = array_diff( $sync_ids, $processed_ids, $delete_ids );

						// Anything left to process?
						if ( ! empty( $resync_ids ) ) {

							WC_PB_Core_Compatibility::log( sprintf( 'Restarting task - processed %s IDs.', count( $processed_ids ) ), 'info', 'wc_pb_db_sync_tasks' );

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
					$data_store->delete_bundled_items_stock_sync_status( $delete_ids );
				}

				if ( count( $processed_ids ) + count( $delete_ids ) === count( $sync_ids ) ) {
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
