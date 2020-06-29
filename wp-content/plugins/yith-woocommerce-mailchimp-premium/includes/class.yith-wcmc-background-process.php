<?php
/**
 * Background Process class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Mailchimp
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCMC' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WC_Background_Process', false ) ) {
	include_once( YITH_WCMC_INC . 'libraries/class-wc-background-process.php' );
} // Include WC_Background_Process local copy, when WC < 3.3.1

if ( ! class_exists( 'YITH_WCMC_Background_Process' ) ) {
	/**
	 * WooCommerce MailChimp Background Process
	 * This specific process, handles batches of tasks; anyway, it allows also for single task batches,
	 * identified by unique "id", used as part of the batch name
	 *
	 * When scheduling single task batches, this class will always override previous batches with the same id,
	 * to avoid executing multiple operations, when just the last in queue really matters
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMC_Background_Process extends WC_Background_Process {

		/**
		 * Cron_hook_identifier
		 *
		 * @var mixed
		 * @access protected
		 */
		protected $dispatch_cron_hook_identifier;

		/**
		 * Constructor method
		 *
		 * @return \YITH_WCMC_Background_Process
		 */
		public function __construct() {
			$this->prefix = 'yith_wcmc';
			$this->action = 'bp';

			parent::__construct();

			$this->dispatch_cron_hook_identifier = $this->identifier . '_dispatch_cron';

			// schedule (if not scheduled already) cron to process background queue
			add_action( 'init', array( $this, 'schedule_dispatch_cron' ) );

			add_action( $this->dispatch_cron_hook_identifier, array( $this, 'dispatch' ) );
		}

		/**
		 * Schedule dispatch cron
		 *
		 * @return void
		 */
		public function schedule_dispatch_cron() {
			if ( ! wp_next_scheduled( $this->dispatch_cron_hook_identifier ) ) {
				wp_schedule_event( time(), 'yith_wcmc_cron_schedule', $this->dispatch_cron_hook_identifier );
			}
		}

		/**
		 * Executes a batch of operations, and pop them out of the queue
		 *
		 * @param $batch array Set of data to process
		 *
		 * @return bool|array Array of data if further processing is required, or false if batch could be popped out of the queue
		 */
		public function process_batch( $batch ) {
			if ( count( $batch ) == 1 ) {
				$res = $this->task( $batch[0] );

				if ( ! empty( $res ) ) {
					$res = array( $res );
				}
			} else {
				$res = $this->batch( $batch );
			}

			return $res;
		}

		/**
		 * Executes operation to digest a batch
		 *
		 * @param $items mixed Array of tasks to perform
		 *
		 * @return bool|mixed False if operation is complete; data for further operations otherwise
		 */
		public function batch( $items ) {
			if ( ! defined( 'YITH_WCMC_DOING_BATCH' ) ) {
				define( 'YITH_WCMC_DOING_BATCH', 1 );
			}

			if ( ! empty( $items ) ) {
				foreach ( $items as $item ) {
					$this->task( $item );
				}
			}

			$batch_ops = YITH_WCMC_Premium()->get_batch_ops( true );

			if ( ! empty( $batch_ops ) ) {
				YITH_WCMC_Premium()->do_request( 'post', 'batches', array( 'operations' => $batch_ops ) );
			}

			return false;
		}

		/**
		 * Task
		 *
		 * Executes single task batches
		 *
		 * @param mixed $item Queue item to iterate over.
		 *
		 * @return mixed
		 */
		public function task( $item ) {
			$processor = "maybe_process_{$item['type']}";

			$res = YITH_WCMC_Store()->$processor( $item['id'] );

			YITH_WCMC_Premium()->log( sprintf( _x( 'Task completed: %s %s', 'log message', 'yith-woocommerce-mailchimp' ), $item['type'], $item['id'] ) );

			// stop task iteration, no matter if an error occurred
			// in most cases, if error occurred once (for whatever reason) it will occur in next instances too
			// to avoid creating never-ending tasks and cluttering queue up, we remove items from the queue no matter what the final result
			return false;
		}

		/**
		 * Handle.
		 *
		 * Pass each queue item to the task handler, while remaining
		 * within server memory and time limit constraints.
		 */
		protected function handle() {
			$this->lock_process();

			do {
				$batch = $this->get_batch();
				$res   = false;

				if ( isset( $batch->data ) ) {
					$res = $this->process_batch( $batch->data );
				}

				if ( false !== $res ) {
					$batch->data = $res;
				} else {
					$batch->data = false;
				}

				// Update or delete current batch.
				if ( ! empty( $batch->data ) ) {
					$this->update( $batch->key, $batch->data );
				} else {
					$this->delete( $batch->key );
				}
			} while ( ! $this->batch_limit_exceeded() && ! $this->is_queue_empty() );

			$this->unlock_process();

			// Start next batch or complete process.
			if ( ! $this->is_queue_empty() ) {
				$this->dispatch();
			} else {
				$this->complete();
			}
		}

		/**
		 * Get batch.
		 *
		 * @param $id string|bool Task id for the batch to retrieve, or false if batches of any kind should be retrieved
		 *
		 * @return stdClass|bool Return the first batch from the queue, of false if queue is empty
		 */
		protected function get_batch( $id = false ) {
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

			$id .= ! empty( $id ) ? '_' : '';

			$key = $wpdb->esc_like( $this->identifier . '_batch_' . $id ) . '%';

			$query = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE {$column} LIKE %s ORDER BY {$key_column} ASC LIMIT 1", $key ) ); // @codingStandardsIgnoreLine.

			if ( empty( $query ) ) {
				return false;
			}

			$batch       = new stdClass();
			$batch->key  = $query->$column;
			$batch->data = maybe_unserialize( $query->$value_column );

			return $batch;
		}

		/**
		 * Generate key
		 *
		 * Generates a unique key based on microtime. Queue items are
		 * given a unique key so that they can be merged upon save.
		 *
		 * @param int $length Length.
		 *
		 * @return string
		 */
		protected function generate_key( $length = 64, $id = false ) {
			if ( $id ) {
				$matching_task = $this->get_batch( $id );

				if ( $matching_task ) {
					return $matching_task->key;
				}
			}

			$unique  = md5( microtime() . rand() );
			$id      .= ! empty( $id ) ? '_' : '';
			$prepend = $this->identifier . '_batch_' . $id;

			return substr( $prepend . $unique, 0, $length );
		}

		/**
		 * Save queue
		 *
		 * @return $this
		 */
		public function save() {
			if ( ! empty( $this->data ) ) {

				$id = false;

				if ( count( $this->data ) == 1 ) {
					$id = md5( $this->data[0]['type'] . '_' . $this->data[0]['id'] );
				}

				$key = $this->generate_key( 64, $id );
				update_site_option( $key, $this->data );
			}

			return $this;
		}
	}
}