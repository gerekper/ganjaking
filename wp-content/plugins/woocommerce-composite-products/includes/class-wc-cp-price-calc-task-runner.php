<?php
/**
 * WC_CP_Price_Calc_Task_Runner class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Product Bundles
 * @since    4.0.0
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
 * @class    WC_CP_Price_Calc_Task_Runner
 * @version  4.0.0
 */
class WC_CP_Price_Calc_Task_Runner extends WP_Background_Process {

	/**
	 * Initiate new background process.
	 */
	public function __construct() {

		// Uses unique prefix per blog so each blog has its own queue.
		$this->prefix = 'wp_' . get_current_blog_id();
		$this->action = 'wc_cp_price_calc_task_runner';

		parent::__construct();
	}

	/**
	 * Returns the cron action identifier.
	 *
	 * @since  4.0.0
	 *
	 * @return string
	 */
	public function get_cron_hook_identifier() {
		return $this->cron_hook_identifier;
	}

	/**
	 * Generates a key based on product ID.
	 *
	 * @param int  $length
	 *
	 * @return string
	 */
	protected function generate_key( $length = 64 ) {
		return $this->get_task_key( $this->data[ 0 ], $length );
	}

	/**
	 * Generates a key based on product ID.
	 *
	 * @param array  $task_data
	 * @param int    $length
	 *
	 * @return string
	 */
	protected function get_task_key( $task_data, $length = 64 ) {
		return substr( $this->identifier . '_batch_' . $task_data[ 'composite_id' ], 0, $length );
	}

	/**
	 * Retrieves the stored data of a running task.
	 *
	 * @param  string  $key
	 *
	 * @return array
	 */
	protected function get_task_data( $key ) {

		$batch = get_site_option( $key, array(), false );
		$task  = ! empty( $batch ) ? current( $batch ) : false;

		return $task && ! empty( $task[ 'composite_id' ] ) && ! empty( $task[ 'resume' ] ) && ! empty( $task[ 'hash' ] ) ? $task : false;
	}

	/**
	 * Dispatch updater.
	 */
	public function dispatch() {

		$dispatched = parent::dispatch();

		if ( is_wp_error( $dispatched ) ) {
			WC_CP_Core_Compatibility::log( sprintf( 'Unable to dispatch task runner: %s', $dispatched->get_error_message() ), 'error', 'wc_cp_price_calc_tasks' );
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

		$process_task = true;

		if ( empty( $data ) || empty( $data[ 'composite_id' ] ) || empty( $data[ 'resume' ] ) || empty( $data[ 'hash' ] ) ) {

			WC_CP_Core_Compatibility::log( 'Invalid task data.', 'info', 'wc_cp_price_calc_tasks' );
			return false;
		}

		$hash        = $data[ 'hash' ];
		$iteration   = isset( $data[ 'iteration' ] ) ? absint( $data[ 'iteration' ] ) : 1;
		$resume_from = absint( $data[ 'resume' ] );

		WC_CP_Core_Compatibility::log( sprintf( 'Calculating min/max catalog price of composite product #%s. Resuming from permutation %s. Iteration %s.', absint( $data[ 'composite_id' ] ), $resume_from, $iteration ), 'info', 'wc_cp_price_calc_tasks' );

		if ( false === ( $product = wc_get_product( $data[ 'composite_id' ] ) ) || ! $product->is_type( 'composite' ) ) {

			WC_CP_Core_Compatibility::log( 'Invalid product ID. Task aborted.', 'info', 'wc_cp_price_calc_tasks' );
			return false;
		}

		$price_data = $product->get_data_store()->read_price_data( $product, array(
			'min'       => isset( $data[ 'min' ] ) ? $data[ 'min' ] : false,
			'max'       => isset( $data[ 'max' ] ) ? $data[ 'max' ] : false,
			'resume'    => $resume_from,
			'iteration' => $iteration
		) );

		// If the hash changed, start over!
		if ( $stored_data = $this->get_task_data( $this->get_task_key( $data ) ) ) {

			if ( $price_data[ 'hash' ] !== $hash || $price_data[ 'hash' ] !== $stored_data[ 'hash' ] ) {

				WC_CP_Core_Compatibility::log( 'Τask hash changed: Starting over!', 'info', 'wc_cp_price_calc_tasks' );

				// Ensures that the task will be processed before everything else in the queue.
				add_filter( $this->identifier . '_time_exceeded', '__return_true' );

				return array(
					'hash'         => isset( $stored_data[ 'iteration' ] ) ? $stored_data[ 'hash' ] : $price_data[ 'hash' ],
					'resume'       => 1,
					'composite_id' => $product->get_id()
				);
			}
		}

		if ( isset( $price_data[ 'resume' ] ) ) {

			WC_CP_Core_Compatibility::log( 'Re-scheduling task...', 'info', 'wc_cp_price_calc_tasks' );

			// Ensures that the task will be processed before everything else in the queue.
			add_filter( $this->identifier . '_time_exceeded', '__return_true' );

			return array(
				'min'          => $price_data[ 'min' ],
				'max'          => $price_data[ 'max' ],
				'hash'         => $price_data[ 'hash' ],
				'resume'       => $price_data[ 'resume' ],
				'iteration'    => $iteration + 1,
				'composite_id' => $product->get_id()
			);

		} elseif ( 'failed' === $price_data[ 'status' ] ) {

			WC_CP_Core_Compatibility::log( 'Max allowed iterations count exceeded. Task aborted.', 'info', 'wc_cp_price_calc_tasks' );
			return false;
		}

		WC_CP_Core_Compatibility::log( 'Task complete.', 'info', 'wc_cp_price_calc_tasks' );
		return false;
	}
}
