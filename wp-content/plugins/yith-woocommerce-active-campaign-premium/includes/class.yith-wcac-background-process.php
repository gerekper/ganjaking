<?php
/**
 * Background Process class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 2.0.0
 */

if ( ! defined( 'YITH_WCAC' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WC_Background_Process', false ) ) {
	include_once( YITH_WCAC_INC . 'libraries/class-wc-background-process.php' );
} // Include WC_Background_Process local copy, when WC < 3.3.1

if ( ! class_exists( 'YITH_WCAC_Background_Process' ) ) {
	/**
	 * WooCommerce Active Campaign Background Process
	 * This specific process, handles batches of tasks; anyway, it allows also for single task batches,
	 * identified by unique "id", used as part of the batch name
	 *
	 * When scheduling single task batches, this class will always override previous batches with the same id,
	 * to avoid executing multiple operations, when just the last in queue really matters
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAC_Background_Process extends WC_Background_Process {

		/**
		 * Cron_hook_identifier
		 *
		 * @var mixed
		 * @access protected
		 */
		protected $dispatch_cron_hook_identifier;

		/**
		 * Constructor method
		 */
		public function __construct() {
			$this->prefix = 'yith_wcac';
			$this->action = 'bp';

			parent::__construct();

			$this->dispatch_cron_hook_identifier = $this->identifier . '_dispatch_cron';

			// schedule (if not scheduled already) cron to process background queue.
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
				wp_schedule_event( time(), 'yith_wcac_cron_schedule', $this->dispatch_cron_hook_identifier );
			}
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
			// if item is not yet ready to  be processed, move it to another batch, to be processed at the end of the queue.
			if ( ! $this->_should_process( $item ) ) {
				return $item;
			}

			if ( ! defined( 'YITH_WCAC_DOING_TASK' ) ) {
				define( 'YITH_WCAC_DOING_TASK', true );
			}

			$processor = "maybe_process_{$item['type']}";

			if ( method_exists( YITH_WCAC_Deep_Data(), $processor ) ) {
				$params = $this->_get_processor_params( $item );
				$res    = call_user_func_array( [ YITH_WCAC_Deep_Data(), $processor ], $params );
			}

			// translators: 1. Task type (order/product/...). 2. Item id.
			YITH_WCAC()->log( sprintf( _x( 'Task completed: %1$s %2$s', 'log message', 'yith-woocommerce-active-campaign' ), $item['type'], $item['id'] ) );

			// stop task iteration, no matter if an error occurred
			// in most cases, if error occurred once (for whatever reason) it will occur in next instances too
			// to avoid creating never-ending tasks and cluttering queue up, we remove items from the queue no matter what the final result.
			return false;
		}

		/**
		 * Get batch.
		 *
		 * @param string|bool $task_id Task id for the batch to retrieve, or false if batches of any kind should be retrieved.
		 *
		 * @return stdClass|bool Return the first batch from the queue, of false if queue is empty
		 */
		protected function get_batch( $task_id = false ) {
			global $wpdb;

			$query      = "SELECT * FROM {$wpdb->yith_wcac_background_process_batches}";
			$where      = 'WHERE 1=1';
			$order_by   = 'ORDER BY ID ASC';
			$limit      = 'LIMIT 1';
			$where_args = array();

			if ( $task_id ) {
				$where .= ' AND task_id = %s';
				$where_args[] = $task_id;
			}

			$query = "{$query} {$where} {$order_by} {$limit}";

			if ( ! empty( $where_args ) ) {
				$query = $wpdb->prepare( $where, $where_args ); // @codingStandardsIgnoreLine.
			}

			$row = $wpdb->get_row( $query ); // @codingStandardsIgnoreLine.

			if ( empty( $row ) ) {
				return false;
			}

			$batch          = new stdClass();
			$batch->key     = $row->batch_key;
			$batch->task_id = $row->task_id;
			$batch->data    = maybe_unserialize( $row->batch );

			return $batch;
		}

		/**
		 * Generate key
		 *
		 * Generates a unique key based on microtime. Queue items are
		 * given a unique key so that they can be merged upon save.
		 *
		 * @param int      $length  Length.
		 * @param int|bool $task_id Id of the task.
		 *
		 * @return string
		 */
		protected function generate_key( $length = 32, $task_id = false ) {
			if ( $task_id ) {
				$matching_batch = $this->get_batch( $task_id );

				if ( $matching_batch ) {
					return $matching_batch->key;
				}
			}

			$unique  = md5( microtime() . rand() );

			return substr( $unique, 0, $length );
		}

		/**
		 * Save queue
		 *
		 * @return $this
		 */
		public function save() {
			global $wpdb;

			if ( ! empty( $this->data ) ) {

				$task_id = false;
				$key     = false;

				if ( count( $this->data ) == 1 ) {
					$task_id = md5( $this->data[0]['type'] . '_' . $this->data[0]['id'] );
				}

				// search for batch with a specific task id.
				if ( $task_id ) {
					$key = $wpdb->get_var( $wpdb->prepare( "SELECT batch_key FROM {$wpdb->yith_wcac_background_process_batches} WHERE task_id = %s", $task_id ) );
				}

				if ( ! empty( $key ) ) {
					// task already exist; let's update it.
					$wpdb->update(
						$wpdb->yith_wcac_background_process_batches,
						array(
							'batch' => maybe_serialize( $this->data ),
							'ts'    => gmdate( 'Y-m-d H:i:s' ),
						),
						array( 'batch_key' => $key ),
						array( '%s', '%s' ),
						array( '%s' )
					);
				} else {
					// new batch; let's add a new record.
					$key = $this->generate_key( 64 );

					$record = array(
						'batch_key' => $key,
						'batch'     => maybe_serialize( $this->data ),
					);
					$record_format = array( '%s', '%s' );

					if ( $task_id ) {
						$record['task_id'] = $task_id;
						$record_format[] = '%s';
					}

					$wpdb->insert( $wpdb->yith_wcac_background_process_batches, $record, $record_format );
				}
			}

			return $this;
		}

		/**
		 * Update queue
		 *
		 * @param string $key Key.
		 * @param array  $data Data.
		 *
		 * @return $this
		 */
		public function update( $key, $data ) {
			global $wpdb;

			if ( ! empty( $data ) ) {
				$wpdb->update(
					$wpdb->yith_wcac_background_process_batches,
					array(
						'batch' => maybe_serialize( $data ),
						'ts'    => gmdate( 'Y-m-d H:i:s' ),
					),
					array( 'batch_key' => $key ),
					array( '%s', '%s' ),
					array( '%s' )
				);
			}

			return $this;
		}

		/**
		 * Delete queue
		 *
		 * @param string $key Key.
		 *
		 * @return $this
		 */
		public function delete( $key ) {
			global $wpdb;

			$wpdb->delete( $wpdb->yith_wcac_background_process_batches, array( 'batch_key' => $key ), array( '%s' ) );

			return $this;
		}

		/**
		 * Delete all batches.
		 *
		 * @return WC_Background_Process
		 */
		public function delete_all_batches() {
			global $wpdb;

			$wpdb->query( esc_sql( "DELETE FROM {$wpdb->yith_wcac_background_process_batches} WHERE 1=1" ) ); // @codingStandardsIgnoreLine.

			return $this;
		}

		/**
		 * Is queue empty.
		 *
		 * @return bool
		 */
		protected function is_queue_empty() {
			global $wpdb;

			$count = $wpdb->get_var( esc_sql( "SELECT COUNT(*) FROM {$wpdb->yith_wcac_background_process_batches}" ) ); // @codingStandardsIgnoreLine.

			return ! ( $count > 0 );
		}

		/**
		 * Check if we can process a task
		 *
		 * @param array $item Item to check.
		 * @return bool Whether or not item should be processed
		 */
		private function _should_process( $item ) {
			return apply_filters( 'yith_wcac_should_task_process', true, $item );
		}

		/**
		 * Returns a formatted array of parameters for the method that will actually process task
		 *
		 * @param array $task Task that should be processed.
		 *
		 * @return array Array of formatted parameters for the handler.
		 */
		private function _get_processor_params( $task ) {
			$unset_keys = apply_filters( 'yith_wcac_bp_task_unset_keys', [ 'type' ], $task );

			if ( ! empty( $unset_keys ) ) {
				foreach ( $unset_keys as $key ) {
					if ( isset( $task[ $key ] ) ) {
						unset( $task[ $key ] );
					}
				}
			}

			return array_values( $task );
		}
	}
}