<?php
/**
 * Class YITH_WCBK_Background_Processes
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

require_once 'abstract-yith-wcbk-background-process.php';
require_once 'class-yith-wcbk-background-process-google-calendar-sync.php';
require_once 'class-yith-wcbk-background-process-product-data.php';
require_once 'functions.yith-wcbk-background-process-funtions.php';

if ( ! class_exists( 'YITH_WCBK_Background_Processes' ) ) {
	/**
	 * Class YITH_WCBK_Background_Processes
	 * handle background processes
	 */
	class YITH_WCBK_Background_Processes {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * Google calendar sync - Background process instance.
		 *
		 * @var YITH_WCBK_Background_Process_Google_Calendar_Sync
		 */
		public $google_calendar_sync;

		/**
		 * Product data - Background process instance.
		 *
		 * @var YITH_WCBK_Background_Process_Product_Data
		 */
		public $product_data;

		/**
		 * Product data to dispatch
		 *
		 * @var bool
		 */
		private $product_data_to_dispatch = false;


		/**
		 * YITH_WCBK_Background_Processes constructor.
		 */
		protected function __construct() {
			$this->google_calendar_sync = new YITH_WCBK_Background_Process_Google_Calendar_Sync();
			$this->product_data         = new YITH_WCBK_Background_Process_Product_Data();

			add_action( 'yith_wcbk_background_process_product_data_update', array( $this, 'product_data_update' ), 10, 1 );

			add_action( 'shutdown', array( $this, 'shutdown' ) );

			/**
			 * Handle background processing through Action Scheduler.
			 *
			 * @since 4.1.0
			 */
			add_action( 'yith_wcbk_background_process_run_callback', array( $this, 'run_callback' ), 10, 3 );
		}

		/**
		 * Schedule product data update
		 *
		 * @param int $product_id Product ID.
		 * @param int $after      After seconds.
		 */
		public function schedule_product_data_update( $product_id, $after = 10 ) {
			$hook = 'yith_wcbk_background_process_product_data_update';
			$args = array( $product_id );

			wp_clear_scheduled_hook( $hook, $args );
			wp_schedule_single_event( time() + $after, $hook, $args );

			yith_wcbk_maybe_debug( sprintf( 'Background Process: Product Data update scheduled for product #%s', $product_id ), YITH_WCBK_Logger_Groups::BACKGROUND_PROCESS );
		}

		/**
		 * Add the product data update to the queue of product_data background process
		 * before calling it you should delete the cache and delete external_calendars_last_sync for external update
		 *
		 * @param int $product_id Product ID.
		 */
		public function product_data_update( $product_id ) {
			yith_wcbk_maybe_debug( sprintf( 'Background Process: Product Data update added to queue for product: %s', $product_id ), YITH_WCBK_Logger_Groups::BACKGROUND_PROCESS );

			$this->product_data->push_to_queue(
				array(
					'callback' => 'yith_wcbk_bg_process_booking_product_regenerate_product_data',
					'params'   => array( $product_id ),
				)
			);
			$this->product_data_to_dispatch = true;
		}

		/**
		 * Fires dispatch, if needed, on shutdown
		 */
		public function shutdown() {
			if ( $this->product_data_to_dispatch ) {
				$this->product_data->save()->dispatch();
			}
		}

		/**
		 * Is a specific group running?
		 *
		 * @param string $group The group.
		 *
		 * @return bool
		 */
		public function is_group_running( string $group = 'yith-wcbk-bg-processes' ): bool {
			$running = WC()->queue()->search(
				array(
					'status'   => array( 'pending', 'in-progress' ),
					'group'    => $group,
					'per_page' => 1,
				)
			);

			return (bool) count( $running );
		}

		/**
		 * Schedule single process.
		 *
		 * @param string|callable $callback Callback name.
		 * @param array           $params   Params.
		 * @param string          $group    The group.
		 */
		public function schedule_single( $callback, array $params = array(), string $group = 'yith-wcbk-bg-processes' ) {
			static $last_timestamp = 0;

			if ( ! $last_timestamp ) {
				$last_timestamp = time() + 1;
			}

			WC()->queue()->schedule_single(
				$last_timestamp,
				'yith_wcbk_background_process_run_callback',
				array(
					'callback' => $callback,
					'params'   => $params,
					'group'    => $group,
				),
				$group
			);

			$last_timestamp ++;
		}

		/**
		 * Run callback.
		 *
		 * @param string|callable $callback The callback.
		 * @param array           $params   The params.
		 * @param string          $group    The group.
		 */
		public function run_callback( $callback, array $params = array(), string $group = 'yith-wcbk-bg-processes' ) {
			if ( is_callable( $callback ) ) {
				$this->run_update_callback_start();
				$result = (bool) ( ! ! $params ? call_user_func_array( $callback, $params ) : call_user_func( $callback ) );
				$this->run_update_callback_end( $callback, $params, $group, $result );
			}
		}

		/**
		 * Triggered when a callback will run.
		 */
		protected function run_update_callback_start() {
			if ( ! defined( 'YITH_WCBK_BG_PROCESS_RUNNING' ) ) {
				define( 'YITH_WCBK_BG_PROCESS_RUNNING', true );
			}
		}

		/**
		 * Triggered when a callback has ran.
		 *
		 * @param string|callable $callback Callback name.
		 * @param array           $params   Params.
		 * @param string          $group    The group.
		 * @param bool            $result   Return value from callback. Non-false need to run again.
		 */
		protected function run_update_callback_end( $callback, array $params, string $group = 'yith-wcbk-bg-processes', bool $result = false ) {
			if ( $result ) {
				WC()->queue()->add(
					'yith_wcbk_background_process_run_callback',
					array(
						'callback' => $callback,
						'params'   => $params,
						'group'    => $group,
					),
					$group
				);
			}
		}
	}
}
